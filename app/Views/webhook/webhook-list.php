<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-broadcast-pin text-primary me-2"></i>Gestão de Webhooks
        </h3>
        <p class="text-secondary small mb-0">Configuração de endpoints para disparo de mensagens de WhatsApp e códigos OTP</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Webhooks</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
          <i class="bi bi-list-stars me-1"></i> Total: <?= count($webhooks) ?> Webhook(s)
        </span>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-semibold btn-sm shadow-xs" onclick="abrirModalTeste(null, 'Instância Geral')">
          <i class="bi bi-send-check me-1"></i> Testar Disparo WhatsApp
        </button>

        <?php if (!empty($sys_action->create)) { ?>
          <a href="<?= base_url('webhook/novo') ?>" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Novo Webhook
          </a>
        <?php } ?>
      </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')) { ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div><?= session()->getFlashdata('success') ?></div>
      </div>
    <?php } ?>
    <?php if (session()->getFlashdata('error')) { ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><?= session()->getFlashdata('error') ?></div>
      </div>
    <?php } ?>

    <!-- Tabela Principal -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col" class="ps-4">Identificação / Nome</th>
                <th scope="col">URL do Endpoint</th>
                <th scope="col">Instância Padrão</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-end pe-4" style="width: 170px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($webhooks)) { ?>
                <?php foreach ($webhooks as $wh) { 
                  $isAtivo = ($wh->status == 1);
                ?>
                  <tr>
                    <td class="ps-4">
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2 bg-<?= $isAtivo ? 'success' : 'secondary' ?>">
                          <i class="bi bi-broadcast"></i>
                        </span>
                        <div>
                          <div class="fw-bold text-body fs-6"><?= esc($wh->nome) ?></div>
                          <small class="text-secondary">ID: #<?= $wh->id_webhook ?></small>
                        </div>
                      </div>
                    </td>

                    <td>
                      <code class="text-primary-emphasis user-select-all bg-body-tertiary px-2 py-1 rounded small">
                        <?= esc($wh->url) ?>
                      </code>
                    </td>

                    <td>
                      <span class="badge bg-dark-subtle text-dark-emphasis border rounded-pill px-3 py-1 font-monospace">
                        <i class="bi bi-hdd-network me-1"></i><?= esc($wh->instancia_padrao) ?>
                      </span>
                    </td>

                    <td class="text-center">
                      <span class="badge bg-<?= $isAtivo ? 'success' : 'danger' ?>-subtle text-<?= $isAtivo ? 'success' : 'danger' ?>-emphasis border border-<?= $isAtivo ? 'success' : 'danger' ?>-subtle rounded-pill px-3 py-1 fw-semibold">
                        <i class="bi bi-<?= $isAtivo ? 'check-circle' : 'x-circle' ?> me-1"></i>
                        <?= $isAtivo ? 'Ativo' : 'Inativo' ?>
                      </span>
                    </td>

                    <td class="text-end pe-4">
                      <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-success" onclick="abrirModalTeste(<?= $wh->id_webhook ?>, '<?= esc(addslashes($wh->nome)) ?>')" title="Enviar Teste">
                          <i class="bi bi-whatsapp"></i>
                        </button>

                        <?php if (!empty($sys_action->update)) { ?>
                          <a href="<?= base_url('webhook/editar/' . $wh->id_webhook) ?>" class="btn btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil-fill"></i>
                          </a>
                        <?php } ?>

                        <?php if (!empty($sys_action->delete)) { ?>
                          <a href="<?= base_url('webhook/apagar/' . $wh->id_webhook) ?>" class="btn btn-outline-danger" title="Excluir" onclick="return confirm('Deseja realmente excluir este webhook?')">
                            <i class="bi bi-trash3-fill"></i>
                          </a>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bi bi-broadcast display-4 d-block mb-3 text-secondary opacity-50"></i>
                    <p class="mb-0 fw-semibold">Nenhum webhook cadastrado no momento.</p>
                    <small>Cadastre um endpoint para habilitar o envio de mensagens e OTPs por WhatsApp.</small>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal de Teste de Disparo WhatsApp -->
<div class="modal fade" id="modalTesteWebhook" tabindex="-1" aria-labelledby="modalTesteWebhookLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-success-subtle border-0">
        <h5 class="modal-title fw-bold text-success-emphasis" id="modalTesteWebhookLabel">
          <i class="bi bi-whatsapp me-2"></i> Teste de Envio de Mensagem WhatsApp
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="teste_id_webhook" value="">

        <div class="mb-3">
          <label class="form-label text-muted small fw-bold">Webhook Selecionado</label>
          <div class="p-2 bg-body-tertiary rounded-3 border fw-bold text-body" id="teste_nome_webhook">-</div>
        </div>

        <div class="mb-3">
          <label for="teste_telefone" class="form-label fw-semibold">Número de WhatsApp para Teste <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
            <input type="tel" id="teste_telefone" class="form-control" placeholder="(11) 99999-9999" required>
          </div>
          <small class="text-muted" style="font-size: 0.75rem;">Informe seu número com DDD para receber o teste instantâneo.</small>
        </div>

        <div id="teste_resultado_container" class="d-none mt-3">
          <div class="alert mb-0 rounded-3 small" id="teste_alerta_resultado"></div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="btnDispararTeste" onclick="dispararTesteWebhook()">
          <i class="bi bi-send-fill me-1"></i> Enviar Mensagem de Teste
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  let modalTesteInstance = null;

  function abrirModalTeste(id, nome) {
    document.getElementById('teste_id_webhook').value = id || '';
    document.getElementById('teste_nome_webhook').textContent = nome || 'Webhook Ativo Padrão';
    document.getElementById('teste_telefone').value = '';
    document.getElementById('teste_resultado_container').classList.add('d-none');

    if (!modalTesteInstance) {
      modalTesteInstance = new bootstrap.Modal(document.getElementById('modalTesteWebhook'));
    }
    modalTesteInstance.show();
  }

  function dispararTesteWebhook() {
    const telefone = document.getElementById('teste_telefone').value.trim();
    const idWebhook = document.getElementById('teste_id_webhook').value;
    const btn = document.getElementById('btnDispararTeste');
    const container = document.getElementById('teste_resultado_container');
    const alerta = document.getElementById('teste_alerta_resultado');

    if (!telefone) {
      alert('Por favor, informe o número de WhatsApp para teste.');
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';

    const formData = new FormData();
    formData.append('telefone', telefone);
    formData.append('id_webhook', idWebhook);

    fetch('<?= base_url('webhook/testar') ?>', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Enviar Mensagem de Teste';
      container.classList.remove('d-none');

      if (data.status === 'success') {
        alerta.className = 'alert alert-success border-0 rounded-3 small mb-0';
        alerta.innerHTML = `<strong><i class="bi bi-check-circle-fill me-1"></i> Sucesso!</strong> ${data.message}`;
      } else {
        alerta.className = 'alert alert-danger border-0 rounded-3 small mb-0';
        alerta.innerHTML = `<strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Falha:</strong> ${data.message}`;
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Enviar Mensagem de Teste';
      container.classList.remove('d-none');
      alerta.className = 'alert alert-danger border-0 rounded-3 small mb-0';
      alerta.innerHTML = '<strong>Erro:</strong> Falha de comunicação com o servidor.';
    });
  }
</script>
