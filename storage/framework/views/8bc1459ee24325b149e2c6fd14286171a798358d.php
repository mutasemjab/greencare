<?php $__env->startSection('title', __('messages.elderly_care_types')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?php echo e(__('messages.elderly_care_types')); ?></h3>
                    <a href="<?php echo e(route('elderly-cares.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <?php echo e(__('messages.add_new')); ?>

                    </a>
                </div>

                <div class="card-body">
                   

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="<?php echo e(route('elderly-cares.index')); ?>" class="d-flex">
                                <select name="type_of_service" class="form-control me-2" onchange="this.form.submit()">
                                    <option value=""><?php echo e(__('messages.all_service_types')); ?></option>
                                    <?php $__currentLoopData = \App\Models\TypeElderlyCare::getServiceTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(request('type_of_service') == $key ? 'selected' : ''); ?>>
                                            <?php echo e($value); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input type="hidden" name="min_price" value="<?php echo e(request('min_price')); ?>">
                                <input type="hidden" name="max_price" value="<?php echo e(request('max_price')); ?>">
                            </form>
                        </div>
                        <div class="col-md-8">
                            <form method="GET" action="<?php echo e(route('elderly-cares.index')); ?>" class="d-flex">
                                <input type="number" 
                                       name="min_price" 
                                       class="form-control me-2" 
                                       placeholder="<?php echo e(__('messages.min_price')); ?>" 
                                       value="<?php echo e(request('min_price')); ?>"
                                       step="0.01"
                                       min="0">
                                <input type="number" 
                                       name="max_price" 
                                       class="form-control me-2" 
                                       placeholder="<?php echo e(__('messages.max_price')); ?>" 
                                       value="<?php echo e(request('max_price')); ?>"
                                       step="0.01"
                                       min="0">
                                <button type="submit" class="btn btn-secondary me-2"><?php echo e(__('messages.filter')); ?></button>
                                <a href="<?php echo e(route('elderly-cares.index')); ?>" class="btn btn-outline-secondary"><?php echo e(__('messages.clear')); ?></a>
                                <input type="hidden" name="type_of_service" value="<?php echo e(request('type_of_service')); ?>">
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('messages.id')); ?></th>
                                    <th><?php echo e(__('messages.service_type')); ?></th>
                                    <th><?php echo e(__('messages.price')); ?></th>
                                    <th><?php echo e(__('messages.created_at')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $elderlyCares; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $care): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($care->id); ?></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php echo e($care->translated_service_type); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($care->formatted_price); ?></td>
                                        <td><?php echo e($care->created_at->format('Y-m-d H:i')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('elderly-cares.show', $care)); ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <?php echo e(__('messages.view')); ?>

                                                </a>
                                                <a href="<?php echo e(route('elderly-cares.edit', $care)); ?>" 
                                                   class="btn btn-sm btn-warning">
                                                    <?php echo e(__('messages.edit')); ?>

                                                </a>
                                                <form action="<?php echo e(route('elderly-cares.destroy', $care)); ?>" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('<?php echo e(__('messages.confirm_delete')); ?>')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <?php echo e(__('messages.delete')); ?>

                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <?php echo e(__('messages.no_data_found')); ?>

                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        <?php echo e($elderlyCares->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/elderly-cares/index.blade.php ENDPATH**/ ?>