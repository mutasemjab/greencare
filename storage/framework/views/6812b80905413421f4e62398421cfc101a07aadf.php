<?php $__env->startSection('title', __('messages.app_configurations')); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><?php echo e(__('messages.app_configurations')); ?></h4>
                    <a href="<?php echo e(route('app-configs.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <?php echo e(__('messages.add_new_config')); ?>

                    </a>
                </div>

                <div class="card-body">
                 

                    <?php if($appConfigs->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('messages.id')); ?></th>
                                        <th><?php echo e(__('messages.user_app_links')); ?></th>
                                        <th><?php echo e(__('messages.created_at')); ?></th>
                                        <th><?php echo e(__('messages.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $appConfigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($config->id); ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <?php if($config->google_play_link_user_app): ?>
                                                        <a href="<?php echo e($config->google_play_link_user_app); ?>" target="_blank" class="btn btn-sm btn-success" title="<?php echo e(__('messages.google_play')); ?>">
                                                            <i class="fab fa-google-play"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if($config->app_store_link_user_app): ?>
                                                        <a href="<?php echo e($config->app_store_link_user_app); ?>" target="_blank" class="btn btn-sm btn-primary" title="<?php echo e(__('messages.app_store')); ?>">
                                                            <i class="fab fa-app-store"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if($config->hawawi_link_user_app): ?>
                                                        <a href="<?php echo e($config->hawawi_link_user_app); ?>" target="_blank" class="btn btn-sm btn-info" title="<?php echo e(__('messages.hawawi')); ?>">
                                                            <i class="fas fa-mobile-alt"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if(!$config->google_play_link_user_app && !$config->app_store_link_user_app && !$config->hawawi_link_user_app): ?>
                                                        -
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                           
                                            <td><?php echo e($config->created_at->format('Y-m-d H:i')); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('app-configs.show', $config)); ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('app-configs.edit', $config)); ?>" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('app-configs.destroy', $config)); ?>" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('<?php echo e(__('messages.confirm_delete')); ?>')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            <?php echo e($appConfigs->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted"><?php echo e(__('messages.no_configurations_found')); ?></p>
                            <a href="<?php echo e(route('app-configs.create')); ?>" class="btn btn-primary">
                                <?php echo e(__('messages.create_first_config')); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/app_configs/index.blade.php ENDPATH**/ ?>