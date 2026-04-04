@extends('layouts.admin')

@section('css')
<style>
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
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="row mb-4">
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
                        <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
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

    <!-- Sections & Answers -->
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
                                    $answer = $answersBySection->get($field->id);
                                    $value = $answer ? $answer->formatted_value : null;
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">
                                        {{ app()->getLocale() === 'ar' ? $field->label_ar : $field->label_en }}
                                        @if($field->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <div class="answer-value">
                                        @if($value && $value !== '-')
                                            {{ $value }}
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

</div>
@endsection
