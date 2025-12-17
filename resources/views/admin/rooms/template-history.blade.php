@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.template_history') }} - {{ $room->title }}</h3>
            <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card-body">
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
                                    <p><strong>{{ __('messages.duration') }}:</strong> {{ $entry->assigned_at->diffForHumans($entry->replaced_at, true) }}</p>
                                @else
                                    <p><strong>{{ __('messages.duration') }}:</strong> {{ $entry->assigned_at->diffForHumans() }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('messages.reports_count') }}:</strong> {{ $entry->reports->count() }}</p>
                                @if($entry->notes)
                                    <p><strong>{{ __('messages.notes') }}:</strong> {{ $entry->notes }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @if($entry->reports->count() > 0)
                            <hr>
                            <h6>{{ __('messages.reports_created') }}:</h6>
                            <ul class="list-group">
                                @foreach($entry->reports as $report)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>{{ __('messages.report') }} #{{ $report->id }}</span>
                                        <small class="text-muted">{{ $report->created_at->format('Y-m-d H:i') }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection