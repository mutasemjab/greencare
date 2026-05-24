@extends('layouts.admin')

@section('css')
<style>
/* ── screen styles ───────────────────────────────────── */
.answer-value {
    background: #f8f9fa;
    border-radius: .25rem;
    padding: .4rem .75rem;
    font-weight: 500;
    min-height: 38px;
    display: flex;
    align-items: center;
}
.answer-empty {
    color: #adb5bd;
    font-style: italic;
}
.section-card {
    border-left: 4px solid #007bff;
}

/* ── print styles ────────────────────────────────────── */
@media print {
    /* hide everything that is not the report content */
    .main-header,
    .main-sidebar,
    .main-footer,
    .content-header,
    .btn,
    .no-print {
        display: none !important;
    }

    body, .wrapper, .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        font-size: 12pt;
    }

    .content { padding: 0 !important; }

    /* medical print header */
    .print-header {
        display: block !important;
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 8pt;
        margin-bottom: 12pt;
    }
    .print-header h2 { margin: 0; font-size: 16pt; font-weight: bold; }
    .print-header p  { margin: 2pt 0; font-size: 10pt; }

    .section-card {
        border: 1px solid #ccc !important;
        border-left: 3px solid #000 !important;
        break-inside: avoid;
        margin-bottom: 10pt !important;
    }

    .answer-value {
        background: transparent !important;
        border-bottom: 1px dotted #888;
        border-radius: 0 !important;
        min-height: auto !important;
        padding: 2pt 4pt !important;
    }

    .answer-value img {
        max-height: 80pt !important;
    }

    .card, .card-body, .card-header {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }

    a { color: #000 !important; text-decoration: none !important; }

    .print-footer {
        display: block !important;
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-size: 9pt;
        color: #666;
        border-top: 1px solid #ccc;
        padding-top: 4pt;
    }
}

/* hidden on screen */
.print-header,
.print-footer {
    display: none;
}
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- ── Medical print header (hidden on screen, shown when printing) ── --}}
    <div class="print-header">
        <h2>GreenCare – تقرير طبي</h2>
        <p>
            <strong>{{ __('messages.room') }}:</strong> {{ $room->title }}
            &nbsp;|&nbsp;
            <strong>{{ __('messages.report_datetime') }}:</strong>
            {{ $report->report_datetime ? $report->report_datetime->format('Y-m-d H:i') : $report->created_at->format('Y-m-d H:i') }}
        </p>
        <p>
            <strong>{{ __('messages.created_by') }}:</strong>
            {{ $report->creator->name }} ({{ $report->creator->user_type }})
            &nbsp;|&nbsp;
            <strong>{{ __('messages.template') }}:</strong>
            {{ $report->template->title }}
        </p>
    </div>

    {{-- ── Screen header card ── --}}
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0">
                                <i class="fas fa-file-medical-alt mr-2"></i>
                                {{ $report->template->title }}
                            </h3>
                            <small class="text-muted">
                                {{ __('messages.room') }}: <strong>{{ $room->title }}</strong>
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <button onclick="window.print()" class="btn btn-success btn-sm">
                                <i class="fas fa-print mr-1"></i> {{ __('messages.print') ?? 'طباعة' }}
                            </button>
                            <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>{{ __('messages.created_by') }}:</strong>
                            {{ $report->creator->name }}
                            <small class="text-muted">({{ $report->creator->user_type }})</small>
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('messages.report_datetime') }}:</strong>
                            {{ $report->report_datetime ? $report->report_datetime->format('Y-m-d H:i') : '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('messages.created_at') }}:</strong>
                            {{ $report->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Sections & Answers ── --}}
    @forelse($report->template->sections as $section)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card section-card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group mr-2 text-primary"></i>
                            {{ app()->getLocale() === 'ar' ? $section->title_ar : $section->title_en }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($section->fields as $field)
                                @php
                                    $answer   = $answersBySection->get($field->id);
                                    $rawValue = $answer ? $answer->formatted_value : null;

                                    // ── Resolve display value ──────────────────────────
                                    $displayValue = null;

                                    if (!is_null($rawValue) && $rawValue !== '-') {

                                        // Select / radio / checkbox → look up the option label
                                        if (in_array($field->input_type, ['select', 'radio', 'checkbox'])) {
                                            $selectedKeys = is_array($rawValue)
                                                ? $rawValue
                                                : [$rawValue];

                                            $labels = collect($selectedKeys)->map(function ($key) use ($field) {
                                                // Match by value_en or value_ar (whichever was stored)
                                                $opt = $field->options->first(
                                                    fn($o) => (string)$o->value_en === (string)$key
                                                           || (string)$o->value_ar === (string)$key
                                                );
                                                if ($opt) {
                                                    return app()->getLocale() === 'ar'
                                                        ? $opt->value_ar
                                                        : $opt->value_en;
                                                }
                                                return $key; // fallback: raw value
                                            })->filter()->implode(', ');

                                            $displayValue = $labels ?: $rawValue;

                                        // Photo / signature → strip JSON quotes from filename
                                        } elseif (in_array($field->input_type, ['photo', 'signuture'])) {
                                            $displayValue = trim($rawValue, '"');

                                        // PDF
                                        } elseif ($field->input_type === 'pdf') {
                                            $displayValue = trim($rawValue, '"');

                                        // Everything else (text, number, date, textarea…)
                                        } else {
                                            $displayValue = $rawValue;
                                        }
                                    }
                                @endphp

                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">
                                        {{ app()->getLocale() === 'ar' ? $field->label_ar : $field->label_en }}
                                        @if($field->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <div class="answer-value">
                                        @if($displayValue && $displayValue !== '-')
                                            @if(in_array($field->input_type, ['photo', 'signuture']))
                                                <a href="{{ asset('assets/admin/uploads/' . $displayValue) }}" target="_blank">
                                                    <img src="{{ asset('assets/admin/uploads/' . $displayValue) }}"
                                                         style="max-height:120px; max-width:100%; border-radius:4px; object-fit:contain;"
                                                         alt="{{ $field->input_type }}">
                                                </a>
                                            @elseif($field->input_type === 'pdf')
                                                <a href="{{ asset('assets/admin/uploads/' . $displayValue) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-file-pdf mr-1"></i>{{ __('messages.view_pdf') }}
                                                </a>
                                            @else
                                                {{ $displayValue }}
                                            @endif
                                        @else
                                            <span class="answer-empty">{{ __('messages.no_answer') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">{{ __('messages.no_fields_in_section') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('messages.no_sections_in_template') }}
                </div>
            </div>
        </div>
    @endforelse

    {{-- ── Print footer ── --}}
    <div class="print-footer">
        طُبع من نظام GreenCare — {{ now()->format('Y-m-d H:i') }}
    </div>

</div>
@endsection
