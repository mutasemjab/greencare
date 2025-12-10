<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?php echo e(__('messages.card_numbers_for')); ?>: <?php echo e($card->name); ?></h4>
                    <div>
                        <a href="<?php echo e(route('cards.show', $card)); ?>" class="btn btn-info btn-sm">
                            <?php echo e(__('messages.card_details')); ?>

                        </a>
                        <a href="<?php echo e(route('cards.index')); ?>" class="btn btn-secondary btn-sm">
                            <?php echo e(__('messages.back_to_cards')); ?>

                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Card Info Summary -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-2">
                                            <h6><?php echo e(__('messages.card_name')); ?></h6>
                                            <strong><?php echo e($card->name); ?></strong>
                                        </div>
                                        <div class="col-md-1">
                                            <h6><?php echo e(__('messages.price')); ?></h6>
                                            <span class="badge bg-success"><?php echo e(number_format($card->price, 2)); ?></span>
                                        </div>
                                        <div class="col-md-1">
                                            <h6><?php echo e(__('messages.total_numbers')); ?></h6>
                                            <span class="badge bg-primary"><?php echo e($cardNumbers->total()); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <h6><?php echo e(__('messages.available_for_sale')); ?></h6>
                                            <span class="badge bg-success"><?php echo e($card->available_for_sale_count); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <h6><?php echo e(__('messages.sold_not_assigned')); ?></h6>
                                            <span class="badge bg-info"><?php echo e($card->sold_not_assigned_count); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <h6><?php echo e(__('messages.sold_assigned')); ?></h6>
                                            <span class="badge bg-warning"><?php echo e($card->sold_and_assigned_count); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <h6><?php echo e(__('messages.used_numbers')); ?></h6>
                                            <span class="badge bg-danger"><?php echo e($card->used_card_numbers_count); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Actions -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" action="<?php echo e(route('cards.card-numbers', $card)); ?>" class="d-flex">
                                <select name="status" class="form-select me-2" onchange="this.form.submit()">
                                    <option value=""><?php echo e(__('messages.all_status')); ?></option>
                                    <option value="available" <?php echo e(request('status') == 'available' ? 'selected' : ''); ?>><?php echo e(__('messages.available_for_sale')); ?></option>
                                    <option value="sold_not_assigned" <?php echo e(request('status') == 'sold_not_assigned' ? 'selected' : ''); ?>><?php echo e(__('messages.sold_not_assigned')); ?></option>
                                    <option value="sold_assigned" <?php echo e(request('status') == 'sold_assigned' ? 'selected' : ''); ?>><?php echo e(__('messages.sold_assigned')); ?></option>
                                    <option value="used" <?php echo e(request('status') == 'used' ? 'selected' : ''); ?>><?php echo e(__('messages.used')); ?></option>
                                </select>
                                <select name="activate" class="form-select me-2" onchange="this.form.submit()">
                                    <option value=""><?php echo e(__('messages.all_activate')); ?></option>
                                    <option value="1" <?php echo e(request('activate') == '1' ? 'selected' : ''); ?>><?php echo e(__('messages.active')); ?></option>
                                    <option value="2" <?php echo e(request('activate') == '2' ? 'selected' : ''); ?>><?php echo e(__('messages.inactive')); ?></option>
                                </select>
                                <input type="text" name="search" class="form-control me-2" placeholder="<?php echo e(__('messages.search_user_or_number')); ?>" value="<?php echo e(request('search')); ?>">
                                <button type="submit" class="btn btn-outline-primary"><?php echo e(__('messages.filter')); ?></button>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="<?php echo e(route('cards.regenerate-numbers', $card)); ?>" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('<?php echo e(__('messages.confirm_regenerate')); ?>')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('POST'); ?>
                                <button type="submit" class="btn btn-warning">
                                    <?php echo e(__('messages.regenerate_all')); ?>

                                </button>
                            </form>
                        </div>
                    </div>

                    <?php if($cardNumbers->count() > 0): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cardnumbers-table')): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><?php echo e(__('messages.id')); ?></th>
                                        <th><?php echo e(__('messages.card_number')); ?></th>
                                        <th><?php echo e(__('messages.status')); ?></th>
                                        <th><?php echo e(__('messages.sell_status')); ?></th>
                                        <th><?php echo e(__('messages.activate_status')); ?></th>
                                        <th><?php echo e(__('messages.created_at')); ?></th>
                                        <th><?php echo e(__('messages.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $cardNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cardNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($cardNumber->id); ?></td>
                                            <td>
                                                <strong><?php echo e($cardNumber->number); ?></strong>
                                            </td>
                                           
                                            <td>
                                                <span class="badge <?php echo e($cardNumber->getStatusBadgeClass()); ?>">
                                                    <?php echo e($cardNumber->getStatusText()); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php if($cardNumber->sell == 1): ?>
                                                    <span class="badge bg-info"><?php echo e(__('messages.sold')); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo e(__('messages.not_sold')); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($cardNumber->activate == 1): ?>
                                                    <span class="badge bg-success"><?php echo e(__('messages.active')); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning"><?php echo e(__('messages.inactive')); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($cardNumber->created_at->format('Y-m-d H:i')); ?></td>
                                            <td>
                                                <div class="btn-group-vertical" role="group">
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cardnumbers-edit')): ?>
                                                        <?php if($cardNumber->isAvailableForSale()): ?>
                                                            <!-- Mark as Sold Button -->
                                                            <form action="<?php echo e(route('card-numbers.toggle-sell', $cardNumber)); ?>" 
                                                                method="POST" 
                                                                style="display: inline-block;"
                                                                onsubmit="return confirm('<?php echo e(__('messages.confirm_mark_sold')); ?>')">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('PATCH'); ?>
                                                                <button type="submit" class="btn btn-info btn-sm mb-1">
                                                                    <?php echo e(__('messages.mark_as_sold')); ?>

                                                                </button>
                                                            </form>
                                                            
                                                        <?php elseif($cardNumber->isSoldNotAssigned()): ?>
                                                            <!-- Assign to User Button -->
                                                            <button type="button" class="btn btn-primary btn-sm mb-1" 
                                                                    onclick="showAssignModal(<?php echo e($cardNumber->id); ?>, '<?php echo e($cardNumber->number); ?>')">
                                                                <?php echo e(__('messages.assign_to_user')); ?>

                                                            </button>
                                                            <!-- Mark as Not Sold Button -->
                                                            <form action="<?php echo e(route('card-numbers.toggle-sell', $cardNumber)); ?>" 
                                                                method="POST" 
                                                                style="display: inline-block;"
                                                                onsubmit="return confirm('<?php echo e(__('messages.confirm_mark_not_sold')); ?>')">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('PATCH'); ?>
                                                                <button type="submit" class="btn btn-outline-secondary btn-sm mb-1">
                                                                    <?php echo e(__('messages.mark_as_not_sold')); ?>

                                                                </button>
                                                            </form>
                                                            
                                                        <?php elseif($cardNumber->isSoldAndAssigned()): ?>
                                                            <!-- Mark as Used Button -->
                                                            <form action="<?php echo e(route('card-numbers.mark-used', $cardNumber)); ?>" 
                                                                method="POST" 
                                                                style="display: inline-block;"
                                                                onsubmit="return confirm('<?php echo e(__('messages.confirm_mark_used')); ?>')">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('PATCH'); ?>
                                                                <button type="submit" class="btn btn-success btn-sm mb-1">
                                                                    <?php echo e(__('messages.mark_as_used')); ?>

                                                                </button>
                                                            </form>
                                                        
                                                            
                                                        <?php elseif($cardNumber->isUsed()): ?>
                                                            <!-- Mark as Not Used Button -->
                                                            <form action="<?php echo e(route('card-numbers.toggle-status', $cardNumber)); ?>" 
                                                                method="POST" 
                                                                style="display: inline-block;"
                                                                onsubmit="return confirm('<?php echo e(__('messages.confirm_mark_unused')); ?>')">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('PATCH'); ?>
                                                                <button type="submit" class="btn btn-outline-success btn-sm mb-1">
                                                                    <?php echo e(__('messages.mark_unused')); ?>

                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cardnumbers-activate')): ?>
                                                        <!-- Toggle Activate Button -->
                                                        <form action="<?php echo e(route('card-numbers.toggle-activate', $cardNumber)); ?>" 
                                                            method="POST" 
                                                            style="display: inline-block;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PATCH'); ?>
                                                            <?php if($cardNumber->activate == 1): ?>
                                                                <button type="submit" class="btn btn-warning btn-sm">
                                                                    <?php echo e(__('messages.deactivate')); ?>

                                                                </button>
                                                            <?php else: ?>
                                                                <button type="submit" class="btn btn-secondary btn-sm">
                                                                    <?php echo e(__('messages.activate')); ?>

                                                                </button>
                                                            <?php endif; ?>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>


                        <div class="d-flex justify-content-center">
                            <?php echo e($cardNumbers->appends(request()->query())->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p class="text-muted"><?php echo e(__('messages.no_card_numbers_found')); ?></p>
                            <form action="<?php echo e(route('cards.regenerate-numbers', $card)); ?>" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('<?php echo e(__('messages.confirm_regenerate')); ?>')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('POST'); ?>
                                <button type="submit" class="btn btn-primary">
                                    <?php echo e(__('messages.generate_numbers')); ?>

                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/cards/card-numbers.blade.php ENDPATH**/ ?>