-- Schema SQL completo para Txeka Jobs
-- Compatível com MySQL 8.x. Pode ser importado directamente com `mysql -u user -p < schema.sql`.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS `txekajobs` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `txekajobs`;

-- Tabelas base de autenticação e perfis
DROP TABLE IF EXISTS `otp_tokens`;
DROP TABLE IF EXISTS `device_sessions`;
DROP TABLE IF EXISTS `falhas_integracao_pagamentos`;
DROP TABLE IF EXISTS `logs_actividade`;
DROP TABLE IF EXISTS `parametros_sistema`;
DROP TABLE IF EXISTS `estatisticas_prestadores`;
DROP TABLE IF EXISTS `mensagens_servico`;
DROP TABLE IF EXISTS `notificacoes`;
DROP TABLE IF EXISTS `subscricao_prestador`;
DROP TABLE IF EXISTS `planos_subscricao`;
DROP TABLE IF EXISTS `litigios`;
DROP TABLE IF EXISTS `avaliacoes`;
DROP TABLE IF EXISTS `pagamentos`;
DROP TABLE IF EXISTS `propostas`;
DROP TABLE IF EXISTS `servicos`;
DROP TABLE IF EXISTS `prestador_zona`;
DROP TABLE IF EXISTS `prestador_categoria`;
DROP TABLE IF EXISTS `zonas`;
DROP TABLE IF EXISTS `categorias`;
DROP TABLE IF EXISTS `prestadores`;
DROP TABLE IF EXISTS `clientes`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `curriculum_path` varchar(255) DEFAULT NULL,
  `tipo_perfil` enum('cliente','prestador','admin') NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `status` enum('activo','suspenso','apagado_logico') NOT NULL DEFAULT 'activo',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  KEY `users_tipo_perfil_index` (`tipo_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `morada_principal` varchar(255) DEFAULT NULL,
  `cidade` varchar(255) NOT NULL,
  `bairro_principal` varchar(255) DEFAULT NULL,
  `referencia_localizacao_texto` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `prefere_sms` tinyint(1) NOT NULL DEFAULT '1',
  `prefere_whatsapp` tinyint(1) NOT NULL DEFAULT '1',
  `prefere_email` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clientes_user_id_foreign` (`user_id`),
  CONSTRAINT `clientes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `prestadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `bio` text DEFAULT NULL,
  `documento_identificacao` varchar(255) DEFAULT NULL,
  `tipo_documento` enum('bilhete_identidade','passaporte','dire','carta_conducao') NOT NULL,
  `numero_documento` varchar(255) NOT NULL,
  `tipo_carteira` enum('mpesa','mkesh','emola','outro') NOT NULL DEFAULT 'mpesa',
  `numero_carteira` varchar(255) NOT NULL,
  `estado_verificacao` enum('pendente','verificado','rejeitado') NOT NULL DEFAULT 'pendente',
  `data_verificacao` timestamp NULL DEFAULT NULL,
  `total_servicos_concluidos` int unsigned NOT NULL DEFAULT '0',
  `total_servicos_cancelados` int unsigned NOT NULL DEFAULT '0',
  `total_litigios_procedentes` int unsigned NOT NULL DEFAULT '0',
  `rating_medio_cacheado` decimal(3,2) DEFAULT NULL,
  `esta_disponivel` tinyint(1) NOT NULL DEFAULT '1',
  `aceita_servicos_urgentes` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prestadores_user_id_foreign` (`user_id`),
  CONSTRAINT `prestadores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `zonas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zonas_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `prestador_categoria` (
  `prestador_id` bigint unsigned NOT NULL,
  `categoria_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`prestador_id`,`categoria_id`),
  KEY `prestador_categoria_categoria_id_foreign` (`categoria_id`),
  CONSTRAINT `prestador_categoria_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prestador_categoria_prestador_id_foreign` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `prestador_zona` (
  `prestador_id` bigint unsigned NOT NULL,
  `zona_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`prestador_id`,`zona_id`),
  KEY `prestador_zona_zona_id_foreign` (`zona_id`),
  CONSTRAINT `prestador_zona_prestador_id_foreign` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prestador_zona_zona_id_foreign` FOREIGN KEY (`zona_id`) REFERENCES `zonas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `servicos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `prestador_id` bigint unsigned DEFAULT NULL,
  `categoria_id` bigint unsigned NOT NULL,
  `zona_id` bigint unsigned DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `bairro_texto` varchar(255) DEFAULT NULL,
  `referencia_localizacao_texto` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `orcamento_estimado_min` decimal(12,2) DEFAULT NULL,
  `orcamento_estimado_max` decimal(12,2) DEFAULT NULL,
  `urgencia` enum('agora','hoje','esta_semana') NOT NULL DEFAULT 'esta_semana',
  `origem` enum('web','pwa') NOT NULL DEFAULT 'web',
  `estado` enum('aberto','em_propostas','contratado','em_execucao','concluido','em_litigio','encerrado') NOT NULL DEFAULT 'aberto',
  `data_contratacao` timestamp NULL DEFAULT NULL,
  `data_inicio_execucao` timestamp NULL DEFAULT NULL,
  `data_conclusao` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servicos_cliente_id_foreign` (`cliente_id`),
  KEY `servicos_prestador_id_foreign` (`prestador_id`),
  KEY `servicos_categoria_id_foreign` (`categoria_id`),
  KEY `servicos_zona_id_foreign` (`zona_id`),
  KEY `servicos_estado_index` (`estado`),
  KEY `servicos_categoria_zona_cidade_index` (`categoria_id`,`zona_id`,`cidade`),
  CONSTRAINT `servicos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `servicos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `servicos_prestador_id_foreign` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`),
  CONSTRAINT `servicos_zona_id_foreign` FOREIGN KEY (`zona_id`) REFERENCES `zonas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `propostas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `servico_id` bigint unsigned NOT NULL,
  `prestador_id` bigint unsigned NOT NULL,
  `valor_proposto` decimal(12,2) NOT NULL,
  `mensagem` text DEFAULT NULL,
  `tempo_estimado_execucao` varchar(255) DEFAULT NULL,
  `estado_proposta` enum('enviada','vista_pelo_cliente','aceita','rejeitada','expirada') NOT NULL DEFAULT 'enviada',
  `lead_pago` tinyint(1) NOT NULL DEFAULT '0',
  `fonte_lead` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `propostas_servico_id_foreign` (`servico_id`),
  KEY `propostas_prestador_id_foreign` (`prestador_id`),
  CONSTRAINT `propostas_prestador_id_foreign` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `propostas_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pagamentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo_pagamento` enum('lead','reserva','liberacao_inicial','liberacao_final','comissao','reembolso','subscricao','ajuste','outro') NOT NULL,
  `servico_id` bigint unsigned DEFAULT NULL,
  `prestador_id` bigint unsigned DEFAULT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `admin_id` bigint unsigned DEFAULT NULL,
  `valor` decimal(12,2) NOT NULL,
  `moeda` varchar(10) NOT NULL DEFAULT 'MZN',
  `referencia_externa` varchar(255) DEFAULT NULL,
  `canal_pagamento` enum('mpesa','mkesh','emola','outro') NOT NULL DEFAULT 'mpesa',
  `estado_pagamento` enum('pendente','confirmado','falhado','em_disputa','cancelado') NOT NULL DEFAULT 'pendente',
  `direccao_logica` enum('cliente_para_plataforma','plataforma_para_prestador','cliente_para_prestador_directo','plataforma_ajuste') NOT NULL,
  `metadados` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagamentos_servico_id_foreign` (`servico_id`),
  KEY `pagamentos_prestador_id_foreign` (`prestador_id`),
  KEY `pagamentos_cliente_id_foreign` (`cliente_id`),
  KEY `pagamentos_admin_id_foreign` (`admin_id`),
  CONSTRAINT `pagamentos_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`),
  CONSTRAINT `pagamentos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `pagamentos_prestador_id_foreign` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`),
  CONSTRAINT `pagamentos_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `avaliacoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `servico_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `prestador_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comentario` text DEFAULT NULL,
  `visivel_publico` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `avaliacoes_servico_id_foreign` (`servico_id`),
  KEY `avaliacoes_cliente_id_foreign` (`cliente_id`),
  KEY `avaliacoes_prestador_id_foreign` (`prestador_id`),
  CONSTRAINT `avaliacoes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `avaliacoes_prestador_id_foreign` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`),
  CONSTRAINT `avaliacoes_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `litigios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `servico_id` bigint unsigned NOT NULL,
  `aberto_por_user_id` bigint unsigned NOT NULL,
  `tipo_problema` enum('prestador_nao_compareceu','servico_incompleto','qualidade_insatisfatoria','outro') NOT NULL,
  `descricao` text NOT NULL,
  `estado_litigio` enum('aberto','em_analise','resolvido') NOT NULL DEFAULT 'aberto',
  `decisao` text DEFAULT NULL,
  `percentagem_reembolso_cliente` tinyint unsigned DEFAULT NULL,
  `percentagem_pagamento_prestador` tinyint unsigned DEFAULT NULL,
  `resolvido_por_admin_id` bigint unsigned DEFAULT NULL,
  `aberto_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolvido_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `litigios_servico_id_foreign` (`servico_id`),
  KEY `litigios_aberto_por_user_id_foreign` (`aberto_por_user_id`),
  KEY `litigios_resolvido_por_admin_id_foreign` (`resolvido_por_admin_id`),
  CONSTRAINT `litigios_aberto_por_user_id_foreign` FOREIGN KEY (`aberto_por_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `litigios_resolvido_por_admin_id_foreign` FOREIGN KEY (`resolvido_por_admin_id`) REFERENCES `users` (`id`),
  CONSTRAINT `litigios_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `planos_subscricao` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco_mensal` decimal(12,2) NOT NULL,
  `numero_leads_incluidos` int unsigned NOT NULL DEFAULT '0',
  `prioridade_ranking_inicial` tinyint unsigned NOT NULL DEFAULT '0',
  `numero_maximo_propostas_simultaneas` int unsigned NOT NULL DEFAULT '5',
  `destaque_listagens` tinyint(1) NOT NULL DEFAULT '0',
  `inclui_selo_visual` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `planos_subscricao_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `subscricao_prestador` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prestador_id` bigint unsigned NOT NULL,
  `plano_subscricao_id` bigint unsigned NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime NOT NULL,
  `estado_subscricao` enum('activo','expirado','cancelado') NOT NULL DEFAULT 'activo',
  `renovacao_automatica` tinyint(1) NOT NULL DEFAULT '0',
  `leads_restantes` int unsigned NOT NULL DEFAULT '0',
  `ultimo_pagamento_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscricao_prestador_prestador_id_foreign` (`prestador_id`),
  KEY `subscricao_prestador_plano_subscricao_id_foreign` (`plano_subscricao_id`),
  KEY `subscricao_prestador_ultimo_pagamento_id_foreign` (`ultimo_pagamento_id`),
  CONSTRAINT `subscricao_prestador_plano_subscricao_id_foreign` FOREIGN KEY (`plano_subscricao_id`) REFERENCES `planos_subscricao` (`id`),
  CONSTRAINT `subscricao_prestador_prestador_id_foreign` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`),
  CONSTRAINT `subscricao_prestador_ultimo_pagamento_id_foreign` FOREIGN KEY (`ultimo_pagamento_id`) REFERENCES `pagamentos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notificacoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `canal` enum('sms','whatsapp','email','push') NOT NULL,
  `destino` varchar(255) NOT NULL,
  `conteudo_resumido` varchar(255) NOT NULL,
  `payload_json` json DEFAULT NULL,
  `estado_envio` enum('pendente','enviado','erro','ignorado') NOT NULL DEFAULT 'pendente',
  `tentativa_actual` int unsigned NOT NULL DEFAULT '0',
  `proxima_tentativa_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notificacoes_user_id_foreign` (`user_id`),
  CONSTRAINT `notificacoes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mensagens_servico` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `servico_id` bigint unsigned NOT NULL,
  `remetente_user_id` bigint unsigned NOT NULL,
  `texto_mensagem` text NOT NULL,
  `lido` tinyint(1) NOT NULL DEFAULT '0',
  `apagado_logicamente` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mensagens_servico_servico_id_foreign` (`servico_id`),
  KEY `mensagens_servico_remetente_user_id_foreign` (`remetente_user_id`),
  KEY `mensagens_servico_servico_id_created_at_index` (`servico_id`,`created_at`),
  CONSTRAINT `mensagens_servico_remetente_user_id_foreign` FOREIGN KEY (`remetente_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `mensagens_servico_servico_id_foreign` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `estatisticas_prestadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prestador_id` bigint unsigned NOT NULL,
  `media_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `total_servicos` int unsigned NOT NULL DEFAULT '0',
  `total_servicos_ultimos_90_dias` int unsigned NOT NULL DEFAULT '0',
  `taxa_cancelamento` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total_litigios_procedentes` int unsigned NOT NULL DEFAULT '0',
  `ultimo_calculo_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estatisticas_prestadores_prestador_id_foreign` (`prestador_id`),
  CONSTRAINT `estatisticas_prestadores_prestador_id_foreign` FOREIGN KEY (`prestador_id`) REFERENCES `prestadores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `parametros_sistema` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chave` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parametros_sistema_chave_unique` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `logs_actividade` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `accao` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_actividade_user_id_foreign` (`user_id`),
  CONSTRAINT `logs_actividade_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `falhas_integracao_pagamentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pagamento_id` bigint unsigned DEFAULT NULL,
  `gateway` varchar(255) NOT NULL,
  `payload` text NOT NULL,
  `erro` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `falhas_integracao_pagamentos_pagamento_id_foreign` (`pagamento_id`),
  CONSTRAINT `falhas_integracao_pagamentos_pagamento_id_foreign` FOREIGN KEY (`pagamento_id`) REFERENCES `pagamentos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `device_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `device_fingerprint` varchar(255) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `trusted_at` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_sessions_user_id_device_fingerprint_unique` (`user_id`,`device_fingerprint`),
  CONSTRAINT `device_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `otp_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `device_session_id` bigint unsigned DEFAULT NULL,
  `code_hash` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `consumed_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_tokens_user_id_expires_at_index` (`user_id`,`expires_at`),
  KEY `otp_tokens_device_session_id_foreign` (`device_session_id`),
  CONSTRAINT `otp_tokens_device_session_id_foreign` FOREIGN KEY (`device_session_id`) REFERENCES `device_sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `otp_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
