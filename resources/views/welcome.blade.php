<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Txeka Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <style>
        .hero-video { border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <header class="mb-4 text-center">
        <img src="/images/logo.svg" alt="Txeka Jobs" height="68" class="mb-2">
        <p class="text-muted mb-1">Marketplace mobile-first de serviços locais para Moçambique.</p>
        <p class="fw-semibold">Pagamentos M-Pesa, reputação transparente, reservas com escrow e suporte a backups diários.</p>
    </header>

    <div class="row g-4 align-items-center mb-4">
        <div class="col-lg-7">
            <div class="hero-video">
                <video src="https://samplelib.com/lib/preview/mp4/sample-5s.mp4" autoplay muted loop playsinline class="w-100" poster="/images/logo.svg"></video>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="h5">Serviços rápidos, pagamento seguro</h3>
                    <ul class="text-muted mb-3">
                        <li>Registo com email obrigatório e verificação imediata.</li>
                        <li>Pagamentos com M-Pesa e ledger interno auditável.</li>
                        <li>Ranking inteligente que aprende com avaliações e actividade.</li>
                    </ul>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('register.cliente') }}" class="btn btn-primary">Sou Cliente</a>
                        <a href="{{ route('register.prestador') }}" class="btn btn-success">Sou Prestador</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Já tenho conta</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Clientes</h5>
                    <p class="card-text">Crie pedidos com fotos e orçamento, receba propostas priorizadas por reputação e confirme pagamentos com código de segurança.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('register.cliente') }}" class="btn btn-primary">Criar conta de cliente</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">Já tenho conta</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Prestadores</h5>
                    <p class="card-text">Receba pedidos nas suas zonas, pague leads via subscrição ou avulso, mostre selo de verificado e aumente ranking.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('register.prestador') }}" class="btn btn-success">Criar conta de prestador</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-success">Já tenho conta</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold">Pagamentos preparados</h6>
                    <p class="text-muted mb-0">Gateway M-Pesa pronto, ledger interno, reservas com libertação faseada e logs idempotentes.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold">Reputação & ranking</h6>
                    <p class="text-muted mb-0">Avaliações, estatísticas recalculadas por job nocturno e matching por categoria, zona e subscrição.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold">Segurança</h6>
                    <p class="text-muted mb-0">Verificação de email, backups diários com download pelo admin e CSRF em todas as rotas.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-4 text-center text-muted small">
        &copy; {{ date('Y') }} Txeka Jobs. Código aberto e pronto para produção em LAMP com filas e cron configuráveis.
    </footer>
</div>
</body>
</html>
