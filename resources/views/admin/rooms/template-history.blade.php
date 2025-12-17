@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">{{ __('messages.template_history') }} - {{ $room->title }}</h3>
                <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($history->count() > 0)
                <div class="timeline">
                    @foreach($history as $entry)
                    <div class="card mb-3 {{ $entry->is_active ? 'border-success' : '' }}">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $entry->template->title }}</strong>
                                    @if($entry->is_active)
                                        <span class="badge badge-success ml-2">{{ __('messages.active') }}</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ ucfirst($entry->template->report_type) }}</small>
                                </div>
                                <small class="text-muted">
                                    {{ $entry->assigned_at->format('Y-m-d H:i') }}
                                </small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ __('messages.assigned_by') }}:</strong> {{ $entry->assignedBy->name }}</p>
                                    <p><strong>{{ __('messages.assigned_at') }}:</strong> {{ $entry->assigned_at->format('Y-m-d H:i') }}</p>
                                    @if($entry->replaced_at)
                                        <p><strong>{{ __('messages.replaced_at') }}:</strong> {{ $entry->replaced_at->format('Y-m-d H:i') }}</p>
                                        <p><strong>{{ __('messages.duration') }}:</strong> 
                                            {{ $entry->assigned_at->diffInDays($entry->replaced_at) }} {{ __('messages.days') }}
                                        </p>
                                    @else
                                        <p><strong>{{ __('messages.duration') }}:</strong> {{ $entry->assigned_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @php
                                        // Count reports for this period
                                        $reportsCount = isset($entry->period_reports) 
                                            ? $entry->period_reports->count() 
                                            : $entry->reports_count;
                                    @endphp
                                    <p><strong>{{ __('messages.reports_count') }}:</strong> 
                                        <span class="badge badge-primary">{{ $reportsCount }}</span>
                                    </p>
                                    @if($entry->notes)
                                        <p><strong>{{ __('messages.notes') }}:</strong> {{ $entry->notes }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            @php
                                $reports = isset($entry->period_reports) ? $entry->period_reports : $entry->reports;
                            @endphp
                            
                            @if($reports->count() > 0)
                                <hr>
                                <h6>{{ __('messages.reports_created') }}:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('messages.report') }}</th>
                                                <th>{{ __('messages.created_by') }}</th>
                                                <th>{{ __('messages.created_at') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reports as $report)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ __('messages.report') }} #{{ $report->id }}</strong>
                                                        @if($report->report_datetime)
                                                            <br><small class="text-muted">{{ $report->report_datetime->format('Y-m-d H:i') }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $report->creator->name ?? '-' }}</td>
                                                    <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> {{ __('messages.no_reports_during_period') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('messages.no_template_history') }}</h5>
                    <p class="text-muted">{{ __('messages.no_templates_assigned_yet') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
}

.card.border-success {
    border-width: 2px !important;
}
</style>
@endpush