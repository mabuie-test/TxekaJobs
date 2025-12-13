# Txeka Jobs

Plataforma de marketplace de serviços para Moçambique baseada em Laravel 11 e PHP 8.2+, preparada para alojamento LAMP. O projecto inclui máquina de estados de serviços, monetização (leads, subscrições, reservas), integração M-Pesa e flows completos para clientes, prestadores e administradores.

## Passos rápidos
1. Copie `.env.example` para `.env` e configure credenciais MySQL, `ADMIN_REGISTRATION_TOKEN`, `APP_URL` e gateway (`PAYMENT_GATEWAY_DRIVER=mock|mpesa`).
2. Instale dependências: `composer install` e gere a chave `php artisan key:generate`.
3. Crie a base de dados `txekajobs` e execute `php artisan migrate && php artisan storage:link` (ou importe `schema.sql`).
4. Arranque serviços locais: `php artisan serve`, `php artisan queue:work` e agende `php artisan schedule:run` por cron a cada minuto.
5. Crie um administrador via `POST /api/admin/register` com header `Authorization: Bearer {ADMIN_REGISTRATION_TOKEN}`; o admin receberá email de verificação se tiver email definido.
6. Aceda ao frontend: crie conta em `/registar/cliente` ou `/registar/prestador`, valide email e OTP; complete o perfil em `/perfil` com foto/currículo; `/cliente/servicos` para pedidos, `/prestador/propostas` para propostas e `/admin/backups` para backups.

## Arquitectura em síntese
- **Domínio**: matching de prestadores, monetização (leads, subscrições, reservas), reputação, litígios e notificações multicanal.
- **Pagamentos**: driver configurável (`mock` ou `mpesa`), callbacks idempotentes em `/api/pagamentos/mpesa/callback`, ledger único em `pagamentos` com metadados JSON.
- **Segurança**: login por email/telefone + password + OTP SMS; dispositivos confiáveis em `device_sessions`; policies para serviços; endpoint de admin protegido por token.
- **Estados de serviço**: `App\Services\Servicos\ServicoStateService` com eventos `ServicoCriado/Contratado/Concluido` e jobs de matching.
- **Ranking/Reputação**: `PrestadorRankingService` e `EstatisticasPrestadorService` alimentados por avaliações e histórico de litígios.
- **Filas/cron**: driver database; `schedule:run` dispara matching, recálculo e backup diário (`txeka:backup-diario`).
- **PWA**: manifesto e service worker em `public/manifest.json` e `public/service-worker.js` para cache de assets estáticos.

## Componentes e rotas chave
- **Autenticação**: `/login`, `/otp`, serviços `OtpService` e modelos `OtpToken`/`DeviceSession`.
- **Clientes**: `/cliente/servicos` (listar/criar), `/cliente/servicos/{id}` (detalhes, propostas, timeline). Matching é enfileirado via eventos de serviço.
- **Prestadores**: `/prestador/propostas` (listar) e `/prestador/propostas/create` (enviar proposta) com monetização de lead (`LeadPaymentService`).
- **Admin**: `/admin/backups` (gerar, descarregar, restaurar) e API `/api/admin/register` para bootstrap seguro.
- **Pagamentos**: `App\Services\Payments` com gateways Mock e M-Pesa; callbacks em `/api/pagamentos/mpesa/callback`.

## Documentação detalhada
- `guide.txt` e `final.txt`: passo-a-passo completo para Windows (instalação, base de dados, cron, filas, bootstrap de admin e resolução rápida).
- `docs/ARCHITECTURE_AND_OPERATIONS.md`: visão abrangente de modelos, fluxos de pagamento, máquina de estados, operação diária, troubleshooting e segurança.
- `schema.sql`: DDL completo espelhando as migrations para importação directa em MySQL.

## Notas operacionais
- Filas usam driver de base de dados; configure cron minutely para `php artisan schedule:run`.
- Backup diário em `storage/app/backups` via comando `txeka:backup-diario` (agenda às 03:00); interface `/admin/backups` permite download/restauro.
- Para produção, configure `PAYMENT_GATEWAY_DRIVER=mpesa` e variáveis `MPESA_*`; mantenha `mock` em desenvolvimento.
- Em caso de recuperação, importe `schema.sql` e aplique o backup mais recente.
- Se durante `composer install` aparecer "Target [Illuminate\\Contracts\\Debug\\ExceptionHandler] is not instantiable", confirme que o repositório foi extraído por completo (incluindo `app/Exceptions/Handler.php`) e limpe caches com `php artisan config:clear`.

## Testes
- Unit: `OtpServiceTest`, `LeadPaymentServiceTest`, `ServicoStateServiceTest`, `MpesaPaymentGatewayTest`, `DatabaseBackupServiceTest`.
- Feature: `AdminRegistrationTest`, `MpesaCallbackControllerTest`.
- Executar: `composer install && cp .env.example .env && php artisan key:generate && php artisan migrate && ./vendor/bin/phpunit`.

Para detalhes adicionais consulte `docs/ARCHITECTURE_AND_OPERATIONS.md`.
