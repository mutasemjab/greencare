@extends('layouts.admin')
@section('css')
<style>
/* Select2 styling for Bootstrap 4 */
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

.select2-container--default .select2-selection__choice__remove {
    color: #fff !important;
    margin-right: 5px !important;
}

.select2-container--default .select2-selection__choice__remove:hover {
    color: #fff !important;
}

.select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    padding: 8px 12px !important;
}

.select2-results__option {
    padding: 8px 12px !important;
}

.select2-results__option--highlighted {
    background-color: #007bff !important;
    color: white !important;
}

.form-check-label {
    cursor: pointer;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.border-primary {
    border-color: #007bff !important;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.add_family') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('families.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('families.store') }}" method="POST">
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
                                    <label for="name">{{ __('messages.family_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.enter_family_name') }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Selection Method Toggle -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label>{{ __('messages.family_members') }}</label>
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleSelectionMethod()">
                                            <i class="fas fa-exchange-alt"></i> <span id="toggle-text">{{ __('messages.switch_to_traditional') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Select2 Search Method (Default) -->
                        <div class="row" id="search-method">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="user_ids[]" 
                                            id="patient_select" 
                                            class="form-control patient-select-multiple" 
                                            multiple
                                            style="width: 100%;">
                                        <!-- Options will be loaded via AJAX -->
                                    </select>
                                    <small class="form-text text-muted">{{ __('messages.search_patients_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Patients Preview -->
                        <div class="row" id="selected-patients-preview" style="display: none;">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('messages.selected_patients') }}</label>
                                    <div id="selected-patients-container" class="row">
                                        <!-- Selected patients will be displayed here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Traditional Checkbox Method (Hidden by default) -->
                        <div class="row" id="traditional-method" style="display: none;">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="row">
                                        @forelse($users->where('user_type', 'patient')->where('activate', 1) as $user)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                   name="user_ids_traditional[]" 
                                                                   value="{{ $user->id }}" 
                                                                   id="user{{ $user->id }}" 
                                                                   class="form-check-input"
                                                                   {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}>
                                                            <label for="user{{ $user->id }}" class="form-check-label w-100">
                                                                <div class="d-flex align-items-center">
                                                                    @if($user->photo)
                                                                        <img src="{{ asset('storage/' . $user->photo) }}" 
                                                                             class="rounded-circle mr-2" 
                                                                             width="40" height="40"
                                                                             alt="{{ $user->name }}">
                                                                    @else
                                                                        <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center bg-primary text-white" 
                                                                             style="width: 40px; height: 40px;">
                                                                            {{ substr($user->name, 0, 1) }}
                                                                        </div>
                                                                    @endif
                                                                    <div>
                                                                        <div class="font-weight-bold">{{ $user->name }}</div>
                                                                        <small class="text-muted">
                                                                            <span class="badge badge-secondary">{{ $user->user_type_text }}</span>
                                                                            <span class="badge badge-info">{{ $user->gender_text }}</span>
                                                                        </small>
                                                                        <div class="text-muted small">{{ $user->phone }}</div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    {{ __('messages.no_patients_available') }}
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                @if($users->where('user_type', 'patient')->where('activate', 1)->count() > 0)
                                    <div class="form-group">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAll()">
                                            {{ __('messages.select_all') }}
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAll()">
                                            {{ __('messages.deselect_all') }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('families.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('messages.create_family') }}
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
    let isTraditionalMode = false;
    
    // Initialize Select2 on page load
    initializePatientSelect2();

    function initializePatientSelect2() {
        // Destroy existing instance if it exists
        if ($('.patient-select-multiple').hasClass('select2-hidden-accessible')) {
            $('.patient-select-multiple').select2('destroy');
        }

        // Initialize Select2 for multiple patient selection
        $('.patient-select-multiple').select2({
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
                    params.page = params.page || 1;
                    
                    return {
                        results: data.data.map(function(patient) {
                            return {
                                id: patient.id,
                                text: patient.name + ' - ' + patient.phone + ' (' + patient.gender_text + ')',
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

        // Handle selection changes
        $('.patient-select-multiple').on('change', function() {
            updateSelectedPatientsPreview();
        });
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
            '<img src="' + patient.patient.photo_url + '" class="rounded-circle" width="30" height="30" style="object-fit: cover;">' :
            '<div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 30px; height: 30px; font-size: 0.8rem;">' +
                patient.patient.name.charAt(0).toUpperCase() +
            '</div>';

        return '<div class="d-flex align-items-center py-1">' +
                    '<div class="mr-2">' + photoHtml + '</div>' +
                    '<div class="flex-grow-1">' +
                        '<div class="font-weight-bold">' + patient.patient.name + '</div>' +
                        '<small class="text-muted">' + patient.patient.phone + ' • ' + patient.patient.gender_text + '</small>' +
                    '</div>' +
                '</div>';
    }

    // Format selected patient
    function formatPatientSelection(patient) {
        if (!patient.patient) {
            return patient.text || patient.id;
        }
        
        return patient.patient.name + ' - ' + patient.patient.phone;
    }

    // Update selected patients preview
    function updateSelectedPatientsPreview() {
        const selectedData = $('.patient-select-multiple').select2('data');
        const container = $('#selected-patients-container');
        const preview = $('#selected-patients-preview');
        
        container.empty();
        
        if (selectedData.length > 0) {
            preview.show();
            
            selectedData.forEach(function(item) {
                if (item.patient) {
                    const patient = item.patient;
                    const photoHtml = patient.photo_url ? 
                        '<img src="' + patient.photo_url + '" class="rounded-circle mr-2" width="40" height="40" alt="' + patient.name + '">' :
                        '<div class="rounded-circle mr-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 40px; height: 40px;">' +
                            patient.name.charAt(0).toUpperCase() +
                        '</div>';
                    
                    const cardHtml = 
                        '<div class="col-md-6 col-lg-4 mb-2">' +
                            '<div class="card border-primary">' +
                                '<div class="card-body p-2">' +
                                    '<div class="d-flex align-items-center">' +
                                        photoHtml +
                                        '<div class="flex-grow-1">' +
                                            '<div class="font-weight-bold small">' + patient.name + '</div>' +
                                            '<div class="text-muted" style="font-size: 0.75rem;">' + patient.phone + '</div>' +
                                            '<span class="badge badge-info" style="font-size: 0.6rem;">' + patient.gender_text + '</span>' +
                                        '</div>' +
                                        '<button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="removePatient(' + patient.id + ')">' +
                                            '<i class="fas fa-times"></i>' +
                                        '</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    
                    container.append(cardHtml);
                }
            });
        } else {
            preview.hide();
        }
    }

    // Remove patient from selection
    window.removePatient = function(patientId) {
        const currentValues = $('.patient-select-multiple').val() || [];
        const newValues = currentValues.filter(id => id != patientId);
        $('.patient-select-multiple').val(newValues).trigger('change');
    };

    // Toggle between selection methods
    window.toggleSelectionMethod = function() {
        const searchMethod = $('#search-method');
        const traditionalMethod = $('#traditional-method');
        const toggleText = $('#toggle-text');
        const preview = $('#selected-patients-preview');
        
        if (isTraditionalMode) {
            // Switch to search method
            searchMethod.show();
            traditionalMethod.hide();
            preview.show();
            toggleText.text('{{ __("messages.switch_to_traditional") }}');
            
            // Clear traditional checkboxes and update names
            $('input[name="user_ids_traditional[]"]').prop('checked', false);
            $('input[name="user_ids_traditional[]"]').attr('name', 'user_ids[]');
            
            // Reinitialize Select2
            initializePatientSelect2();
            
            isTraditionalMode = false;
        } else {
            // Switch to traditional method
            searchMethod.hide();
            traditionalMethod.show();
            preview.hide();
            toggleText.text('{{ __("messages.switch_to_search") }}');
            
            // Clear select2 and update names
            $('.patient-select-multiple').val(null).trigger('change');
            $('input[name="user_ids[]"]').attr('name', 'user_ids_traditional[]');
            
            isTraditionalMode = true;
        }
    };

    // Traditional method functions
    window.selectAll = function() {
        if (isTraditionalMode) {
            document.querySelectorAll('input[name="user_ids_traditional[]"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        }
    };

    window.deselectAll = function() {
        if (isTraditionalMode) {
            document.querySelectorAll('input[name="user_ids_traditional[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    };

    // Form submission handler
    $('form').on('submit', function() {
        if (isTraditionalMode) {
            // Rename traditional inputs back to user_ids[]
            $('input[name="user_ids_traditional[]"]').attr('name', 'user_ids[]');
        }
    });
});
</script>
@endpush

