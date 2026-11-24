<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#F0F9FF">
    <title><?php echo $__env->yieldContent('title', 'Panel Admin'); ?> · Paiminstore</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, "Segoe UI", sans-serif; background: #eef1f6; color: #1f2937; }
        .shell { display: flex; min-height: 100vh; }
        aside { width: 220px; background: #111827; color: #d1d5db; padding: 20px 0; flex-shrink: 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .brand { font-size: 18px; font-weight: 700; letter-spacing: .5px; color: #fff; padding: 0 20px 18px; border-bottom: 1px solid #374151; }
        .brand span { color: #f59e0b; }
        nav a { display: block; padding: 11px 20px; text-decoration: none; color: #d1d5db; font-size: 14px; }
        nav a:hover, nav a.active { background: #1f2937; color: #fff; }
        .logout { padding: 16px 20px; border-top: 1px solid #374151; margin-top: 20px; }
        .logout button { width: 100%; background: transparent; color: #fca5a5; border: 1px solid #7f1d1d; padding: 8px; border-radius: 6px; cursor: pointer; }
        main { flex: 1; padding: 26px 30px; max-width: calc(100vw - 220px); }
        h1 { font-size: 22px; margin: 0 0 18px; }
        h2 { font-size: 16px; margin: 0 0 12px; }
        .card { background: #fff; border-radius: 10px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(15,23,42,.08); margin-bottom: 18px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 18px; }
        .stat { background: #fff; border-radius: 10px; padding: 16px 18px; box-shadow: 0 1px 3px rgba(15,23,42,.08); }
        .stat small { color: #6b7280; }
        .stat strong { display: block; font-size: 22px; margin-top: 6px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 9px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th { color: #6b7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .3px; }
        .btn { display: inline-block; background: #1d4ed8; color: #fff; border: 0; padding: 9px 14px; border-radius: 7px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn.sm { padding: 5px 9px; font-size: 12px; border-radius: 6px; }
        .btn.gray { background: #6b7280; } .btn.red { background: #dc2626; } .btn.green { background: #16a34a; }
        .btn.outline { background: #fff; color: #1e40af; border: 1px solid #bfdbfe; }
        input, select, textarea { width: 100%; padding: 9px 10px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px; background: #fff; }
        label { display: block; font-size: 13px; color: #374151; margin: 12px 0 5px; }
        .row { display: flex; gap: 12px; flex-wrap: wrap; }
        .row > * { flex: 1; min-width: 180px; }
        .alert { border-radius: 8px; padding: 11px 14px; margin-bottom: 14px; font-size: 14px; }
        .alert.success { background: #dcfce7; color: #166534; }
        .alert.err { background: #fee2e2; color: #991b1b; }
        .alert.warn { background: #fef3c7; color: #92400e; }
        .badge { display: inline-block; padding: 3px 9px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge.green { background: #dcfce7; color: #166534; } .badge.blue { background: #dbeafe; color: #1e40af; }
        .badge.amber { background: #fef3c7; color: #92400e; } .badge.red { background: #fee2e2; color: #991b1b; }
        .badge.muted { background: #e5e7eb; color: #374151; }
        .bar-chart { display: flex; align-items: flex-end; gap: 8px; height: 160px; margin-top: 12px; }
        .bar-col { flex: 1; text-align: center; }
        .bar { background: #1d4ed8; border-radius: 4px 4px 0 0; margin: 0 auto; width: 70%; min-height: 2px; }
        .bar-label { font-size: 11px; color: #6b7280; margin-top: 6px; }
        .toolbar { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 14px; }
        .toolbar form { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; }
        .toolbar .field { min-width: 140px; }
        .toolbar label { margin: 0 0 4px; }
        .chat-msg { padding: 9px 12px; border-radius: 8px; background: #f3f4f6; margin-bottom: 8px; max-width: 70%; }
        .chat-msg.admin { background: #dbeafe; margin-left: auto; }
        .chat-msg small { color: #6b7280; display: block; margin-top: 4px; font-size: 11px; }
        .empty { color: #6b7280; padding: 12px 0; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
        .topbar h1 { margin: 0; }
        .top-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .role-badge { background: #dbeafe; color: #1e40af; border-radius: 999px; padding: 4px 10px; font-size: 12px; font-weight: 600; }
        .table-wrap { overflow-x: auto; }
        .nav-toggle { display: none; position: fixed; top: 14px; left: 14px; z-index: 50; width: 42px; height: 38px; background: #111827; border: 1px solid #374151; border-radius: 8px; padding: 9px; flex-direction: column; justify-content: space-between; cursor: pointer; }
        .nav-toggle span { display: block; height: 2px; background: #fff; border-radius: 2px; }
        .nav-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.45); z-index: 35; }
        .nav-overlay.show { display: block; }
        @media (max-width: 900px) {
            .shell { display: block; }
            aside { position: fixed; left: 0; top: 0; z-index: 40; transform: translateX(-105%); transition: transform .2s ease; }
            aside.open { transform: translateX(0); }
            main { max-width: 100%; padding: 70px 16px 24px; }
            .nav-toggle { display: flex; }
            .grid { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); }
            .topbar { align-items: flex-start; }
        }
    </style>
</head>
<body>
<button class="nav-toggle" id="navToggle" aria-label="Buka menu"><span></span><span></span><span></span></button>
<div class="nav-overlay" id="navOverlay"></div>
<div class="shell">
    <aside id="adminSidebar">
        <div class="brand">PAIMIN<span>STORE</span></div>
        <nav>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.dashboard*')]); ?>">Dashboard</a>
            <a href="<?php echo e(route('admin.orders.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.orders.*')]); ?>">Order</a>
            <a href="<?php echo e(route('admin.boosters.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.boosters.*')]); ?>">Booster</a>
            <a href="<?php echo e(route('admin.payments.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.payments.*')]); ?>">Pembayaran</a>
            <a href="<?php echo e(route('admin.categories.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.categories.*')]); ?>">Kategori</a>
            <a href="<?php echo e(route('admin.packages.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.packages.*')]); ?>">Paket</a>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.users.*')]); ?>">Pengguna</a>
            <a href="<?php echo e(route('admin.chats.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.chats.*')]); ?>">Chat</a>
            <a href="<?php echo e(route('admin.reports.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.reports.*')]); ?>">Laporan</a>
            <a href="<?php echo e(route('admin.leaderboard.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.leaderboard.*')]); ?>">Leaderboard</a>
            <a href="<?php echo e(route('admin.logs.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->routeIs('admin.logs.*')]); ?>">Log Aktivitas</a>
        </nav>
        <div class="logout">
            <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit">Keluar</button>
            </form>
        </div>
    </aside>
    <main>
        <?php if(session('success')): ?>
            <div class="alert success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('temp_password')): ?>
            <div class="alert warn">Password sementara: <strong><?php echo e(session('temp_password')); ?></strong> — catat sekarang, hanya tampil sekali.</div>
        <?php endif; ?>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="alert err"><?php echo e($error); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
<script>
    const navToggle = document.getElementById('navToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const navOverlay = document.getElementById('navOverlay');
    const closeNav = () => { adminSidebar.classList.remove('open'); navOverlay.classList.remove('show'); };
    navToggle.addEventListener('click', () => {
        adminSidebar.classList.toggle('open');
        navOverlay.classList.toggle('show');
    });
    navOverlay.addEventListener('click', closeNav);
    adminSidebar.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', closeNav));
</script>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/layouts/admin.blade.php ENDPATH**/ ?>