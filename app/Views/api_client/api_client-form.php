<?php
$isEdit = !empty($cliente);
?>

<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-cpu-fill text-primary me-2"></i><?= $isEdit ? 'Editar Aplicação de API' : 'Nova Aplicação de API' ?>
        </h3>
        <p class="text-secondary small mb-0">Configure os parâmetros de autenticação OAuth2, rate limit e tempo de expiração do JWT</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('api-client') ?>">Aplicações API</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Editar' : 'Novo' ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card card-outline card-primary shadow-sm border-0 rounded-4">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0">
              <i class="bi bi-sliders me-2 text-primary"></i>Parâmetros da Aplicação
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" action="<?= base_url('api-client/salvar') ?>">
              <input type="hidden" name="id_api_client" value="<?= esc($cliente->id_api_client ?? 0) ?>">

              <!-- Nome da Aplicação -->
              <div class="mb-3">
                <label for="nome_aplicacao" class="form-label fw-semibold">
                  Nome da Aplicação / Integração <span class="text-danger">*</span>
                </label>
                <input type="text" name="nome_aplicacao" id="nome_aplicacao" class="form-control" placeholder="Ex: n8n - Cobrança de Escalas, Bot de Atendimento..." value="<?= esc($cliente->nome_aplicacao ?? '') ?>" required maxlength="150">
                <small class="text-muted">Um nome descritivo para você identificar quem está consumindo os dados.</small>
              </div>

              <!-- Client ID -->
              <div class="mb-3">
                <label for="client_id" class="form-label fw-semibold">
                  Client ID <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-body-tertiary font-monospace">@</span>
                  <input type="text" name="client_id" id="client_id" class="form-control font-monospace" placeholder="ex: advec_n8n_crawler" value="<?= esc($cliente->client_id ?? $sugestaoClientId ?? '') ?>" required maxlength="100">
                </div>
                <small class="text-muted">Identificador único enviado no payload de autenticação OAuth2.</small>
              </div>

              <div class="row g-3 mb-3">
                <!-- Rate Limit -->
                <div class="col-md-6">
                  <label for="limite_requisicoes_minuto" class="form-label fw-semibold">
                    <i class="bi bi-speedometer2 text-primary me-1"></i> Rate Limit (Requisições por Minuto) <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <input type="number" name="limite_requisicoes_minuto" id="limite_requisicoes_minuto" class="form-control" min="5" max="1000" value="<?= esc($cliente->limite_requisicoes_minuto ?? 60) ?>" required>
                    <span class="input-group-text bg-body-tertiary small">req / min</span>
                  </div>
                  <small class="text-muted">Protege o servidor contra ataques de sobrecarga e loops no n8n. Padrão: 60 req/min.</small>
                </div>

                <!-- Tempo de Expiração do JWT -->
                <div class="col-md-6">
                  <label for="tempo_expiracao" class="form-label fw-semibold">
                    <i class="bi bi-clock-history text-primary me-1"></i> Tempo de Vida do Token (JWT) <span class="text-danger">*</span>
                  </label>
                  <select name="tempo_expiracao" id="tempo_expiracao" class="form-select" required>
                    <option value="1800" <?= (isset($cliente->tempo_expiracao) && $cliente->tempo_expiracao == 1800) ? 'selected' : '' ?>>30 minutos (1800s)</option>
                    <option value="3600" <?= (!isset($cliente->tempo_expiracao) || $cliente->tempo_expiracao == 3600) ? 'selected' : '' ?>>1 hora (3600s) - Recomendado</option>
                    <option value="7200" <?= (isset($cliente->tempo_expiracao) && $cliente->tempo_expiracao == 7200) ? 'selected' : '' ?>>2 horas (7200s)</option>
                    <option value="86400" <?= (isset($cliente->tempo_expiracao) && $cliente->tempo_expiracao == 86400) ? 'selected' : '' ?>>24 horas (86400s)</option>
                  </select>
                  <small class="text-muted">Tempo que o n8n poderá utilizar o mesmo token antes de renovar.</small>
                </div>
              </div>

              <!-- Status -->
              <div class="mb-3">
                <label for="status" class="form-label fw-semibold">Status de Ativação <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                  <option value="1" <?= (!isset($cliente->status) || $cliente->status == 1) ? 'selected' : '' ?>>🟢 Ativo (Requisições Liberadas)</option>
                  <option value="0" <?= (isset($cliente->status) && $cliente->status == 0) ? 'selected' : '' ?>>🔴 Inativo (Bloquear Todas as Requisições)</option>
                </select>
                <small class="text-muted">Se inativo, o n8n ou bot receberá 403 Forbidden imediatamente ao tentar emitir token.</small>
              </div>

              <!-- Descrição / Observações -->
              <div class="mb-4">
                <label for="descricao" class="form-label fw-semibold">Observações / Descrição</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="3" placeholder="Informações de contato do responsável, webhook de destino, etc."><?= esc($cliente->descricao ?? '') ?></textarea>
              </div>

              <?php if (!$isEdit) { ?>
                <div class="alert alert-info border-0 rounded-3 small p-3 mb-4">
                  <div class="fw-bold mb-1"><i class="bi bi-shield-check me-1"></i> Geração Automática de Credenciais</div>
                  Ao salvar, um <strong>Client Secret</strong> e uma chave <strong>JWT Secret</strong> criptográfica serão gerados automaticamente para esta aplicação. Você poderá visualizá-los e copiá-los na listagem.
                </div>
              <?php } ?>

              <!-- Botões -->
              <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('api-client') ?>" class="btn btn-light rounded-pill px-4">
                  Cancelar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                  <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Salvar Alterações' : 'Criar Aplicação' ?>
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
