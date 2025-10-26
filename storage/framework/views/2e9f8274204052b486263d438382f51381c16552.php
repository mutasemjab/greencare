<?php $__env->startSection('title', __('messages.appointment_providers')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?php echo e(__('messages.appointment_providers')); ?></h3>
                    <div class="card-tools">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('appointmentProvider-add')): ?>
                            <a href="<?php echo e(route('appointment-providers.create')); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> <?php echo e(__('messages.add_appointment_provider')); ?>

                            </a>
                        <?php endif; ?>
                       
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="<?php echo e(route('appointment-providers.index')); ?>" id="filterForm">
                                <div class="row">
                                    <!-- Status Filter -->
                                    <div class="col-md-2 mb-3">
                                        <label for="status" class="form-label"><?php echo e(__('messages.status')); ?></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="all" <?php echo e($status == 'all' ? 'selected' : ''); ?>><?php echo e(__('messages.all_statuses')); ?></option>
                                            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $statusName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e($status == $key ? 'selected' : ''); ?>>
                                                    <?php echo e(__('messages.status_' . strtolower(str_replace(' ', '_', $statusName)))); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <!-- Date From -->
                                    <div class="col-md-2 mb-3">
                                        <label for="date_from" class="form-label"><?php echo e(__('messages.date_from')); ?></label>
                                        <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e($dateFrom); ?>">
                                    </div>

                                    <!-- Date To -->
                                    <div class="col-md-2 mb-3">
                                        <label for="date_to" class="form-label"><?php echo e(__('messages.date_to')); ?></label>
                                        <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e($dateTo); ?>">
                                    </div>

                                    <!-- User Filter -->
                                    <div class="col-md-2 mb-3">
                                        <label for="user_id" class="form-label"><?php echo e(__('messages.user')); ?></label>
                                        <select name="user_id" id="user_id" class="form-control">
                                            <option value=""><?php echo e(__('messages.all_users')); ?></option>
                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($user->id); ?>" <?php echo e($userId == $user->id ? 'selected' : ''); ?>>
                                                    <?php echo e($user->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <!-- Provider Filter -->
                                    <div class="col-md-2 mb-3">
                                        <label for="provider_id" class="form-label"><?php echo e(__('messages.provider')); ?></label>
                                        <select name="provider_id" id="provider_id" class="form-control">
                                            <option value=""><?php echo e(__('messages.all_providers')); ?></option>
                                            <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($provider->id); ?>" <?php echo e($providerId == $provider->id ? 'selected' : ''); ?>>
                                                    <?php echo e($provider->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <!-- Search -->
                                    <div class="col-md-2 mb-3">
                                        <label for="search" class="form-label"><?php echo e(__('messages.search')); ?></label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               placeholder="<?php echo e(__('messages.search_patients_phone_address')); ?>" value="<?php echo e($search); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Filter Buttons -->
                                    <div class="col-md-12 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary mr-2">
                                            <i class="fas fa-filter"></i> <?php echo e(__('messages.filter')); ?>

                                        </button>
                                        <a href="<?php echo e(route('appointment-providers.index')); ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> <?php echo e(__('messages.clear')); ?>

                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong><?php echo e(__('messages.total_appointments')); ?>:</strong> <?php echo e($appointments->total()); ?>

                                <?php if($status !== 'all'): ?>
                                    - <strong><?php echo e(__('messages.filtered_by_status')); ?>:</strong> 
                                    <?php echo e(__('messages.status_' . strtolower(str_replace(' ', '_', $statusOptions[$status])))); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('messages.id')); ?></th>
                                    <th><?php echo e(__('messages.patient_name')); ?></th>
                                    <th><?php echo e(__('messages.patient_phone')); ?></th>
                                    <th><?php echo e(__('messages.provider')); ?></th>
                                    <th><?php echo e(__('messages.user')); ?></th>
                                    <th><?php echo e(__('messages.appointment_date')); ?></th>
                                    <th><?php echo e(__('messages.appointment_time')); ?></th>
                                    <th><?php echo e(__('messages.status')); ?></th>
                                    <th><?php echo e(__('messages.created_at')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($appointment->id); ?></td>
                                        <td>
                                            <strong><?php echo e($appointment->name_of_patient); ?></strong>
                                            <?php if($appointment->address): ?>
                                                <br><small class="text-muted"><?php echo e(Str::limit($appointment->address, 30)); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="tel:<?php echo e($appointment->phone_of_patient); ?>" class="text-primary">
                                                <?php echo e($appointment->phone_of_patient); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <?php if($appointment->provider): ?>
                                                <div>
                                                    <strong><?php echo e($appointment->provider->name); ?></strong><br>
                                                    <small class="text-muted">
                                                        <?php echo e(__('messages.experience')); ?>: <?php echo e($appointment->provider->number_years_experience); ?>

                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted"><?php echo e(__('messages.not_available')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($appointment->user): ?>
                                                <div>
                                                    <strong><?php echo e($appointment->user->name); ?></strong><br>
                                                    <small class="text-muted"><?php echo e($appointment->user->email); ?></small>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted"><?php echo e(__('messages.not_available')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($appointment->date_of_appointment): ?>
                                                <?php echo e($appointment->date_of_appointment->format('Y-m-d')); ?>

                                            <?php else: ?>
                                                <span class="text-muted"><?php echo e(__('messages.not_specified')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($appointment->time_of_appointment): ?>
                                                <?php echo e($appointment->time_of_appointment->format('H:i')); ?>

                                            <?php else: ?>
                                                <span class="text-muted"><?php echo e(__('messages.not_specified')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('appointmentProvider-edit')): ?>
                                                <select class="form-control form-control-sm status-select" 
                                                        data-id="<?php echo e($appointment->id); ?>" 
                                                        style="min-width: 120px;">
                                                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $statusName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($key); ?>" 
                                                                <?php echo e($appointment->status == $key ? 'selected' : ''); ?>>
                                                            <?php echo e(__('messages.status_' . strtolower(str_replace(' ', '_', $statusName)))); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            <?php else: ?>
                                                <span class="badge <?php echo e($appointment->status_badge_class); ?>">
                                                    <?php echo e(__('messages.status_' . strtolower(str_replace(' ', '_', $appointment->status_name)))); ?>

                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($appointment->created_at->format('Y-m-d H:i')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('appointmentProvider-table')): ?>
                                                    <a href="<?php echo e(route('appointment-providers.show', $appointment->id)); ?>" 
                                                       class="btn btn-info btn-sm" title="<?php echo e(__('messages.view')); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('appointmentProvider-edit')): ?>
                                                    <a href="<?php echo e(route('appointment-providers.edit', $appointment->id)); ?>" 
                                                       class="btn btn-warning btn-sm" title="<?php echo e(__('messages.edit')); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('appointmentProvider-delete')): ?>
                                                    <form action="<?php echo e(route('appointment-providers.destroy', $appointment->id)); ?>" 
                                                          method="POST" style="display: inline-block;">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-danger btn-sm delete-btn" 
                                                                title="<?php echo e(__('messages.delete')); ?>"
                                                                data-confirm="<?php echo e(__('messages.confirm_delete_appointment')); ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <?php echo e(__('messages.no_appointment_providers_found')); ?>

                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($appointments->hasPages()): ?>
                        <div class="d-flex justify-content-center">
                            <?php echo e($appointments->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const selects = ['#status', '#user_id', '#provider_id'];
    selects.forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            element.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        }
    });

    // Status change handler
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const appointmentId = this.dataset.id;
            const newStatus = this.value;
            const originalStatus = this.querySelector('option[selected]').value;
            
            // Show loading state
            this.disabled = true;
            
            fetch(`<?php echo e(route('appointment-providers.update-status', ':id')); ?>`.replace(':id', appointmentId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Update the selected option
                    this.querySelector('option[selected]').removeAttribute('selected');
                    this.querySelector(`option[value="${newStatus}"]`).setAttribute('selected', 'selected');
                } else {
                    // Revert selection
                    this.value = originalStatus;
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert selection
                this.value = originalStatus;
                showAlert('error', '<?php echo e(__("messages.error_occurred")); ?>');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });

    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const confirmMessage = this.dataset.confirm;
            
            if (confirm(confirmMessage)) {
                this.closest('form').submit();
            }
        });
    });

    // Alert function
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Insert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const alert = cardBody.querySelector(`.${alertClass}`);
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/appointment-providers/index.blade.php ENDPATH**/ ?>