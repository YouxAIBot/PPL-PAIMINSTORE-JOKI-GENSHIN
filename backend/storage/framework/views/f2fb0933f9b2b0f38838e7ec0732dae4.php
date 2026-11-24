<?php
$map = [
    'pending_payment' => 'muted', 'pending' => 'muted',
    'queued' => 'blue', 'in_progress' => 'amber',
    'completed' => 'green', 'paid' => 'green',
    'cancelled' => 'red', 'failed' => 'red', 'refunded' => 'red',
    'banned' => 'red', 'active' => 'green',
];
?>
<span class="badge <?php echo e($map[$status] ?? 'muted'); ?>"><?php echo e(str_replace('_', ' ', $status)); ?></span>
<?php /**PATH /var/www/html/resources/views/admin/partials/status.blade.php ENDPATH**/ ?>