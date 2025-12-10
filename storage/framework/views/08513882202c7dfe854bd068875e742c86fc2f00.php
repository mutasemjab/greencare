<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?php echo e(__('messages.pos_list')); ?></h4>
                    <a href="<?php echo e(route('pos.create')); ?>" class="btn btn-primary">
                        <?php echo e(__('messages.create_pos')); ?>

                    </a>
                </div>

                <div class="card-body">
                  
                    <?php if($posRecords->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><?php echo e(__('messages.id')); ?></th>
                                        <th><?php echo e(__('messages.country_name')); ?></th>
                                        <th><?php echo e(__('messages.name')); ?></th>
                                        <th><?php echo e(__('messages.phone')); ?></th>
                                        <th><?php echo e(__('messages.google_map_link')); ?></th>
                                        <th><?php echo e(__('messages.address')); ?></th>
                                        <th><?php echo e(__('messages.created_at')); ?></th>
                                        <th><?php echo e(__('messages.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $posRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($pos->id); ?></td>
                                            <td><?php echo e($pos->country_name); ?></td>
                                            <td><?php echo e($pos->name); ?></td>
                                            <td><?php echo e($pos->phone); ?></td>
                                            <td><?php echo e($pos->google_map_link ?? null); ?></td>
                                            <td><?php echo e(Str::limit($pos->address, 50)); ?></td>
                                            <td><?php echo e($pos->created_at->format('Y-m-d')); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                   
                                                    <a href="<?php echo e(route('pos.edit', $pos)); ?>" 
                                                       class="btn btn-warning btn-sm">
                                                        <?php echo e(__('messages.edit')); ?>

                                                    </a>
                                                   
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            <?php echo e($posRecords->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p class="text-muted"><?php echo e(__('messages.no_pos_found')); ?></p>
                            <a href="<?php echo e(route('pos.create')); ?>" class="btn btn-primary">
                                <?php echo e(__('messages.create_first_pos')); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/pos/index.blade.php ENDPATH**/ ?>