<?php $__env->startSection('title', 'Chat Pengguna'); ?>
<?php $__env->startSection('content'); ?>
<h1>Inbox Chat</h1>
<div class="card">
    <table>
        <thead><tr><th>Pengguna</th><th>Pesan Terakhir</th><th>Belum Dibaca</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $last = $c->messages->first(); ?>
            <tr>
                <td><?php echo e($c->user?->name); ?></td>
                <td><?php echo e($last?->body); ?></td>
                <td><?php if($c->admin_unread): ?> <span class="badge blue"><?php echo e($c->admin_unread); ?></span> <?php else: ?> - <?php endif; ?></td>
                <td><a class="btn sm" href="<?php echo e(route('admin.chats.show', $c)); ?>">Buka</a></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="empty">Belum ada percakapan.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($conversations->links()); ?>

</div>
<style>table td { max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/chats/index.blade.php ENDPATH**/ ?>