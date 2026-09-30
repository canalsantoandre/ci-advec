<?php
  $isEdit = !empty($webhook);
?>

<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-broadcast-pin text-primary me-2"></i><?= $isEdit ? 'Editar Webhook' : 'Novo Webhook' ?>
        </h3>
        <p class="text-secondary small mb-0">Configuração de parâmetros de integração para envio de WhatsApp e OTP</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('webhook') ?>">Webhooks</a></li>
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

        <!-- Card do Formulário -->
        <div class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-sliders me-2"></i>Parâmetros do Webhook
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" action="<?= base_url('webhook/salvar') ?>">
              <input type="hidden" name="id_webhook" value="<?= esc($webhook->id_webhook ?? 0) ?>">

              <!-- Nome Identificador -->
              <div class="mb-3">
                <label for="nome" class="form-label fw-semibold">Identificação do Webhook <span class="text-danger">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Evolution API - Produção, Z-API Oficial..." value="<?= esc($webhook->nome ?? '') ?>" required maxlength="150">
                <small class="text-muted" style="font-size: 0.75rem;">Nome de referência interna para a equipe de gestão.</small>
              </div>

              <!-- URL do Endpoint -->
              <div class="mb-3">
                <label for="url" class="form-label fw-semibold">URL do Endpoint HTTP POST <span class="text-danger">*</span></label>
                <input type="url" name="url" id="url" class="form-control font-monospace" placeholder="https://api.meuservidor.com/message/sendText" value="<?= esc($webhook->url ?? '') ?>" required maxlength="255">
                <small class="text-muted" style="font-size: 0.75rem;">URL do serviço que receberá as requisições POST com o payload JSON.</small>
              </div>

              <!-- Instância e Status -->
              <div class="row g-3 mb-4">
                <div class="col-md-7">
                  <label for="instancia_padrao" class="form-label fw-semibold">Instância Padrão da API <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-hdd-network"></i></span>
                    <input type="text" name="instancia_padrao" id="instancia_padrao" class="form-control font-monospace" placeholder="Ex: advec_sa_prod, principal..." value="<?= esc($webhook->instancia_padrao ?? '') ?>" required maxlength="100">
                  </div>
                  <small class="text-muted" style="font-size: 0.75rem;">Nome exato da sessão/instância configurada no seu gateway de WhatsApp.</small>
                </div>

                <div class="col-md-5">
                  <label for="status" class="form-label fw-semibold">Status de Operação</label>
                  <select name="status" id="status" class="form-select">
                    <option value="1" <?= (!isset($webhook->status) || $webhook->status == 1) ? 'selected' : '' ?>>Ativo (Disparos Habilitados)</option>
                    <option value="0" <?= (isset($webhook->status) && $webhook->status == 0) ? 'selected' : '' ?>>Inativo</option>
                  </select>
                </div>
              </div>

              <!-- Card Explicativo do Formato do Payload -->
              <div class="card bg-body-tertiary border-0 rounded-4 p-3 mb-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="bi bi-code-square text-primary fs-5"></i>
                  <h6 class="fw-bold mb-0 text-body">Estrutura do Payload Enviado (JSON List/Array)</h6>
                </div>
                <p class="small text-secondary mb-2">
                  O sistema enviará obrigatoriamente um payload HTTP POST em formato de lista JSON com o telefone sanitizado e código OTP:
                </p>
                <pre class="bg-dark text-success-emphasis p-3 rounded-3 mb-0 small user-select-all" style="font-family: monospace;"><code>[
  {
    "instance": "<span class="text-warning">instancia_padrao_do_banco</span>",
    "to": "5511999999999",
    "message": "ADVEC: Seu código de verificação é: 123456"
  }
]</code></pre>
              </div>

              <!-- Botões de Ação -->
              <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <a href="<?= base_url('webhook') ?>" class="btn btn-light rounded-pill px-4">
                  <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                  <i class="bi bi-check2-circle me-1"></i> Salvar Configurações
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
