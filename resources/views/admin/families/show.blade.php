@extends('layouts.admin')
@section('css')
<style>
/* Fix Select2 dropdown z-index and styling */
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

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}

.select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
}

.select2-results__option {
    padding: 8px 12px !important;
}

.select2-results__option--highlighted {
    background-color: #007bff !important;
    color: white !important;
}

/* Modal adjustments */
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.family_details') }}: {{ $family->name }}</h3>
                        <div class="btn-group">
                            <a href="{{ route('families.edit', $family) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                            </a>
                            <a href="{{ route('families.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Family Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.family_information') }}</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.family_name') }}:</strong></td>
                                            <td>{{ $family->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.members_count') }}:</strong></td>
                                            <td>
                                                <span class="badge badge-info">{{ $family->users->count() }} {{ __('messages.members') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                            <td>{{ $family->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.updated_at') }}:</strong></td>
                                            <td>{{ $family->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.quick_actions') }}</h5>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success btn-block mb-2" data-toggle="modal" data-target="#addMemberModal">
                                            <i class="fas fa-user-plus"></i> {{ __('messages.add_member') }}
                                        </button>
                                        <a href="{{ route('families.edit', $family) }}" class="btn btn-warning btn-block">
                                            <i class="fas fa-edit"></i> {{ __('messages.edit_family') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Members -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('messages.family_members') }} ({{ $family->users->count() }})</h5>
                                </div>
                                <div class="card-body">
                                    @if($family->users->count() > 0)
                                        <div class="row">
                                            @foreach($family->users as $user)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="card h-100 border-primary">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center mb-3">
                                                                @if($user->photo)
                                                                    <img src="{{ asset('storage/' . $user->photo) }}" 
                                                                         class="rounded-circle mr-3" 
                                                                         width="50" height="50"
                                                                         alt="{{ $user->name }}">
                                                                @else
                                                                    <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center bg-primary text-white" 
                                                                         style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                                        {{ substr($user->name, 0, 1) }}
                                                                    </div>
                                                                @endif
                                                                <div class="flex-grow-1">
                                                                    <h6 class="card-title mb-1">{{ $user->name }}</h6>
                                                                    <p class="card-text text-muted small mb-0">{{ $user->phone }}</p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <span class="badge badge-secondary mr-1">{{ $user->user_type_text }}</span>
                                                                <span class="badge badge-info mr-1">{{ $user->gender_text }}</span>
                                                                <span class="badge badge-{{ $user->activate == 1 ? 'success' : 'danger' }}">
                                                                    {{ $user->active_status_text }}
                                                                </span>
                                                            </div>

                                                            <div class="text-muted small mb-3">
                                                                <div><strong>{{ __('messages.date_of_birth') }}:</strong> {{ $user->date_of_birth }}</div>
                                                                @if($user->email)
                                                                    <div><strong>{{ __('messages.email') }}:</strong> {{ $user->email }}</div>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex justify-content-end">
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-danger" 
                                                                        data-toggle="modal" 
                                                                        data-target="#removeMemberModal{{ $user->id }}">
                                                                    <i class="fas fa-user-minus"></i> {{ __('messages.remove') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Remove Member Modal -->
                                                <div class="modal fade" id="removeMemberModal{{ $user->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('messages.remove_member') }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('messages.are_you_sure_remove_member') }} "<strong>{{ $user->name }}</strong>" {{ __('messages.from_family') }} "<strong>{{ $family->name }}</strong>"?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    {{ __('messages.cancel') }}
                                                                </button>
                                                                <form action="{{ route('families.remove-member', $family) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                                    <button type="submit" class="btn btn-danger">
                                                                        {{ __('messages.remove') }}
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">{{ __('messages.no_members_in_family') }}</p>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMemberModal">
                                                {{ __('messages.add_first_member') }}
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.add_member_to_family') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('families.add-member', $family) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_id">{{ __('messages.select_patient') }}</label>
                        <select name="user_id" id="user_id" class="form-control patient-select" required style="width: 100%;">
                            <option value="">{{ __('messages.search_and_select_patient') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('messages.search_patient_help') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> {{ __('messages.add_member') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Wait for modal to be fully shown before initializing Select2
    $('#addMemberModal').on('shown.bs.modal', function () {
        initializePatientSelect2();
    });

    // Clean up when modal is hidden
    $('#addMemberModal').on('hidden.bs.modal', function () {
        if ($('.patient-select').hasClass('select2-hidden-accessible')) {
            $('.patient-select').select2('destroy');
        }
        $('.patient-select').val('').trigger('change');
    });

    function initializePatientSelect2() {
        // Destroy existing instance if it exists
        if ($('.patient-select').hasClass('select2-hidden-accessible')) {
            $('.patient-select').select2('destroy');
        }

        // Initialize Select2
        $('.patient-select').select2({
            dropdownParent: $('#addMemberModal'), // CRITICAL: This fixes the focus issue
            placeholder: '{{ __("messages.search_patient_by_name_or_phone") }}',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("api.patients.search") }}',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        search: params.term,
                        family_id: {{ $family->id }},
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
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
                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: formatPatient,
            templateSelection: formatPatientSelection,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        // Open dropdown automatically after initialization
        setTimeout(function() {
            $('.patient-select').select2('open');
        }, 100);
    }

    // Format patient display in dropdown
    function formatPatient(patient) {
        if (patient.loading) {
            return '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> {{ __("messages.searching") }}...</div>';
        }

        if (!patient.patient) {
            return patient.text;
        }

        var photoHtml = patient.patient.photo_url ? 
            '<img src="' + patient.patient.photo_url + '" class="rounded-circle" width="32" height="32" style="object-fit: cover;">' :
            '<div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 32px; height: 32px; font-size: 0.9rem;">' +
                patient.patient.name.charAt(0).toUpperCase() +
            '</div>';

        return '<div class="d-flex align-items-center py-1">' +
                    '<div class="mr-3">' + photoHtml + '</div>' +
                    '<div class="flex-grow-1">' +
                        '<div class="font-weight-bold">' + patient.patient.name + '</div>' +
                        '<small class="text-muted">' + patient.patient.phone + ' • ' + patient.patient.gender_text + '</small>' +
                    '</div>' +
                '</div>';
    }

    // Format selected patient
    function formatPatientSelection(patient) {
        if (!patient.patient) {
            return patient.text || '{{ __("messages.search_and_select_patient") }}';
        }
        
        return patient.patient.name + ' - ' + patient.patient.phone;
    }

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// Fix for modal and Select2 focus issues
$.fn.modal.Constructor.prototype._enforceFocus = function() {
    $(document)
        .off('focusin.bs.modal')
        .on('focusin.bs.modal', $.proxy(function(e) {
            if (this.$element[0] !== e.target && 
                !this.$element.has(e.target).length &&
                !$(e.target).closest('.select2-dropdown').length) {
                this.$element.trigger('focus');
            }
        }, this));
};
</script>
@endpush

