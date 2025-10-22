@extends('layouts.admin')

@section('title', __('messages.edit_appointment_provider'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.edit_appointment_provider') }} #{{ $appointment->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('appointment-providers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
                        </a>
                        @can('appointmentProvider-table')
                            <a href="{{ route('appointment-providers.show', $appointment->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> {{ __('messages.view') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <form action="{{ route('appointment-providers.update', $appointment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Patient Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">{{ __('messages.patient_information') }}</h5>
                                
                                <div class="form-group">
                                    <label for="name_of_patient">{{ __('messages.patient_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name_of_patient') is-invalid @enderror" 
                                           id="name_of_patient" 
                                           name="name_of_patient" 
                                           value="{{ old('name_of_patient', $appointment->name_of_patient) }}"
                                           placeholder="{{ __('messages.enter_patient_name') }}" 
                                           required>
                                    @error('name_of_patient')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone_of_patient">{{ __('messages.patient_phone') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('phone_of_patient') is-invalid @enderror" 
                                           id="phone_of_patient" 
                                           name="phone_of_patient" 
                                           value="{{ old('phone_of_patient', $appointment->phone_of_patient) }}"
                                           placeholder="{{ __('messages.enter_patient_phone') }}" 
                                           required>
                                    @error('phone_of_patient')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="address">{{ __('messages.address') }}</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" 
                                              name="address" 
                                              rows="3"
                                              placeholder="{{ __('messages.enter_address') }}">{{ old('address', $appointment->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lat">{{ __('messages.latitude') }}</label>
                                            <input type="number" 
                                                   class="form-control @error('lat') is-invalid @enderror" 
                                                   id="lat" 
                                                   name="lat" 
                                                   value="{{ old('lat', $appointment->lat) }}"
                                                   step="any"
                                                   placeholder="{{ __('messages.enter_latitude') }}">
                                            @error('lat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lng">{{ __('messages.longitude') }}</label>
                                            <input type="number" 
                                                   class="form-control @error('lng') is-invalid @enderror" 
                                                   id="lng" 
                                                   name="lng" 
                                                   value="{{ old('lng', $appointment->lng) }}"
                                                   step="any"
                                                   placeholder="{{ __('messages.enter_longitude') }}">
                                            @error('lng')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">{{ __('messages.description') }}</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4"
                                              placeholder="{{ __('messages.enter_description') }}">{{ old('description', $appointment->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Appointment Details -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">{{ __('messages.appointment_details') }}</h5>

                                <div class="form-group">
                                    <label for="user_id">{{ __('messages.user') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" 
                                            id="user_id" 
                                            name="user_id" 
                                            required>
                                        <option value="">{{ __('messages.select_user') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    {{ old('user_id', $appointment->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="provider_id">{{ __('messages.provider') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('provider_id') is-invalid @enderror" 
                                            id="provider_id" 
                                            name="provider_id" 
                                            required>
                                        <option value="">{{ __('messages.select_provider') }}</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" 
                                                    {{ old('provider_id', $appointment->provider_id) == $provider->id ? 'selected' : '' }}
                                                    data-price="{{ $provider->price }}">
                                                {{ $provider->name }} - {{ number_format($provider->price, 2) }} {{ __('messages.currency') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provider_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_of_appointment">{{ __('messages.appointment_date') }}</label>
                                            <input type="date" 
                                                   class="form-control @error('date_of_appointment') is-invalid @enderror" 
                                                   id="date_of_appointment" 
                                                   name="date_of_appointment" 
                                                   value="{{ old('date_of_appointment', $appointment->date_of_appointment ? $appointment->date_of_appointment->format('Y-m-d') : '') }}">
                                            @error('date_of_appointment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="time_of_appointment">{{ __('messages.appointment_time') }}</label>
                                            <input type="time" 
                                                   class="form-control @error('time_of_appointment') is-invalid @enderror" 
                                                   id="time_of_appointment" 
                                                   name="time_of_appointment" 
                                                   value="{{ old('time_of_appointment', $appointment->time_of_appointment ? $appointment->time_of_appointment->format('H:i') : '') }}">
                                            @error('time_of_appointment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="status">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        @foreach($statusOptions as $key => $statusName)
                                            <option value="{{ $key }}" 
                                                    {{ old('status', $appointment->status) == $key ? 'selected' : '' }}>
                                                {{ __('messages.status_' . strtolower(str_replace(' ', '_', $statusName))) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Location Helper -->
                                <div class="form-group">
                                    <label>{{ __('messages.location_helper') }}</label>
                                    <div class="btn-group d-block">
                                        <button type="button" class="btn btn-outline-info btn-sm" id="getCurrentLocation">
                                            <i class="fas fa-map-marker-alt"></i> {{ __('messages.get_current_location') }}
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="openMaps">
                                            <i class="fas fa-map"></i> {{ __('messages.open_maps') }}
                                        </button>
                                        @if($appointment->lat && $appointment->lng)
                                            <button type="button" class="btn btn-outline-success btn-sm" id="viewCurrentLocation">
                                                <i class="fas fa-eye"></i> {{ __('messages.view_saved_location') }}
                                            </button>
                                        @endif
                                    </div>
                                    <small class="form-text text-muted">{{ __('messages.location_helper_text') }}</small>
                                </div>

                                <!-- Current Information Display -->
                                <div class="form-group">
                                    <label>{{ __('messages.current_information') }}</label>
                                    <div class="card bg-light">
                                        <div class="card-body p-2">
                                            <small>
                                                <strong>{{ __('messages.created_at') }}:</strong> {{ $appointment->created_at->format('Y-m-d H:i:s') }}<br>
                                                <strong>{{ __('messages.last_updated') }}:</strong> {{ $appointment->updated_at->format('Y-m-d H:i:s') }}<br>
                                                <strong>{{ __('messages.current_status') }}:</strong> 
                                                <span class="badge {{ $appointment->status_badge_class }}">
                                                    {{ __('messages.status_' . strtolower(str_replace(' ', '_', $appointment->status_name))) }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('messages.update_appointment') }}
                                </button>
                                <a href="{{ route('appointment-providers.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                                </a>
                                @can('appointmentProvider-delete')
                                    <button type="button" class="btn btn-danger float-right" id="deleteBtn">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete_appointment') }}
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </form>

                @can('appointmentProvider-delete')
                    <form id="deleteForm" action="{{ route('appointment-providers.destroy', $appointment->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current location
    document.getElementById('getCurrentLocation').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('lat').value = position.coords.latitude;
                document.getElementById('lng').value = position.coords.longitude;
                
                // Show success message
                showAlert('success', '{{ __("messages.location_obtained_successfully") }}');
            }, function(error) {
                let errorMessage = '{{ __("messages.location_error") }}';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = '{{ __("messages.location_permission_denied") }}';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = '{{ __("messages.location_unavailable") }}';
                        break;
                    case error.TIMEOUT:
                        errorMessage = '{{ __("messages.location_timeout") }}';
                        break;
                }
                showAlert('error', errorMessage);
            });
        } else {
            showAlert('error', '{{ __("messages.geolocation_not_supported") }}');
        }
    });

    // Open maps with current form values
    document.getElementById('openMaps').addEventListener('click', function() {
        const lat = document.getElementById('lat').value;
        const lng = document.getElementById('lng').value;
        
        if (lat && lng) {
            const mapsUrl = `https://maps.google.com/maps?q=${lat},${lng}`;
            window.open(mapsUrl, '_blank');
        } else {
            showAlert('warning', '{{ __("messages.please_enter_coordinates_first") }}');
        }
    });

    // View saved location
    @if($appointment->lat && $appointment->lng)
    document.getElementById('viewCurrentLocation').addEventListener('click', function() {
        const lat = {{ $appointment->lat }};
        const lng = {{ $appointment->lng }};
        const mapsUrl = `https://maps.google.com/maps?q=${lat},${lng}`;
        window.open(mapsUrl, '_blank');
    });
    @endif

    // Provider selection handler
    document.getElementById('provider_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.dataset.price;
        
        if (price) {
            showAlert('info', '{{ __("messages.provider_price") }}: ' + price + ' {{ __("messages.currency") }}');
        }
    });

    // Delete button handler
    @can('appointmentProvider-delete')
    document.getElementById('deleteBtn').addEventListener('click', function() {
        if (confirm('{{ __("messages.confirm_delete_appointment") }}')) {
            document.getElementById('deleteForm').submit();
        }
    });
    @endcan

    // Form validation
    document.querySelector('form[method="POST"]').addEventListener('submit', function(e) {
        const requiredFields = ['name_of_patient', 'phone_of_patient', 'user_id', 'provider_id', 'status'];
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showAlert('error', '{{ __("messages.please_fill_required_fields") }}');
        }
    });

    // Alert function
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Insert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const alert = cardBody.querySelector(`.${alertClass}`);
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>
@endsection