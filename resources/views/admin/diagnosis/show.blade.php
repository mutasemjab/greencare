@extends('layouts.admin')

@section('css')
<style>
.info-card {
    border-left: 4px solid #007bff;
    background-color: #f8f9fa;
    padding: 15px;
    margin-bottom: 20px;
}

.medication-card {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #fff;
}

.schedule-badge {
    display: inline-block;
    padding: 5px 10px;
    margin: 5px;
    border-radius: 3px;
    font-size: 0.85rem;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.diagnosis_details') }}</h3>
                        <div>
                            @can('diagnosis-edit')
                                <a href="{{ route('diagnosis.edit', $diagnosis) }}" class="btn btn-warning btn-sm mr-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('diagnosis.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Appointment Information -->
                    <div class="card border-info mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-check"></i> {{ __('messages.appointment_information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ __('messages.patient_name') }}:</strong> {{ $diagnosis->appointment->name_of_patient }}</p>
                                    <p><strong>{{ __('messages.patient_phone') }}:</strong> 
                                        <a href="tel:{{ $diagnosis->appointment->phone_of_patient }}">{{ $diagnosis->appointment->phone_of_patient }}</a>
                                    </p>
                                    @if($diagnosis->appointment->address)
                                        <p><strong>{{ __('messages.address') }}:</strong> {{ $diagnosis->appointment->address }}</p>
                                    @endif
                                    @if($diagnosis->appointment->description)
                                        <p><strong>{{ __('messages.description') }}:</strong> {{ $diagnosis->appointment->description }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ __('messages.provider') }}:</strong> {{ $diagnosis->appointment->provider->name }}</p>
                                    <p><strong>{{ __('messages.appointment_date') }}:</strong> 
                                        {{ $diagnosis->appointment->date_of_appointment ? $diagnosis->appointment->date_of_appointment->format('Y-m-d') : '-' }}
                                    </p>
                                    <p><strong>{{ __('messages.appointment_time') }}:</strong> 
                                        {{ $diagnosis->appointment->time_of_appointment ?? '-' }}
                                    </p>
                                    <p><strong>{{ __('messages.status') }}:</strong> 
                                        <span class="badge badge-{{ $diagnosis->appointment->status_badge }}">
                                            {{ $diagnosis->appointment->status_text }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnosis Information -->
                    <div class="card border-success mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-stethoscope"></i> {{ __('messages.diagnosis_information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>{{ __('messages.diagnosed_by') }}:</strong> {{ $diagnosis->diagnosedBy->name }}</p>
                                    <p><strong>{{ __('messages.diagnosed_at') }}:</strong> {{ $diagnosis->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    @if($diagnosis->room)
                                        <p><strong>{{ __('messages.assigned_room') }}:</strong> 
                                            <a href="{{ route('rooms.show', $diagnosis->room) }}">{{ $diagnosis->room->title }}</a>
                                        </p>
                                    @else
                                        <p><strong>{{ __('messages.assigned_room') }}:</strong> <span class="text-muted">{{ __('messages.no_room') }}</span></p>
                                    @endif
                                    <p><strong>{{ __('messages.last_updated') }}:</strong> {{ $diagnosis->updated_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-primary"><i class="fas fa-notes-medical"></i> {{ __('messages.diagnosis') }}</h6>
                                    <div class="info-card">
                                        {{ $diagnosis->diagnosis }}
                                    </div>
                                </div>
                            </div>

                            @if($diagnosis->symptoms)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-danger"><i class="fas fa-thermometer"></i> {{ __('messages.symptoms') }}</h6>
                                        <div class="info-card">
                                            {{ $diagnosis->symptoms }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($diagnosis->treatment_plan)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-success"><i class="fas fa-heartbeat"></i> {{ __('messages.treatment_plan') }}</h6>
                                        <div class="info-card">
                                            {{ $diagnosis->treatment_plan }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($diagnosis->notes)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-secondary"><i class="fas fa-sticky-note"></i> {{ __('messages.notes') }}</h6>
                                        <div class="info-card">
                                            {{ $diagnosis->notes }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Medications -->
                    @if($diagnosis->medications->count() > 0)
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-pills"></i> {{ __('messages.medications') }}
                                    <span class="badge badge-light ml-2">{{ $diagnosis->medications->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($diagnosis->medications as $medication)
                                    <div class="medication-card">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-capsules"></i> {{ $medication->name }}
                                                </h6>
                                                <p class="mb-1">
                                                    @if($medication->dosage)
                                                        <strong>{{ __('messages.dosage') }}:</strong> {{ $medication->dosage }}
                                                    @endif
                                                    @if($medication->quantity)
                                                        | <strong>{{ __('messages.quantity') }}:</strong> {{ $medication->quantity }}
                                                    @endif
                                                </p>
                                                @if($medication->notes)
                                                    <p class="text-muted mb-2">
                                                        <i class="fas fa-info-circle"></i> {{ $medication->notes }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="text-secondary">{{ __('messages.schedules') }}:</h6>
                                                @foreach($medication->schedules as $schedule)
                                                    <div class="schedule-badge bg-light border">
                                                        <i class="fas fa-clock text-primary"></i> {{ $schedule->time }}
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar"></i> {{ ucfirst($schedule->frequency) }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> {{ __('messages.no_medications_prescribed') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection