<?php $__env->startSection('css'); ?>
<style>
.schedule-item {
    background-color: #f8f9fa;
    border-left: 4px solid #28a745 !important;
}

.schedule-title {
    color: #28a745;
    margin: 0;
}

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

.form-check-input {
    margin-top: 0.3rem;
}

template {
    display: none;
}
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?php echo e(__('messages.create_medication')); ?></h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('medications.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> <?php echo e(__('messages.back')); ?>

                        </a>
                    </div>
                </div>

                <form action="<?php echo e(route('medications.store')); ?>" method="POST">
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

                        <!-- Medication Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id"><?php echo e(__('messages.patient')); ?> <span class="text-danger">*</span></label>
                                    <select name="patient_id" 
                                            id="patient_id" 
                                            class="form-control patient-select <?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            required
                                            style="width: 100%;">
                                        <?php if($selectedPatient): ?>
                                            <option value="<?php echo e($selectedPatient->id); ?>" selected>
                                                <?php echo e($selectedPatient->name); ?> - <?php echo e($selectedPatient->phone); ?>

                                            </option>
                                        <?php endif; ?>
                                    </select>
                                    <?php $__errorArgs = ['patient_id'];
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
                                    <label for="room_id"><?php echo e(__('messages.room')); ?></label>
                                    <select name="room_id" id="room_id" class="form-control">
                                        <option value=""><?php echo e(__('messages.select_room')); ?></option>
                                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($room->id); ?>" 
                                                    <?php echo e((old('room_id', $selectedRoom?->id) == $room->id) ? 'selected' : ''); ?>>
                                                <?php echo e($room->title); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name"><?php echo e(__('messages.medication_name')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('name')); ?>" 
                                           placeholder="<?php echo e(__('messages.enter_medication_name')); ?>" 
                                           required>
                                    <?php $__errorArgs = ['name'];
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dosage"><?php echo e(__('messages.dosage')); ?></label>
                                    <input type="text" 
                                           name="dosage" 
                                           id="dosage" 
                                           class="form-control <?php $__errorArgs = ['dosage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('dosage')); ?>" 
                                           placeholder="<?php echo e(__('messages.enter_dosage')); ?>">
                                    <?php $__errorArgs = ['dosage'];
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quantity"><?php echo e(__('messages.quantity')); ?></label>
                                    <input type="number" 
                                           name="quantity" 
                                           id="quantity" 
                                           class="form-control <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('quantity')); ?>" 
                                           placeholder="<?php echo e(__('messages.enter_quantity')); ?>"
                                           min="1">
                                    <?php $__errorArgs = ['quantity'];
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
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="notes"><?php echo e(__('messages.medication_notes')); ?></label>
                                    <textarea name="notes" 
                                              id="notes" 
                                              class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              rows="3" 
                                              placeholder="<?php echo e(__('messages.enter_medication_notes')); ?>"><?php echo e(old('notes')); ?></textarea>
                                    <?php $__errorArgs = ['notes'];
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="active" 
                                               id="active" 
                                               class="form-check-input" 
                                               value="1" 
                                               <?php echo e(old('active', true) ? 'checked' : ''); ?>>
                                        <label for="active" class="form-check-label">
                                            <?php echo e(__('messages.active_medication')); ?>

                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Medication Schedules -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5><?php echo e(__('messages.medication_schedules')); ?></h5>
                                    <button type="button" class="btn btn-success btn-sm" onclick="addSchedule()">
                                        <i class="fas fa-plus"></i> <?php echo e(__('messages.add_schedule')); ?>

                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="schedules-container">
                            <!-- Schedules will be added here dynamically -->
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('medications.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> <?php echo e(__('messages.cancel')); ?>

                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo e(__('messages.create_medication')); ?>

                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Template -->
<template id="schedule-template">
    <div class="schedule-item border rounded p-3 mb-3" data-schedule-index="">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="schedule-title"><?php echo e(__('messages.schedule')); ?> <span class="schedule-number"></span></h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeSchedule(this)">
                <i class="fas fa-trash"></i> <?php echo e(__('messages.remove_schedule')); ?>

            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo e(__('messages.schedule_time')); ?> <span class="text-danger">*</span></label>
                    <input type="time" 
                           name="schedules[INDEX][time]" 
                           class="form-control schedule-time" 
                           required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo e(__('messages.schedule_frequency')); ?> <span class="text-danger">*</span></label>
                    <select name="schedules[INDEX][frequency]" 
                            class="form-control schedule-frequency" 
                            required>
                        <option value=""><?php echo e(__('messages.select_frequency')); ?></option>
                        <option value="daily"><?php echo e(__('messages.frequency_daily')); ?></option>
                        <option value="weekly"><?php echo e(__('messages.frequency_weekly')); ?></option>
                        <option value="monthly"><?php echo e(__('messages.frequency_monthly')); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let scheduleIndex = 0;

$(document).ready(function() {
    // Initialize patient Select2
    $('.patient-select').select2({
        placeholder: '<?php echo e(__("messages.search_and_select_patient")); ?>',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '<?php echo e(route("api.patients.search")); ?>',
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
            }
        },
        minimumInputLength: 2,
        templateResult: formatPatient,
        templateSelection: formatPatientSelection
    });

    // Add initial schedule
    addSchedule();

    // Format patient display in dropdown
    function formatPatient(patient) {
        if (patient.loading) {
            return '<?php echo e(__("messages.searching")); ?>...';
        }

        if (!patient.patient) {
            return patient.text;
        }

        return '<div class="d-flex align-items-center">' +
                    '<div class="mr-2">' +
                        '<div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-white" style="width: 30px; height: 30px; font-size: 0.8rem;">' +
                            patient.patient.name.charAt(0).toUpperCase() +
                        '</div>' +
                    '</div>' +
                    '<div>' +
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
});

function addSchedule() {
    const template = document.getElementById('schedule-template').content.cloneNode(true);
    const scheduleDiv = template.querySelector('.schedule-item');
    
    // Update schedule index
    scheduleDiv.setAttribute('data-schedule-index', scheduleIndex);
    
    // Update schedule number
    template.querySelector('.schedule-number').textContent = scheduleIndex + 1;
    
    // Update input names
    const inputs = template.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.name) {
            input.name = input.name.replace('INDEX', scheduleIndex);
        }
    });
    
    document.getElementById('schedules-container').appendChild(template);
    
    scheduleIndex++;
    updateScheduleNumbers();
}

function removeSchedule(button) {
    if (document.querySelectorAll('.schedule-item').length <= 1) {
        alert('<?php echo e(__("messages.cannot_remove_last_schedule")); ?>');
        return;
    }
    
    button.closest('.schedule-item').remove();
    updateScheduleNumbers();
    reindexSchedules();
}

function updateScheduleNumbers() {
    document.querySelectorAll('.schedule-item').forEach((schedule, index) => {
        schedule.querySelector('.schedule-number').textContent = index + 1;
    });
}

function reindexSchedules() {
    document.querySelectorAll('.schedule-item').forEach((schedule, scheduleIdx) => {
        schedule.setAttribute('data-schedule-index', scheduleIdx);
        
        // Update all inputs in this schedule
        schedule.querySelectorAll('input, select').forEach(input => {
            if (input.name && input.name.includes('schedules[')) {
                input.name = input.name.replace(/schedules\[\d+\]/, `schedules[${scheduleIdx}]`);
            }
        });
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/medications/create.blade.php ENDPATH**/ ?>