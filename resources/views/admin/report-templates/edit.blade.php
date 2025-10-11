@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.edit_report_template') }}: {{ $reportTemplate->title_en }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('report-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('report-templates.update', $reportTemplate) }}" method="POST" id="templateForm">
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

                        <!-- Template Basic Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title_en">{{ __('messages.title_en') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title_en" 
                                           id="title_en" 
                                           class="form-control @error('title_en') is-invalid @enderror" 
                                           value="{{ old('title_en', $reportTemplate->title_en) }}" 
                                           placeholder="{{ __('messages.enter_title_en') }}" 
                                           required>
                                    @error('title_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title_ar">{{ __('messages.title_ar') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title_ar" 
                                           id="title_ar" 
                                           class="form-control @error('title_ar') is-invalid @enderror" 
                                           value="{{ old('title_ar', $reportTemplate->title_ar) }}" 
                                           placeholder="{{ __('messages.enter_title_ar') }}" 
                                           required>
                                    @error('title_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="created_for">{{ __('messages.created_for') }} <span class="text-danger">*</span></label>
                                    <select name="created_for" 
                                            id="created_for" 
                                            class="form-control @error('created_for') is-invalid @enderror" 
                                            required>
                                        <option value="">{{ __('messages.select_user_type') }}</option>
                                        <option value="doctor" {{ old('created_for', $reportTemplate->created_for) == 'doctor' ? 'selected' : '' }}>{{ __('messages.doctor') }}</option>
                                        <option value="nurse" {{ old('created_for', $reportTemplate->created_for) == 'nurse' ? 'selected' : '' }}>{{ __('messages.nurse') }}</option>
                                    </select>
                                    @error('created_for')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="report_type">{{ __('messages.report_type') }} <span class="text-danger">*</span></label>
                                    <select name="report_type" 
                                            id="report_type" 
                                            class="form-control @error('report_type') is-invalid @enderror" 
                                            onchange="handleReportTypeChange(this)"
                                            required>
                                        <option value="">{{ __('messages.select_report_type') }}</option>
                                        <option value="initial_setup" {{ old('report_type', $reportTemplate->report_type ?? '') == 'initial_setup' ? 'selected' : '' }}>{{ __('messages.initial_setup') }}</option>
                                        <option value="recurring" {{ old('report_type', $reportTemplate->report_type ?? '') == 'recurring' ? 'selected' : '' }}>{{ __('messages.recurring') }}</option>
                                    </select>
                                    @error('report_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Frequency field - only shown when report_type is 'recurring' -->
                        <div class="row">
                            <div class="col-md-6" id="frequency-container" style="{{ old('report_type', $reportTemplate->report_type ?? '') == 'recurring' ? 'display: block;' : 'display: none;' }}">
                                <div class="form-group">
                                    <label for="frequency">{{ __('messages.frequency') }} <span class="text-danger">*</span></label>
                                    <select name="frequency" 
                                            id="frequency" 
                                            class="form-control @error('frequency') is-invalid @enderror"
                                            {{ old('report_type', $reportTemplate->report_type ?? '') == 'recurring' ? 'required' : '' }}>
                                        <option value="">{{ __('messages.select_frequency') }}</option>
                                        <option value="daily" {{ old('frequency', $reportTemplate->frequency ?? '') == 'daily' ? 'selected' : '' }}>{{ __('messages.daily') }}</option>
                                        <option value="weekly" {{ old('frequency', $reportTemplate->frequency ?? '') == 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                                        <option value="monthly" {{ old('frequency', $reportTemplate->frequency ?? '') == 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
                                    </select>
                                    @error('frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> {{ __('messages.frequency_help') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Sections Container -->
                        <div id="sections-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>{{ __('messages.template_sections') }}</h4>
                                <button type="button" class="btn btn-success" onclick="addSection()">
                                    <i class="fas fa-plus"></i> {{ __('messages.add_section') }}
                                </button>
                            </div>

                            <div id="sections-list">
                                <!-- Existing sections will be populated here -->
                                @foreach($reportTemplate->sections as $sectionIndex => $section)
                                    <div class="section-item border rounded p-3 mb-3" data-section-index="{{ $sectionIndex }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="section-title">{{ __('messages.section') }} <span class="section-number">{{ $sectionIndex + 1 }}</span></h5>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeSection(this)">
                                                <i class="fas fa-trash"></i> {{ __('messages.remove_section') }}
                                            </button>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('messages.section_title_en') }} <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           name="sections[{{ $sectionIndex }}][title_en]" 
                                                           class="form-control section-title-en" 
                                                           value="{{ old('sections.' . $sectionIndex . '.title_en', $section->title_en) }}"
                                                           placeholder="{{ __('messages.enter_section_title_en') }}" 
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('messages.section_title_ar') }} <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           name="sections[{{ $sectionIndex }}][title_ar]" 
                                                           class="form-control section-title-ar" 
                                                           value="{{ old('sections.' . $sectionIndex . '.title_ar', $section->title_ar) }}"
                                                           placeholder="{{ __('messages.enter_section_title_ar') }}" 
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="fields-container">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6>{{ __('messages.section_fields') }}</h6>
                                                <button type="button" class="btn btn-sm btn-info" onclick="addField(this)">
                                                    <i class="fas fa-plus"></i> {{ __('messages.add_field') }}
                                                </button>
                                            </div>
                                            <div class="fields-list">
                                                @foreach($section->fields as $fieldIndex => $field)
                                                    <div class="field-item border-left border-info pl-3 mb-3" data-field-index="{{ $fieldIndex }}">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="field-title">{{ __('messages.field') }} <span class="field-number">{{ $fieldIndex + 1 }}</span></h6>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeField(this)">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>{{ __('messages.field_label_en') }} <span class="text-danger">*</span></label>
                                                                    <input type="text" 
                                                                           name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][label_en]" 
                                                                           class="form-control" 
                                                                           value="{{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.label_en', $field->label_en) }}"
                                                                           placeholder="{{ __('messages.enter_field_label_en') }}" 
                                                                           required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>{{ __('messages.field_label_ar') }} <span class="text-danger">*</span></label>
                                                                    <input type="text" 
                                                                           name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][label_ar]" 
                                                                           class="form-control" 
                                                                           value="{{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.label_ar', $field->label_ar) }}"
                                                                           placeholder="{{ __('messages.enter_field_label_ar') }}" 
                                                                           required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>{{ __('messages.input_type') }} <span class="text-danger">*</span></label>
                                                                    <select name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][input_type]" 
                                                                            class="form-control field-input-type" 
                                                                            onchange="handleInputTypeChange(this)"
                                                                            required>
                                                                        <option value="">{{ __('messages.select_input_type') }}</option>
                                                                        <option value="text" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'text' ? 'selected' : '' }}>{{ __('messages.input_type_text') }}</option>
                                                                        <option value="textarea" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'textarea' ? 'selected' : '' }}>{{ __('messages.input_type_textarea') }}</option>
                                                                        <option value="number" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'number' ? 'selected' : '' }}>{{ __('messages.input_type_number') }}</option>
                                                                        <option value="date" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'date' ? 'selected' : '' }}>{{ __('messages.input_type_date') }}</option>
                                                                        <option value="select" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'select' ? 'selected' : '' }}>{{ __('messages.input_type_select') }}</option>
                                                                        <option value="radio" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'radio' ? 'selected' : '' }}>{{ __('messages.input_type_radio') }}</option>
                                                                        <option value="checkbox" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'checkbox' ? 'selected' : '' }}>{{ __('messages.input_type_checkbox') }}</option>
                                                                        <option value="boolean" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'boolean' ? 'selected' : '' }}>{{ __('messages.input_type_boolean') }}</option>
                                                                        <option value="gender" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'gender' ? 'selected' : '' }}>{{ __('messages.input_type_gender') }}</option>
                                                                        <option value="photo" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'photo' ? 'selected' : '' }}>{{ __('messages.input_type_photo') }}</option>
                                                                        <option value="pdf" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'pdf' ? 'selected' : '' }}>{{ __('messages.input_type_pdf') }}</option>
                                                                        <option value="signuture" {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.input_type', $field->input_type) == 'signuture' ? 'selected' : '' }}>{{ __('messages.input_type_signuture') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input type="checkbox" 
                                                                           name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][required]" 
                                                                           class="form-check-input" 
                                                                           value="1"
                                                                           {{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.required', $field->required) ? 'checked' : '' }}>
                                                                    <label class="form-check-label">
                                                                        {{ __('messages.required_field') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Field Options Container -->
                                                        <div class="field-options-container mt-3" style="{{ in_array($field->input_type, ['select', 'radio', 'checkbox']) ? 'display: block;' : 'display: none;' }}">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <label class="font-weight-bold">{{ __('messages.field_options') }}</label>
                                                                <button type="button" class="btn btn-sm btn-success" onclick="addFieldOption(this)">
                                                                    <i class="fas fa-plus"></i> {{ __('messages.add_option') }}
                                                                </button>
                                                            </div>
                                                            <div class="options-list">
                                                                @foreach($field->options as $optionIndex => $option)
                                                                    <div class="option-item row mb-2" data-option-index="{{ $optionIndex }}">
                                                                        <div class="col-md-5">
                                                                            <input type="text" 
                                                                                   name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][options][{{ $optionIndex }}][value_en]" 
                                                                                   class="form-control" 
                                                                                   value="{{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.options.' . $optionIndex . '.value_en', $option->value_en) }}"
                                                                                   placeholder="{{ __('messages.option_value_en') }}" 
                                                                                   required>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <input type="text" 
                                                                                   name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][options][{{ $optionIndex }}][value_ar]" 
                                                                                   class="form-control" 
                                                                                   value="{{ old('sections.' . $sectionIndex . '.fields.' . $fieldIndex . '.options.' . $optionIndex . '.value_ar', $option->value_ar) }}"
                                                                                   placeholder="{{ __('messages.option_value_ar') }}" 
                                                                                   required>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFieldOption(this)">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('report-templates.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <div>
                                <a href="{{ route('report-templates.show', $reportTemplate) }}" class="btn btn-info me-2">
                                    <i class="fas fa-eye"></i> {{ __('messages.view_details') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('messages.update_template') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Section Template (for new sections) -->
<template id="section-template">
    <div class="section-item border rounded p-3 mb-3" data-section-index="">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title">{{ __('messages.section') }} <span class="section-number"></span></h5>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeSection(this)">
                <i class="fas fa-trash"></i> {{ __('messages.remove_section') }}
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('messages.section_title_en') }} <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="sections[INDEX][title_en]" 
                           class="form-control section-title-en" 
                           placeholder="{{ __('messages.enter_section_title_en') }}" 
                           required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('messages.section_title_ar') }} <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="sections[INDEX][title_ar]" 
                           class="form-control section-title-ar" 
                           placeholder="{{ __('messages.enter_section_title_ar') }}" 
                           required>
                </div>
            </div>
        </div>

        <div class="fields-container">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>{{ __('messages.section_fields') }}</h6>
                <button type="button" class="btn btn-sm btn-info" onclick="addField(this)">
                    <i class="fas fa-plus"></i> {{ __('messages.add_field') }}
                </button>
            </div>
            <div class="fields-list">
                <!-- Fields will be added here -->
            </div>
        </div>
    </div>
</template>

<!-- Field Template (for new fields) -->
<template id="field-template">
    <div class="field-item border-left border-info pl-3 mb-3" data-field-index="">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="field-title">{{ __('messages.field') }} <span class="field-number"></span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeField(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('messages.field_label_en') }} <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="sections[SECTION_INDEX][fields][FIELD_INDEX][label_en]" 
                           class="form-control" 
                           placeholder="{{ __('messages.enter_field_label_en') }}" 
                           required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('messages.field_label_ar') }} <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="sections[SECTION_INDEX][fields][FIELD_INDEX][label_ar]" 
                           class="form-control" 
                           placeholder="{{ __('messages.enter_field_label_ar') }}" 
                           required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('messages.input_type') }} <span class="text-danger">*</span></label>
                    <select name="sections[SECTION_INDEX][fields][FIELD_INDEX][input_type]" 
                            class="form-control field-input-type" 
                            onchange="handleInputTypeChange(this)"
                            required>
                        <option value="">{{ __('messages.select_input_type') }}</option>
                        <option value="text">{{ __('messages.input_type_text') }}</option>
                        <option value="textarea">{{ __('messages.input_type_textarea') }}</option>
                        <option value="number">{{ __('messages.input_type_number') }}</option>
                        <option value="date">{{ __('messages.input_type_date') }}</option>
                        <option value="select">{{ __('messages.input_type_select') }}</option>
                        <option value="radio">{{ __('messages.input_type_radio') }}</option>
                        <option value="checkbox">{{ __('messages.input_type_checkbox') }}</option>
                        <option value="boolean">{{ __('messages.input_type_boolean') }}</option>
                        <option value="gender">{{ __('messages.input_type_gender') }}</option>
                        <option value="photo">{{ __('messages.input_type_photo') }}</option>
                        <option value="pdf">{{ __('messages.input_type_pdf') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-check">
                    <input type="checkbox" 
                           name="sections[SECTION_INDEX][fields][FIELD_INDEX][required]" 
                           class="form-check-input" 
                           value="1">
                    <label class="form-check-label">
                        {{ __('messages.required_field') }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Field Options Container (for select, radio, checkbox) -->
        <div class="field-options-container mt-3" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="font-weight-bold">{{ __('messages.field_options') }}</label>
                <button type="button" class="btn btn-sm btn-success" onclick="addFieldOption(this)">
                    <i class="fas fa-plus"></i> {{ __('messages.add_option') }}
                </button>
            </div>
            <div class="options-list">
                <!-- Options will be added here -->
            </div>
        </div>
    </div>
</template>

<!-- Field Option Template (for new options) -->
<template id="option-template">
    <div class="option-item row mb-2" data-option-index="">
        <div class="col-md-5">
            <input type="text" 
                   name="sections[SECTION_INDEX][fields][FIELD_INDEX][options][OPTION_INDEX][value_en]" 
                   class="form-control" 
                   placeholder="{{ __('messages.option_value_en') }}" 
                   required>
        </div>
        <div class="col-md-5">
            <input type="text" 
                   name="sections[SECTION_INDEX][fields][FIELD_INDEX][options][OPTION_INDEX][value_ar]" 
                   class="form-control" 
                   placeholder="{{ __('messages.option_value_ar') }}" 
                   required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFieldOption(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let sectionIndex = {{ $reportTemplate->sections->count() }};

$(document).ready(function() {
    // Initialize existing sections
    updateSectionNumbers();
    
    // Set initial state for report type and frequency
    const reportTypeSelect = document.getElementById('report_type');
    if (reportTypeSelect.value) {
        handleReportTypeChange(reportTypeSelect);
    }
});

function addSection() {
    const template = document.getElementById('section-template').content.cloneNode(true);
    const sectionDiv = template.querySelector('.section-item');
    
    // Update section index
    sectionDiv.setAttribute('data-section-index', sectionIndex);
    
    // Update section number
    template.querySelector('.section-number').textContent = sectionIndex + 1;
    
    // Update input names
    const inputs = template.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.name) {
            input.name = input.name.replace('INDEX', sectionIndex);
        }
    });
    
    document.getElementById('sections-list').appendChild(template);
    
    // Add initial field to this section
    addField(sectionDiv.querySelector('.btn-info'));
    
    sectionIndex++;
    updateSectionNumbers();
}

function removeSection(button) {
    if (document.querySelectorAll('.section-item').length <= 1) {
        alert('{{ __("messages.cannot_remove_last_section") }}');
        return;
    }
    
    button.closest('.section-item').remove();
    updateSectionNumbers();
    reindexSections();
}

function addField(button) {
    const section = button.closest('.section-item');
    const sectionIdx = section.getAttribute('data-section-index');
    const fieldsList = section.querySelector('.fields-list');
    const currentFields = fieldsList.querySelectorAll('.field-item');
    const fieldIdx = currentFields.length;
    
    const template = document.getElementById('field-template').content.cloneNode(true);
    const fieldDiv = template.querySelector('.field-item');
    
    // Update field index
    fieldDiv.setAttribute('data-field-index', fieldIdx);
    
    // Update field number
    template.querySelector('.field-number').textContent = fieldIdx + 1;
    
    // Update input names
    const inputs = template.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.name) {
            input.name = input.name.replace('SECTION_INDEX', sectionIdx).replace('FIELD_INDEX', fieldIdx);
        }
    });
    
    fieldsList.appendChild(template);
    updateFieldNumbers(section);
}

function removeField(button) {
    const section = button.closest('.section-item');
    const fieldsList = section.querySelector('.fields-list');
    
    if (fieldsList.querySelectorAll('.field-item').length <= 1) {
        alert('{{ __("messages.cannot_remove_last_field") }}');
        return;
    }
    
    button.closest('.field-item').remove();
    updateFieldNumbers(section);
    reindexFields(section);
}

function handleInputTypeChange(select) {
    const field = select.closest('.field-item');
    const optionsContainer = field.querySelector('.field-options-container');
    const needsOptions = ['select', 'radio', 'checkbox'].includes(select.value);
    
    if (needsOptions) {
        optionsContainer.style.display = 'block';
        // Add initial option if none exist
        if (optionsContainer.querySelector('.options-list').children.length === 0) {
            addFieldOption(optionsContainer.querySelector('.btn-success'));
        }
    } else {
        optionsContainer.style.display = 'none';
        // Clear existing options
        optionsContainer.querySelector('.options-list').innerHTML = '';
    }
}

function addFieldOption(button) {
    const field = button.closest('.field-item');
    const sectionIdx = field.closest('.section-item').getAttribute('data-section-index');
    const fieldIdx = field.getAttribute('data-field-index');
    const optionsList = button.closest('.field-options-container').querySelector('.options-list');
    const currentOptions = optionsList.querySelectorAll('.option-item');
    const optionIdx = currentOptions.length;
    
    const template = document.getElementById('option-template').content.cloneNode(true);
    const optionDiv = template.querySelector('.option-item');
    
    // Update option index
    optionDiv.setAttribute('data-option-index', optionIdx);
    
    // Update input names
    const inputs = template.querySelectorAll('input');
    inputs.forEach(input => {
        input.name = input.name.replace('SECTION_INDEX', sectionIdx)
                              .replace('FIELD_INDEX', fieldIdx)
                              .replace('OPTION_INDEX', optionIdx);
    });
    
    optionsList.appendChild(template);
}

function removeFieldOption(button) {
    const optionsList = button.closest('.options-list');
    
    if (optionsList.querySelectorAll('.option-item').length <= 1) {
        alert('{{ __("messages.cannot_remove_last_option") }}');
        return;
    }
    
    button.closest('.option-item').remove();
    reindexOptions(optionsList);
}

function updateSectionNumbers() {
    document.querySelectorAll('.section-item').forEach((section, index) => {
        section.querySelector('.section-number').textContent = index + 1;
    });
}

function updateFieldNumbers(section) {
    section.querySelectorAll('.field-item').forEach((field, index) => {
        field.querySelector('.field-number').textContent = index + 1;
    });
}

function reindexSections() {
    document.querySelectorAll('.section-item').forEach((section, sectionIdx) => {
        section.setAttribute('data-section-index', sectionIdx);
        
        // Update all inputs in this section
        section.querySelectorAll('input, select').forEach(input => {
            if (input.name && input.name.includes('sections[')) {
                input.name = input.name.replace(/sections\[\d+\]/, `sections[${sectionIdx}]`);
            }
        });
        
        // Reindex fields in this section
        reindexFields(section);
    });
}

function reindexFields(section) {
    const sectionIdx = section.getAttribute('data-section-index');
    section.querySelectorAll('.field-item').forEach((field, fieldIdx) => {
        field.setAttribute('data-field-index', fieldIdx);
        
        // Update all inputs in this field
        field.querySelectorAll('input, select').forEach(input => {
            if (input.name && input.name.includes('[fields][')) {
                input.name = input.name.replace(/\[fields\]\[\d+\]/, `[fields][${fieldIdx}]`);
            }
        });
        
        // Reindex options in this field
        const optionsList = field.querySelector('.options-list');
        if (optionsList) {
            reindexOptions(optionsList);
        }
    });
}

function reindexOptions(optionsList) {
    optionsList.querySelectorAll('.option-item').forEach((option, optionIdx) => {
        option.setAttribute('data-option-index', optionIdx);
        
        // Update all inputs in this option
        option.querySelectorAll('input').forEach(input => {
            if (input.name && input.name.includes('[options][')) {
                input.name = input.name.replace(/\[options\]\[\d+\]/, `[options][${optionIdx}]`);
            }
        });
    });
}

// Handle report type changes
function handleReportTypeChange(select) {
    const frequencyContainer = document.getElementById('frequency-container');
    const frequencySelect = document.getElementById('frequency');
    
    if (select.value === 'recurring') {
        frequencyContainer.style.display = 'block';
        frequencySelect.setAttribute('required', 'required');
        
        // Auto-select frequency based on created_for
        const createdFor = document.getElementById('created_for').value;
        if (createdFor === 'nurse' && !frequencySelect.value) {
            frequencySelect.value = 'daily';
        } else if (createdFor === 'doctor' && !frequencySelect.value) {
            frequencySelect.value = 'monthly';
        }
    } else {
        frequencyContainer.style.display = 'none';
        frequencySelect.removeAttribute('required');
        if (select.value === 'initial_setup') {
            frequencySelect.value = '';
        }
    }
}

// Auto-suggest frequency when created_for changes
document.getElementById('created_for').addEventListener('change', function() {
    const reportType = document.getElementById('report_type').value;
    const frequencySelect = document.getElementById('frequency');
    
    if (reportType === 'recurring') {
        if (this.value === 'nurse') {
            frequencySelect.value = 'daily';
        } else if (this.value === 'doctor') {
            frequencySelect.value = 'monthly';
        }
    }
});

// Form validation
document.getElementById('templateForm').addEventListener('submit', function(e) {
    const reportType = document.getElementById('report_type').value;
    const frequency = document.getElementById('frequency').value;
    
    if (reportType === 'recurring' && !frequency) {
        e.preventDefault();
        alert('{{ __("messages.frequency_required_for_recurring") }}');
        document.getElementById('frequency').focus();
        return false;
    }
});
</script>
@endpush