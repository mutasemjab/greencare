<?php $__env->startSection('title', __('messages.shower_appointments')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-shower me-2"></i>
            <?php echo e(__('messages.shower_appointments')); ?>

        </h2>
        <a href="<?php echo e(route('showers.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            <?php echo e(__('messages.new_appointment')); ?>

        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1"><?php echo e(__('messages.total_appointments')); ?></h6>
                            <h3 class="mb-0"><?php echo e($stats['total']); ?></h3>
                        </div>
                        <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1"><?php echo e(__('messages.today_appointments')); ?></h6>
                            <h3 class="mb-0"><?php echo e($stats['today']); ?></h3>
                        </div>
                        <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1"><?php echo e(__('messages.this_month_appointments')); ?></h6>
                            <h3 class="mb-0"><?php echo e($stats['this_month']); ?></h3>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1"><?php echo e(__('messages.total_revenue')); ?></h6>
                            <h3 class="mb-0"><?php echo e(number_format($stats['total_revenue'], 2)); ?></h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Stats -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1"><?php echo e(__('messages.card_payments')); ?></h6>
                            <h4 class="mb-0 text-primary"><?php echo e($stats['card_payments']); ?></h4>
                        </div>
                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1"><?php echo e(__('messages.cash_payments')); ?></h6>
                            <h4 class="mb-0 text-success"><?php echo e($stats['cash_payments']); ?></h4>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('showers.index')); ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label"><?php echo e(__('messages.from_date')); ?></label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?php echo e(__('messages.to_date')); ?></label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?php echo e(__('messages.user')); ?></label>
                        <select name="user_id" class="form-control">
                            <option value=""><?php echo e(__('messages.all')); ?></option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                    <?php echo e($user->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?php echo e(__('messages.payment_method')); ?></label>
                        <select name="payment_method" class="form-control">
                            <option value=""><?php echo e(__('messages.all')); ?></option>
                            <option value="card" <?php echo e(request('payment_method') == 'card' ? 'selected' : ''); ?>>
                                <?php echo e(__('messages.card')); ?>

                            </option>
                            <option value="cash" <?php echo e(request('payment_method') == 'cash' ? 'selected' : ''); ?>>
                                <?php echo e(__('messages.cash')); ?>

                            </option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label"><?php echo e(__('messages.patient_code')); ?></label>
                        <input type="text" name="code_patient" class="form-control" 
                               placeholder="<?php echo e(__('messages.search_by_patient_code')); ?>" 
                               value="<?php echo e(request('code_patient')); ?>">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i><?php echo e(__('messages.search')); ?>

                        </button>
                        <a href="<?php echo e(route('showers.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i><?php echo e(__('messages.reset')); ?>

                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo e(__('messages.id')); ?></th>
                            <th><?php echo e(__('messages.user')); ?></th>
                            <th><?php echo e(__('messages.patient_code')); ?></th>
                            <th><?php echo e(__('messages.date_and_time')); ?></th>
                            <th><?php echo e(__('messages.price')); ?></th>
                            <th><?php echo e(__('messages.payment_method')); ?></th>
                            <th><?php echo e(__('messages.notes')); ?></th>
                            <th><?php echo e(__('messages.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $showers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shower): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($shower->id); ?></td>
                            <td>
                                <div>
                                    <strong><?php echo e($shower->user->name); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo e($shower->user->email); ?></small>
                                </div>
                            </td>
                            <td>
                                <?php if($shower->code_patient): ?>
                                    <span class="badge bg-info"><?php echo e($shower->code_patient); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo e($shower->date_of_shower->format('Y-m-d')); ?>

                                </div>
                                <?php if($shower->time_of_shower): ?>
                                    <div>
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo e($shower->time_of_shower->format('H:i')); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong class="text-success"><?php echo e(number_format($shower->price, 2)); ?> <?php echo e(__('messages.currency')); ?></strong>
                            </td>
                            <td>
                                <?php if($shower->card_number_id): ?>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-credit-card me-1"></i>
                                        <?php echo e(__('messages.card')); ?>

                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo e($shower->cardNumber->number ?? ''); ?>

                                    </small>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                        <?php echo e(__('messages.cash')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($shower->note): ?>
                                    <small><?php echo e(Str::limit($shower->note, 30)); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('showers.show', $shower->id)); ?>" 
                                       class="btn btn-sm btn-info" 
                                       title="<?php echo e(__('messages.view')); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('showers.edit', $shower->id)); ?>" 
                                       class="btn btn-sm btn-warning" 
                                       title="<?php echo e(__('messages.edit')); ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('showers.destroy', $shower->id)); ?>" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('<?php echo e(__('messages.confirm_delete')); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="<?php echo e(__('messages.delete')); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted"><?php echo e(__('messages.no_appointments')); ?></p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($showers->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/showers/index.blade.php ENDPATH**/ ?>