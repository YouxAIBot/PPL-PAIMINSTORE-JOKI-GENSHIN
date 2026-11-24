<?php $__env->startSection('title', 'Pembayaran'); ?>
<?php $__env->startSection('content'); ?>
<h1>Pembayaran</h1>
<div class="toolbar">
    <form method="GET" action="<?php echo e(route('admin.payments.index')); ?>">
        <div class="field"><label>Metode</label>
            <select name="method">
                <option value="">Semua</option>
                <option value="manual" <?php if($method === 'manual'): echo 'selected'; endif; ?>>manual</option>
                <option value="gateway" <?php if($method === 'gateway'): echo 'selected'; endif; ?>>gateway</option>
            </select>
        </div>
        <div class="field"><label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                <?php $__currentLoopData = ['pending','paid','failed','refunded']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php if($status === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Order</th><th>Metode</th><th>Jumlah</th><th>Refunded</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>#<?php echo e($p->id); ?></td>
                <td><a href="<?php echo e(route('admin.orders.show', $p->order)); ?>"><?php echo e($p->order?->order_no); ?></a></td>
                <td><?php echo e($p->method); ?></td>
                <td>Rp <?php echo e(number_format($p->amount, 0, ',', '.')); ?></td>
                <td>Rp <?php echo e(number_format($p->refunded_amount, 0, ',', '.')); ?></td>
                <td><?php echo $__env->make('admin.partials.status', ['status' => $p->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                <td>
                    <?php if($p->status === 'pending' && $p->method === 'manual'): ?>
                        <form method="POST" action="<?php echo e(route('admin.payments.approve', $p)); ?>" style="display:inline">
                            <?php echo csrf_field(); ?><button class="btn sm green" type="submit">Setujui</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('admin.payments.reject', $p)); ?>" style="display:inline" onsubmit="return confirm('Tolak bukti transfer ini?')">
                            <?php echo csrf_field(); ?><input type="hidden" name="reason" value="Bukti transfer ditolak admin">
                            <button class="btn sm red" type="submit">Tolak</button>
                        </form>
                    <?php endif; ?>
                    <?php if(in_array($p->status, ['paid','refunded'], true) && ($p->amount - $p->refunded_amount) > 0): ?>
                        <form method="POST" action="<?php echo e(route('admin.payments.refund', $p)); ?>" style="display:flex;gap:6px;align-items:center">
                            <?php echo csrf_field(); ?>
                            <input type="number" name="amount" min="1" max="<?php echo e($p->amount - $p->refunded_amount); ?>" placeholder="Rp" style="width:110px;padding:5px">
                            <button class="btn sm gray" type="submit">Refund</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="empty">Tidak ada pembayaran.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($payments->links()); ?>

</div>
<style>table form { margin-top: 0; }</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/payments/index.blade.php ENDPATH**/ ?>