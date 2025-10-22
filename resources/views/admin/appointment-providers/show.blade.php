@extends('layouts.admin')

@section('title', __('messages.view_appointment_provider'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.appointment_provider_details') }} #{{ $appointment->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('appointment-providers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
                        </a>
                        @can('appointmentProvider-edit')
                            <a href="{{ route('appointment-providers.edit', $appointment->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                            </a>
                        @endcan
                        @can('appointmentProvider-delete')
                            <form action="{{ route('appointment-providers.destroy', $appointment->id) }}" 
                                  method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm delete-btn" 
                                        data-confirm="{{ __('messages.confirm_delete_appointment') }}">
                                    <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Patient Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user"></i> {{ __('messages.patient_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.patient_name') }}:</strong></td>
                                            <td>{{ $appointment->name_of_patient }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.patient_phone') }}:</strong></td>
                                            <td>
                                                <a href="tel:{{ $appointment->phone_of_patient }}" class="text-primary">
                                                    <i class="fas fa-phone"></i> {{ $appointment->phone_of_patient }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.address') }}:</strong></td>
                                            <td>
                                                @if($appointment->address)
                                                    {{ $appointment->address }}
                                                    @if($appointment->lat && $appointment->lng)
                                                        <br>
                                                        <a href="https://maps.google.com/maps?q={{ $appointment->lat }},{{ $appointment->lng }}" 
                                                           target="_blank" class="btn btn-outline-info btn-sm mt-1">
                                                            <i class="fas fa-map-marker-alt"></i> {{ __('messages.view_on_map') }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ __('messages.not_specified') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($appointment->lat && $appointment->lng)
                                        <tr>
                                            <td><strong>{{ __('messages.coordinates') }}:</strong></td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ __('messages.latitude') }}: {{ $appointment->lat }}<br>
                                                    {{ __('messages.longitude') }}: {{ $appointment->lng }}
                                                </small>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>{{ __('messages.description') }}:</strong></td>
                                            <td>
                                                @if($appointment->description)
                                                    <div class="text-break">{{ $appointment->description }}</div>
                                                @else
                                                    <span class="text-muted">{{ __('messages.no_description') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Details -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-alt"></i> {{ __('messages.appointment_details') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.appointment_date') }}:</strong></td>
                                            <td>
                                                @if($appointment->date_of_appointment)
                                                    <span class="badge badge-info">
                                                        {{ $appointment->date_of_appointment->format('Y-m-d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('messages.not_specified') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.appointment_time') }}:</strong></td>
                                            <td>
                                                @if($appointment->time_of_appointment)
                                                    <span class="badge badge-info">
                                                        {{ $appointment->time_of_appointment->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('messages.not_specified') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.status') }}:</strong></td>
                                            <td>
                                                <span class="badge {{ $appointment->status_badge_class }} badge-lg">
                                                    {{ __('messages.status_' . strtolower(str_replace(' ', '_', $appointment->status_name))) }}
                                                </span>
                                                @can('appointmentProvider-edit')
                                                    <br><small class="text-muted">{{ __('messages.click_edit_to_change_status') }}</small>
                                                @endcan
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                            <td>
                                                <i class="fas fa-clock text-muted"></i> 
                                                {{ $appointment->created_at->format('Y-m-d H:i:s') }}
                                                <br><small class="text-muted">{{ $appointment->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.last_updated') }}:</strong></td>
                                            <td>
                                                <i class="fas fa-edit text-muted"></i> 
                                                {{ $appointment->updated_at->format('Y-m-d H:i:s') }}
                                                <br><small class="text-muted">{{ $appointment->updated_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Provider Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-md"></i> {{ __('messages.provider_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($appointment->provider)
                                        <div class="row">
                                            @if($appointment->provider->photo)
                                                <div class="col-md-4 text-center">
                                                    <img src="{{ asset('assets/admin/uploads/' . $appointment->provider->photo) }}" 
                                                         alt="{{ $appointment->provider->name }}" 
                                                         class="img-fluid rounded-circle" 
                                                         style="width: 80px; height: 80px; object-fit: cover;">
                                                </div>
                                            @endif
                                            <div class="{{ $appointment->provider->photo ? 'col-md-8' : 'col-md-12' }}">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>{{ __('messages.name') }}:</strong></td>
                                                        <td>{{ $appointment->provider->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('messages.experience') }}:</strong></td>
                                                        <td>{{ $appointment->provider->number_years_experience }} {{ __('messages.years') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('messages.price') }}:</strong></td>
                                                        <td>
                                                            <span class="text-success font-weight-bold">
                                                                {{ number_format($appointment->provider->price, 2) }} {{ __('messages.currency') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('messages.rating') }}:</strong></td>
                                                        <td>
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $appointment->provider->rating)
                                                                    <i class="fas fa-star text-warning"></i>
                                                                @else
                                                                    <i class="far fa-star text-muted"></i>
                                                                @endif
                                                            @endfor
                                                            <span class="ml-1">({{ number_format($appointment->provider->rating, 1) }})</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        @if($appointment->provider->description)
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <strong>{{ __('messages.description') }}:</strong>
                                                    <p class="text-break mt-1">{{ $appointment->provider->description }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                            <p>{{ __('messages.provider_not_available') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-circle"></i> {{ __('messages.user_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($appointment->user)
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('messages.name') }}:</strong></td>
                                                <td>{{ $appointment->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('messages.email') }}:</strong></td>
                                                <td>
                                                    <a href="mailto:{{ $appointment->user->email }}" class="text-primary">
                                                        <i class="fas fa-envelope"></i> {{ $appointment->user->email }}
                                                    </a>
                                                </td>
                                            </tr>
                                            @if($appointment->user->phone)
                                            <tr>
                                                <td><strong>{{ __('messages.phone') }}:</strong></td>
                                                <td>
                                                    <a href="tel:{{ $appointment->user->phone }}" class="text-primary">
                                                        <i class="fas fa-phone"></i> {{ $appointment->user->phone }}
                                                    </a>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td><strong>{{ __('messages.member_since') }}:</strong></td>
                                                <td>
                                                    <i class="fas fa-calendar text-muted"></i> 
                                                    {{ $appointment->user->created_at->format('Y-m-d') }}
                                                    <br><small class="text-muted">{{ $appointment->user->created_at->diffForHumans() }}</small>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- User's Other Appointments Count -->
                                        <div class="mt-3">
                                            <div class="bg-light p-2 rounded">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> 
                                                    {{ __('messages.total_appointments_by_user') }}: 
                                                    <strong>{{ \App\Models\AppointmentProvider::where('user_id', $appointment->user->id)->count() }}</strong>
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                            <p>{{ __('messages.user_not_available') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt"></i> {{ __('messages.quick_actions') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        @if($appointment->phone_of_patient)
                                            <a href="tel:{{ $appointment->phone_of_patient }}" class="btn btn-success">
                                                <i class="fas fa-phone"></i> {{ __('messages.call_patient') }}
                                            </a>
                                        @endif

                                        @if($appointment->user && $appointment->user->email)
                                            <a href="mailto:{{ $appointment->user->email }}" class="btn btn-primary">
                                                <i class="fas fa-envelope"></i> {{ __('messages.email_user') }}
                                            </a>
                                        @endif

                                        @if($appointment->lat && $appointment->lng)
                                            <a href="https://maps.google.com/maps?q={{ $appointment->lat }},{{ $appointment->lng }}" 
                                               target="_blank" class="btn btn-info">
                                                <i class="fas fa-map-marker-alt"></i> {{ __('messages.get_directions') }}
                                            </a>
                                        @endif

                                        @can('appointmentProvider-edit')
                                            <a href="{{ route('appointment-providers.edit', $appointment->id) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i> {{ __('messages.edit_appointment') }}
                                            </a>
                                        @endcan
                                    </div>
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

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteButton = document.querySelector('.delete-btn');
    if (deleteButton) {
        deleteButton.addEventListener('click', function(e) {
            e.preventDefault();
            const confirmMessage = this.dataset.confirm;
            
            if (confirm(confirmMessage)) {
                this.closest('form').submit();
            }
        });
    }
});
</script>

@endsection