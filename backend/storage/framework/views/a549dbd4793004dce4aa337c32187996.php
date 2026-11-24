<?php $__env->startSection('title', 'Kategori Joki'); ?>
<?php $__env->startSection('content'); ?>
<div class="toolbar" style="justify-content: space-between; align-items: center;">
    <h1 style="margin:0">Kategori Joki</h1>
    <a class="btn" href="<?php echo e(route('admin.categories.create')); ?>">+ Tambah Kategori</a>
</div>
<div class="card">
    <table>
        <thead><tr><th>Nama</th><th>Slug</th><th>Paket</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($c->name); ?></td>
                <td><?php echo e($c->slug); ?></td>
                <td><?php echo e($c->packages_count); ?></td>
                <td><?php echo $__env->make('admin.partials.status', ['status' => $c->is_active ? 'active' : 'banned'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                <td>
                    <a class="btn sm gray" href="<?php echo e(route('admin.categories.edit', $c)); ?>">Ubah</a>
                    <form method="POST" action="<?php echo e(route('admin.categories.destroy', $c)); ?>" style="display:inline" onsubmit="return confirm('Hapus kategori ini?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn sm red" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="empty">Belum ada kategori.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($categories->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/categories/index.blade.php ENDPATH**/ ?>