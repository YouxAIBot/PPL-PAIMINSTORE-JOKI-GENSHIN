<?php $__env->startSection('title', 'Log Aktivitas'); ?>
<?php $__env->startSection('content'); ?>
<h1>Log Aktivitas Admin</h1>
<div class="toolbar">
    <form method="GET" action="<?php echo e(route('admin.logs.index')); ?>">
        <div class="field"><label>Aksi</label><input name="action" value="<?php echo e($action); ?>" placeholder="mis. order."></div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>Waktu</th><th>Aktor</th><th>Aksi</th><th>Entitas</th><th>IP</th><th>Meta</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($l->created_at?->format('d M Y H:i:s')); ?></td>
                <td><?php echo e($l->actor?->name ?? 'sistem'); ?></td>
                <td><code><?php echo e($l->action); ?></code></td>
                <td><?php echo e($l->entity_type); ?> <?php echo e($l->entity_id ? '#'.$l->entity_id : ''); ?></td>
                <td><?php echo e($l->ip); ?></td>
                <td><small><?php echo e(json_encode($l->meta)); ?></small></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="empty">Belum ada log.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($logs->links()); ?>

</div>
<style>table td { max-width: 220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/logs/index.blade.php ENDPATH**/ ?>