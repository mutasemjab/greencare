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
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.edit_room') }}: {{ $room->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('rooms.update', $room) }}" method="POST">
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

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
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
                                            <small class="float-right">{{ __('messages.current') }}: {{ $room->patients->count() }}</small>
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
                                            <small class="float-right">{{ __('messages.current') }}: {{ $room->doctors->count() }}</small>
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
                                            <small class="float-right">{{ __('messages.current') }}: {{ $room->nurses->count() }}</small>
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

                        <!-- Family Members -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users mr-2"></i>
                                            {{ __('messages.family_members') }}
                                            <small class="float-right">{{ __('messages.current') }}: {{ $room->familyMembers->count() }}</small>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="family_members[]" 
                                                    id="family_members_select" 
                                                    class="form-control family-members-select" 
                                                    multiple
                                                    style="width: 100%;">
                                                @foreach($room->familyMembers as $member)
                                                    <option value="{{ $member->id }}" selected>
                                                        {{ $member->name }} - {{ $member->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">{{ __('messages.search_family_members_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <div>
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-info mr-2">
                                    <i class="fas fa-eye"></i> {{ __('messages.view_details') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('messages.update_room') }}
                                </button>
                            </div>
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
        templateSelection: formatUserSelection
    });

    // Initialize Select2 for doctors
    $('.doctors-select').select2({
        placeholder: '{{ __("messages.search_and_select_doctors") }}',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("api.doctors") }}',
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
                    results: data.map(function(doctor) {
                        return {
                            id: doctor.id,
                            text: doctor.name + ' - ' + doctor.phone,
                            user: doctor
                        };
                    })
                };
            }
        },
        minimumInputLength: 2,
        templateResult: formatUser,
        templateSelection: formatUserSelection
    });

    // Initialize Select2 for nurses
    $('.nurses-select').select2({
        placeholder: '{{ __("messages.search_and_select_nurses") }}',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("api.nurses") }}',
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
                    results: data.map(function(nurse) {
                        return {
                            id: nurse.id,
                            text: nurse.name + ' - ' + nurse.phone,
                            user: nurse
                        };
                    })
                };
            }
        },
        minimumInputLength: 2,
        templateResult: formatUser,
        templateSelection: formatUserSelection
    });

    // Initialize Select2 for family members
    $('.family-members-select').select2({
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
            processResults: function (data) {
                return {
                    results: data.data.map(function(user) {
                        return {
                            id: user.id,
                            text: user.name + ' - ' + user.phone,
                            user: user
                        };
                    })
                };
            }
        },
        minimumInputLength: 2,
        templateResult: formatUser,
        templateSelection: formatUserSelection
    });

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
                        '<div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 30px; height: 30px; font-size: 0.8rem;">' +
                            userData.name.charAt(0).toUpperCase() +
                        '</div>' +
                    '</div>' +
                    '<div>' +
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

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
