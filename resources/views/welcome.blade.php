<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Txeka Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
</head>
<body class="bg-light">
<div class="container py-4">
    <header class="mb-4 text-center">
        <h1 class="fw-bold">Txeka Jobs</h1>
        <p class="text-muted mb-0">Marketplace de serviços locais em Moçambique, mobile first e seguro.</p>
    </header>

    <div class="alert alert-primary d-flex align-items-center gap-2" role="alert">
        <div>
            <strong>Txeka Jobs:</strong> ligamos clientes e prestadores com segurança, OTP, escrow opcional e pagamentos mobile.
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
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-semibold">Pagamentos preparados</h6>
                    <p class="text-muted mb-0">Gateway M-Pesa pronto, ledger interno, reservas com libertação faseada e logs idempotentes.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-semibold">Reputação & ranking</h6>
                    <p class="text-muted mb-0">Avaliações, estatísticas recalculadas por job nocturno e matching por categoria, zona e subscrição.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-semibold">Segurança</h6>
                    <p class="text-muted mb-0">OTP via SMS, verificação de email, backups diários com download pelo admin e CSRF em todas as rotas.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-4 text-center text-muted small">
        &copy; {{ date('Y') }} Txeka Jobs. Plataforma mobile first para serviços locais em Moçambique.
    </footer>
</div>
</body>
</html>
