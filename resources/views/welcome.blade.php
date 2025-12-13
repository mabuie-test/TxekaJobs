<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Txeka Jobs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: radial-gradient(circle at 15% 20%, rgba(37,99,235,0.12), transparent 32%),
                        radial-gradient(circle at 85% 10%, rgba(249,115,22,0.15), transparent 30%),
                        #0f172a;
            color: #0f172a;
        }
        .glass {
            background: rgba(255,255,255,0.92);
            border-radius: 22px;
            box-shadow: 0 30px 80px rgba(15,23,42,0.35);
            border: 1px solid rgba(255,255,255,0.25);
            backdrop-filter: blur(10px);
        }
        .hero-video {
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.45);
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .45rem .85rem;
            border-radius: 999px;
            background: linear-gradient(120deg, #2563eb, #f97316);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(37,99,235,0.35);
        }
        .feature-card {
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.35);
            background: rgba(255,255,255,0.96);
            box-shadow: 0 16px 40px rgba(0,0,0,0.08);
            height: 100%;
        }
        .gradient-divider {
            height: 4px;
            width: 60px;
            border-radius: 999px;
            background: linear-gradient(120deg, #2563eb, #f97316);
        }
    </style>
</head>
<body>
<div class="container py-5">
    <header class="mb-5 text-center text-white">
        <div class="pill mb-3">Plataforma premium de serviços locais</div>
        <div class="d-flex justify-content-center mb-2">
            <img src="/images/logo.svg" alt="Txeka Jobs" height="78" class="mb-2">
        </div>
        <h1 class="fw-bold display-6">Experiência mobile-first, pagamentos seguros e reputação transparente</h1>
        <p class="lead text-white-50">Ligamos clientes e prestadores em todo o país com M-Pesa, reservas com escrow, ranking inteligente e backups diários.</p>
    </header>

    <div class="row g-4 align-items-center mb-5">
        <div class="col-lg-7">
            <div class="hero-video">
                <video src="https://cdn.coverr.co/videos/coverr-working-on-a-computer-7505/1080p.mp4" autoplay muted loop playsinline class="w-100" poster="/images/logo.svg"></video>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="glass p-4 text-start">
                <div class="gradient-divider mb-3"></div>
                <h3 class="fw-bold mb-3">Abra a sua conta e receba propostas em minutos</h3>
                <ul class="text-muted fw-semibold mb-4">
                    <li class="mb-2">Email obrigatório com verificação imediata.</li>
                    <li class="mb-2">Pagamentos com M-Pesa, ledger auditável e reservas com escrow.</li>
                    <li class="mb-2">Matching inteligente que aprende com tendências e avaliações.</li>
                </ul>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('register.cliente') }}" class="btn btn-primary btn-lg px-4">Sou Cliente</a>
                    <a href="{{ route('register.prestador') }}" class="btn btn-success btn-lg px-4">Sou Prestador</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light text-dark btn-lg px-4">Já tenho conta</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-4">
            <div class="feature-card p-3">
                <h6 class="fw-semibold">Pagamentos preparados</h6>
                <p class="text-muted mb-1">Gateway M-Pesa pronto, ledger interno e reservas com libertação faseada.</p>
                <span class="badge bg-success">Escrow</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card p-3">
                <h6 class="fw-semibold">Reputação & ranking</h6>
                <p class="text-muted mb-1">Avaliações, estatísticas nocturnas e matching por categoria, zona e subscrição.</p>
                <span class="badge bg-primary">Aprendizado</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card p-3">
                <h6 class="fw-semibold">Segurança</h6>
                <p class="text-muted mb-1">Verificação de email, backups diários e CSRF em todas as rotas.</p>
                <span class="badge bg-dark">Backups</span>
            </div>
        </div>
    </div>

    <div class="glass mt-4 p-4 text-center text-white">
        <h5 class="fw-bold mb-2">Pronto para entrar?</h5>
        <p class="text-white-50 mb-3">Em menos de 3 minutos cria a sua conta, valida o email e começa a receber propostas.</p>
        <a href="{{ route('register.cliente') }}" class="btn btn-light text-primary me-2">Criar conta de cliente</a>
        <a href="{{ route('register.prestador') }}" class="btn btn-outline-light">Criar conta de prestador</a>
    </div>

    <footer class="mt-4 text-center text-white-50 small">
        &copy; {{ date('Y') }} Txeka Jobs — plataforma mobile-first com design contemporâneo.
    </footer>
</div>
</body>
</html>
