<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?php echo e(__('messages.card_details')); ?></h4>
                    <div>
                        <a href="<?php echo e(route('cards.card-numbers', $card)); ?>" class="btn btn-info btn-sm">
                            <?php echo e(__('messages.view_numbers')); ?>

                        </a>
                        <a href="<?php echo e(route('cards.edit', $card)); ?>" class="btn btn-warning btn-sm">
                            <?php echo e(__('messages.edit')); ?>

                        </a>
                        <a href="<?php echo e(route('cards.index')); ?>" class="btn btn-secondary btn-sm">
                            <?php echo e(__('messages.back_to_list')); ?>

                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="40%"><?php echo e(__('messages.id')); ?></th>
                                        <td><?php echo e($card->id); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(__('messages.pos')); ?></th>
                                        <td>
                                            <?php if($card->pos): ?>
                                                <span class="badge bg-info"><?php echo e($card->pos->name); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e(__('messages.no_pos')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                 
                                    <tr>
                                        <th><?php echo e(__('messages.name')); ?></th>
                                        <td><?php echo e($card->name); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(__('messages.price')); ?></th>
                                        <td><strong><?php echo e(number_format($card->price, 2)); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(__('messages.number_of_cards')); ?></th>
                                        <td><span class="badge bg-primary"><?php echo e(number_format($card->number_of_cards)); ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(__('messages.created_at')); ?></th>
                                        <td><?php echo e($card->created_at->format('Y-m-d H:i:s')); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(__('messages.updated_at')); ?></th>
                                        <td><?php echo e($card->updated_at->format('Y-m-d H:i:s')); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><?php echo e(__('messages.card_numbers_statistics')); ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h5 class="text-success mb-1"><?php echo e($card->cardNumbers->count()); ?></h5>
                                                <small><?php echo e(__('messages.total_generated')); ?></small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h5 class="text-primary mb-1"><?php echo e($card->active_card_numbers_count); ?></h5>
                                                <small><?php echo e(__('messages.active_numbers')); ?></small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <h5 class="text-danger mb-1"><?php echo e($card->used_card_numbers_count); ?></h5>
                                                <small><?php echo e(__('messages.used_numbers')); ?></small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <h5 class="text-info mb-1"><?php echo e($card->unused_card_numbers_count); ?></h5>
                                                <small><?php echo e(__('messages.unused_numbers')); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5><?php echo e(__('messages.recent_card_numbers')); ?></h5>
                        <?php if($card->cardNumbers->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th><?php echo e(__('messages.number')); ?></th>
                                            <th><?php echo e(__('messages.status')); ?></th>
                                            <th><?php echo e(__('messages.activate_status')); ?></th>
                                            <th><?php echo e(__('messages.created_at')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $card->cardNumbers->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cardNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($cardNumber->number); ?></td>
                                                <td>
                                                    <?php if($cardNumber->status == 1): ?>
                                                        <span class="badge bg-danger"><?php echo e(__('messages.used')); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success"><?php echo e(__('messages.not_used')); ?></span>
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
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if($card->cardNumbers->count() > 10): ?>
                                <div class="text-center mt-2">
                                    <a href="<?php echo e(route('cards.card-numbers', $card)); ?>" class="btn btn-outline-primary">
                                        <?php echo e(__('messages.view_all_numbers')); ?> (<?php echo e($card->cardNumbers->count()); ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <?php echo e(__('messages.no_card_numbers_generated')); ?>

                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <form action="<?php echo e(route('cards.regenerate-numbers', $card)); ?>" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('<?php echo e(__('messages.confirm_regenerate')); ?>')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('POST'); ?>
                                <button type="submit" class="btn btn-success me-md-2">
                                    <?php echo e(__('messages.regenerate_numbers')); ?>

                                </button>
                            </form>
                            <a href="<?php echo e(route('cards.edit', $card)); ?>" class="btn btn-warning me-md-2">
                                <?php echo e(__('messages.edit')); ?>

                            </a>
                            <form action="<?php echo e(route('cards.destroy', $card)); ?>" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('<?php echo e(__('messages.confirm_delete')); ?>')">
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
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/cards/show.blade.php ENDPATH**/ ?>