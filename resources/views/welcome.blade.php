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

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Clientes</h5>
                    <p class="card-text">Crie pedidos, receba propostas, contrate com escrow opcional e acompanhe o serviço.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">Entrar / Registar</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Prestadores</h5>
                    <p class="card-text">Ofereça serviços por categorias e zonas, pague leads por subscrição ou avulso, mantenha reputação.</p>
                    <a href="{{ route('login') }}" class="btn btn-success w-100">Aceder ao painel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="alert alert-info">
            <strong>Segurança:</strong> acesso protegido com OTP via SMS, sessões de dispositivo confiável e registo de autenticação.
        </div>
    </div>
</div>
</body>
</html>
