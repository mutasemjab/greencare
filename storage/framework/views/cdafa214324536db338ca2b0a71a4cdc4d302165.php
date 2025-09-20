<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?php echo e(__('messages.medications')); ?></h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('medication-add')): ?>
                        <a href="<?php echo e(route('medications.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo e(__('messages.add_medication')); ?>

                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                 

                    <!-- Search and Filter Form -->
                    <form method="GET" action="<?php echo e(route('medications.index')); ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="<?php echo e(__('messages.search_medications')); ?>"
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="room_id" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_rooms')); ?></option>
                                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($room->id); ?>" <?php echo e(request('room_id') == $room->id ? 'selected' : ''); ?>>
                                            <?php echo e($room->title); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="patient_id" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_patients')); ?></option>
                                    <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($patient->id); ?>" <?php echo e(request('patient_id') == $patient->id ? 'selected' : ''); ?>>
                                            <?php echo e($patient->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="active" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_status')); ?></option>
                                    <option value="1" <?php echo e(request('active') === '1' ? 'selected' : ''); ?>><?php echo e(__('messages.active')); ?></option>
                                    <option value="0" <?php echo e(request('active') === '0' ? 'selected' : ''); ?>><?php echo e(__('messages.inactive')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> <?php echo e(__('messages.search')); ?>

                                    </button>
                                    <a href="<?php echo e(route('medications.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> <?php echo e(__('messages.clear')); ?>

                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('messages.medication_name')); ?></th>
                                    <th><?php echo e(__('messages.patient')); ?></th>
                                    <th><?php echo e(__('messages.room')); ?></th>
                                    <th><?php echo e(__('messages.dosage')); ?></th>
                                    <th><?php echo e(__('messages.schedules')); ?></th>
                                    <th><?php echo e(__('messages.compliance_rate')); ?></th>
                                    <th><?php echo e(__('messages.status')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $medications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $medication): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration + ($medications->currentPage() - 1) * $medications->perPage()); ?></td>
                                        <td>
                                            <strong><?php echo e($medication->name); ?></strong>
                                            <?php if($medication->notes): ?>
                                                <br><small class="text-muted"><?php echo e(Str::limit($medication->notes, 30)); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if($medication->patient->photo): ?>
                                                    <img src="<?php echo e(asset('storage/' . $medication->patient->photo)); ?>" 
                                                         class="rounded-circle mr-2" 
                                                         width="30" height="30"
                                                         alt="<?php echo e($medication->patient->name); ?>">
                                                <?php else: ?>
                                                    <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center bg-warning text-white" 
                                                         style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        <?php echo e(substr($medication->patient->name, 0, 1)); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="font-weight-bold"><?php echo e($medication->patient->name); ?></div>
                                                    <small class="text-muted"><?php echo e($medication->patient->phone); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($medication->room): ?>
                                                <span class="badge badge-info"><?php echo e($medication->room->title); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($medication->dosage): ?>
                                                <span class="badge badge-secondary"><?php echo e($medication->dosage); ?></span>
                                            <?php endif; ?>
                                            <?php if($medication->quantity): ?>
                                                <br><small class="text-muted"><?php echo e($medication->quantity); ?> <?php echo e(__('messages.per_dose')); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary"><?php echo e($medication->schedules->count()); ?> <?php echo e(__('messages.times')); ?></span>
                                            <div class="mt-1">
                                                <?php $__currentLoopData = $medication->schedules->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <small class="d-block text-muted"><?php echo e($schedule->formatted_time); ?> (<?php echo e($schedule->frequency_text); ?>)</small>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($medication->schedules->count() > 2): ?>
                                                    <small class="text-muted">+<?php echo e($medication->schedules->count() - 2); ?> <?php echo e(__('messages.more')); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                                $rate = $medication->compliance_rate;
                                                $class = $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger');
                                            ?>
                                            <span class="badge badge-<?php echo e($class); ?>"><?php echo e($rate); ?>%</span>
                                        </td>
                                        <td>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('medication-edit')): ?>
                                                <form action="<?php echo e(route('medications.toggle-active', $medication)); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-<?php echo e($medication->active ? 'success' : 'secondary'); ?> btn-toggle-status"
                                                            title="<?php echo e($medication->active ? __('messages.active') : __('messages.inactive')); ?>">
                                                        <i class="fas fa-<?php echo e($medication->active ? 'toggle-on' : 'toggle-off'); ?>"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge badge-<?php echo e($medication->active ? 'success' : 'secondary'); ?>">
                                                    <?php echo e($medication->active ? __('messages.active') : __('messages.inactive')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('medication-table')): ?>
                                                    <a href="<?php echo e(route('medications.show', $medication)); ?>" 
                                                       class="btn btn-sm btn-info" title="<?php echo e(__('messages.view')); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('medication-edit')): ?>
                                                    <a href="<?php echo e(route('medications.edit', $medication)); ?>" 
                                                       class="btn btn-sm btn-warning" title="<?php echo e(__('messages.edit')); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('medication-delete')): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="<?php echo e(__('messages.delete')); ?>"
                                                            data-toggle="modal" 
                                                            data-target="#deleteModal<?php echo e($medication->id); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('medication-delete')): ?>
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo e($medication->id); ?>" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"><?php echo e(__('messages.confirm_delete')); ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo e(__('messages.are_you_sure_delete_medication')); ?> "<strong><?php echo e($medication->name); ?></strong>" <?php echo e(__('messages.for_patient')); ?> "<strong><?php echo e($medication->patient->name); ?></strong>"?
                                                                <br><small class="text-muted"><?php echo e(__('messages.delete_medication_warning')); ?></small>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    <?php echo e(__('messages.cancel')); ?>

                                                                </button>
                                                                <form action="<?php echo e(route('medications.destroy', $medication)); ?>" method="POST" class="d-inline">
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php echo method_field('DELETE'); ?>
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <?php echo e(__('messages.delete')); ?>

                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                                                <p class="text-muted"><?php echo e(__('messages.no_medications_found')); ?></p>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('medication-add')): ?>
                                                    <a href="<?php echo e(route('medications.create')); ?>" class="btn btn-primary">
                                                        <?php echo e(__('messages.add_first_medication')); ?>

                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($medications->hasPages()): ?>
                        <div class="d-flex justify-content-center">
                            <?php echo e($medications->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirm status toggle
    $('.btn-toggle-status').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var isActive = $(this).hasClass('btn-success');
        var action = isActive ? '<?php echo e(__("messages.deactivate")); ?>' : '<?php echo e(__("messages.activate")); ?>';
        
        if (confirm('<?php echo e(__("messages.are_you_sure")); ?> ' + action.toLowerCase() + ' <?php echo e(__("messages.this_medication")); ?>?')) {
            form.submit();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/medications/index.blade.php ENDPATH**/ ?>