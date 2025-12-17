@extends('layouts.admin')

@section('css')
<style>
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: .25rem;
    background: #fff;
    display: flex;
    margin-bottom: 1rem;
    min-height: 80px;
    padding: .5rem;
    position: relative;
}

.info-box-icon {
    border-radius: .25rem;
    align-items: center;
    display: flex;
    font-size: 1.875rem;
    justify-content: center;
    text-align: center;
    width: 70px;
}

.info-box-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.8;
    flex: 1;
    padding: 0 10px;
}

.info-box-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.info-box-number {
    display: block;
    font-weight: 700;
    font-size: 1.125rem;
}

.user-card {
    transition: transform 0.2s;
}

.user-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,.1);
}

.timeline {
    position: relative;
    padding-left: 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: 12px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-marker.active {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
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
                            <h3 class="card-title mb-0">{{ $room->title }}</h3>
                            @if($room->code)
                                <small class="text-muted">{{ __('messages.room_code') }}: <strong>{{ $room->code }}</strong></small>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('rooms.template-history', $room) }}" class="btn btn-secondary btn-sm mr-2">
                                <i class="fas fa-history"></i> {{ __('messages.template_history') }}
                            </a>
                            @can('room-edit')
                                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-warning btn-sm mr-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($room->description)
                        <div class="alert alert-info">
                            <strong>{{ __('messages.description') }}:</strong> {{ $room->description }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-3">
                            <strong>{{ __('messages.family') }}:</strong>
                            @if($room->family)
                                <span class="badge badge-info">{{ $room->family->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <strong>{{ __('messages.discount') }}:</strong>
                            {{ $room->discount }}%
                        </div>
                        <div class="col-md-3">
                            <strong>{{ __('messages.created_at') }}:</strong>
                            {{ $room->created_at->format('Y-m-d H:i') }}
                        </div>
                        <div class="col-md-3">
                            <strong>{{ __('messages.updated_at') }}:</strong>
                            {{ $room->updated_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-injured"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('messages.patients') }}</span>
                    <span class="info-box-number">{{ $room->patients->count() }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-user-md"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('messages.doctors') }}</span>
                    <span class="info-box-number">{{ $room->doctors->count() }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-nurse"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('messages.nurses') }}</span>
                    <span class="info-box-number">{{ $room->nurses->count() }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file-medical-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('messages.reports') }}</span>
                    <span class="info-box-number">{{ $room->reports->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Template History Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history"></i> {{ __('messages.template_history') }}
                        </h5>
                        <a href="{{ route('rooms.template-history', $room) }}" class="btn btn-sm btn-light">
                            <i class="fas fa-eye"></i> {{ __('messages.view_full_history') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $templateHistory = $room->templateHistory()->with(['template', 'assignedBy'])->limit(5)->get();
                    @endphp

                    @if($templateHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.template') }}</th>
                                        <th>{{ __('messages.assigned_by') }}</th>
                                        <th>{{ __('messages.assigned_at') }}</th>
                                        <th>{{ __('messages.replaced_at') }}</th>
                                        <th>{{ __('messages.duration') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templateHistory as $history)
                                        <tr class="{{ $history->is_active ? 'table-success' : '' }}">
                                            <td>
                                                <strong>{{ $history->template->title }}</strong>
                                                <br><small class="text-muted">{{ ucfirst($history->template->report_type) }}</small>
                                            </td>
                                            <td>{{ $history->assignedBy->name }}</td>
                                            <td>
                                                <small>{{ $history->assigned_at->format('Y-m-d H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($history->replaced_at)
                                                    <small>{{ $history->replaced_at->format('Y-m-d H:i') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($history->replaced_at)
                                                    <small>{{ $history->assigned_at->diffInDays($history->replaced_at) }} {{ __('messages.days') }}</small>
                                                @else
                                                    <small>{{ $history->assigned_at->diffForHumans() }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($history->is_active)
                                                    <span class="badge badge-success">{{ __('messages.active') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ __('messages.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($history->notes)
                                                    <small>{{ Str::limit($history->notes, 50) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($room->templateHistory()->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('rooms.template-history', $room) }}" class="btn btn-sm btn-outline-danger">
                                    {{ __('messages.view_all') }} ({{ $room->templateHistory()->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('messages.no_template_history') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Room Members -->
    <div class="row">
        <!-- Patients -->
        <div class="col-md-6 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-user-injured"></i> {{ __('messages.patients') }}
                        <span class="badge badge-light ml-2">{{ $room->patients->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($room->patients as $patient)
                        <div class="card user-card mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    @if($patient->photo)
                                        <img src="{{ asset('assets/admin/uploads/' . $patient->photo) }}" 
                                             class="rounded-circle mr-3" 
                                             width="50" height="50"
                                             alt="{{ $patient->name }}">
                                    @else
                                        <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center bg-warning text-white" 
                                             style="width: 50px; height: 50px; font-size: 1.2rem;">
                                            {{ substr($patient->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $patient->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $patient->phone }}
                                        </small>
                                        @if($patient->email)
                                            <br><small class="text-muted">
                                                <i class="fas fa-envelope"></i> {{ $patient->email }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="badge badge-{{ $patient->gender == 1 ? 'info' : 'pink' }}">
                                            {{ $patient->gender_text }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">{{ __('messages.no_patients') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Doctors -->
        <div class="col-md-6 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-md"></i> {{ __('messages.doctors') }}
                        <span class="badge badge-light ml-2">{{ $room->doctors->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($room->doctors as $doctor)
                        <div class="card user-card mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    @if($doctor->photo)
                                        <img src="{{ asset('assets/admin/uploads/' . $doctor->photo) }}" 
                                             class="rounded-circle mr-3" 
                                             width="50" height="50"
                                             alt="{{ $doctor->name }}">
                                    @else
                                        <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center bg-primary text-white" 
                                             style="width: 50px; height: 50px; font-size: 1.2rem;">
                                            {{ substr($doctor->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $doctor->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $doctor->phone }}
                                        </small>
                                        @if($doctor->email)
                                            <br><small class="text-muted">
                                                <i class="fas fa-envelope"></i> {{ $doctor->email }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="badge badge-{{ $doctor->gender == 1 ? 'info' : 'pink' }}">
                                            {{ $doctor->gender_text }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">{{ __('messages.no_doctors') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Nurses -->
        <div class="col-md-6 mb-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-nurse"></i> {{ __('messages.nurses') }}
                        <span class="badge badge-light ml-2">{{ $room->nurses->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($room->nurses as $nurse)
                        <div class="card user-card mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    @if($nurse->photo)
                                        <img src="{{ asset('assets/admin/uploads/' . $nurse->photo) }}" 
                                             class="rounded-circle mr-3" 
                                             width="50" height="50"
                                             alt="{{ $nurse->name }}">
                                    @else
                                        <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center bg-success text-white" 
                                             style="width: 50px; height: 50px; font-size: 1.2rem;">
                                            {{ substr($nurse->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $nurse->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $nurse->phone }}
                                        </small>
                                        @if($nurse->email)
                                            <br><small class="text-muted">
                                                <i class="fas fa-envelope"></i> {{ $nurse->email }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="badge badge-{{ $nurse->gender == 1 ? 'info' : 'pink' }}">
                                            {{ $nurse->gender_text }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">{{ __('messages.no_nurses') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Family Members -->
        @if($room->familyMembers->count() > 0)
        <div class="col-md-6 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> {{ __('messages.family_members') }}
                        <span class="badge badge-light ml-2">{{ $room->familyMembers->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($room->familyMembers as $familyMember)
                        <div class="card user-card mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    @if($familyMember->photo)
                                        <img src="{{ asset('assets/admin/uploads/' . $familyMember->photo) }}" 
                                             class="rounded-circle mr-3" 
                                             width="50" height="50"
                                             alt="{{ $familyMember->name }}">
                                    @else
                                        <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center bg-info text-white" 
                                             style="width: 50px; height: 50px; font-size: 1.2rem;">
                                            {{ substr($familyMember->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $familyMember->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $familyMember->phone }}
                                        </small>
                                        @if($familyMember->email)
                                            <br><small class="text-muted">
                                                <i class="fas fa-envelope"></i> {{ $familyMember->email }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="badge badge-{{ $familyMember->gender == 1 ? 'info' : 'pink' }}">
                                            {{ $familyMember->gender_text }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Reports Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-medical-alt"></i> {{ __('messages.reports') }}
                        <span class="badge badge-light ml-2">{{ $room->reports->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($room->reports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('messages.template') }}</th>
                                        <th>{{ __('messages.created_by') }}</th>
                                        <th>{{ __('messages.report_datetime') }}</th>
                                        <th>{{ __('messages.created_at') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($room->reports as $report)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $report->template->title }}</strong>
                                                <br><small class="text-muted">{{ ucfirst($report->template->report_type) }}</small>
                                            </td>
                                            <td>{{ $report->creator->name }}</td>
                                            <td>
                                                @if($report->report_datetime)
                                                    {{ $report->report_datetime->format('Y-m-d H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('messages.no_reports_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Medications Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-pills"></i> {{ __('messages.medications') }}
                        <span class="badge badge-light ml-2">{{ $room->medications->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($room->medications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('messages.patient') }}</th>
                                        <th>{{ __('messages.medication_name') }}</th>
                                        <th>{{ __('messages.dosage') }}</th>
                                        <th>{{ __('messages.quantity') }}</th>
                                        <th>{{ __('messages.schedules') }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($room->medications as $medication)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $medication->patient->name }}</td>
                                            <td><strong>{{ $medication->name }}</strong></td>
                                            <td>{{ $medication->dosage ?? '-' }}</td>
                                            <td>{{ $medication->quantity ?? '-' }}</td>
                                            <td>
                                                @if($medication->schedules->count() > 0)
                                                    @foreach($medication->schedules as $schedule)
                                                        <span class="badge badge-primary">
                                                            {{ $schedule->time }} ({{ $schedule->frequency }})
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($medication->notes)
                                                    <small>{{ Str::limit($medication->notes, 50) }}</small>
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
                            <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('messages.no_medications_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush