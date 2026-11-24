<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div class="topbar">
    <div>
        <h1>Panel Admin</h1>
        <span class="role-badge">Role: <?php echo e($roleLabel); ?></span>
    </div>
    <div class="top-actions">
        <a class="btn outline" href="<?php echo e(route('admin.reports.export-csv')); ?>">Export Excel</a>
        <a class="btn" href="<?php echo e(route('admin.orders.create')); ?>">Tambah Order</a>
    </div>
</div>

<div class="grid">
    <div class="stat"><small>Total Order Hari Ini</small><strong><?php echo e(number_format($todayOrders)); ?></strong></div>
    <div class="stat"><small>Pendapatan Hari Ini</small><strong>Rp <?php echo e(number_format($todayRevenue, 0, ',', '.')); ?></strong></div>
    <div class="stat"><small>Antrian</small><strong><?php echo e(number_format($queueCount)); ?></strong></div>
    <div class="stat"><small>Perlu Verifikasi</small><strong><?php echo e(number_format($needVerification)); ?></strong></div>
</div>

<div class="card">
    <h2>Daftar Antrian Aktif</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Order</th><th>Pengguna</th><th>Paket</th><th>Prioritas</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $activeQueue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><a href="<?php echo e(route('admin.orders.show', $o)); ?>"><?php echo e($o->order_no); ?></a></td>
                    <td><?php echo e($o->user?->name); ?></td>
                    <td><?php echo e($o->package?->name); ?></td>
                    <td><?php echo e($o->queue_priority); ?></td>
                    <td><?php echo $__env->make('admin.partials.status', ['status' => $o->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                    <td><a class="btn sm" href="<?php echo e(route('admin.orders.show', $o)); ?>">Kelola</a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="empty">Tidak ada antrian aktif.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>Verifikasi Pembayaran &amp; Penugasan Booster</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Order</th><th>Pengguna</th><th>Paket</th><th>Metode</th><th>Jumlah</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $verifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($p->order?->order_no); ?></td>
                    <td><?php echo e($p->order?->user?->name); ?></td>
                    <td><?php echo e($p->order?->package?->name); ?></td>
                    <td><?php echo e($p->method); ?></td>
                    <td>Rp <?php echo e(number_format($p->amount, 0, ',', '.')); ?></td>
                    <td>
                        <form method="POST" action="<?php echo e(route('admin.payments.approve', $p)); ?>" style="display:inline">
                            <?php echo csrf_field(); ?>
                            <button class="btn sm green" type="submit">Verifikasi</button>
                        </form>
                        <a class="btn sm gray" href="<?php echo e(route('admin.orders.show', $p->order)); ?>">Detail</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="empty">Tidak ada pembayaran perlu diverifikasi.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>