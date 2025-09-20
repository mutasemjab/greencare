<?php $__env->startSection('title'); ?>
<?php echo e(__('messages.Home')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentheader'); ?>
<?php echo e(__('messages.Home')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentheaderlink'); ?>
<a href="<?php echo e(route('admin.dashboard')); ?>"> <?php echo e(__('messages.Home')); ?> </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentheaderactive'); ?>
<?php echo e(__('messages.Show')); ?>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
<div class="row" style="background-image: url(<?php echo e(asset('assets/admin/imgs/dash.jpg')); ?>) ;background-size:cover;background-repeate:ni-repeate; min-height:600px;">

</div>


<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>