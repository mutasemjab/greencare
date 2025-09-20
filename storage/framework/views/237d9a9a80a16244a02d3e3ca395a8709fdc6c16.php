<?php $__env->startSection('title', __('messages.add_new_config')); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><?php echo e(__('messages.add_new_config')); ?></h4>
                    <a href="<?php echo e(route('app-configs.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> <?php echo e(__('messages.back')); ?>

                    </a>
                </div>

                <div class="card-body">
                    <form action="<?php echo e(route('app-configs.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                      

                        <!-- User App Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-user"></i> <?php echo e(__('messages.user_app_configuration')); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- User App Google Play Link -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="google_play_link_user_app" class="form-label"><?php echo e(__('messages.google_play_link_user_app')); ?></label>
                                            <input type="url" 
                                                   class="form-control <?php $__errorArgs = ['google_play_link_user_app'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="google_play_link_user_app" 
                                                   name="google_play_link_user_app" 
                                                   value="<?php echo e(old('google_play_link_user_app')); ?>"
                                                   placeholder="<?php echo e(__('messages.enter_google_play_link_user_app')); ?>">
                                            <?php $__errorArgs = ['google_play_link_user_app'];
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
                                    <!-- User App App Store Link -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="app_store_link_user_app" class="form-label"><?php echo e(__('messages.app_store_link_user_app')); ?></label>
                                            <input type="url" 
                                                   class="form-control <?php $__errorArgs = ['app_store_link_user_app'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="app_store_link_user_app" 
                                                   name="app_store_link_user_app" 
                                                   value="<?php echo e(old('app_store_link_user_app')); ?>"
                                                   placeholder="<?php echo e(__('messages.enter_app_store_link_user_app')); ?>">
                                            <?php $__errorArgs = ['app_store_link_user_app'];
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
                                    <!-- User App Hawawi Link -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="hawawi_link_user_app" class="form-label"><?php echo e(__('messages.hawawi_link_user_app')); ?></label>
                                            <input type="url" 
                                                   class="form-control <?php $__errorArgs = ['hawawi_link_user_app'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="hawawi_link_user_app" 
                                                   name="hawawi_link_user_app" 
                                                   value="<?php echo e(old('hawawi_link_user_app')); ?>"
                                                   placeholder="<?php echo e(__('messages.enter_hawawi_link_user_app')); ?>">
                                            <?php $__errorArgs = ['hawawi_link_user_app'];
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
                                    <!-- User App Minimum Versions -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="min_version_google_play_user_app" class="form-label"><?php echo e(__('messages.min_version_google_play_user_app')); ?></label>
                                            <input type="text" 
                                                   class="form-control <?php $__errorArgs = ['min_version_google_play_user_app'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="min_version_google_play_user_app" 
                                                   name="min_version_google_play_user_app" 
                                                   value="<?php echo e(old('min_version_google_play_user_app')); ?>"
                                                   placeholder="<?php echo e(__('messages.enter_version')); ?>">
                                            <?php $__errorArgs = ['min_version_google_play_user_app'];
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

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="min_version_app_store_user_app" class="form-label"><?php echo e(__('messages.min_version_app_store_user_app')); ?></label>
                                            <input type="text" 
                                                   class="form-control <?php $__errorArgs = ['min_version_app_store_user_app'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="min_version_app_store_user_app" 
                                                   name="min_version_app_store_user_app" 
                                                   value="<?php echo e(old('min_version_app_store_user_app')); ?>"
                                                   placeholder="<?php echo e(__('messages.enter_version')); ?>">
                                            <?php $__errorArgs = ['min_version_app_store_user_app'];
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

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="min_version_hawawi_user_app" class="form-label"><?php echo e(__('messages.min_version_hawawi_user_app')); ?></label>
                                            <input type="text" 
                                                   class="form-control <?php $__errorArgs = ['min_version_hawawi_user_app'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="min_version_hawawi_user_app" 
                                                   name="min_version_hawawi_user_app" 
                                                   value="<?php echo e(old('min_version_hawawi_user_app')); ?>"
                                                   placeholder="<?php echo e(__('messages.enter_version')); ?>">
                                            <?php $__errorArgs = ['min_version_hawawi_user_app'];
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
                            </div>
                        </div>

                        

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo e(route('app-configs.index')); ?>" class="btn btn-secondary me-md-2">
                                <?php echo e(__('messages.cancel')); ?>

                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo e(__('messages.save')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/app_configs/create.blade.php ENDPATH**/ ?>