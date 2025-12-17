@extends('layouts.admin')

@section('title', __('messages.appointment_providers'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.appointment_providers') }}</h3>
                    <div class="card-tools">
                        @can('appointmentProvider-add')
                            <a href="{{ route('appointment-providers.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> {{ __('messages.add_appointment_provider') }}
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('appointment-providers.index') }}" id="filterForm">
                                <div class="row">
                                    <!-- Status Filter -->
                                    <div class="col-md-2 mb-3">
                                        <label for="status" class="form-label">{{ __('messages.status') }}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>{{ __('messages.all_statuses') }}</option>
                                            @foreach($statusOptions as $key => $statusName)
                                                <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>
                                                    {{ __('messages.status_' . strtolower(str_replace(' ', '_', $statusName))) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Diagnosis Status Filter -->
                                    <div class="col-md-2 mb-3">
                                        <label for="has_diagnosis" class="form-label">{{ __('messages.diagnosis_status') }}</label>
                                        <select name="has_diagnosis" id="has_diagnosis" class="form-control">
                                            <option value="">{{ __('messages.all') }}</option>
                                            <option value="1" {{ request('has_diagnosis') == '1' ? 'selected' : '' }}>{{ __('messages.with_diagnosis') }}</option>
                                            <option value="0" {{ request('has_diagnosis') == '0' ? 'selected' : '' }}>{{ __('messages.without_diagnosis') }}</option>
                                        </select>
                                    </div>

                                    <!-- Date From -->
                                    <div class="col-md-2 mb-3">
                                        <label for="date_from" class="form-label">{{ __('messages.date_from') }}</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                                    </div>

                                    <!-- Date To -->
                                    <div class="col-md-2 mb-3">
                                        <label for="date_to" class="form-label">{{ __('messages.date_to') }}</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                                    </div>

                                    <!-- Provider Filter -->
                                    <div class="col-md-2 mb-3">
                                        <label for="provider_id" class="form-label">{{ __('messages.provider') }}</label>
                                        <select name="provider_id" id="provider_id" class="form-control">
                                            <option value="">{{ __('messages.all_providers') }}</option>
                                            @foreach($providers as $provider)
                                                <option value="{{ $provider->id }}" {{ $providerId == $provider->id ? 'selected' : '' }}>
                                                    {{ $provider->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Search -->
                                    <div class="col-md-2 mb-3">
                                        <label for="search" class="form-label">{{ __('messages.search') }}</label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               placeholder="{{ __('messages.search_patients_phone_address') }}" value="{{ $search }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary mr-2">
                                            <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                                        </button>
                                        <a href="{{ route('appointment-providers.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> {{ __('messages.clear') }}
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.patient_name') }}</th>
                                    <th>{{ __('messages.patient_phone') }}</th>
                                    <th>{{ __('messages.provider') }}</th>
                                    <th>{{ __('messages.appointment_date') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.diagnosis_status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->id }}</td>
                                        <td>
                                            <strong>{{ $appointment->name_of_patient }}</strong>
                                            @if($appointment->address)
                                                <br><small class="text-muted">{{ Str::limit($appointment->address, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="tel:{{ $appointment->phone_of_patient }}" class="text-primary">
                                                {{ $appointment->phone_of_patient }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($appointment->provider)
                                                <strong>{{ $appointment->provider->name }}</strong><br>
                                                <small class="text-muted">
                                                    {{ __('messages.experience') }}: {{ $appointment->provider->number_years_experience }}
                                                </small>
                                            @else
                                                <span class="text-muted">{{ __('messages.not_available') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($appointment->date_of_appointment)
                                                {{ $appointment->date_of_appointment->format('Y-m-d') }}
                                                @if($appointment->time_of_appointment)
                                                    <br><small>{{ $appointment->time_of_appointment }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">{{ __('messages.not_specified') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('appointmentProvider-edit')
                                                <select class="form-control form-control-sm status-select" 
                                                        data-id="{{ $appointment->id }}" 
                                                        style="min-width: 120px;">
                                                    @foreach($statusOptions as $key => $statusName)
                                                        <option value="{{ $key }}" 
                                                                {{ $appointment->status == $key ? 'selected' : '' }}>
                                                            {{ __('messages.status_' . strtolower(str_replace(' ', '_', $statusName))) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <span class="badge {{ $appointment->status_badge_class }}">
                                                    {{ $appointment->status_name }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td>
                                            @if($appointment->diagnosis)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> {{ __('messages.diagnosed') }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-circle"></i> {{ __('messages.pending_diagnosis') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('appointmentProvider-table')
                                                    <a href="{{ route('appointment-providers.show', $appointment->id) }}" 
                                                       class="btn btn-info btn-sm" title="{{ __('messages.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan

                                                @if($appointment->diagnosis)
                                                    @can('diagnosis-table')
                                                        <a href="{{ route('diagnosis.show', $appointment->diagnosis->id) }}" 
                                                           class="btn btn-success btn-sm" title="{{ __('messages.view_diagnosis') }}">
                                                            <i class="fas fa-stethoscope"></i>
                                                        </a>
                                                    @endcan
                                                    @can('diagnosis-edit')
                                                        <a href="{{ route('diagnosis.edit', $appointment->diagnosis->id) }}" 
                                                           class="btn btn-warning btn-sm" title="{{ __('messages.edit_diagnosis') }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                @else
                                                    @can('diagnosis-add')
                                                        <a href="{{ route('diagnosis.create.appointment', $appointment->id) }}" 
                                                           class="btn btn-primary btn-sm" title="{{ __('messages.add_diagnosis') }}">
                                                            <i class="fas fa-plus"></i> {{ __('messages.diagnose') }}
                                                        </a>
                                                    @endcan
                                                @endif

                                                @can('appointmentProvider-delete')
                                                    <form action="{{ route('appointment-providers.destroy', $appointment->id) }}" 
                                                          method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm delete-btn" 
                                                                title="{{ __('messages.delete') }}"
                                                                data-confirm="{{ __('messages.confirm_delete_appointment') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_appointment_providers_found') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($appointments->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $appointments->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status change handler
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const appointmentId = this.dataset.id;
            const newStatus = this.value;
            const originalStatus = this.querySelector('option[selected]').value;
            
            this.disabled = true;
            
            fetch(`{{ route('appointment-providers.update-status', ':id') }}`.replace(':id', appointmentId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    this.querySelector('option[selected]').removeAttribute('selected');
                    this.querySelector(`option[value="${newStatus}"]`).setAttribute('selected', 'selected');
                } else {
                    this.value = originalStatus;
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                this.value = originalStatus;
                showAlert('error', '{{ __("messages.error_occurred") }}');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });

    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        document.querySelector('.card-body').insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = document.querySelector(`.${alertClass}`);
            if (alert) alert.remove();
        }, 5000);
    }
});
</script>
@endsection