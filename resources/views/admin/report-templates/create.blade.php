@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.create_report_template') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('report-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('report-templates.store') }}" method="POST" id="templateForm">
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

                        <!-- Template Basic Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title_en">{{ __('messages.title_en') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title_en" 
                                           id="title_en" 
                                           class="form-control @error('title_en') is-invalid @enderror" 
                                           value="{{ old('title_en') }}" 
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
                                           value="{{ old('title_ar') }}" 
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
                                        <option value="doctor" {{ old('created_for') == 'doctor' ? 'selected' : '' }}>{{ __('messages.doctor') }}</option>
                                        <option value="nurse" {{ old('created_for') == 'nurse' ? 'selected' : '' }}>{{ __('messages.nurse') }}</option>
                                    </select>
                                    @error('created_for')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                <!-- Sections will be added here dynamically -->
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('report-templates.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('messages.create_template') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Section Template -->
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

<!-- Field Template -->
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

<!-- Field Option Template -->
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
let sectionIndex = 0;

$(document).ready(function() {
    // Add initial section
    addSection();
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
</script>
@endpush

