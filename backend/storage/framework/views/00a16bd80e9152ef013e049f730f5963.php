<?php $__env->startSection('title', 'Paket Joki'); ?>
<?php $__env->startSection('content'); ?>
<div class="toolbar" style="justify-content: space-between; align-items: center;">
    <h1 style="margin:0">Paket Joki</h1>
    <a class="btn" href="<?php echo e(route('admin.packages.create')); ?>">+ Tambah Paket</a>
</div>
<div class="card">
    <table>
        <thead><tr><th>Paket</th><th>Kategori</th><th>Harga</th><th>Estimasi</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($p->name); ?></td>
                <td><?php echo e($p->category?->name); ?></td>
                <td>Rp <?php echo e(number_format($p->price, 0, ',', '.')); ?></td>
                <td><?php echo e($p->estimated_hours); ?> jam</td>
                <td><?php echo $__env->make('admin.partials.status', ['status' => $p->is_active ? 'active' : 'banned'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                <td>
                    <a class="btn sm gray" href="<?php echo e(route('admin.packages.edit', $p)); ?>">Ubah</a>
                    <form method="POST" action="<?php echo e(route('admin.packages.destroy', $p)); ?>" style="display:inline" onsubmit="return confirm('Hapus paket ini?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn sm red" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="empty">Belum ada paket.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($packages->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/packages/index.blade.php ENDPATH**/ ?>