<div class="card">
    <form method="POST" action="<?php echo e(isset($package) ? route('admin.packages.update', $package) : route('admin.packages.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if(isset($package)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <div class="row">
            <div>
                <label>Kategori</label>
                <select name="category_id" required>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if(old('category_id', $package->category_id ?? null) == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label>Nama paket</label>
                <input name="name" value="<?php echo e(old('name', $package->name ?? '')); ?>" required>
            </div>
        </div>
        <label>Tier</label>
        <input name="tier" value="<?php echo e(old('tier', $package->tier ?? '')); ?>" placeholder="mis. Dasar, Menengah, Tinggi">
        <label>Deskripsi</label>
        <textarea name="description" rows="3"><?php echo e(old('description', $package->description ?? '')); ?></textarea>
        <div class="row">
            <div>
                <label>Harga (Rp)</label>
                <input type="number" min="0" step="1000" name="price" value="<?php echo e(old('price', $package->price ?? '')); ?>" required>
            </div>
            <div>
                <label>Estimasi durasi (jam)</label>
                <input type="number" min="1" name="estimated_hours" value="<?php echo e(old('estimated_hours', $package->estimated_hours ?? '')); ?>" required>
            </div>
        </div>
        <label style="margin-top:12px"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', isset($package) ? $package->is_active : true)): echo 'checked'; endif; ?>> Aktif</label>
        <div style="margin-top:16px; display:flex; gap:10px;">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn gray" href="<?php echo e(route('admin.packages.index')); ?>">Batal</a>
        </div>
    </form>
</div>
<?php /**PATH /var/www/html/resources/views/admin/packages/_form.blade.php ENDPATH**/ ?>