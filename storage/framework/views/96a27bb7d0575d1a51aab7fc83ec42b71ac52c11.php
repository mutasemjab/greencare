

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><?php echo e(__('messages.report_template')); ?>: <?php echo e($reportTemplate->title); ?></h3>
                        <div class="btn-group">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report-template-edit')): ?>
                                <a href="<?php echo e(route('report-templates.edit', $reportTemplate)); ?>" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> <?php echo e(__('messages.edit')); ?>

                                </a>
                                <form action="<?php echo e(route('report-templates.duplicate', $reportTemplate)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" 
                                            class="btn btn-secondary"
                                            onclick="return confirm('<?php echo e(__('messages.confirm_duplicate_template')); ?>')"
                                            title="<?php echo e(__('messages.duplicate')); ?>">
                                        <i class="fas fa-copy"></i> <?php echo e(__('messages.duplicate')); ?>

                                    </button>
                                </form>
                            <?php endif; ?>
                            <a href="<?php echo e(route('report-templates.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> <?php echo e(__('messages.back')); ?>

                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Template Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo e(__('messages.template_information')); ?></h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong><?php echo e(__('messages.title_en')); ?>:</strong></td>
                                            <td><?php echo e($reportTemplate->title_en); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.title_ar')); ?>:</strong></td>
                                            <td><?php echo e($reportTemplate->title_ar); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.created_for')); ?>:</strong></td>
                                            <td>
                                                <span class="badge badge-<?php echo e($reportTemplate->created_for == 'doctor' ? 'primary' : 'success'); ?>">
                                                    <?php echo e($reportTemplate->created_for_text); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.sections_count')); ?>:</strong></td>
                                            <td>
                                                <span class="badge badge-info"><?php echo e($reportTemplate->sections->count()); ?> <?php echo e(__('messages.sections')); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.created_at')); ?>:</strong></td>
                                            <td><?php echo e($reportTemplate->created_at->format('Y-m-d H:i')); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.updated_at')); ?>:</strong></td>
                                            <td><?php echo e($reportTemplate->updated_at->format('Y-m-d H:i')); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo e(__('messages.template_stats')); ?></h5>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h3 class="text-primary"><?php echo e($reportTemplate->sections->count()); ?></h3>
                                            <small class="text-muted"><?php echo e(__('messages.sections')); ?></small>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="text-success"><?php echo e($reportTemplate->sections->sum(function($section) { return $section->fields->count(); })); ?></h3>
                                            <small class="text-muted"><?php echo e(__('messages.total_fields')); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Preview -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?php echo e(__('messages.template_preview')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <?php if($reportTemplate->sections->count() > 0): ?>
                                        <?php $__currentLoopData = $reportTemplate->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="section-preview mb-4">
                                                <div class="section-header bg-primary text-white p-3 rounded-top">
                                                    <h5 class="mb-0">
                                                        <i class="fas fa-folder-open mr-2"></i>
                                                        <?php echo e($section->title); ?>

                                                        <small class="float-right">
                                                            <?php echo e($section->fields->count()); ?> <?php echo e(__('messages.fields')); ?>

                                                        </small>
                                                    </h5>
                                                </div>
                                                
                                                <div class="section-content border border-top-0 p-3">
                                                    <?php if($section->fields->count() > 0): ?>
                                                        <div class="row">
                                                            <?php $__currentLoopData = $section->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="field-preview border rounded p-3 bg-light">
                                                                        <div class="field-header mb-2">
                                                                            <strong><?php echo e($field->label); ?></strong>
                                                                            <?php if($field->required): ?>
                                                                                <span class="text-danger">*</span>
                                                                            <?php endif; ?>
                                                                            <span class="badge badge-secondary badge-sm ml-2"><?php echo e($field->input_type_text); ?></span>
                                                                        </div>
                                                                        
                                                                        <div class="field-demo">
                                                                            <?php switch($field->input_type):
                                                                                case ('text'): ?>
                                                                                    <input type="text" class="form-control" placeholder="<?php echo e($field->label); ?>" disabled>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php case ('textarea'): ?>
                                                                                    <textarea class="form-control" rows="3" placeholder="<?php echo e($field->label); ?>" disabled></textarea>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php case ('number'): ?>
                                                                                    <input type="number" class="form-control" placeholder="<?php echo e($field->label); ?>" disabled>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php case ('date'): ?>
                                                                                    <input type="date" class="form-control" disabled>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php case ('select'): ?>
                                                                                    <select class="form-control" disabled>
                                                                                        <option><?php echo e(__('messages.select_option')); ?></option>
                                                                                        <?php $__currentLoopData = $field->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                            <option><?php echo e($option->value); ?></option>
                                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                    </select>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php case ('radio'): ?>
                                                                                    <?php $__currentLoopData = $field->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio" disabled>
                                                                                            <label class="form-check-label"><?php echo e($option->value); ?></label>
                                                                                        </div>
                                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php case ('checkbox'): ?>
                                                                                    <?php $__currentLoopData = $field->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="checkbox" disabled>
                                                                                            <label class="form-check-label"><?php echo e($option->value); ?></label>
                                                                                        </div>
                                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php case ('boolean'): ?>
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label"><?php echo e(__('messages.yes')); ?></label>
                                                                                    </div>
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label"><?php echo e(__('messages.no')); ?></label>
                                                                                    </div>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php case ('gender'): ?>
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label"><?php echo e(__('messages.male')); ?></label>
                                                                                    </div>
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label"><?php echo e(__('messages.female')); ?></label>
                                                                                    </div>
                                                                                    <?php break; ?>
                                                                                
                                                                                <?php default: ?>
                                                                                    <input type="text" class="form-control" placeholder="<?php echo e($field->label); ?>" disabled>
                                                                            <?php endswitch; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="text-center text-muted py-3">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            <?php echo e(__('messages.no_fields_in_section')); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <p class="text-muted"><?php echo e(__('messages.no_sections_in_template')); ?></p>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report-template-edit')): ?>
                                                <a href="<?php echo e(route('report-templates.edit', $reportTemplate)); ?>" class="btn btn-primary">
                                                    <?php echo e(__('messages.add_sections')); ?>

                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/report-templates/show.blade.php ENDPATH**/ ?>