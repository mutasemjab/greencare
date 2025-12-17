@extends('layouts.admin')

@section('css')
<style>
.medication-item {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
    position: relative;
}

.medication-item .remove-medication {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.edit_diagnosis') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('diagnosis.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('diagnosis.update', $diagnosis->id) }}" method="POST">
                    @csrf
                    @method('PUT')

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

                        <!-- Appointment Info -->
                        <div class="card mb-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle"></i> {{ __('messages.appointment_information') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>{{ __('messages.patient_name') }}:</strong> {{ $diagnosis->appointment->name_of_patient }}</p>
                                        <p><strong>{{ __('messages.patient_phone') }}:</strong> {{ $diagnosis->appointment->phone_of_patient }}</p>
                                        @if($diagnosis->appointment->address)
                                            <p><strong>{{ __('messages.address') }}:</strong> {{ $diagnosis->appointment->address }}</p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>{{ __('messages.provider') }}:</strong> {{ $diagnosis->appointment->provider->name }}</p>
                                        <p><strong>{{ __('messages.diagnosed_by') }}:</strong> {{ $diagnosis->diagnosedBy->name }}</p>
                                        <p><strong>{{ __('messages.diagnosed_at') }}:</strong> {{ $diagnosis->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Room Assignment (Optional) -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="room_id">{{ __('messages.assign_to_room') }} ({{ __('messages.optional') }})</label>
                                    <select name="room_id" id="room_id" class="form-control">
                                        <option value="">{{ __('messages.no_room') }}</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" {{ old('room_id', $diagnosis->room_id) == $room->id ? 'selected' : '' }}>
                                                {{ $room->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">{{ __('messages.room_assignment_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnosis Details -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="diagnosis">{{ __('messages.diagnosis') }} <span class="text-danger">*</span></label>
                                    <textarea name="diagnosis" 
                                              id="diagnosis" 
                                              class="form-control @error('diagnosis') is-invalid @enderror" 
                                              rows="4" 
                                              placeholder="{{ __('messages.enter_diagnosis') }}" 
                                              required>{{ old('diagnosis', $diagnosis->diagnosis) }}</textarea>
                                    @error('diagnosis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="symptoms">{{ __('messages.symptoms') }}</label>
                                    <textarea name="symptoms" 
                                              id="symptoms" 
                                              class="form-control @error('symptoms') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="{{ __('messages.enter_symptoms') }}">{{ old('symptoms', $diagnosis->symptoms) }}</textarea>
                                    @error('symptoms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="treatment_plan">{{ __('messages.treatment_plan') }}</label>
                                    <textarea name="treatment_plan" 
                                              id="treatment_plan" 
                                              class="form-control @error('treatment_plan') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="{{ __('messages.enter_treatment_plan') }}">{{ old('treatment_plan', $diagnosis->treatment_plan) }}</textarea>
                                    @error('treatment_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">{{ __('messages.notes') }}</label>
                                    <textarea name="notes" 
                                              id="notes" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              rows="2" 
                                              placeholder="{{ __('messages.enter_notes') }}">{{ old('notes', $diagnosis->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Medications Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-pills"></i> {{ __('messages.medications') }}
                                            </h6>
                                            <button type="button" class="btn btn-light btn-sm" onclick="addMedication()">
                                                <i class="fas fa-plus"></i> {{ __('messages.add_medication') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="medications-container">
                                            <!-- Existing medications will be loaded here -->
                                            @foreach($diagnosis->medications as $index => $medication)
                                                <div class="medication-item" data-medication="{{ $index }}">
                                                    <button type="button" class="btn btn-sm btn-danger remove-medication" onclick="removeMedication({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('messages.medication_name') }} <span class="text-danger">*</span></label>
                                                                <input type="text" name="medications[{{ $index }}][name]" class="form-control" 
                                                                       value="{{ $medication->name }}" placeholder="{{ __('messages.enter_medication_name') }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('messages.dosage') }}</label>
                                                                <input type="text" name="medications[{{ $index }}][dosage]" class="form-control" 
                                                                       value="{{ $medication->dosage }}" placeholder="{{ __('messages.enter_dosage') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('messages.quantity') }}</label>
                                                                <input type="number" name="medications[{{ $index }}][quantity]" class="form-control" 
                                                                       value="{{ $medication->quantity }}" placeholder="{{ __('messages.enter_quantity') }}" min="1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>{{ __('messages.medication_notes') }}</label>
                                                                <textarea name="medications[{{ $index }}][notes]" class="form-control" rows="2" 
                                                                          placeholder="{{ __('messages.enter_medication_notes') }}">{{ $medication->notes }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>{{ __('messages.schedules') }} <span class="text-danger">*</span></label>
                                                            <button type="button" class="btn btn-sm btn-outline-primary mb-2" onclick="addSchedule({{ $index }})">
                                                                <i class="fas fa-clock"></i> {{ __('messages.add_schedule') }}
                                                            </button>
                                                            <div id="schedules-{{ $index }}">
                                                                @foreach($medication->schedules as $scheduleIndex => $schedule)
                                                                    <div class="row mb-2 schedule-row" data-schedule="{{ $schedule->id }}">
                                                                        <div class="col-md-5">
                                                                            <input type="time" name="medications[{{ $index }}][schedules][{{ $schedule->id }}][time]" 
                                                                                   class="form-control" value="{{ $schedule->time }}" required>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <select name="medications[{{ $index }}][schedules][{{ $schedule->id }}][frequency]" class="form-control" required>
                                                                                <option value="daily" {{ $schedule->frequency == 'daily' ? 'selected' : '' }}>{{ __('messages.daily') }}</option>
                                                                                <option value="weekly" {{ $schedule->frequency == 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                                                                                <option value="monthly" {{ $schedule->frequency == 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeSchedule({{ $schedule->id }})">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('diagnosis.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> {{ __('messages.update_diagnosis') }}
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
let medicationCounter = {{ $diagnosis->medications->count() }};

function addMedication() {
    medicationCounter++;
    
    const medicationHtml = `
        <div class="medication-item" data-medication="${medicationCounter}">
            <button type="button" class="btn btn-sm btn-danger remove-medication" onclick="removeMedication(${medicationCounter})">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('messages.medication_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="medications[${medicationCounter}][name]" class="form-control" 
                               placeholder="{{ __('messages.enter_medication_name') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('messages.dosage') }}</label>
                        <input type="text" name="medications[${medicationCounter}][dosage]" class="form-control" 
                               placeholder="{{ __('messages.enter_dosage') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('messages.quantity') }}</label>
                        <input type="number" name="medications[${medicationCounter}][quantity]" class="form-control" 
                               placeholder="{{ __('messages.enter_quantity') }}" min="1">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ __('messages.medication_notes') }}</label>
                        <textarea name="medications[${medicationCounter}][notes]" class="form-control" rows="2" 
                                  placeholder="{{ __('messages.enter_medication_notes') }}"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <label>{{ __('messages.schedules') }} <span class="text-danger">*</span></label>
                    <button type="button" class="btn btn-sm btn-outline-primary mb-2" onclick="addSchedule(${medicationCounter})">
                        <i class="fas fa-clock"></i> {{ __('messages.add_schedule') }}
                    </button>
                    <div id="schedules-${medicationCounter}">
                        <!-- Schedules will be added here -->
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('medications-container').insertAdjacentHTML('beforeend', medicationHtml);
    addSchedule(medicationCounter);
}

function removeMedication(medicationId) {
    document.querySelector(`[data-medication="${medicationId}"]`).remove();
}

function addSchedule(medicationId) {
    const scheduleId = Date.now();
    const scheduleHtml = `
        <div class="row mb-2 schedule-row" data-schedule="${scheduleId}">
            <div class="col-md-5">
                <input type="time" name="medications[${medicationId}][schedules][${scheduleId}][time]" 
                       class="form-control" required>
            </div>
            <div class="col-md-5">
                <select name="medications[${medicationId}][schedules][${scheduleId}][frequency]" class="form-control" required>
                    <option value="daily">{{ __('messages.daily') }}</option>
                    <option value="weekly">{{ __('messages.weekly') }}</option>
                    <option value="monthly">{{ __('messages.monthly') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSchedule(${scheduleId})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    document.getElementById(`schedules-${medicationId}`).insertAdjacentHTML('beforeend', scheduleHtml);
}

function removeSchedule(scheduleId) {
    document.querySelector(`[data-schedule="${scheduleId}"]`).remove();
}
</script>
@endpush