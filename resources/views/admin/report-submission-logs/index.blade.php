@extends('layouts.admin')

@section('title', 'Report Submission Logs')

@section('contentheader', 'Report Submission Logs')
@section('contentheaderlink', '<a href="' . route('admin.dashboard') . '">Dashboard</a>')
@section('contentheaderactive', 'Report Submission Logs')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Summary Cards --}}
        <div class="row mb-3">
            <div class="col-6 col-md-2">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $summary['attempts'] }}</h3>
                        <p>Attempts</p>
                    </div>
                    <div class="icon"><i class="fas fa-paper-plane"></i></div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $summary['success'] }}</h3>
                        <p>Success</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $summary['failed'] }}</h3>
                        <p>Failed</p>
                    </div>
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $summary['duplicate'] }}</h3>
                        <p>Duplicates</p>
                    </div>
                    <div class="icon"><i class="fas fa-copy"></i></div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $summary['validation_error'] }}</h3>
                        <p>Validation Errors</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3>{{ $summary['total'] }}</h3>
                        <p>Total Entries</p>
                    </div>
                    <div class="icon"><i class="fas fa-list"></i></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-1"></i>
                    Logs — {{ $selectedDate ?? 'No log files found' }}
                </h3>
            </div>

            <div class="card-body">

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.report-submission-logs.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="text-sm font-weight-bold">Log Date</label>
                            <select name="date" class="form-control form-control-sm">
                                @forelse($files as $file)
                                    <option value="{{ $file }}" {{ $selectedDate === $file ? 'selected' : '' }}>
                                        {{ $file }}
                                    </option>
                                @empty
                                    <option>No log files yet</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="text-sm font-weight-bold">Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All Statuses</option>
                                <option value="success"          {{ $statusFilter === 'success'          ? 'selected' : '' }}>Success</option>
                                <option value="failed"           {{ $statusFilter === 'failed'           ? 'selected' : '' }}>Failed</option>
                                <option value="duplicate"        {{ $statusFilter === 'duplicate'        ? 'selected' : '' }}>Duplicate</option>
                                <option value="validation_error" {{ $statusFilter === 'validation_error' ? 'selected' : '' }}>Validation Error</option>
                                <option value="info"             {{ $statusFilter === 'info'             ? 'selected' : '' }}>Info / Attempt</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="text-sm font-weight-bold">Report Type</label>
                            <select name="report_type" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <option value="nurse"  {{ $typeFilter === 'nurse'  ? 'selected' : '' }}>Nurse (Hourly)</option>
                                <option value="doctor" {{ $typeFilter === 'doctor' ? 'selected' : '' }}>Doctor (Monthly)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="text-sm font-weight-bold">Search</label>
                            <input type="text" name="search" value="{{ $search }}"
                                   class="form-control form-control-sm"
                                   placeholder="User name, room ID, error…">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.report-submission-logs.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                @if(empty($files))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        No report submission log files found. Logs will appear here after the first report submission.
                    </div>
                @elseif(empty($entries))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        No log entries match the selected filters.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover" id="logsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:140px">Time</th>
                                    <th style="width:80px">Level</th>
                                    <th style="width:100px">Status</th>
                                    <th style="width:90px">Type</th>
                                    <th style="width:70px">Room</th>
                                    <th>User</th>
                                    <th style="width:80px">Date</th>
                                    <th style="width:60px">Hour</th>
                                    <th style="width:70px">Report</th>
                                    <th style="width:90px">Answers</th>
                                    <th>Stage / Event</th>
                                    <th style="width:50px">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $entry)
                                @php
                                    $ctx = $entry['ctx'];
                                    $status = $entry['event_status'];
                                    $rowClass = match($status) {
                                        'success'          => '',
                                        'failed'           => 'table-danger',
                                        'duplicate'        => 'table-warning',
                                        'validation_error' => 'table-warning',
                                        default            => 'table-light',
                                    };
                                    $badgeClass = match($status) {
                                        'success'          => 'badge-success',
                                        'failed'           => 'badge-danger',
                                        'duplicate'        => 'badge-warning',
                                        'validation_error' => 'badge-secondary',
                                        default            => 'badge-info',
                                    };
                                    $badgeLabel = match($status) {
                                        'success'          => 'SUCCESS',
                                        'failed'           => 'FAILED',
                                        'duplicate'        => 'DUPLICATE',
                                        'validation_error' => 'VALIDATION',
                                        default            => 'INFO',
                                    };
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="text-nowrap small">{{ $entry['datetime'] }}</td>
                                    <td>
                                        <span class="badge {{ $entry['level'] === 'ERROR' ? 'badge-danger' : ($entry['level'] === 'WARNING' ? 'badge-warning' : 'badge-info') }}">
                                            {{ $entry['level'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </td>
                                    <td>
                                        @if(($ctx['report_type'] ?? '') === 'nurse')
                                            <span class="badge badge-primary">Nurse</span>
                                        @elseif(($ctx['report_type'] ?? '') === 'doctor')
                                            <span class="badge badge-dark">Doctor</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $ctx['room_id'] ?? '—' }}</td>
                                    <td class="small">
                                        {{ $ctx['user_name'] ?? '—' }}
                                        @if(!empty($ctx['user_type']))
                                            <br><span class="text-muted">({{ $ctx['user_type'] }})</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $ctx['date'] ?? '—' }}</td>
                                    <td class="text-center small">{{ $ctx['hour'] ?? '—' }}</td>
                                    <td class="text-center">
                                        @if(!empty($ctx['report_id']))
                                            <span class="badge badge-secondary">#{{ $ctx['report_id'] }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center small">
                                        @if(isset($ctx['answers_saved']) || isset($ctx['answers_count']))
                                            {{ $ctx['answers_saved'] ?? $ctx['answers_count'] ?? 0 }}
                                            @if(isset($ctx['answers_total']))
                                                / {{ $ctx['answers_total'] }}
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="small text-monospace">
                                        {{ $entry['event'] }}
                                        @if(!empty($ctx['stage']))
                                            <br><span class="text-muted">stage: {{ $ctx['stage'] }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-outline-secondary"
                                                data-toggle="modal"
                                                data-target="#detailModal"
                                                data-payload="{{ htmlspecialchars(json_encode($ctx, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES) }}"
                                                data-event="{{ $entry['event'] }}"
                                                data-time="{{ $entry['datetime'] }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mt-2">Showing {{ count($entries) }} entries (newest first)</p>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-1"></i>
                    Log Entry Details
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-1">
                    <strong>Event:</strong> <span id="modalEvent" class="text-monospace"></span><br>
                    <strong>Time:</strong> <span id="modalTime"></span>
                </p>
                <hr>
                <pre id="modalPayload" style="max-height:400px;overflow:auto;background:#f4f4f4;padding:12px;border-radius:4px;font-size:12px;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#detailModal').on('show.bs.modal', function (e) {
        var btn = $(e.relatedTarget);
        $('#modalEvent').text(btn.data('event'));
        $('#modalTime').text(btn.data('time'));
        try {
            var parsed = JSON.parse(btn.data('payload'));
            $('#modalPayload').text(JSON.stringify(parsed, null, 2));
        } catch(err) {
            $('#modalPayload').text(btn.data('payload'));
        }
    });
});
</script>
@endpush
@endsection
