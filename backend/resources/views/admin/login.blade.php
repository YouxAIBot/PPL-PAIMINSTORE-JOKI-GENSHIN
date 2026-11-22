<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Panel Admin Paiminstore</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #111827; font-family: system-ui, sans-serif; }
        .box { background: #fff; width: 360px; padding: 30px; border-radius: 12px; }
        .brand { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .brand span { color: #f59e0b; }
        p.sub { color: #6b7280; font-size: 13px; margin: 0 0 18px; }
        input { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 7px; margin-bottom: 12px; font-size: 14px; }
        button { width: 100%; background: #4f46e5; color: #fff; border: 0; padding: 11px; border-radius: 7px; font-size: 15px; cursor: pointer; }
        .err { background: #fee2e2; color: #991b1b; border-radius: 7px; padding: 9px 12px; font-size: 13px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="brand">PAIMIN<span>STORE</span></div>
        <p class="sub">Masuk panel admin</p>
        @foreach ($errors->all() as $error)
            <div class="err">{{ $error }}</div>
        @endforeach
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
    </div>
</body>
</html>
