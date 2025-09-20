@extends('layouts.admin')

@section('css')
<style>
.schedule-item {
    background-color: #f8f9fa;
    border-left: 4px solid #28a745 !important;
}

.schedule-title {
    color: #28a745;
    margin: 0;
}

.select2-container {
    z-index: 9999 !important;
}

.select2-dropdown {
    z-index: 9999 !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--default .select2-selection--single {
    height: 38px !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 12px !important;
}

.form-check-input {
    margin-top: 0.3rem;
}

template {
    display: none;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.create_medication') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('medications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('medications.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Medication Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">{{ __('messages.patient') }} <span class="text-danger">*</span></label>
                                    <select name="patient_id" 
                                            id="patient_id" 
                                            class="form-control patient-select @error('patient_id') is-invalid @enderror" 
                                            required
                                            style="width: 100%;">
                                        @if($selectedPatient)
                                            <option value="{{ $selectedPatient->id }}" selected>
                                                {{ $selectedPatient->name }} - {{ $selectedPatient->phone }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('patient_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">{{ __('messages.room') }}</label>
                                    <select name="room_id" id="room_id" class="form-control">
                                        <option value="">{{ __('messages.select_room') }}</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" 
                                                    {{ (old('room_id', $selectedRoom?->id) == $room->id) ? 'selected' : '' }}>
                                                {{ $room->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('messages.medication_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.enter_medication_name') }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dosage">{{ __('messages.dosage') }}</label>
                                    <input type="text" 
                                           name="dosage" 
                                           id="dosage" 
                                           class="form-control @error('dosage') is-invalid @enderror" 
                                           value="{{ old('dosage') }}" 
                                           placeholder="{{ __('messages.enter_dosage') }}">
                                    @error('dosage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quantity">{{ __('messages.quantity') }}</label>
                                    <input type="number" 
                                           name="quantity" 
                                           id="quantity" 
                                           class="form-control @error('quantity') is-invalid @enderror" 
                                           value="{{ old('quantity') }}" 
                                           placeholder="{{ __('messages.enter_quantity') }}"
                                           min="1">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="notes">{{ __('messages.medication_notes') }}</label>
                                    <textarea name="notes" 
                                              id="notes" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="{{ __('messages.enter_medication_notes') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="active" 
                                               id="active" 
                                               class="form-check-input" 
                                               value="1" 
                                               {{ old('active', true) ? 'checked' : '' }}>
                                        <label for="active" class="form-check-label">
                                            {{ __('messages.active_medication') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Medication Schedules -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>{{ __('messages.medication_schedules') }}</h5>
                                    <button type="button" class="btn btn-success btn-sm" onclick="addSchedule()">
                                        <i class="fas fa-plus"></i> {{ __('messages.add_schedule') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="schedules-container">
                            <!-- Schedules will be added here dynamically -->
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('medications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('messages.create_medication') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Template -->
<template id="schedule-template">
    <div class="schedule-item border rounded p-3 mb-3" data-schedule-index="">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="schedule-title">{{ __('messages.schedule') }} <span class="schedule-number"></span></h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeSchedule(this)">
                <i class="fas fa-trash"></i> {{ __('messages.remove_schedule') }}
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('messages.schedule_time') }} <span class="text-danger">*</span></label>
                    <input type="time" 
                           name="schedules[INDEX][time]" 
                           class="form-control schedule-time" 
                           required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('messages.schedule_frequency') }} <span class="text-danger">*</span></label>
                    <select name="schedules[INDEX][frequency]" 
                            class="form-control schedule-frequency" 
                            required>
                        <option value="">{{ __('messages.select_frequency') }}</option>
                        <option value="daily">{{ __('messages.frequency_daily') }}</option>
                        <option value="weekly">{{ __('messages.frequency_weekly') }}</option>
                        <option value="monthly">{{ __('messages.frequency_monthly') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let scheduleIndex = 0;

$(document).ready(function() {
    // Initialize patient Select2
    $('.patient-select').select2({
        placeholder: '{{ __("messages.search_and_select_patient") }}',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("api.patients.search") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: data.data.map(function(patient) {
                        return {
                            id: patient.id,
                            text: patient.name + ' - ' + patient.phone + ' (' + patient.gender_text + ')',
                            patient: patient
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page
                    }
                };
            }
        },
        minimumInputLength: 2,
        templateResult: formatPatient,
        templateSelection: formatPatientSelection
    });

    // Add initial schedule
    addSchedule();

    // Format patient display in dropdown
    function formatPatient(patient) {
        if (patient.loading) {
            return '{{ __("messages.searching") }}...';
        }

        if (!patient.patient) {
            return patient.text;
        }

        return '<div class="d-flex align-items-center">' +
                    '<div class="mr-2">' +
                        '<div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-white" style="width: 30px; height: 30px; font-size: 0.8rem;">' +
                            patient.patient.name.charAt(0).toUpperCase() +
                        '</div>' +
                    '</div>' +
                    '<div>' +
                        '<div class="font-weight-bold">' + patient.patient.name + '</div>' +
                        '<small class="text-muted">' + patient.patient.phone + ' • ' + patient.patient.gender_text + '</small>' +
                    '</div>' +
                '</div>';
    }

    // Format selected patient
    function formatPatientSelection(patient) {
        if (!patient.patient) {
            return patient.text || patient.id;
        }
        
        return patient.patient.name + ' - ' + patient.patient.phone;
    }
});

function addSchedule() {
    const template = document.getElementById('schedule-template').content.cloneNode(true);
    const scheduleDiv = template.querySelector('.schedule-item');
    
    // Update schedule index
    scheduleDiv.setAttribute('data-schedule-index', scheduleIndex);
    
    // Update schedule number
    template.querySelector('.schedule-number').textContent = scheduleIndex + 1;
    
    // Update input names
    const inputs = template.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.name) {
            input.name = input.name.replace('INDEX', scheduleIndex);
        }
    });
    
    document.getElementById('schedules-container').appendChild(template);
    
    scheduleIndex++;
    updateScheduleNumbers();
}

function removeSchedule(button) {
    if (document.querySelectorAll('.schedule-item').length <= 1) {
        alert('{{ __("messages.cannot_remove_last_schedule") }}');
        return;
    }
    
    button.closest('.schedule-item').remove();
    updateScheduleNumbers();
    reindexSchedules();
}

function updateScheduleNumbers() {
    document.querySelectorAll('.schedule-item').forEach((schedule, index) => {
        schedule.querySelector('.schedule-number').textContent = index + 1;
    });
}

function reindexSchedules() {
    document.querySelectorAll('.schedule-item').forEach((schedule, scheduleIdx) => {
        schedule.setAttribute('data-schedule-index', scheduleIdx);
        
        // Update all inputs in this schedule
        schedule.querySelectorAll('input, select').forEach(input => {
            if (input.name && input.name.includes('schedules[')) {
                input.name = input.name.replace(/schedules\[\d+\]/, `schedules[${scheduleIdx}]`);
            }
        });
    });
}
</script>
@endpush
