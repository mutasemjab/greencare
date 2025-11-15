@extends('layouts.admin')

@section('css')
<style>
.select2-container {
    z-index: 9999 !important;
}

.select2-dropdown {
    z-index: 9999 !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    min-height: 38px !important;
}

.select2-container--default .select2-selection__choice {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
    padding: 0.25rem 0.5rem !important;
    margin: 0.25rem 0.25rem 0.25rem 0 !important;
}

.card.border-warning .card-header {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.card.border-primary .card-header {
    background-color: #007bff !important;
}

.card.border-success .card-header {
    background-color: #28a745 !important;
}

.card.border-info .card-header {
    background-color: #17a2b8 !important;
}

.card.border-danger .card-header {
    background-color: #dc3545 !important;
}

.card.border-secondary .card-header {
    background-color: #6c757d !important;
}

.family-members-preview {
    max-height: 300px;
    overflow-y: auto;
}

.medication-item {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 15px;
    margin-bottom: 10px;
    background-color: #f8f9fa;
}

.medication-item .remove-medication {
    position: absolute;
    top: 10px;
    right: 10px;
}

.medication-item {
    position: relative;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.edit_room') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('rooms.update', $room->id) }}" method="POST">
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

                        <!-- Room Basic Information -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">{{ __('messages.room_title') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $room->title) }}" 
                                           placeholder="{{ __('messages.enter_room_title') }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="family_id">{{ __('messages.family') }}</label>
                                    <select name="family_id" id="family_id" class="form-control">
                                        <option value="">{{ __('messages.select_family') }}</option>
                                        @foreach($families as $family)
                                            <option value="{{ $family->id }}" {{ old('family_id', $room->family_id) == $family->id ? 'selected' : '' }}>
                                                {{ $family->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">{{ __('messages.room_description') }}</label>
                                    <textarea name="description" 
                                              id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="{{ __('messages.enter_room_description') }}">{{ old('description', $room->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="discount">{{ __('messages.room_discount') }}</label>
                                    <input type="number" name="discount" 
                                              class="form-control @error('discount') is-invalid @enderror" 
                                              placeholder="{{ __('messages.enter_room_discount') }}" value="{{ old('discount', $room->discount) }}">
                                    @error('discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Room Members -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('messages.room_members') }}</h5>
                            </div>
                        </div>

                        <!-- Patients (Required) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-injured mr-2"></i>
                                            {{ __('messages.patients') }} <span class="text-danger">*</span>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="patients[]" 
                                                    id="patients_select" 
                                                    class="form-control patients-select" 
                                                    multiple 
                                                    required
                                                    style="width: 100%;">
                                                @foreach($room->patients as $patient)
                                                    <option value="{{ $patient->id }}" selected>
                                                        {{ $patient->name }} - {{ $patient->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">{{ __('messages.search_patients_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Doctors -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-md mr-2"></i>
                                            {{ __('messages.doctors') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="doctors[]" 
                                                    id="doctors_select" 
                                                    class="form-control doctors-select" 
                                                    multiple
                                                    style="width: 100%;">
                                                @foreach($room->doctors as $doctor)
                                                    <option value="{{ $doctor->id }}" selected>
                                                        {{ $doctor->name }} - {{ $doctor->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">{{ __('messages.search_doctors_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nurses -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-nurse mr-2"></i>
                                            {{ __('messages.nurses') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="nurses[]" 
                                                    id="nurses_select" 
                                                    class="form-control nurses-select" 
                                                    multiple
                                                    style="width: 100%;">
                                                @foreach($room->nurses as $nurse)
                                                    <option value="{{ $nurse->id }}" selected>
                                                        {{ $nurse->name }} - {{ $nurse->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">{{ __('messages.search_nurses_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Family Members (if needed) -->
                        @if($room->familyMembers->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users mr-2"></i>
                                            {{ __('messages.family_members') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="family_members[]" 
                                                    id="family_members_select" 
                                                    class="form-control family-members-individual-select" 
                                                    multiple
                                                    style="width: 100%;">
                                                @foreach($room->familyMembers as $familyMember)
                                                    <option value="{{ $familyMember->id }}" selected>
                                                        {{ $familyMember->name }} - {{ $familyMember->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">{{ __('messages.search_family_members_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('messages.update_room') }}
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
    let selectedPatients = [];

    // Initialize selected patients from pre-loaded data
    @foreach($room->patients as $patient)
        selectedPatients.push({
            id: {{ $patient->id }},
            name: '{{ $patient->name }}',
            phone: '{{ $patient->phone }}'
        });
    @endforeach

    // Initialize Select2 for patients
    $('.patients-select').select2({
        placeholder: '{{ __("messages.search_and_select_patients") }}',
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
            processResults: function (data, params) {
                return {
                    results: data.data.map(function(patient) {
                        return {
                            id: patient.id,
                            text: patient.name + ' - ' + patient.phone,
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
        templateResult: formatUser,
        templateSelection: formatUserSelection,
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Track selected patients
    $('.patients-select').on('change', function() {
        selectedPatients = [];
        $(this).select2('data').forEach(function(item) {
            if (item.patient) {
                selectedPatients.push({
                    id: item.patient.id,
                    name: item.patient.name,
                    phone: item.patient.phone
                });
            } else {
                // For pre-selected options
                selectedPatients.push({
                    id: item.id,
                    name: item.text.split(' - ')[0],
                    phone: item.text.split(' - ')[1]
                });
            }
        });
    });

    // Initialize Select2 for doctors
    $('.doctors-select').select2({
        placeholder: '{{ __("messages.search_and_select_doctors") }}',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("api.doctors.search") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.data.map(function(doctor) {
                        return {
                            id: doctor.id,
                            text: doctor.name + ' - ' + doctor.phone,
                            user: doctor
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page
                    }
                };
            }
        },
        minimumInputLength: 2,
        templateResult: formatUser,
        templateSelection: formatUserSelection,
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Initialize Select2 for nurses
    $('.nurses-select').select2({
        placeholder: '{{ __("messages.search_and_select_nurses") }}',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("api.nurses.search") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.data.map(function(nurse) {
                        return {
                            id: nurse.id,
                            text: nurse.name + ' - ' + nurse.phone,
                            user: nurse
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page
                    }
                };
            }
        },
        minimumInputLength: 2,
        templateResult: formatUser,
        templateSelection: formatUserSelection,
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Initialize Select2 for family members (if present)
    if ($('.family-members-individual-select').length) {
        $('.family-members-individual-select').select2({
            placeholder: '{{ __("messages.search_and_select_family_members") }}',
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
                processResults: function (data, params) {
                    return {
                        results: data.data.map(function(user) {
                            return {
                                id: user.id,
                                text: user.name + ' - ' + user.phone,
                                user: user
                            };
                        }),
                        pagination: {
                            more: data.current_page < data.last_page
                        }
                    };
                }
            },
            minimumInputLength: 2,
            templateResult: formatUser,
            templateSelection: formatUserSelection,
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    }

    // Format user display in dropdown
    function formatUser(user) {
        if (user.loading) {
            return '{{ __("messages.searching") }}...';
        }

        var userData = user.patient || user.user || user;
        if (!userData) {
            return user.text;
        }

        return '<div class="d-flex align-items-center">' +
                    '<div class="mr-2">' +
                        '<div class="font-weight-bold">' + userData.name + '</div>' +
                        '<small class="text-muted">' + userData.phone + '</small>' +
                    '</div>' +
                '</div>';
    }

    // Format selected user
    function formatUserSelection(user) {
        var userData = user.patient || user.user || user;
        if (!userData) {
            return user.text || user.id;
        }
        
        return userData.name + ' - ' + userData.phone;
    }
});
</script>
@endpush