<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?php echo e(__('messages.patients')); ?></h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-add')): ?>
                        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo e(__('messages.add_patient')); ?>

                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">
         

                    <!-- Search and Filter Form -->
                    <form method="GET" action="<?php echo e(route('users.index')); ?>" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="<?php echo e(__('messages.search_patients')); ?>"
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="gender" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_genders')); ?></option>
                                    <option value="1" <?php echo e(request('gender') == '1' ? 'selected' : ''); ?>><?php echo e(__('messages.male')); ?></option>
                                    <option value="2" <?php echo e(request('gender') == '2' ? 'selected' : ''); ?>><?php echo e(__('messages.female')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="activate" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_status')); ?></option>
                                    <option value="1" <?php echo e(request('activate') == '1' ? 'selected' : ''); ?>><?php echo e(__('messages.active')); ?></option>
                                    <option value="2" <?php echo e(request('activate') == '2' ? 'selected' : ''); ?>><?php echo e(__('messages.inactive')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> <?php echo e(__('messages.search')); ?>

                                    </button>
                                    <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> <?php echo e(__('messages.clear')); ?>

                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('messages.photo')); ?></th>
                                    <th><?php echo e(__('messages.name')); ?></th>
                                    <th><?php echo e(__('messages.phone')); ?></th>
                                    <th><?php echo e(__('messages.email')); ?></th>
                                    <th><?php echo e(__('messages.gender')); ?></th>
                                    <th><?php echo e(__('messages.date_of_birth')); ?></th>
                                    <th><?php echo e(__('messages.status')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration + ($users->currentPage() - 1) * $users->perPage()); ?></td>
                                        <td>
                                            <?php if($user->photo): ?>
                                                <img src="<?php echo e(asset('assets/admin/uploads/' . $user->photo)); ?>" 
                                                     class="rounded-circle" 
                                                     width="40" height="40"
                                                     alt="<?php echo e($user->name); ?>">
                                            <?php else: ?>
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" 
                                                     style="width: 40px; height: 40px;">
                                                    <?php echo e(substr($user->name, 0, 1)); ?>

                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo e($user->name); ?></strong>
                                        </td>
                                        <td><?php echo e($user->phone); ?></td>
                                        <td><?php echo e($user->email ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e($user->gender == 1 ? 'info' : 'pink'); ?>">
                                                <?php echo e($user->gender_text); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($user->date_of_birth); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e($user->activate == 1 ? 'success' : 'danger'); ?>">
                                                <?php echo e($user->active_status_text); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-table')): ?>
                                                    <a href="<?php echo e(route('users.show', $user)); ?>" 
                                                       class="btn btn-sm btn-info" title="<?php echo e(__('messages.view')); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-edit')): ?>
                                                    <a href="<?php echo e(route('users.edit', $user)); ?>" 
                                                       class="btn btn-sm btn-warning" title="<?php echo e(__('messages.edit')); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('users.toggle-status', $user)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-<?php echo e($user->activate == 1 ? 'secondary' : 'success'); ?>"
                                                                title="<?php echo e($user->activate == 1 ? __('messages.deactivate') : __('messages.activate')); ?>"
                                                                onclick="return confirm('<?php echo e(__('messages.confirm_status_change')); ?>')">
                                                            <i class="fas fa-<?php echo e($user->activate == 1 ? 'times' : 'check'); ?>"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-delete')): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="<?php echo e(__('messages.delete')); ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal<?php echo e($user->id); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-delete')): ?>
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo e($user->id); ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"><?php echo e(__('messages.confirm_delete')); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo e(__('messages.are_you_sure_delete_patient')); ?> "<strong><?php echo e($user->name); ?></strong>"?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <?php echo e(__('messages.cancel')); ?>

                                                                </button>
                                                                <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline">
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
                                                <i class="fas fa-user-injured fa-3x text-muted mb-3"></i>
                                                <p class="text-muted"><?php echo e(__('messages.no_patients_found')); ?></p>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-add')): ?>
                                                    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                                                        <?php echo e(__('messages.add_first_patient')); ?>

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
                    <?php if($users->hasPages()): ?>
                        <div class="d-flex justify-content-center">
                            <?php echo e($users->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/users/index.blade.php ENDPATH**/ ?>