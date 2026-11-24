<?php $__env->startSection('title', 'Booster'); ?>
<?php $__env->startSection('content'); ?>
<h1>Booster</h1>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Booster</th><th>Status</th><th>Order Aktif</th><th>Selesai</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $boosters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($b->name); ?></td>
                    <td><?php echo $__env->make('admin.partials.status', ['status' => $b->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                    <td><?php echo e($b->active_orders); ?></td>
                    <td><?php echo e($b->completed_orders); ?></td>
                    <td><a class="btn sm" href="<?php echo e(route('admin.boosters.show', $b)); ?>">Order</a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="empty">Belum ada booster.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/boosters/index.blade.php ENDPATH**/ ?>