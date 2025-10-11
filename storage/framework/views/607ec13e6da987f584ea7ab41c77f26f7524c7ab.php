<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?php echo e(__('messages.create_report_template')); ?></h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('report-templates.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> <?php echo e(__('messages.back')); ?>

                        </a>
                    </div>
                </div>

                <form action="<?php echo e(route('report-templates.store')); ?>" method="POST" id="templateForm">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Template Basic Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title_en"><?php echo e(__('messages.title_en')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title_en" 
                                           id="title_en" 
                                           class="form-control <?php $__errorArgs = ['title_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('title_en')); ?>" 
                                           placeholder="<?php echo e(__('messages.enter_title_en')); ?>" 
                                           required>
                                    <?php $__errorArgs = ['title_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title_ar"><?php echo e(__('messages.title_ar')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title_ar" 
                                           id="title_ar" 
                                           class="form-control <?php $__errorArgs = ['title_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('title_ar')); ?>" 
                                           placeholder="<?php echo e(__('messages.enter_title_ar')); ?>" 
                                           required>
                                    <?php $__errorArgs = ['title_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="created_for"><?php echo e(__('messages.created_for')); ?> <span class="text-danger">*</span></label>
                                    <select name="created_for" 
                                            id="created_for" 
                                            class="form-control <?php $__errorArgs = ['created_for'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            required>
                                        <option value=""><?php echo e(__('messages.select_user_type')); ?></option>
                                        <option value="doctor" <?php echo e(old('created_for') == 'doctor' ? 'selected' : ''); ?>><?php echo e(__('messages.doctor')); ?></option>
                                        <option value="nurse" <?php echo e(old('created_for') == 'nurse' ? 'selected' : ''); ?>><?php echo e(__('messages.nurse')); ?></option>
                                    </select>
                                    <?php $__errorArgs = ['created_for'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Add these fields after the created_for field in your form -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="report_type"><?php echo e(__('messages.report_type')); ?> <span class="text-danger">*</span></label>
                                <select name="report_type" 
                                        id="report_type" 
                                        class="form-control <?php $__errorArgs = ['report_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        onchange="handleReportTypeChange(this)"
                                        required>
                                    <option value=""><?php echo e(__('messages.select_report_type')); ?></option>
                                    <option value="initial_setup" <?php echo e(old('report_type') == 'initial_setup' ? 'selected' : ''); ?>><?php echo e(__('messages.initial_setup')); ?></option>
                                    <option value="recurring" <?php echo e(old('report_type') == 'recurring' ? 'selected' : ''); ?>><?php echo e(__('messages.recurring')); ?></option>
                                </select>
                                <?php $__errorArgs = ['report_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Frequency field - only shown when report_type is 'recurring' -->
                        <div class="col-md-6" id="frequency-container" style="display: none;">
                            <div class="form-group">
                                <label for="frequency"><?php echo e(__('messages.frequency')); ?> <span class="text-danger">*</span></label>
                                <select name="frequency" 
                                        id="frequency" 
                                        class="form-control <?php $__errorArgs = ['frequency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value=""><?php echo e(__('messages.select_frequency')); ?></option>
                                    <option value="daily" <?php echo e(old('frequency') == 'daily' ? 'selected' : ''); ?>><?php echo e(__('messages.daily')); ?></option>
                                    <option value="weekly" <?php echo e(old('frequency') == 'weekly' ? 'selected' : ''); ?>><?php echo e(__('messages.weekly')); ?></option>
                                    <option value="monthly" <?php echo e(old('frequency') == 'monthly' ? 'selected' : ''); ?>><?php echo e(__('messages.monthly')); ?></option>
                                </select>
                                <?php $__errorArgs = ['frequency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> <?php echo e(__('messages.frequency_help')); ?>

                                </small>
                            </div>
                        </div>


                        <hr>

                        <!-- Sections Container -->
                        <div id="sections-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4><?php echo e(__('messages.template_sections')); ?></h4>
                                <button type="button" class="btn btn-success" onclick="addSection()">
                                    <i class="fas fa-plus"></i> <?php echo e(__('messages.add_section')); ?>

                                </button>
                            </div>

                            <div id="sections-list">
                                <!-- Sections will be added here dynamically -->
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('report-templates.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> <?php echo e(__('messages.cancel')); ?>

                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo e(__('messages.create_template')); ?>

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
            <h5 class="section-title"><?php echo e(__('messages.section')); ?> <span class="section-number"></span></h5>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeSection(this)">
                <i class="fas fa-trash"></i> <?php echo e(__('messages.remove_section')); ?>

            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo e(__('messages.section_title_en')); ?> <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="sections[INDEX][title_en]" 
                           class="form-control section-title-en" 
                           placeholder="<?php echo e(__('messages.enter_section_title_en')); ?>" 
                           required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo e(__('messages.section_title_ar')); ?> <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="sections[INDEX][title_ar]" 
                           class="form-control section-title-ar" 
                           placeholder="<?php echo e(__('messages.enter_section_title_ar')); ?>" 
                           required>
                </div>
            </div>
        </div>

        <div class="fields-container">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6><?php echo e(__('messages.section_fields')); ?></h6>
                <button type="button" class="btn btn-sm btn-info" onclick="addField(this)">
                    <i class="fas fa-plus"></i> <?php echo e(__('messages.add_field')); ?>

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
            <h6 class="field-title"><?php echo e(__('messages.field')); ?> <span class="field-number"></span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeField(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo e(__('messages.field_label_en')); ?> <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="sections[SECTION_INDEX][fields][FIELD_INDEX][label_en]" 
                           class="form-control" 
                           placeholder="<?php echo e(__('messages.enter_field_label_en')); ?>" 
                           required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo e(__('messages.field_label_ar')); ?> <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="sections[SECTION_INDEX][fields][FIELD_INDEX][label_ar]" 
                           class="form-control" 
                           placeholder="<?php echo e(__('messages.enter_field_label_ar')); ?>" 
                           required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo e(__('messages.input_type')); ?> <span class="text-danger">*</span></label>
                    <select name="sections[SECTION_INDEX][fields][FIELD_INDEX][input_type]" 
                            class="form-control field-input-type" 
                            onchange="handleInputTypeChange(this)"
                            required>
                        <option value=""><?php echo e(__('messages.select_input_type')); ?></option>
                        <option value="text"><?php echo e(__('messages.input_type_text')); ?></option>
                        <option value="textarea"><?php echo e(__('messages.input_type_textarea')); ?></option>
                        <option value="number"><?php echo e(__('messages.input_type_number')); ?></option>
                        <option value="date"><?php echo e(__('messages.input_type_date')); ?></option>
                        <option value="select"><?php echo e(__('messages.input_type_select')); ?></option>
                        <option value="radio"><?php echo e(__('messages.input_type_radio')); ?></option>
                        <option value="checkbox"><?php echo e(__('messages.input_type_checkbox')); ?></option>
                        <option value="boolean"><?php echo e(__('messages.input_type_boolean')); ?></option>
                        <option value="gender"><?php echo e(__('messages.input_type_gender')); ?></option>
                        <option value="photo"><?php echo e(__('messages.input_type_photo')); ?></option>
                        <option value="pdf"><?php echo e(__('messages.input_type_pdf')); ?></option>
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
                        <?php echo e(__('messages.required_field')); ?>

                    </label>
                </div>
            </div>
        </div>

        <!-- Field Options Container (for select, radio, checkbox) -->
        <div class="field-options-container mt-3" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="font-weight-bold"><?php echo e(__('messages.field_options')); ?></label>
                <button type="button" class="btn btn-sm btn-success" onclick="addFieldOption(this)">
                    <i class="fas fa-plus"></i> <?php echo e(__('messages.add_option')); ?>

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
                   placeholder="<?php echo e(__('messages.option_value_en')); ?>" 
                   required>
        </div>
        <div class="col-md-5">
            <input type="text" 
                   name="sections[SECTION_INDEX][fields][FIELD_INDEX][options][OPTION_INDEX][value_ar]" 
                   class="form-control" 
                   placeholder="<?php echo e(__('messages.option_value_ar')); ?>" 
                   required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFieldOption(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
        alert('<?php echo e(__("messages.cannot_remove_last_section")); ?>');
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
        alert('<?php echo e(__("messages.cannot_remove_last_field")); ?>');
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
        alert('<?php echo e(__("messages.cannot_remove_last_option")); ?>');
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


<script>
// Add this to your existing JavaScript section
function handleReportTypeChange(select) {
    const frequencyContainer = document.getElementById('frequency-container');
    const frequencySelect = document.getElementById('frequency');
    
    if (select.value === 'recurring') {
        frequencyContainer.style.display = 'block';
        frequencySelect.setAttribute('required', 'required');
        
        // Auto-select frequency based on created_for
        const createdFor = document.getElementById('created_for').value;
        if (createdFor === 'nurse') {
            frequencySelect.value = 'daily';
        } else if (createdFor === 'doctor') {
            frequencySelect.value = 'monthly';
        }
    } else {
        frequencyContainer.style.display = 'none';
        frequencySelect.removeAttribute('required');
        frequencySelect.value = '';
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

// Check on page load if report_type is already selected (for old input)
document.addEventListener('DOMContentLoaded', function() {
    const reportTypeSelect = document.getElementById('report_type');
    if (reportTypeSelect.value) {
        handleReportTypeChange(reportTypeSelect);
    }
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/report-templates/create.blade.php ENDPATH**/ ?>