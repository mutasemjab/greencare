

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?php echo e(__('messages.Brands')); ?></h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('brand-add')): ?>
                        <a href="<?php echo e(route('brands.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo e(__('messages.Add_Brand')); ?>

                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('messages.Photo')); ?></th>
                                    <th><?php echo e(__('messages.Name_English')); ?></th>
                                    <th><?php echo e(__('messages.Name_Arabic')); ?></th>
                                    <th><?php echo e(__('messages.Products_Count')); ?></th>
                                    <th><?php echo e(__('messages.Created_At')); ?></th>
                                    <th><?php echo e(__('messages.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration + ($brands->currentPage() - 1) * $brands->perPage()); ?></td>
                                        <td>
                                            <?php if($brand->photo): ?>
                                                <img src="<?php echo e(asset('assets/admin/uploads/'.$brand->photo)); ?>" 
                                                     alt="<?php echo e($brand->name_en); ?>" 
                                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-tags text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo e($brand->name_en); ?></strong>
                                        </td>
                                        <td>
                                            <strong><?php echo e($brand->name_ar); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo e($brand->products_count ?? 0); ?> <?php echo e(__('messages.Products')); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($brand->created_at->format('Y-m-d')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('brand-edit')): ?>
                                                    <a href="<?php echo e(route('brands.edit', $brand)); ?>" 
                                                       class="btn btn-sm btn-warning" title="<?php echo e(__('messages.Edit')); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                             
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center"><?php echo e(__('messages.No_Brands_Found')); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        <?php echo e($brands->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/brands/index.blade.php ENDPATH**/ ?>