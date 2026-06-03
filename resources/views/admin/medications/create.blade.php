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

                        {{-- Patient & Room --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">{{ __('messages.patient') }} <span class="text-danger">*</span></label>
                                    <select name="patient_id" id="patient_id"
                                            class="form-control select2 @error('patient_id') is-invalid @enderror"
                                            required style="width: 100%;">
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
                                            <option value="{{ $room->id }}" {{ old('room_id', $selectedRoom?->id) == $room->id ? 'selected' : '' }}>
                                                {{ $room->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Name / Dosage / Quantity --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('messages.medication_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           placeholder="{{ __('messages.enter_medication_name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dosage">{{ __('messages.dosage') }}</label>
                                    <input type="text" name="dosage" id="dosage"
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
                                    <input type="number" name="quantity" id="quantity"
                                           class="form-control @error('quantity') is-invalid @enderror"
                                           value="{{ old('quantity') }}"
                                           placeholder="{{ __('messages.enter_quantity') }}" min="1">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Routes & Notes --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="routes">{{ __('messages.routes') }}</label>
                                    <select name="routes" id="routes" class="form-control @error('routes') is-invalid @enderror">
                                        <option value="">{{ __('messages.select_routes') }}</option>
                                        <option value="oral"           {{ old('routes') == 'oral'           ? 'selected' : '' }}>{{ __('messages.route_oral') }}</option>
                                        <option value="intravenous"    {{ old('routes') == 'intravenous'    ? 'selected' : '' }}>{{ __('messages.route_intravenous') }}</option>
                                        <option value="intramuscular"  {{ old('routes') == 'intramuscular'  ? 'selected' : '' }}>{{ __('messages.route_intramuscular') }}</option>
                                        <option value="subcutaneous"   {{ old('routes') == 'subcutaneous'   ? 'selected' : '' }}>{{ __('messages.route_subcutaneous') }}</option>
                                        <option value="topical"        {{ old('routes') == 'topical'        ? 'selected' : '' }}>{{ __('messages.route_topical') }}</option>
                                        <option value="inhaled"        {{ old('routes') == 'inhaled'        ? 'selected' : '' }}>{{ __('messages.route_inhaled') }}</option>
                                        <option value="other"          {{ old('routes') == 'other'          ? 'selected' : '' }}>{{ __('messages.route_other') }}</option>
                                    </select>
                                    @error('routes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="notes">{{ __('messages.medication_notes') }}</label>
                                    <textarea name="notes" id="notes" rows="2"
                                              class="form-control @error('notes') is-invalid @enderror"
                                              placeholder="{{ __('messages.enter_medication_notes') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Schedules --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('messages.medication_schedules') }}</h5>
                            <button type="button" id="add-schedule-btn" class="btn btn-success btn-sm" onclick="addSchedule()">
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

const DOW_OPTIONS = `
    <option value="0">Sunday</option>
    <option value="1">Monday</option>
    <option value="2">Tuesday</option>
    <option value="3">Wednesday</option>
    <option value="4">Thursday</option>
    <option value="5">Friday</option>
    <option value="6">Saturday</option>`;

$(document).ready(function() {
    $('#patient_id').select2({ placeholder: '{{ __("messages.search_and_select_patient") }}', allowClear: true, width: '100%' });
    addSchedule();
    $('#quantity').on('input', checkAddButton);
});

function addSchedule() {
    const max = parseInt($('#quantity').val()) || 99;
    const current = document.querySelectorAll('#schedules-container > div').length;
    if (current >= max) {
        alert('{{ __("messages.cannot_remove_last_schedule") }}'.replace('remove', 'add more — limit is ' + max));
        return;
    }

    const html = `
        <div class="border rounded p-3 mb-3 bg-light" style="border-left:4px solid #28a745 !important;" data-schedule-index="${scheduleIndex}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 text-success">{{ __('messages.schedule') }} <span class="schedule-number">${scheduleIndex + 1}</span></h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSchedule(this)">
                    <i class="fas fa-trash"></i> {{ __('messages.remove_schedule') }}
                </button>
            </div>
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>{{ __('messages.schedule_time') }} <span class="text-danger">*</span></label>
                        <input type="time" name="schedules[${scheduleIndex}][time]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>{{ __('messages.schedule_frequency') }} <span class="text-danger">*</span></label>
                        <select name="schedules[${scheduleIndex}][frequency]" class="form-control" required onchange="toggleDayFields(this)">
                            <option value="">{{ __('messages.select_frequency') }}</option>
                            <option value="daily">{{ __('messages.frequency_daily') }}</option>
                            <option value="weekly">{{ __('messages.frequency_weekly') }}</option>
                            <option value="monthly">{{ __('messages.frequency_monthly') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 day-of-week-group" style="display:none;">
                    <div class="form-group mb-0">
                        <label>{{ __('messages.day_of_week') }} <span class="text-danger">*</span></label>
                        <select name="schedules[${scheduleIndex}][day_of_week]" class="form-control">${DOW_OPTIONS}</select>
                    </div>
                </div>
                <div class="col-md-4 day-of-month-group" style="display:none;">
                    <div class="form-group mb-0">
                        <label>{{ __('messages.day_of_month') }} <span class="text-danger">*</span></label>
                        <input type="number" name="schedules[${scheduleIndex}][day_of_month]" class="form-control" min="1" max="31" placeholder="1-31">
                    </div>
                </div>
            </div>
        </div>`;

    document.getElementById('schedules-container').insertAdjacentHTML('beforeend', html);

    scheduleIndex++;
    updateScheduleNumbers();
    checkAddButton();
}

function toggleDayFields(select) {
    const row    = select.closest('[data-schedule-index]');
    const dowGrp = row.querySelector('.day-of-week-group');
    const domGrp = row.querySelector('.day-of-month-group');
    const dowSel = dowGrp.querySelector('select');
    const domInp = domGrp.querySelector('input');

    if (select.value === 'weekly') {
        dowGrp.style.display = ''; domGrp.style.display = 'none';
        dowSel.required = true;   domInp.required = false;
    } else if (select.value === 'monthly') {
        dowGrp.style.display = 'none'; domGrp.style.display = '';
        dowSel.required = false;       domInp.required = true;
    } else {
        dowGrp.style.display = 'none'; domGrp.style.display = 'none';
        dowSel.required = false;       domInp.required = false;
    }
}

function removeSchedule(button) {
    if (document.querySelectorAll('#schedules-container > div').length <= 1) {
        alert('{{ __("messages.cannot_remove_last_schedule") }}');
        return;
    }
    button.closest('[data-schedule-index]').remove();
    reindexSchedules();
    checkAddButton();
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
        el.querySelectorAll('input, select').forEach(inp => {
            inp.name = inp.name.replace(/schedules\[\d+\]/, `schedules[${i}]`);
        });
    });
}

function checkAddButton() {
    const max     = parseInt($('#quantity').val()) || 99;
    const current = document.querySelectorAll('#schedules-container > div').length;
    const btn     = document.getElementById('add-schedule-btn');
    if (btn) btn.disabled = current >= max;
}
</script>
@endpush
