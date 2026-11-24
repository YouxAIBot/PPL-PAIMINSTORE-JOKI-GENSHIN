<?php $__env->startSection('title', 'Leaderboard'); ?>
<?php $__env->startSection('content'); ?>
<h1>Leaderboard</h1>
<div class="toolbar">
    <form method="POST" action="<?php echo e(route('admin.leaderboard.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="field"><label>Periode</label>
            <select name="period">
                <option value="all" <?php if($period === 'all'): echo 'selected'; endif; ?>>Semua waktu</option>
                <option value="month" <?php if($period === 'month'): echo 'selected'; endif; ?>>Bulan ini</option>
                <option value="week" <?php if($period === 'week'): echo 'selected'; endif; ?>>Minggu ini</option>
            </select>
        </div>
        <button class="btn sm" type="submit">Simpan periode</button>
    </form>
    <form method="POST" action="<?php echo e(route('admin.leaderboard.store')); ?>">
        <?php echo csrf_field(); ?><input type="hidden" name="action" value="reset">
        <button class="btn sm red" type="submit" onclick="return confirm('Reset leaderboard ke semua waktu?')">Reset</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>Rank</th><th>Nama</th><th>Total Belanja</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>#<?php echo e($i + 1); ?></td>
                <td><?php echo e($r['name']); ?></td>
                <td>Rp <?php echo e(number_format($r['total'], 0, ',', '.')); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($rows->isEmpty()): ?>
            <tr><td colspan="3" class="empty">Belum ada order selesai.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<style>form { display: inline-block; }</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/leaderboard/index.blade.php ENDPATH**/ ?>