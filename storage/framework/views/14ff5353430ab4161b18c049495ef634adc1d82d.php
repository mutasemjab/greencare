<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?php echo e(__('messages.families')); ?></h3>
                    <a href="<?php echo e(route('families.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <?php echo e(__('messages.add_family')); ?>

                    </a>
                </div>

                <div class="card-body">
                 

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('messages.family_name')); ?></th>
                                    <th><?php echo e(__('messages.members_count')); ?></th>
                                    <th><?php echo e(__('messages.created_at')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $families; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $family): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration + ($families->currentPage() - 1) * $families->perPage()); ?></td>
                                        <td>
                                            <strong><?php echo e($family->name); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($family->users_count); ?> <?php echo e(__('messages.members')); ?></span>
                                        </td>
                                        <td><?php echo e($family->created_at->format('Y-m-d H:i')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('families.show', $family)); ?>" 
                                                   class="btn btn-sm btn-info" title="<?php echo e(__('messages.view')); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('families.edit', $family)); ?>" 
                                                   class="btn btn-sm btn-warning" title="<?php echo e(__('messages.edit')); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="<?php echo e(__('messages.delete')); ?>"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal<?php echo e($family->id); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo e($family->id); ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"><?php echo e(__('messages.confirm_delete')); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php echo e(__('messages.are_you_sure_delete_family')); ?> "<strong><?php echo e($family->name); ?></strong>"?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <?php echo e(__('messages.cancel')); ?>

                                                            </button>
                                                            <form action="<?php echo e(route('families.destroy', $family)); ?>" method="POST" class="d-inline">
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
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                <p class="text-muted"><?php echo e(__('messages.no_families_found')); ?></p>
                                                <a href="<?php echo e(route('families.create')); ?>" class="btn btn-primary">
                                                    <?php echo e(__('messages.add_first_family')); ?>

                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($families->hasPages()): ?>
                        <div class="d-flex justify-content-center">
                            <?php echo e($families->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/families/index.blade.php ENDPATH**/ ?>