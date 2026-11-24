<?php $__env->startSection('title', 'Order'); ?>
<?php $__env->startSection('content'); ?>
<h1>Order</h1>
<div class="toolbar">
    <form method="GET" action="<?php echo e(route('admin.orders.index')); ?>">
        <div class="field"><label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php if($status === $s): echo 'selected'; endif; ?>><?php echo e(str_replace('_', ' ', $s)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="field"><label>Cari no. order</label><input name="search" value="<?php echo e(request('search')); ?>"></div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>No. Order</th><th>Pengguna</th><th>Paket</th><th>Total</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($o->order_no); ?></td>
                <td><?php echo e($o->user?->name); ?></td>
                <td><?php echo e($o->package?->name); ?></td>
                <td>Rp <?php echo e(number_format($o->total_amount, 0, ',', '.')); ?></td>
                <td><?php echo $__env->make('admin.partials.status', ['status' => $o->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                <td><?php echo e($o->created_at?->format('d M Y H:i')); ?></td>
                <td><a class="btn sm" href="<?php echo e(route('admin.orders.show', $o)); ?>">Detail</a></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="empty">Tidak ada order.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($orders->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/orders/index.blade.php ENDPATH**/ ?>