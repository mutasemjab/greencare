<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?php echo e(__('messages.report_templates')); ?></h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report-template-add')): ?>
                        <a href="<?php echo e(route('report-templates.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo e(__('messages.add_report_template')); ?>

                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                

                    <!-- Search and Filter Form -->
                    <form method="GET" action="<?php echo e(route('report-templates.index')); ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="<?php echo e(__('messages.search_templates')); ?>"
                                       value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="created_for" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_types')); ?></option>
                                    <option value="doctor" <?php echo e(request('created_for') == 'doctor' ? 'selected' : ''); ?>><?php echo e(__('messages.doctor')); ?></option>
                                    <option value="nurse" <?php echo e(request('created_for') == 'nurse' ? 'selected' : ''); ?>><?php echo e(__('messages.nurse')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> <?php echo e(__('messages.search')); ?>

                                    </button>
                                    <a href="<?php echo e(route('report-templates.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> <?php echo e(__('messages.clear')); ?>

                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('messages.title_en')); ?></th>
                                    <th><?php echo e(__('messages.title_ar')); ?></th>
                                    <th><?php echo e(__('messages.created_for')); ?></th>
                                    <th><?php echo e(__('messages.sections_count')); ?></th>
                                    <th><?php echo e(__('messages.created_at')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration + ($templates->currentPage() - 1) * $templates->perPage()); ?></td>
                                        <td>
                                            <strong><?php echo e($template->title_en); ?></strong>
                                        </td>
                                        <td><?php echo e($template->title_ar); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($template->created_for == 'doctor' ? 'primary' : 'success'); ?>">
                                                <?php echo e($template->created_for_text); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?php echo e($template->sections->count()); ?> <?php echo e(__('messages.sections')); ?></span>
                                        </td>
                                        <td><?php echo e($template->created_at->format('Y-m-d H:i')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report-template-table')): ?>
                                                    <a href="<?php echo e(route('report-templates.show', $template)); ?>" 
                                                       class="btn btn-sm btn-info" title="<?php echo e(__('messages.view')); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report-template-edit')): ?>
                                                    <a href="<?php echo e(route('report-templates.edit', $template)); ?>" 
                                                       class="btn btn-sm btn-warning" title="<?php echo e(__('messages.edit')); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('report-templates.duplicate', $template)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-secondary"
                                                                title="<?php echo e(__('messages.duplicate')); ?>"
                                                                onclick="return confirm('<?php echo e(__('messages.confirm_duplicate_template')); ?>')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report-template-delete')): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="<?php echo e(__('messages.delete')); ?>"
                                                            data-toggle="modal" 
                                                            data-target="#deleteModal<?php echo e($template->id); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report-template-delete')): ?>
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo e($template->id); ?>" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"><?php echo e(__('messages.confirm_delete')); ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php echo e(__('messages.are_you_sure_delete_template')); ?> "<strong><?php echo e($template->title_en); ?></strong>"?
                                                                <br><small class="text-muted"><?php echo e(__('messages.delete_template_warning')); ?></small>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    <?php echo e(__('messages.cancel')); ?>

                                                                </button>
                                                                <form action="<?php echo e(route('report-templates.destroy', $template)); ?>" method="POST" class="d-inline">
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
                                        <td colspan="7" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                                <p class="text-muted"><?php echo e(__('messages.no_templates_found')); ?></p>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report-template-add')): ?>
                                                    <a href="<?php echo e(route('report-templates.create')); ?>" class="btn btn-primary">
                                                        <?php echo e(__('messages.add_first_template')); ?>

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
                    <?php if($templates->hasPages()): ?>
                        <div class="d-flex justify-content-center">
                            <?php echo e($templates->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/report-templates/index.blade.php ENDPATH**/ ?>