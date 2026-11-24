<?php $__env->startSection('title', 'Laporan'); ?>
<?php $__env->startSection('content'); ?>
<h1>Laporan Mingguan</h1>
<div class="toolbar">
    <form method="GET" action="<?php echo e(route('admin.reports.index')); ?>">
        <div class="field"><label>Dari</label><input type="date" name="start" value="<?php echo e($start->toDateString()); ?>"></div>
        <div class="field"><label>Sampai</label><input type="date" name="end" value="<?php echo e($end->toDateString()); ?>"></div>
        <button class="btn sm" type="submit">Terapkan</button>
    </form>
    <span style="flex:1"></span>
    <a class="btn sm green" href="<?php echo e(route('admin.reports.export-csv', ['start'=>$start->toDateString(), 'end'=>$end->toDateString()])); ?>">Ekspor Excel (CSV)</a>
    <a class="btn sm" href="<?php echo e(route('admin.reports.export-pdf', ['start'=>$start->toDateString(), 'end'=>$end->toDateString()])); ?>">Ekspor PDF</a>
</div>

<div class="grid">
    <div class="stat"><small>Pendapatan</small><strong>Rp <?php echo e(number_format($revenue, 0, ',', '.')); ?></strong></div>
    <div class="stat"><small>Transaksi Sukses</small><strong><?php echo e(number_format($success)); ?></strong></div>
    <div class="stat"><small>Gagal</small><strong><?php echo e(number_format($failed)); ?></strong></div>
    <div class="stat"><small>Refund Penuh</small><strong><?php echo e(number_format($refunded)); ?></strong></div>
</div>

<div class="card">
    <h2>Tren Pendapatan per Minggu (8 minggu)</h2>
    <div class="bar-chart">
        <?php $__currentLoopData = $trend; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bar-col">
                <div class="bar" style="height: <?php echo e(round(($t['total'] / $maxTotal) * 140)); ?>px" title="Rp <?php echo e(number_format($t['total'], 0, ',', '.')); ?>"></div>
                <div class="bar-label"><?php echo e($t['label']); ?></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<div class="card">
    <h2>Rincian per Kategori</h2>
    <table>
        <thead><tr><th>Kategori</th><th>Jumlah Order</th><th>Pendapatan</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $perCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($row['category']); ?></td>
                <td><?php echo e(number_format($row['total_orders'])); ?></td>
                <td>Rp <?php echo e(number_format((float) $row['total'], 0, ',', '.')); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3" class="empty">Belum ada data pada periode ini.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<style>.toolbar { justify-content: flex-start; }</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/reports/index.blade.php ENDPATH**/ ?>