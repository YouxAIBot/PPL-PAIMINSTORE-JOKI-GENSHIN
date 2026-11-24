<?php $__env->startSection('title', 'Tambah Kategori'); ?>
<?php $__env->startSection('content'); ?>
<h1>Tambah Kategori Joki</h1>
<?php echo $__env->make('admin.categories._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/categories/create.blade.php ENDPATH**/ ?>