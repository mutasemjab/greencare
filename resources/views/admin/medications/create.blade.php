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

                        <h5 class="mb-3">{{ __('messages.medication_schedules') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_time">{{ __('messages.schedule_time') }} <span class="text-danger">*</span></label>
                                    <input type="time"
                                           name="schedules[0][time]"
                                           id="schedule_time"
                                           class="form-control @error('schedules.0.time') is-invalid @enderror"
                                           value="{{ old('schedules.0.time') }}"
                                           required>
                                    @error('schedules.0.time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_frequency">{{ __('messages.schedule_frequency') }} <span class="text-danger">*</span></label>
                                    <select name="schedules[0][frequency]"
                                            id="schedule_frequency"
                                            class="form-control @error('schedules.0.frequency') is-invalid @enderror"
                                            required>
                                        <option value="">{{ __('messages.select_frequency') }}</option>
                                        <option value="daily" {{ old('schedules.0.frequency') == 'daily' ? 'selected' : '' }}>{{ __('messages.frequency_daily') }}</option>
                                        <option value="weekly" {{ old('schedules.0.frequency') == 'weekly' ? 'selected' : '' }}>{{ __('messages.frequency_weekly') }}</option>
                                        <option value="monthly" {{ old('schedules.0.frequency') == 'monthly' ? 'selected' : '' }}>{{ __('messages.frequency_monthly') }}</option>
                                    </select>
                                    @error('schedules.0.frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#patient_id').select2({
        placeholder: '{{ __("messages.search_and_select_patient") }}',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
