<?php $__env->startSection('title', 'Tambah Order'); ?>
<?php $__env->startSection('content'); ?>
<div class="topbar">
    <h1>Tambah Order</h1>
    <div class="top-actions">
        <a class="btn gray" href="<?php echo e(route('admin.orders.index')); ?>">Kembali</a>
    </div>
</div>

<div class="card">
    <form method="POST" action="<?php echo e(route('admin.orders.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div>
                <label>Pelanggan</label>
                <select name="user_id" required>
                    <option value="">Pilih pelanggan</option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if(old('user_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?> (<?php echo e($c->email); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label>Paket</label>
                <select name="package_id" required>
                    <option value="">Pilih paket</option>
                    <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php if(old('package_id') == $p->id): echo 'selected'; endif; ?>>
                            <?php echo e($p->name); ?> — <?php echo e($p->category?->name); ?> — Rp <?php echo e(number_format($p->price, 0, ',', '.')); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div>
                <label>UID Game</label>
                <input name="uid" value="<?php echo e(old('uid')); ?>" required>
            </div>
            <div>
                <label>Server</label>
                <select name="server" required>
                    <?php $__currentLoopData = ['asia', 'europe', 'america', 'twhkmo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $server): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($server); ?>" <?php if(old('server') === $server): echo 'selected'; endif; ?>><?php echo e(strtoupper($server)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div>
                <label>Username / Email Akun Game</label>
                <input type="text" name="credential_username" required maxlength="255" autocomplete="off" value="<?php echo e(old('credential_username')); ?>">
            </div>
            <div>
                <label>Password Akun Game</label>
                <input type="password" name="credential_password" required maxlength="255" autocomplete="new-password">
            </div>
        </div>

        <div class="row">
            <div>
                <label>Booster (opsional)</label>
                <select name="booster_id">
                    <option value="">Belum ditugaskan</option>
                    <?php $__currentLoopData = $boosters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($b->id); ?>" <?php if(old('booster_id') == $b->id): echo 'selected'; endif; ?>><?php echo e($b->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label>Status Awal</label>
                <select name="status" required>
                    <option value="pending_payment" <?php if(old('status') === 'pending_payment'): echo 'selected'; endif; ?>>Menunggu Pembayaran</option>
                    <option value="queued" <?php if(old('status') === 'queued'): echo 'selected'; endif; ?>>Antrian</option>
                </select>
            </div>
            <div>
                <label>Prioritas Antrian</label>
                <input name="queue_priority" type="number" value="<?php echo e(old('queue_priority', 0)); ?>">
            </div>
        </div>

        <label>Catatan Pelanggan</label>
        <textarea name="customer_notes" rows="2"><?php echo e(old('customer_notes')); ?></textarea>

        <div style="margin-top:16px">
            <button class="btn green" type="submit">Simpan Order</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/orders/create.blade.php ENDPATH**/ ?>