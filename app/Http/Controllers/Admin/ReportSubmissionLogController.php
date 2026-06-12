<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportSubmissionLogController extends Controller
{
    // Log files live in storage/logs/report-submissions-YYYY-MM-DD.log
    private string $logPrefix = 'report-submissions';

    public function index(Request $request)
    {
        $files = $this->getLogFiles();
        $selectedDate = $request->get('date', $files[0] ?? null);
        $statusFilter = $request->get('status', '');
        $typeFilter   = $request->get('report_type', '');
        $search       = trim($request->get('search', ''));

        $entries = [];
        if ($selectedDate && isset($files[array_search($selectedDate, $files)])) {
            $entries = $this->parseLogFile($selectedDate);
        }

        // Apply filters
        if ($statusFilter) {
            $entries = array_filter($entries, fn($e) => ($e['event_status'] ?? '') === $statusFilter);
        }
        if ($typeFilter) {
            $entries = array_filter($entries, fn($e) => ($e['ctx']['report_type'] ?? '') === $typeFilter);
        }
        if ($search !== '') {
            $entries = array_filter($entries, function ($e) use ($search) {
                $haystack = strtolower(json_encode($e));
                return str_contains($haystack, strtolower($search));
            });
        }

        $entries = array_values($entries);

        // Summary counts for the selected file (before filters)
        $allEntries = $selectedDate ? $this->parseLogFile($selectedDate) : [];
        $summary = [
            'total'            => count($allEntries),
            'success'          => count(array_filter($allEntries, fn($e) => ($e['event_status'] ?? '') === 'success')),
            'failed'           => count(array_filter($allEntries, fn($e) => ($e['event_status'] ?? '') === 'failed')),
            'duplicate'        => count(array_filter($allEntries, fn($e) => ($e['event_status'] ?? '') === 'duplicate')),
            'validation_error' => count(array_filter($allEntries, fn($e) => ($e['event_status'] ?? '') === 'validation_error')),
            'attempts'         => count(array_filter($allEntries, fn($e) => ($e['level'] ?? '') === 'INFO' && str_contains($e['event'] ?? '', 'ATTEMPT'))),
        ];

        return view('admin.report-submission-logs.index', compact(
            'files', 'selectedDate', 'entries', 'summary',
            'statusFilter', 'typeFilter', 'search'
        ));
    }

    // Returns list of available log dates (newest first)
    private function getLogFiles(): array
    {
        $logDir = storage_path('logs');
        $dates  = [];

        if (!is_dir($logDir)) {
            return $dates;
        }

        foreach (scandir($logDir, SCANDIR_SORT_DESCENDING) as $file) {
            // Matches: report-submissions-2026-06-12.log
            if (preg_match('/^' . preg_quote($this->logPrefix, '/') . '-(\d{4}-\d{2}-\d{2})\.log$/', $file, $m)) {
                $dates[] = $m[1];
            }
        }

        return $dates;
    }

    // Parse one log file, return array of structured entries
    private function parseLogFile(string $date): array
    {
        $path = storage_path("logs/{$this->logPrefix}-{$date}.log");

        if (!file_exists($path)) {
            return [];
        }

        $lines   = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $entries = [];

        // Pattern: [2026-06-12 10:30:00] channel.LEVEL: EVENT_NAME {"key":"val"} []
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \S+\.(\w+): (\S+) (\{.*\}) \[\]$/';

        foreach ($lines as $line) {
            if (!preg_match($pattern, $line, $m)) {
                continue;
            }

            [, $datetime, $level, $event, $ctxJson] = $m;
            $ctx = json_decode($ctxJson, true) ?? [];

            $entries[] = [
                'datetime'     => $datetime,
                'level'        => $level,
                'event'        => $event,
                'event_status' => $this->resolveStatus($event, $level),
                'ctx'          => $ctx,
            ];
        }

        // Newest first
        return array_reverse($entries);
    }

    private function resolveStatus(string $event, string $level): string
    {
        if ($event === 'SUBMIT_SUCCESS') return 'success';
        if (str_contains($event, 'DUPLICATE')) return 'duplicate';
        if (str_contains($event, 'VALIDATION')) return 'validation_error';
        if ($level === 'ERROR' || str_contains($event, 'EXCEPTION') || str_contains($event, 'FAILED')) return 'failed';
        return 'info';
    }
}
