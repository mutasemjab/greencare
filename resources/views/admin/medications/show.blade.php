@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.medication_details') }}: {{ $medication->name }}</h3>
                        <div class="btn-group">
                            @can('medication-edit')
                                <a href="{{ route('medications.edit', $medication) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('medications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
               

                    <!-- Medication Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.medication_information') }}</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.medication_name') }}:</strong></td>
                                            <td>{{ $medication->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.patient') }}:</strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($medication->patient->photo)
                                                        <img src="{{ asset('storage/' . $medication->patient->photo) }}" 
                                                             class="rounded-circle mr-2" 
                                                             width="40" height="40"
                                                             alt="{{ $medication->patient->name }}">
                                                    @else
                                                        <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center bg-warning text-white" 
                                                             style="width: 40px; height: 40px;">
                                                            {{ substr($medication->patient->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-weight-bold">{{ $medication->patient->name }}</div>
                                                        <small class="text-muted">{{ $medication->patient->phone }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($medication->room)
                                            <tr>
                                                <td><strong>{{ __('messages.room') }}:</strong></td>
                                                <td>
                                                    <a href="{{ route('rooms.show', $medication->room) }}" class="badge badge-info">
                                                        {{ $medication->room->title }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($medication->dosage)
                                            <tr>
                                                <td><strong>{{ __('messages.dosage') }}:</strong></td>
                                                <td><span class="badge badge-secondary">{{ $medication->dosage }}</span></td>
                                            </tr>
                                        @endif
                                        @if($medication->quantity)
                                            <tr>
                                                <td><strong>{{ __('messages.quantity') }}:</strong></td>
                                                <td>{{ $medication->quantity }} {{ __('messages.per_dose') }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>{{ __('messages.status') }}:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $medication->active ? 'success' : 'secondary' }}">
                                                    {{ $medication->active ? __('messages.active') : __('messages.inactive') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($medication->notes)
                                            <tr>
                                                <td><strong>{{ __('messages.notes') }}:</strong></td>
                                                <td>{{ $medication->notes }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                            <td>{{ $medication->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.quick_stats') }}</h5>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h3 class="text-primary">{{ $medication->schedules->count() }}</h3>
                                            <small class="text-muted">{{ __('messages.schedules') }}</small>
                                        </div>
                                        <div class="col-6">
                                            @php
                                                $rate = $medication->compliance_rate;
                                                $class = $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <h3 class="text-{{ $class }}">{{ $rate }}%</h3>
                                            <small class="text-muted">{{ __('messages.compliance') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedules -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('messages.medication_schedules') }} ({{ $medication->schedules->count() }})</h5>
                                </div>
                                <div class="card-body">
                                    @if($medication->schedules->count() > 0)
                                        <div class="row">
                                            @foreach($medication->schedules as $schedule)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="card border-success">
                                                        <div class="card-body text-center">
                                                            <h4 class="text-primary">{{ $schedule->formatted_time }}</h4>
                                                            <span class="badge badge-success">{{ $schedule->frequency_text }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">{{ __('messages.no_schedules_found') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Doses -->
                    @if($upcomingLogs->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="card-title mb-0">{{ __('messages.upcoming_doses') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.scheduled_time') }}</th>
                                                        <th>{{ __('messages.status') }}</th>
                                                        <th>{{ __('messages.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($upcomingLogs as $log)
                                                        <tr>
                                                            <td>{{ $log->scheduled_time->format('Y-m-d H:i') }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $log->status_badge_class }}">
                                                                    {{ $log->status_text }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @can('medication-edit')
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-success" 
                                                                            data-toggle="modal" 
                                                                            data-target="#markTakenModal{{ $log->id }}">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                @endcan
                                                            </td>
                                                        </tr>

                                                        @can('medication-edit')
                                                            <!-- Mark Taken Modal -->
                                                            <div class="modal fade" id="markTakenModal{{ $log->id }}" tabindex="-1" role="dialog">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">{{ __('messages.mark_as_taken') }}</h5>
                                                                            <button type="button" class="close" data-dismiss="modal">
                                                                                <span>&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <form action="{{ route('medication-logs.mark-taken', $log) }}" method="POST">
                                                                            @csrf
                                                                            <div class="modal-body">
                                                                                <p>{{ __('messages.mark_medication_taken_confirm') }}</p>
                                                                                <div class="form-group">
                                                                                    <label for="notes{{ $log->id }}">{{ __('messages.notes') }}</label>
                                                                                    <textarea name="notes" 
                                                                                              id="notes{{ $log->id }}" 
                                                                                              class="form-control" 
                                                                                              rows="3" 
                                                                                              placeholder="{{ __('messages.optional_notes') }}"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                                    {{ __('messages.cancel') }}
                                                                                </button>
                                                                                <button type="submit" class="btn btn-success">
                                                                                    {{ __('messages.mark_taken') }}
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Overdue Doses -->
                    @if($overdueLogs->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="card-title mb-0">{{ __('messages.overdue_doses') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.scheduled_time') }}</th>
                                                        <th>{{ __('messages.overdue_by') }}</th>
                                                        <th>{{ __('messages.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($overdueLogs as $log)
                                                        <tr>
                                                            <td>{{ $log->scheduled_time->format('Y-m-d H:i') }}</td>
                                                            <td>{{ $log->scheduled_time->diffForHumans() }}</td>
                                                            <td>
                                                                @can('medication-edit')
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-success" 
                                                                            data-toggle="modal" 
                                                                            data-target="#markTakenModal{{ $log->id }}">
                                                                        <i class="fas fa-check"></i> {{ __('messages.taken') }}
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-warning ml-1" 
                                                                            data-toggle="modal" 
                                                                            data-target="#markMissedModal{{ $log->id }}">
                                                                        <i class="fas fa-times"></i> {{ __('messages.missed') }}
                                                                    </button>
                                                                @endcan
                                                            </td>
                                                        </tr>

                                                        @can('medication-edit')
                                                            <!-- Mark Missed Modal -->
                                                            <div class="modal fade" id="markMissedModal{{ $log->id }}" tabindex="-1" role="dialog">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">{{ __('messages.mark_as_missed') }}</h5>
                                                                            <button type="button" class="close" data-dismiss="modal">
                                                                                <span>&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <form action="{{ route('medication-logs.mark-missed', $log) }}" method="POST">
                                                                            @csrf
                                                                            <div class="modal-body">
                                                                                <p>{{ __('messages.mark_medication_missed_confirm') }}</p>
                                                                                <div class="form-group">
                                                                                    <label for="missed_notes{{ $log->id }}">{{ __('messages.reason') }}</label>
                                                                                    <textarea name="notes" 
                                                                                              id="missed_notes{{ $log->id }}" 
                                                                                              class="form-control" 
                                                                                              rows="3" 
                                                                                              placeholder="{{ __('messages.reason_for_missing') }}"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                                    {{ __('messages.cancel') }}
                                                                                </button>
                                                                                <button type="submit" class="btn btn-warning">
                                                                                    {{ __('messages.mark_missed') }}
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Medication History -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('messages.recent_logs') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($medication->logs->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.scheduled_time') }}</th>
                                                        <th>{{ __('messages.status') }}</th>
                                                        <th>{{ __('messages.taken_at') }}</th>
                                                        <th>{{ __('messages.notes') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($medication->logs as $log)
                                                        <tr>
                                                            <td>{{ $log->scheduled_time->format('Y-m-d H:i') }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $log->status_badge_class }}">
                                                                    {{ $log->status_text }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($log->taken_at)
                                                                    {{ $log->taken_at->format('Y-m-d H:i') }}
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($log->notes)
                                                                    {{ Str::limit($log->notes, 50) }}
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">{{ __('messages.no_medication_history') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

