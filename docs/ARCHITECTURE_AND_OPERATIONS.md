# Txeka Jobs — Arquitectura, Operação e Flows

Este documento descreve a arquitectura completa, fluxos críticos, operações de produção e recomendações de segurança para executar o Txeka Jobs em ambientes LAMP comuns.

## 1. Visão geral
- **Stack**: PHP 8.2+, Laravel 11, MySQL 8, Blade + Bootstrap (mobile-first) e filas com driver de base de dados.
- **Domínios principais**: matching de prestadores, monetização por leads/subscrições/reservas, reputação e gestão de litígios.
- **Modos de pagamento**: gateway Mock (local) ou M-Pesa (via `karson/mpesa-php-sdk`). Drivers seleccionados via `PAYMENT_GATEWAY_DRIVER`.

## 2. Modelos e relações
- **User** (`tipo_perfil`: cliente, prestador, admin) com verificação de email/telefone e estado.
- **Cliente / Prestador** vinculados a `users`, com prefs de comunicação e configuração de carteira (prestador).
- **Categorias** e **Zonas** mapeadas para prestadores via tabelas pivot; `servicos` referenciam categoria/zonas.
- **Servicos**: pedidos de clientes com máquina de estados (`aberto` → `em_propostas` → `contratado` → `em_execucao` → `concluido`/`em_litigio`/`encerrado`).
- **Propostas**: enviadas por prestadores, ligadas a pagamentos de lead quando aplicável.
- **Pagamentos**: ledger único (`tipo_pagamento`, `estado_pagamento`, `direccao_logica`, `metadados` JSON) utilizado em leads, subscrições, reservas, libertações, comissões e reembolsos.
- **Subscrições** e **planos**: controlam `leads_restantes`, prioridade de ranking e benefícios.
- **Avaliacoes / EstatisticasPrestador**: reputação agregada, alimentada por eventos e jobs agendados.
- **Litigios**: controlam bloqueio/libertação de fundos e decisões administrativas.
- **Notificacoes**: SMS/WhatsApp/email/push em filas, usadas para OTP, estados de serviço e comunicações.
- **Mensagens de serviço**: chat simples por serviço com polling.
- **Logs e parâmetros**: `logs_actividade`, `parametros_sistema` e `falhas_integracao_pagamentos` dão suporte a auditoria e configuração runtime.

## 3. Fluxos de autenticação e segurança
- Login por email **ou** telefone + password, seguido de OTP por SMS (serviço `SmsService`) salvo em `otp_tokens`.
- **Device fingerprint**: guardado em `device_sessions`; dispositivos confiáveis recebem menos pedidos de OTP.
- **Admin bootstrap**: endpoint protegido `POST /api/admin/register` com header `Authorization: Bearer {ADMIN_REGISTRATION_TOKEN}`.
- **Policies**: `ServicoPolicy` garante que clientes só acedem aos próprios pedidos e prestadores apenas a serviços relacionados.
- **Protecção de callbacks**: `MpesaCallbackController` é idempotente, grava metadados e bloqueia regressões de estado.

## 4. Monetização e pagamentos
- **Leads**: `LeadPaymentService` consome `leads_restantes` de subscrição; se esgotados, cria pagamento `lead` (estado `pendente`) e chama gateway.
- **Subscrições**: criação de pagamento `subscricao`; após confirmação, cria registo em `subscricao_prestador` com datas e `leads_restantes` iniciais.
- **Reservas (escrow)**: percentagem configurável (`RESERVA_PERCENTUAL_PADRAO` ou `parametros_sistema`). Ao aceitar proposta, cria pagamento `reserva`; libertações (`liberacao_inicial`/`liberacao_final`) ocorrem em transições de estado.
- **Reembolsos / comissões**: decididos em litígio e registados no ledger.
- **Gateways**: 
  - **Mock**: confirma imediatamente para desenvolvimento.
  - **M-Pesa**: usa SDK `karson/mpesa-php-sdk`; callback em `/api/pagamentos/mpesa/callback` actualiza pagamentos de forma idempotente.

## 5. Matching e ranking
- Eventos `ServicoCriado`/`ServicoContratado` disparam `MatchPrestadoresJob`.
- `PrestadorRankingService` calcula score ponderando: categoria, zona, verificação, reputação, prioridade do plano, actividade recente e equilíbrio de carga.
- Notificações aos top N prestadores são enviadas via Jobs; propostas são ordenadas por score no frontend.

## 6. Máquina de estados de serviço
`ServicoStateService` centraliza transições com transacções e emissão de eventos:
- `abrirServico` → `marcarComoEmPropostas` → `contratarPrestador` (fixa `prestador_id`, cria reserva se aplicável)
- `marcarChegadaPrestador` → `marcarInicioExecucao` (liberta tranche inicial se reserva activa)
- `marcarConclusao` / `abrirLitigio` (bloqueia libertações) → `encerrarServico` (liberta/ajusta fundos conforme decisão)
- Eventos alimentam reputação, matching e notificações.

## 7. Backups e recuperação
- `DatabaseBackupService` usa `mysqldump` para gerar `.sql` em `storage/app/backups`.
- Comando: `php artisan txeka:backup-diario`; agendado às 03:00 (ver `app/Console/Kernel.php`).
- Interface admin `/admin/backups` para gerar, descarregar e restaurar backups (upload de `.sql` aplicado via `mysql`).
- Recomendações: activar modo manutenção antes de restaurar (`php artisan down`) e reativar após (`php artisan up`).

## 8. Operação diária
- **Filas**: `php artisan queue:work --queue=default` (driver database). Supervisione com systemd/supervisor ou cron de reinício.
- **Scheduler**: tarefa a cada minuto executando `php artisan schedule:run` para matching, backups e recálculos.
- **Logs**: manter rotação em `storage/logs/laravel.log`; exportar `falhas_integracao_pagamentos` para auditoria.
- **Health-check**: rota raiz ou `php artisan schedule:run --verbose` em cron para detectar falhas de agenda.

## 9. Administração
- Painel admin (rotas protegidas) inclui: aprovação de prestadores, gestão de planos/subscrições, parâmetros do sistema, serviços, litígios, pagamentos e backups.
- Acções críticas devem ser registadas em `logs_actividade` (camada de aplicação deve invocar gravação em pontos sensíveis).

## 10. Testes e QA
- **Unitários**: cobrem `OtpService`, `LeadPaymentService`, `ServicoStateService`, `MpesaPaymentGateway`, `DatabaseBackupService`.
- **Feature**: `AdminRegistrationTest`, `MpesaCallbackControllerTest` validam endpoints críticos.
- **Como executar**:
  ```bash
  composer install
  cp .env.example .env && php artisan key:generate
  php artisan migrate
  ./vendor/bin/phpunit
  ```
- Em ambientes offline, use `schema.sql` e configure um repositório local de dependências ou executar `phpunit` após baixar vendor.

## 11. Segurança e hardening
- Manter `APP_DEBUG=false` em produção; usar HTTPS e cabeçalhos de segurança via Apache/Nginx.
- Restringir acesso ao `/admin` e ao callback M-Pesa a IPs conhecidos quando possível.
- Rotacionar `ADMIN_REGISTRATION_TOKEN` após bootstrap inicial.
- Guardar segredos no `.env` e usar permissões mínimas para o utilizador MySQL.
- Sanitização/validação via Form Requests; todas as queries usam Eloquent ou bindings preparados.

## 12. Desempenho e escalabilidade
- Driver de filas baseado em BD para hosting simples; pode migrar para Redis sem alterar código.
- Caches possíveis: ranking de prestadores, parâmetros do sistema, manifest PWA e catálogos (categorias/zonas).
- Adicionar índices complementares se surgirem novos filtros; `schema.sql` e migrations já incluem os principais.

## 13. PWA e frontend
- Manifesto em `public/manifest.json` e service worker básico em `public/service-worker.js` para cache de assets estáticos.
- Layouts Bootstrap mobile-first em `resources/views/layouts/app.blade.php`; páginas principais em `resources/views/cliente` e `resources/views/prestador`.
- Chat por serviço usa polling com AJAX simples, compatível com hosting sem websockets.

## 14. Troubleshooting
- **Filas não processam**: ver tabela `jobs`/`failed_jobs`; correr `php artisan queue:retry all`.
- **Callback M-Pesa não actualiza**: verificar logs de `MpesaCallbackController` e tabela `pagamentos` (estado deve ficar `confirmado`). Repetir callback é seguro (idempotente).
- **OTP não recebido**: confirmar registos em `notificacoes`; em dev, OTP é gravado para debug. Ajustar validade em `OtpService` se necessário.
- **Backup falhou**: confirmar `mysqldump` no PATH e permissões de escrita em `storage/app/backups`.

## 15. Deployment rápido
1. Copiar código para servidor LAMP e apontar DocumentRoot para `public`.
2. `composer install --no-dev`, `php artisan key:generate`, configurar `.env` com credenciais e gateway desejado.
3. `php artisan migrate --force` ou importar `schema.sql` (para ambientes sem PHP CLI).
4. Configurar cron: `* * * * * php /caminho/artisan schedule:run >> /var/log/txeka-cron.log 2>&1`.
5. Iniciar worker: `php /caminho/artisan queue:work --sleep=3 --tries=3` via supervisor ou systemd.
6. Criar admin via endpoint seguro e activar backups diários.

Com estes pontos, a operação diária e a evolução do Txeka Jobs ficam documentadas para equipas de desenvolvimento e operações.
