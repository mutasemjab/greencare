@extends('layouts.admin')

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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">{{ __('messages.patient') }} <span class="text-danger">*</span></label>
                                    <select name="patient_id"
                                            id="patient_id"
                                            class="form-control select2 @error('patient_id') is-invalid @enderror"
                                            required
                                            style="width: 100%;">
                                        <option value="">{{ __('messages.select_patient') }}</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatient?->id) == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->name }} - {{ $patient->phone }}
                                            </option>
                                        @endforeach
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
                                                    {{ old('room_id', $selectedRoom?->id) == $room->id ? 'selected' : '' }}>
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
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('messages.medication_schedules') }}</h5>
                            <button type="button" class="btn btn-success btn-sm" onclick="addSchedule()">
                                <i class="fas fa-plus"></i> {{ __('messages.add_schedule') }}
                            </button>
                        </div>

                        <div id="schedules-container"></div>
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
@endsection

@push('scripts')
<script>
let scheduleIndex = 0;

$(document).ready(function() {
    $('#patient_id').select2({
        placeholder: '{{ __("messages.search_and_select_patient") }}',
        allowClear: true,
        width: '100%'
    });

    addSchedule();
});

function addSchedule() {
    const html = `
        <div class="border rounded p-3 mb-3 bg-light" style="border-left: 4px solid #28a745 !important;" data-schedule-index="${scheduleIndex}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 text-success">{{ __('messages.schedule') }} <span class="schedule-number">${scheduleIndex + 1}</span></h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSchedule(this)">
                    <i class="fas fa-trash"></i> {{ __('messages.remove_schedule') }}
                </button>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('messages.schedule_time') }} <span class="text-danger">*</span></label>
                        <input type="time" name="schedules[${scheduleIndex}][time]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('messages.schedule_frequency') }} <span class="text-danger">*</span></label>
                        <select name="schedules[${scheduleIndex}][frequency]" class="form-control" required>
                            <option value="">{{ __('messages.select_frequency') }}</option>
                            <option value="daily">{{ __('messages.frequency_daily') }}</option>
                            <option value="weekly">{{ __('messages.frequency_weekly') }}</option>
                            <option value="monthly">{{ __('messages.frequency_monthly') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>`;

    document.getElementById('schedules-container').insertAdjacentHTML('beforeend', html);
    scheduleIndex++;
    updateScheduleNumbers();
}

function removeSchedule(button) {
    if (document.querySelectorAll('#schedules-container > div').length <= 1) {
        alert('{{ __("messages.cannot_remove_last_schedule") }}');
        return;
    }
    button.closest('[data-schedule-index]').remove();
    reindexSchedules();
}

function updateScheduleNumbers() {
    document.querySelectorAll('#schedules-container > div').forEach((el, i) => {
        el.querySelector('.schedule-number').textContent = i + 1;
    });
}

function reindexSchedules() {
    document.querySelectorAll('#schedules-container > div').forEach((el, i) => {
        el.setAttribute('data-schedule-index', i);
        el.querySelector('.schedule-number').textContent = i + 1;
        el.querySelectorAll('input, select').forEach(input => {
            input.name = input.name.replace(/schedules\[\d+\]/, `schedules[${i}]`);
        });
    });
}
</script>
@endpush
