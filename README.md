# Txeka Jobs

Plataforma de marketplace de serviços para Moçambique baseada em Laravel 11 e PHP 8.2+, preparada para alojamento LAMP. Este repositório contém esquema de dados inicial, serviços de domínio para pagamentos mock, notificações SMS/WhatsApp e máquina de estados de serviços.

## Passos rápidos
1. Copie `.env.example` para `.env` e configure credenciais MySQL.
2. Instale dependências com `composer install` (necessita acesso à internet).
3. Gere chave de aplicação com `php artisan key:generate`.
4. Execute migrations com `php artisan migrate`.
5. Inicie servidor com `php artisan serve` e filas com `php artisan queue:work`.

## Notas
- Sistema de filas configurado para driver de base de dados.
- Gateway de pagamento mock em `App\Services\Payments\MockPaymentGateway` actualiza pagamentos para estado confirmado.
- Gateway M-Pesa baseado em [`karson/mpesa-php-sdk`](https://github.com/karson/mpesa-php-sdk) pode ser activado configurando `PAYMENT_GATEWAY_DRIVER=mpesa` e credenciais `MPESA_*` no `.env`. Configure `MPESA_CALLBACK_URL` para apontar para `/api/pagamentos/mpesa/callback` exposto pela aplicação.
- Serviços de notificação (`SmsService` e `WhatsappService`) guardam mensagens em tabela `notificacoes` para processamento posterior.
- Máquina de estados de serviços em `App\Services\Servicos\ServicoStateService` valida transições críticas.
- Autenticação reforçada: login por email/telefone com password, envio de OTP por SMS e marcação de dispositivos confiáveis em `device_sessions` + `otp_tokens`.
