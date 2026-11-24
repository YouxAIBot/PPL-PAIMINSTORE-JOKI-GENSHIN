<?php $__env->startSection('title', 'Pengguna'); ?>
<?php $__env->startSection('content'); ?>
<h1>Pengguna</h1>
<div class="toolbar">
    <form method="GET" action="<?php echo e(route('admin.users.index')); ?>">
        <div class="field"><label>Peran</label>
            <select name="role">
                <option value="">Semua</option>
                <?php $__currentLoopData = ['user','admin','booster']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($r); ?>" <?php if($role === $r): echo 'selected'; endif; ?>><?php echo e($r); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="field"><label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                <?php $__currentLoopData = ['active','banned']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php if($status === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="field"><label>Cari nama/email</label><input name="search" value="<?php echo e(request('search')); ?>"></div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Status</th><th>Bergabung</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($u->name); ?></td>
                <td><?php echo e($u->email); ?></td>
                <td><?php echo e($u->role); ?></td>
                <td><?php echo $__env->make('admin.partials.status', ['status' => $u->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                <td><?php echo e($u->created_at?->format('d M Y')); ?></td>
                <td><a class="btn sm" href="<?php echo e(route('admin.users.show', $u)); ?>">Detail</a></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="empty">Tidak ada pengguna.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($users->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/users/index.blade.php ENDPATH**/ ?>