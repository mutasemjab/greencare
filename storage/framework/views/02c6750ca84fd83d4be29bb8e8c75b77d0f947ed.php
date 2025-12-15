<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?php echo e(__('messages.cards_list')); ?></h4>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('card-add')): ?>
                    <a href="<?php echo e(route('cards.create')); ?>" class="btn btn-primary">
                        <?php echo e(__('messages.create_card')); ?>

                    </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('card-table')): ?>
                    <?php if($cards->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><?php echo e(__('messages.id')); ?></th>
                                        <th><?php echo e(__('messages.pos')); ?></th>
                                        <th><?php echo e(__('messages.name')); ?></th>
                                        <th><?php echo e(__('messages.number_of_use_for_one_card')); ?></th>
                                        <th><?php echo e(__('messages.number_of_cards')); ?></th>
                                        <th><?php echo e(__('messages.generated_numbers')); ?></th>
                                        <th><?php echo e(__('messages.active_inactive')); ?></th>
                                        <th><?php echo e(__('messages.used_unused')); ?></th>
                                        <th><?php echo e(__('messages.created_at')); ?></th>
                                        <th><?php echo e(__('messages.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($card->id); ?></td>
                                            <td>
                                                <?php if($card->pos): ?>
                                                    <span class="badge bg-info"><?php echo e($card->pos->name); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo e(__('messages.no_pos')); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($card->name); ?></td>
                                            <td><?php echo e(number_format($card->number_of_use_for_one_card, 2)); ?></td>
                                            
                                            <td>
                                                <span class="badge bg-primary"><?php echo e(number_format($card->number_of_cards)); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?php echo e($card->cardNumbers->count()); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?php echo e($card->active_card_numbers_count); ?></span> /
                                                <span class="badge bg-warning"><?php echo e($card->cardNumbers->count() - $card->active_card_numbers_count); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger"><?php echo e($card->used_card_numbers_count); ?></span> /
                                                <span class="badge bg-info"><?php echo e($card->unused_card_numbers_count); ?></span>
                                            </td>
                                            <td><?php echo e($card->created_at->format('Y-m-d')); ?></td>
                                            <td>
                                                <div class="btn-group-vertical" role="group">
                                                    <a href="<?php echo e(route('cards.show', $card)); ?>" 
                                                       class="btn btn-info btn-sm mb-1">
                                                        <?php echo e(__('messages.view')); ?>

                                                    </a>
                                                    <a href="<?php echo e(route('cards.card-numbers', $card)); ?>" 
                                                       class="btn btn-secondary btn-sm mb-1">
                                                        <?php echo e(__('messages.view_numbers')); ?>

                                                    </a>

                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('card-edit')): ?>
                                                    <a href="<?php echo e(route('cards.edit', $card)); ?>" 
                                                       class="btn btn-warning btn-sm mb-1">
                                                        <?php echo e(__('messages.edit')); ?>

                                                    </a>
                                                    <?php endif; ?>

                                                    <form action="<?php echo e(route('cards.regenerate-numbers', $card)); ?>" 
                                                          method="POST" 
                                                          style="display: inline-block;"
                                                          onsubmit="return confirm('<?php echo e(__('messages.confirm_regenerate')); ?>')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('POST'); ?>
                                                        <button type="submit" class="btn btn-success btn-sm mb-1">
                                                            <?php echo e(__('messages.regenerate')); ?>

                                                        </button>
                                                    </form>
                                                    
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('card-delete')): ?>
                                                    <form action="<?php echo e(route('cards.destroy', $card)); ?>" 
                                                          method="POST" 
                                                          style="display: inline-block;"
                                                          onsubmit="return confirm('<?php echo e(__('messages.confirm_delete')); ?>')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <?php echo e(__('messages.delete')); ?>

                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            <?php echo e($cards->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p class="text-muted"><?php echo e(__('messages.no_cards_found')); ?></p>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('card-add')): ?>
                            <a href="<?php echo e(route('cards.create')); ?>" class="btn btn-primary">
                                <?php echo e(__('messages.create_first_card')); ?>

                            </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/cards/index.blade.php ENDPATH**/ ?>