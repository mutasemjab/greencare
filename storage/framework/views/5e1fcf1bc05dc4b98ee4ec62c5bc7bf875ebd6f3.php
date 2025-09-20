<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?php echo e(__('messages.rooms')); ?></h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('room-add')): ?>
                        <a href="<?php echo e(route('rooms.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo e(__('messages.add_room')); ?>

                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                  

                    <!-- Search and Filter Form -->
                    <form method="GET" action="<?php echo e(route('rooms.index')); ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="<?php echo e(__('messages.search_rooms')); ?>"
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="family_id" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_families')); ?></option>
                                    <?php $__currentLoopData = $families; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $family): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($family->id); ?>" <?php echo e(request('family_id') == $family->id ? 'selected' : ''); ?>>
                                            <?php echo e($family->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> <?php echo e(__('messages.search')); ?>

                                    </button>
                                    <a href="<?php echo e(route('rooms.index')); ?>" class="btn btn-outline-secondary">
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
                                    <th><?php echo e(__('messages.room_title')); ?></th>
                                    <th><?php echo e(__('messages.family')); ?></th>
                                    <th><?php echo e(__('messages.patients')); ?></th>
                                    <th><?php echo e(__('messages.doctors')); ?></th>
                                    <th><?php echo e(__('messages.nurses')); ?></th>
                                    <th><?php echo e(__('messages.reports')); ?></th>
                                    <th><?php echo e(__('messages.created_at')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration + ($rooms->currentPage() - 1) * $rooms->perPage()); ?></td>
                                        <td>
                                            <strong><?php echo e($room->title); ?></strong>
                                            <?php if($room->description): ?>
                                                <br><small class="text-muted"><?php echo e(Str::limit($room->description, 50)); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($room->family): ?>
                                                <span class="badge badge-info"><?php echo e($room->family->name); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning"><?php echo e($room->patients->count()); ?></span>
                                            <?php if($room->patients->count() > 0): ?>
                                                <div class="mt-1">
                                                    <?php $__currentLoopData = $room->patients->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <small class="d-block text-muted"><?php echo e($patient->name); ?></small>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($room->patients->count() > 2): ?>
                                                        <small class="text-muted">+<?php echo e($room->patients->count() - 2); ?> <?php echo e(__('messages.more')); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary"><?php echo e($room->doctors->count()); ?></span>
                                            <?php if($room->doctors->count() > 0): ?>
                                                <div class="mt-1">
                                                    <?php $__currentLoopData = $room->doctors->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <small class="d-block text-muted"><?php echo e($doctor->name); ?></small>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($room->doctors->count() > 2): ?>
                                                        <small class="text-muted">+<?php echo e($room->doctors->count() - 2); ?> <?php echo e(__('messages.more')); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-success"><?php echo e($room->nurses->count()); ?></span>
                                            <?php if($room->nurses->count() > 0): ?>
                                                <div class="mt-1">
                                                    <?php $__currentLoopData = $room->nurses->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nurse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <small class="d-block text-muted"><?php echo e($nurse->name); ?></small>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($room->nurses->count() > 2): ?>
                                                        <small class="text-muted">+<?php echo e($room->nurses->count() - 2); ?> <?php echo e(__('messages.more')); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary"><?php echo e($room->reports->count()); ?></span>
                                        </td>
                                        <td><?php echo e($room->created_at->format('Y-m-d H:i')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('room-table')): ?>
                                                    <a href="<?php echo e(route('rooms.show', $room)); ?>" 
                                                       class="btn btn-sm btn-info" title="<?php echo e(__('messages.view')); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('room-edit')): ?>
                                                    <a href="<?php echo e(route('rooms.edit', $room)); ?>" 
                                                       class="btn btn-sm btn-warning" title="<?php echo e(__('messages.edit')); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('room-delete')): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="<?php echo e(__('messages.delete')); ?>"
                                                            data-toggle="modal" 
                                                            data-target="#deleteModal<?php echo e($room->id); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('room-delete')): ?>
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo e($room->id); ?>" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"><?php echo e(__('messages.confirm_delete')); ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo e(__('messages.are_you_sure_delete_room')); ?> "<strong><?php echo e($room->title); ?></strong>"?
                                                                <br><small class="text-muted"><?php echo e(__('messages.delete_room_warning')); ?></small>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    <?php echo e(__('messages.cancel')); ?>

                                                                </button>
                                                                <form action="<?php echo e(route('rooms.destroy', $room)); ?>" method="POST" class="d-inline">
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
                                                <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                                                <p class="text-muted"><?php echo e(__('messages.no_rooms_found')); ?></p>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('room-add')): ?>
                                                    <a href="<?php echo e(route('rooms.create')); ?>" class="btn btn-primary">
                                                        <?php echo e(__('messages.add_first_room')); ?>

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
                    <?php if($rooms->hasPages()): ?>
                        <div class="d-flex justify-content-center">
                            <?php echo e($rooms->appends(request()->query())->links()); ?>

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
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/rooms/index.blade.php ENDPATH**/ ?>