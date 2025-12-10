<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->enum('tipo_perfil', ['cliente', 'prestador', 'admin'])->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->enum('status', ['activo', 'suspenso', 'apagado_logico'])->default('activo');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('morada_principal')->nullable();
            $table->string('cidade')->nullable();
            $table->string('bairro_principal')->nullable();
            $table->text('referencia_localizacao_texto')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('prefere_sms')->default(true);
            $table->boolean('prefere_whatsapp')->default(true);
            $table->boolean('prefere_email')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('prestadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->string('documento_identificacao')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->enum('tipo_carteira', ['mpesa', 'mkesh', 'emola', 'outro'])->default('mpesa');
            $table->string('numero_carteira')->nullable();
            $table->enum('estado_verificacao', ['pendente', 'verificado', 'rejeitado'])->default('pendente');
            $table->timestamp('data_verificacao')->nullable();
            $table->unsignedInteger('total_servicos_concluidos')->default(0);
            $table->unsignedInteger('total_servicos_cancelados')->default(0);
            $table->unsignedInteger('total_litigios_procedentes')->default(0);
            $table->decimal('rating_medio_cacheado', 3, 2)->nullable();
            $table->boolean('esta_disponivel')->default(true);
            $table->boolean('aceita_servicos_urgentes')->default(true);
            $table->timestamps();
        });

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cidade');
            $table->string('slug')->unique();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('prestador_categoria', function (Blueprint $table) {
            $table->foreignId('prestador_id')->constrained('prestadores')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->primary(['prestador_id', 'categoria_id']);
        });

        Schema::create('prestador_zona', function (Blueprint $table) {
            $table->foreignId('prestador_id')->constrained('prestadores')->cascadeOnDelete();
            $table->foreignId('zona_id')->constrained('zonas')->cascadeOnDelete();
            $table->primary(['prestador_id', 'zona_id']);
        });

        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('prestador_id')->nullable()->constrained('prestadores');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('zona_id')->nullable()->constrained('zonas');
            $table->string('titulo');
            $table->text('descricao');
            $table->string('cidade');
            $table->string('bairro_texto')->nullable();
            $table->text('referencia_localizacao_texto')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('orcamento_estimado_min', 12, 2)->nullable();
            $table->decimal('orcamento_estimado_max', 12, 2)->nullable();
            $table->enum('urgencia', ['agora', 'hoje', 'esta_semana'])->default('esta_semana');
            $table->enum('origem', ['web', 'pwa'])->default('web');
            $table->enum('estado', ['aberto', 'em_propostas', 'contratado', 'em_execucao', 'concluido', 'em_litigio', 'encerrado'])->index()->default('aberto');
            $table->timestamp('data_contratacao')->nullable();
            $table->timestamp('data_inicio_execucao')->nullable();
            $table->timestamp('data_conclusao')->nullable();
            $table->timestamps();
            $table->index(['categoria_id', 'zona_id', 'cidade']);
        });

        Schema::create('propostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos')->cascadeOnDelete();
            $table->foreignId('prestador_id')->constrained('prestadores')->cascadeOnDelete();
            $table->decimal('valor_proposto', 12, 2);
            $table->text('mensagem')->nullable();
            $table->string('tempo_estimado_execucao')->nullable();
            $table->enum('estado_proposta', ['enviada', 'vista_pelo_cliente', 'aceita', 'rejeitada', 'expirada'])->default('enviada');
            $table->boolean('lead_pago')->default(false);
            $table->string('fonte_lead')->nullable();
            $table->timestamps();
        });

        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_pagamento', ['lead', 'reserva', 'liberacao_inicial', 'liberacao_final', 'comissao', 'reembolso', 'subscricao', 'ajuste', 'outro']);
            $table->foreignId('servico_id')->nullable()->constrained('servicos');
            $table->foreignId('prestador_id')->nullable()->constrained('prestadores');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->decimal('valor', 12, 2);
            $table->string('moeda', 10)->default('MZN');
            $table->string('referencia_externa')->nullable();
            $table->enum('canal_pagamento', ['mpesa', 'mkesh', 'emola', 'outro'])->default('mpesa');
            $table->enum('estado_pagamento', ['pendente', 'confirmado', 'falhado', 'em_disputa', 'cancelado'])->default('pendente');
            $table->enum('direccao_logica', ['cliente_para_plataforma', 'plataforma_para_prestador', 'cliente_para_prestador_directo', 'plataforma_ajuste']);
            $table->json('metadados')->nullable();
            $table->timestamps();
        });

        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('prestador_id')->constrained('prestadores');
            $table->unsignedTinyInteger('rating');
            $table->text('comentario')->nullable();
            $table->boolean('visivel_publico')->default(true);
            $table->timestamps();
        });

        Schema::create('litigios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos');
            $table->foreignId('aberto_por_user_id')->constrained('users');
            $table->enum('tipo_problema', ['prestador_nao_compareceu', 'servico_incompleto', 'qualidade_insatisfatoria', 'outro']);
            $table->text('descricao');
            $table->enum('estado_litigio', ['aberto', 'em_analise', 'resolvido'])->default('aberto');
            $table->text('decisao')->nullable();
            $table->unsignedTinyInteger('percentagem_reembolso_cliente')->nullable();
            $table->unsignedTinyInteger('percentagem_pagamento_prestador')->nullable();
            $table->foreignId('resolvido_por_admin_id')->nullable()->constrained('users');
            $table->timestamp('aberto_em')->useCurrent();
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();
        });

        Schema::create('planos_subscricao', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->decimal('preco_mensal', 12, 2);
            $table->unsignedInteger('numero_leads_incluidos')->default(0);
            $table->unsignedTinyInteger('prioridade_ranking_inicial')->default(0);
            $table->unsignedInteger('numero_maximo_propostas_simultaneas')->default(5);
            $table->boolean('destaque_listagens')->default(false);
            $table->boolean('inclui_selo_visual')->default(false);
            $table->timestamps();
        });

        Schema::create('subscricao_prestador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestador_id')->constrained('prestadores');
            $table->foreignId('plano_subscricao_id')->constrained('planos_subscricao');
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim');
            $table->enum('estado_subscricao', ['activo', 'expirado', 'cancelado'])->default('activo');
            $table->boolean('renovacao_automatica')->default(false);
            $table->unsignedInteger('leads_restantes')->default(0);
            $table->foreignId('ultimo_pagamento_id')->nullable()->constrained('pagamentos');
            $table->timestamps();
        });

        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('canal', ['sms', 'whatsapp', 'email', 'push']);
            $table->string('destino');
            $table->string('conteudo_resumido');
            $table->json('payload_json')->nullable();
            $table->enum('estado_envio', ['pendente', 'enviado', 'erro', 'ignorado'])->default('pendente');
            $table->unsignedInteger('tentativa_actual')->default(0);
            $table->timestamp('proxima_tentativa_em')->nullable();
            $table->timestamps();
        });

        Schema::create('mensagens_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos')->cascadeOnDelete();
            $table->foreignId('remetente_user_id')->constrained('users');
            $table->text('texto_mensagem');
            $table->boolean('lido')->default(false);
            $table->boolean('apagado_logicamente')->default(false);
            $table->timestamps();
            $table->index(['servico_id', 'created_at']);
        });

        Schema::create('estatisticas_prestadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestador_id')->constrained('prestadores');
            $table->decimal('media_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_servicos')->default(0);
            $table->unsignedInteger('total_servicos_ultimos_90_dias')->default(0);
            $table->decimal('taxa_cancelamento', 5, 2)->default(0);
            $table->unsignedInteger('total_litigios_procedentes')->default(0);
            $table->timestamp('ultimo_calculo_em')->nullable();
            $table->timestamps();
        });

        Schema::create('parametros_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('chave')->unique();
            $table->string('valor');
            $table->text('descricao')->nullable();
            $table->timestamps();
        });

        Schema::create('logs_actividade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('accao');
            $table->text('descricao')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('falhas_integracao_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagamento_id')->nullable()->constrained('pagamentos');
            $table->string('gateway');
            $table->text('payload');
            $table->text('erro')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('falhas_integracao_pagamentos');
        Schema::dropIfExists('logs_actividade');
        Schema::dropIfExists('parametros_sistema');
        Schema::dropIfExists('estatisticas_prestadores');
        Schema::dropIfExists('mensagens_servico');
        Schema::dropIfExists('notificacoes');
        Schema::dropIfExists('subscricao_prestador');
        Schema::dropIfExists('planos_subscricao');
        Schema::dropIfExists('litigios');
        Schema::dropIfExists('avaliacoes');
        Schema::dropIfExists('pagamentos');
        Schema::dropIfExists('propostas');
        Schema::dropIfExists('servicos');
        Schema::dropIfExists('prestador_zona');
        Schema::dropIfExists('prestador_categoria');
        Schema::dropIfExists('zonas');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('prestadores');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('users');
    }
};
