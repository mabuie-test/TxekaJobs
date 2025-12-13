<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Txeka Jobs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --txeka-primary: #2563eb;
            --txeka-secondary: #f97316;
            --txeka-bg: radial-gradient(circle at 20% 20%, rgba(37,99,235,0.10), transparent 35%),
                          radial-gradient(circle at 80% 0%, rgba(249,115,22,0.12), transparent 30%),
                          #f6f8fb;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--txeka-bg);
        }
        .navbar-glass {
            backdrop-filter: blur(14px);
            background: rgba(37, 99, 235, 0.92);
            box-shadow: 0 10px 30px rgba(37,99,235,0.25);
        }
        .content-card {
            background: #fff;
            border: 1px solid rgba(37,99,235,0.05);
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(15,23,42,0.06);
        }
        .badge-glow {
            background: linear-gradient(120deg, #2563eb, #f97316);
            color: #fff;
            box-shadow: 0 8px 24px rgba(37,99,235,0.35);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-glass mb-4 sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="/images/logo.svg" alt="Txeka Jobs" height="34">
            <span>Txeka Jobs</span>
        </a>
        <div class="d-flex gap-2 align-items-center">
            @auth
                <span class="navbar-text text-white small">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-light" type="submit">Sair</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">Entrar</a>
                <a href="{{ route('register.cliente') }}" class="btn btn-sm btn-light text-primary">Criar conta</a>
            @endauth
        </div>
    </div>
</nav>
<main class="container pb-5">
    @if(session('status'))
        <div class="alert alert-success content-card p-3">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger content-card p-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="content-card p-4">
        @yield('content')
    </div>
</main>
</body>
</html>
