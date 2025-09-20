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
                    <h3 class="card-title">{{ __('messages.create_room') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('rooms.store') }}" method="POST">
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

                        <!-- Room Basic Information -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">{{ __('messages.room_title') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title') }}" 
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
                                            <option value="{{ $family->id }}" {{ old('family_id') == $family->id ? 'selected' : '' }}>
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
                                              placeholder="{{ __('messages.enter_room_description') }}">{{ old('description') }}</textarea>
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
                                            </select>
                                            <small class="form-text text-muted">{{ __('messages.search_nurses_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                      

                        <hr>

                        <!-- Reports Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-file-medical-alt mr-2"></i>
                                            {{ __('messages.report_templates') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="report_templates[]" 
                                                    id="report_templates_select" 
                                                    class="form-control report-templates-select" 
                                                    multiple
                                                    style="width: 100%;">
                                            </select>
                                            <small class="form-text text-muted">{{ __('messages.search_report_templates_help') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medications Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-pills mr-2"></i>
                                            {{ __('messages.medications') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <p class="mb-0 text-muted">{{ __('messages.medications_help') }}</p>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addMedication()">
                                                <i class="fas fa-plus"></i> {{ __('messages.add_medication') }}
                                            </button>
                                        </div>
                                        <div id="medications-container">
                                            <!-- Medications will be added here dynamically -->
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
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('messages.create_room') }}
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
    let isFamilyIndividualMode = false;
    let medicationCounter = 0;
    let selectedPatients = [];

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

    // Track selected patients for medications
    $('.patients-select').on('change', function() {
        selectedPatients = [];
        $(this).select2('data').forEach(function(item) {
            if (item.patient) {
                selectedPatients.push({
                    id: item.patient.id,
                    name: item.patient.name,
                    phone: item.patient.phone
                });
            }
        });
        updateMedicationPatientOptions();
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
        templateSelection: formatUserSelection,
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Initialize Select2 for family search
    $('.family-members-select').select2({
        placeholder: '{{ __("messages.search_and_select_families") }}',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("api.families.search") }}',
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
                    results: data.data.map(function(family) {
                        return {
                            id: family.id,
                            text: family.name + ' (' + family.members_count + ' members)',
                            family: family
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page
                    }
                };
            }
        },
        minimumInputLength: 2,
        templateResult: formatFamily,
        templateSelection: formatFamilySelection,
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Initialize Select2 for report templates
    $('.report-templates-select').select2({
        placeholder: '{{ __("messages.search_and_select_report_templates") }}',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("api.report-templates.search") }}',
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
                    results: data.data.map(function(template) {
                        return {
                            id: template.id,
                            text: template.name,
                            template: template
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page
                    }
                };
            }
        },
        minimumInputLength: 2,
        templateResult: formatReportTemplate,
        templateSelection: formatReportTemplateSelection,
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Handle family selection to auto-populate individual members
    $('.family-members-select').on('select2:select', function (e) {
        var selectedFamily = e.params.data.family;
        
        if (selectedFamily && selectedFamily.members) {
            // Create hidden inputs for each family member
            var form = $(this).closest('form');
            
            // Remove existing family member inputs for this family
            form.find('input[name="family_member_ids[]"]').remove();
            
            // Add hidden inputs for each family member
            selectedFamily.members.forEach(function(member) {
                form.append('<input type="hidden" name="family_member_ids[]" value="' + member.id + '">');
            });
            
            // Show success message
            showFamilyMembersPreview();
        }
    });

    // Handle family deselection
    $('.family-members-select').on('select2:unselect', function (e) {
        var form = $(this).closest('form');
        form.find('input[name="family_member_ids[]"]').remove();
        hideFamilyMembersPreview();
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

    // Format family display in dropdown
    function formatFamily(family) {
        if (family.loading) {
            return '{{ __("messages.searching") }}...';
        }

        if (!family.family) {
            return family.text;
        }

        var familyData = family.family;
        var membersHtml = '';
        
        if (familyData.members && familyData.members.length > 0) {
            var displayMembers = familyData.members.slice(0, 3); // Show first 3 members
            var remainingCount = familyData.members.length - 3;
            
            membersHtml = '<div class="mt-1">';
            displayMembers.forEach(function(member, index) {
                if (index > 0) membersHtml += ', ';
                membersHtml += '<span class="badge badge-light">' + member.name + '</span>';
            });
            
            if (remainingCount > 0) {
                membersHtml += ' <span class="text-muted">+' + remainingCount + ' more</span>';
            }
            membersHtml += '</div>';
        }

        return '<div class="py-2">' +
                    '<div class="d-flex align-items-center">' +
                        '<div class="mr-2">' +
                            '<div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white" style="width: 35px; height: 35px; font-size: 0.9rem;">' +
                                '<i class="fas fa-users"></i>' +
                            '</div>' +
                        '</div>' +
                        '<div class="flex-grow-1">' +
                            '<div class="font-weight-bold">' + familyData.name + '</div>' +
                            '<small class="text-muted">' + familyData.members_count + ' member(s)</small>' +
                            membersHtml +
                        '</div>' +
                    '</div>' +
                '</div>';
    }

    // Format selected family
    function formatFamilySelection(family) {
        if (!family.family) {
            return family.text || family.id;
        }
        
        return family.family.name + ' (' + family.family.members_count + ' members)';
    }

    // Format report template display in dropdown
    function formatReportTemplate(template) {
        if (template.loading) {
            return '{{ __("messages.searching") }}...';
        }

        if (!template.template) {
            return template.text;
        }

        var templateData = template.template;
        return '<div class="py-2">' +
                    '<div class="d-flex align-items-center">' +
                        '<div class="mr-2">' +
                            '<div class="rounded-circle d-flex align-items-center justify-content-center bg-danger text-white" style="width: 35px; height: 35px; font-size: 0.9rem;">' +
                                '<i class="fas fa-file-medical-alt"></i>' +
                            '</div>' +
                        '</div>' +
                        '<div class="flex-grow-1">' +
                            '<div class="font-weight-bold">' + templateData.name + '</div>' +
                            '<small class="text-muted">' + (templateData.description || 'No description') + '</small>' +
                            '<div class="mt-1">' +
                                '<span class="badge badge-primary">' + templateData.sections_count + ' sections</span> ' +
                                '<span class="badge badge-secondary">' + templateData.fields_count + ' fields</span>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
    }

    // Format selected report template
    function formatReportTemplateSelection(template) {
        if (!template.template) {
            return template.text || template.id;
        }
        
        return template.template.name;
    }

    // Show family members preview
    function showFamilyMembersPreview() {
        var selectedFamilies = $('.family-members-select').select2('data');
        var form = $('.family-members-select').closest('form');
        
        if (selectedFamilies.length > 0) {
            var previewHtml = '<div id="family-members-preview" class="mt-3">' +
                                '<div class="alert alert-info family-members-preview">' +
                                    '<h6><i class="fas fa-info-circle"></i> Selected Family Members:</h6>' +
                                    '<div class="row">';
            
            selectedFamilies.forEach(function(familyItem) {
                if (familyItem.family && familyItem.family.members) {
                    familyItem.family.members.forEach(function(member) {
                        var photoHtml = member.photo_url ? 
                            '<img src="' + member.photo_url + '" class="rounded-circle mr-2" width="30" height="30" alt="' + member.name + '">' :
                            '<div class="rounded-circle mr-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 30px; height: 30px; font-size: 0.7rem;">' +
                                member.name.charAt(0).toUpperCase() +
                            '</div>';
                        
                        previewHtml += '<div class="col-md-6 mb-2">' +
                                        '<div class="d-flex align-items-center">' +
                                            photoHtml +
                                            '<div>' +
                                                '<small class="font-weight-bold">' + member.name + '</small><br>' +
                                                '<small class="text-muted">' + member.phone + '</small>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>';
                    });
                }
            });
            
            previewHtml += '</div></div></div>';
            
            // Remove existing preview and add new one
            $('#family-members-preview').remove();
            $('.family-members-select').closest('.card-body').append(previewHtml);
        }
    }

    // Hide family members preview
    function hideFamilyMembersPreview() {
        $('#family-members-preview').remove();
    }

    // Toggle between family search and individual search
    window.toggleFamilySelectionMethod = function() {
        const familySearchMethod = $('#family-search-method');
        const individualSearchMethod = $('#individual-search-method');
        const toggleText = $('#family-toggle-text');
        
        if (isFamilyIndividualMode) {
            // Switch to family search method
            familySearchMethod.show();
            individualSearchMethod.hide();
            toggleText.text('{{ __("messages.switch_to_individual") }}');
            
            // Clear individual selections
            if ($('.individual-family-members-select').hasClass('select2-hidden-accessible')) {
                $('.individual-family-members-select').val(null).trigger('change');
            }
            
            isFamilyIndividualMode = false;
        } else {
            // Switch to individual search method
            familySearchMethod.hide();
            individualSearchMethod.show();
            toggleText.text('{{ __("messages.switch_to_family") }}');
            
            // Clear family selections and hidden inputs
            if ($('.family-members-select').hasClass('select2-hidden-accessible')) {
                $('.family-members-select').val(null).trigger('change');
            }
            $('input[name="family_member_ids[]"]').remove();
            hideFamilyMembersPreview();
            
            // Initialize individual search if not already done
            initializeIndividualFamilyMembersSelect();
            
            isFamilyIndividualMode = true;
        }
    };

    // Initialize individual family members search
    function initializeIndividualFamilyMembersSelect() {
        if ($('.individual-family-members-select').hasClass('select2-hidden-accessible')) {
            return; // Already initialized
        }
        
        $('.individual-family-members-select').select2({
            placeholder: '{{ __("messages.search_and_select_individual_family_members") }}',
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
            templateSelection: formatUserSelection,
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    }

    // Add medication function
    window.addMedication = function() {
        if (selectedPatients.length === 0) {
            alert('{{ __("messages.please_select_patients_first") }}');
            return;
        }

        medicationCounter++;
        var patientOptions = '<option value="">{{ __("messages.select_patient") }}</option>';
        selectedPatients.forEach(function(patient) {
            patientOptions += '<option value="' + patient.id + '">' + patient.name + ' - ' + patient.phone + '</option>';
        });

        var medicationHtml = 
            '<div class="medication-item" data-medication="' + medicationCounter + '">' +
                '<button type="button" class="btn btn-sm btn-outline-danger remove-medication" onclick="removeMedication(' + medicationCounter + ')">' +
                    '<i class="fas fa-times"></i>' +
                '</button>' +
                '<div class="row">' +
                    '<div class="col-md-4">' +
                        '<div class="form-group">' +
                            '<label>{{ __("messages.patient") }} <span class="text-danger">*</span></label>' +
                            '<select name="medications[' + medicationCounter + '][patient_id]" class="form-control" required>' +
                                patientOptions +
                            '</select>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<div class="form-group">' +
                            '<label>{{ __("messages.medication_name") }} <span class="text-danger">*</span></label>' +
                            '<input type="text" name="medications[' + medicationCounter + '][name]" class="form-control" placeholder="{{ __("messages.enter_medication_name") }}" required>' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-4">' +
                        '<div class="form-group">' +
                            '<label>{{ __("messages.dosage") }}</label>' +
                            '<input type="text" name="medications[' + medicationCounter + '][dosage]" class="form-control" placeholder="{{ __("messages.enter_dosage") }}">' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="row">' +
                    '<div class="col-md-4">' +
                        '<div class="form-group">' +
                            '<label>{{ __("messages.quantity") }}</label>' +
                            '<input type="number" name="medications[' + medicationCounter + '][quantity]" class="form-control" placeholder="{{ __("messages.enter_quantity") }}" min="1">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-8">' +
                        '<div class="form-group">' +
                            '<label>{{ __("messages.notes") }}</label>' +
                            '<textarea name="medications[' + medicationCounter + '][notes]" class="form-control" rows="2" placeholder="{{ __("messages.enter_medication_notes") }}"></textarea>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        $('#medications-container').append(medicationHtml);
    };

    // Remove medication function
    window.removeMedication = function(medicationId) {
        $('[data-medication="' + medicationId + '"]').remove();
    };

    // Update medication patient options when patients change
    function updateMedicationPatientOptions() {
        var patientOptions = '<option value="">{{ __("messages.select_patient") }}</option>';
        selectedPatients.forEach(function(patient) {
            patientOptions += '<option value="' + patient.id + '">' + patient.name + ' - ' + patient.phone + '</option>';
        });

        $('#medications-container select[name*="[patient_id]"]').each(function() {
            var currentValue = $(this).val();
            $(this).html(patientOptions);
            
            // Restore selection if still valid
            if (currentValue && selectedPatients.find(p => p.id == currentValue)) {
                $(this).val(currentValue);
            }
        });
    }
});
</script>
@endpush