<div class="card">
    <form method="POST" action="<?php echo e(isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if(isset($category)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <label>Nama kategori</label>
        <input name="name" value="<?php echo e(old('name', $category->name ?? '')); ?>" required>
        <label>Deskripsi</label>
        <textarea name="description" rows="3"><?php echo e(old('description', $category->description ?? '')); ?></textarea>
        <label style="margin-top:12px"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', isset($category) ? $category->is_active : true)): echo 'checked'; endif; ?>> Aktif</label>
        <div style="margin-top:16px; display:flex; gap:10px;">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn gray" href="<?php echo e(route('admin.categories.index')); ?>">Batal</a>
        </div>
    </form>
</div>
<?php /**PATH /var/www/html/resources/views/admin/categories/_form.blade.php ENDPATH**/ ?>