<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  
  <!-- PWA & Mobile Fullscreen Settings (Oculta barra do navegador no mobile) -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ADVEC Voluntário">
  <meta name="theme-color" content="#0f172a">
  <meta name="msapplication-navbutton-color" content="#0f172a">
  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <link rel="apple-touch-icon" href="<?= base_url('logo-advec.png') ?>">

  <title><?= esc($title ?? 'Minhas Escalas - ADVEC') ?></title>

  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
      background-color: #f1f5f9;
      min-height: 100dvh;
    }
    [data-bs-theme="dark"] body {
      background-color: #0b1120;
    }

    .escala-card {
      border: 1px solid rgba(0, 0, 0, 0.08);
      border-radius: 1.1rem;
      background: #fff;
      transition: all 0.2s ease;
      overflow: hidden;
    }
    [data-bs-theme="dark"] .escala-card {
      border-color: rgba(255, 255, 255, 0.08);
      background: #1e293b;
    }
    .escala-card:hover {
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .status-badge-pendente {
      background-color: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
    }
    .status-badge-confirmado {
      background-color: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }
    .status-badge-recusado {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .btn-confirmar {
      background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
      border: none;
      color: #fff;
      font-weight: 700;
    }
    .btn-confirmar:hover {
      background: linear-gradient(135deg, #15803d 0%, #166534 100%);
      color: #fff;
    }

    .pulse-warning {
      animation: pulse-warn 1.8s infinite;
    }
    @keyframes pulse-warn {
      0% { opacity: 1; }
      50% { opacity: 0.65; }
      100% { opacity: 1; }
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <?= view('portal_voluntario/_nav', ['voluntario' => $voluntario, 'menuAtivo' => 'agenda']) ?>

  <div class="container max-w-portal py-3 px-3">

    <!-- Seletor de Mês & Header -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-body">
      <div class="card-body p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div>
            <h4 class="fw-bold mb-0 text-body">
              <i class="bi bi-calendar2-week text-primary me-2"></i>Minhas Escalas
            </h4>
            <p class="text-secondary small mb-0">Consulte os cultos em que você foi escalado para servir</p>
          </div>

          <!-- Navegador de Mês -->
          <div class="d-flex align-items-center gap-1">
            <?php
              $prevMes = ($mes == 1) ? 12 : $mes - 1;
              $prevAno = ($mes == 1) ? $ano - 1 : $ano;
              $nextMes = ($mes == 12) ? 1 : $mes + 1;
              $nextAno = ($mes == 12) ? $ano + 1 : $ano;
            ?>
            <a href="<?= base_url("portal/agenda/{$prevAno}/{$prevMes}") ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-2" title="Mês Anterior">
              <i class="bi bi-chevron-left"></i>
            </a>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold fs-6">
              <?= esc($nomeMes) ?> / <?= $ano ?>
            </span>
            <a href="<?= base_url("portal/agenda/{$nextAno}/{$nextMes}") ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-2" title="Próximo Mês">
              <i class="bi bi-chevron-right"></i>
            </a>
          </div>
        </div>

        <!-- Contadores Rápidos do Mês -->
        <div class="row g-2 mt-2 pt-2 border-top">
          <div class="col-4">
            <div class="p-2 rounded-3 bg-body-tertiary text-center border">
              <span class="d-block text-secondary small" style="font-size: 0.72rem;">TOTAL</span>
              <strong class="fs-5 text-body"><?= $totalMes ?></strong>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 rounded-3 bg-success-subtle text-center border border-success-subtle">
              <span class="d-block text-success-emphasis small fw-semibold" style="font-size: 0.72rem;">CONFIRMADAS</span>
              <strong class="fs-5 text-success-emphasis"><?= $totalConfirmadas ?></strong>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 rounded-3 <?= ($totalPendentes > 0 ? 'bg-warning-subtle border-warning-subtle' : 'bg-body-tertiary') ?> text-center border">
              <span class="d-block text-warning-emphasis small fw-semibold" style="font-size: 0.72rem;">PENDENTES</span>
              <strong class="fs-5 text-warning-emphasis"><?= $totalPendentes ?></strong>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de Escalas -->
    <?php if (empty($escalas)) { ?>
      <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-body">
        <div class="mb-3">
          <i class="bi bi-calendar-x text-muted" style="font-size: 3.5rem;"></i>
        </div>
        <h5 class="fw-bold text-body">Nenhuma escala para <?= esc($nomeMes) ?> de <?= $ano ?></h5>
        <p class="text-secondary small mb-3">Você ainda não possui escalas agendadas neste mês.</p>
        <div>
          <a href="<?= base_url('portal/agenda/' . date('Y/m')) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold">
            <i class="bi bi-arrow-clockwise me-1"></i> Ver Mês Atual
          </a>
        </div>
      </div>
    <?php } else { ?>
      <div class="d-flex flex-column gap-3">
        <?php foreach ($escalas as $esc) { 
          $conf = strtoupper((string)($esc->status_confirmacao ?: 'PENDENTE'));
          $corDep = !empty($esc->cor_departamento) ? $esc->cor_departamento : '#2563eb';
          $corCulto = !empty($esc->cor_evento) ? $esc->cor_evento : '#2563eb';
          
          $tsData = strtotime($esc->data_culto);
          $diaNum = date('d', $tsData);
          $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
          $diaSemanaNome = $diasSemana[(int)date('w', $tsData)] ?? '';
          $isPassado = (strtotime($esc->data_culto . ' 23:59:59') < time());
        ?>
          <div class="escala-card shadow-sm" id="card_escala_<?= $esc->id_escala_voluntario ?>" style="border-left: 5px solid <?= esc($corCulto) ?> !important;">
            
            <div class="p-3">
              <!-- Top Row: Data, Horário & Status -->
              <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                  <div class="text-center p-2 rounded-3 bg-body-tertiary border" style="min-width: 54px;">
                    <span class="d-block text-uppercase fw-bold text-primary" style="font-size: 0.7rem;"><?= $diaSemanaNome ?></span>
                    <strong class="fs-4 text-body d-block font-monospace" style="line-height: 1;"><?= $diaNum ?></strong>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-0 text-body fs-6"><?= esc($esc->titulo_culto) ?></h6>
                    <small class="text-muted">
                      <i class="bi bi-clock me-1"></i><?= substr($esc->horario_inicio, 0, 5) ?> - <?= substr($esc->horario_termino, 0, 5) ?>
                    </small>
                  </div>
                </div>

                <!-- Status Badge -->
                <div id="badge_status_<?= $esc->id_escala_voluntario ?>">
                  <?php if ($conf === 'CONFIRMADO') { ?>
                    <span class="badge status-badge-confirmado rounded-pill px-3 py-2 fw-bold small">
                      <i class="bi bi-check-circle-fill me-1"></i> Confirmado
                    </span>
                  <?php } elseif ($conf === 'RECUSADO') { ?>
                    <span class="badge status-badge-recusado rounded-pill px-3 py-2 fw-bold small" title="Justificativa: <?= esc($esc->justificativa_recusa) ?>">
                      <i class="bi bi-x-circle-fill me-1"></i> Desmarcado
                    </span>
                  <?php } else { ?>
                    <span class="badge status-badge-pendente pulse-warning rounded-pill px-3 py-2 fw-bold small">
                      <i class="bi bi-hourglass-split me-1"></i> Resposta Pendente
                    </span>
                  <?php } ?>
                </div>
              </div>

              <!-- Detalhes do Departamento e Sub-área -->
              <div class="p-2 rounded-3 bg-body-tertiary border mb-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill text-white px-2 py-1 small" style="background-color: <?= esc($corDep) ?>;">
                      <?= esc($esc->nome_departamento) ?>
                    </span>
                    <span class="fw-bold text-body small">
                      <i class="bi bi-grid-fill me-1 text-primary"></i> Sub-área: <strong class="text-primary-emphasis"><?= esc($esc->nome_area) ?></strong>
                    </span>
                  </div>

                  <?php if (!empty($esc->lider_departamento)) { ?>
                    <small class="text-muted">
                      <i class="bi bi-person-badge me-1"></i> Líder: <?= esc($esc->lider_departamento) ?>
                    </small>
                  <?php } ?>
                </div>

                <?php if (!empty($esc->observacao)) { ?>
                  <div class="mt-2 pt-2 border-top small text-secondary">
                    <i class="bi bi-info-circle-fill text-info me-1"></i> <strong>Instrução da Liderança:</strong> <?= esc($esc->observacao) ?>
                  </div>
                <?php } ?>

                <?php if ($conf === 'RECUSADO' && !empty($esc->justificativa_recusa)) { ?>
                  <div class="mt-2 pt-2 border-top small text-danger">
                    <i class="bi bi-chat-left-text-fill me-1"></i> <strong>Sua justificativa:</strong> <?= esc($esc->justificativa_recusa) ?>
                  </div>
                <?php } ?>
              </div>

              <!-- Action Buttons -->
              <?php if (!$isPassado) { ?>
                <div class="d-flex flex-wrap gap-2" id="acoes_escala_<?= $esc->id_escala_voluntario ?>">
                  <?php if ($conf !== 'CONFIRMADO') { ?>
                    <button type="button" class="btn btn-confirmar btn-sm rounded-pill px-3 py-2 flex-grow-1" onclick="confirmarPresencaAjax(<?= $esc->id_escala_voluntario ?>)">
                      <i class="bi bi-check-lg me-1"></i> Confirmar Presença
                    </button>
                  <?php } ?>

                  <?php if ($conf !== 'RECUSADO') { ?>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 flex-grow-1" onclick="abrirModalRecusa(<?= $esc->id_escala_voluntario ?>, '<?= esc($esc->titulo_culto) ?>', '<?= date('d/m/Y', $tsData) ?>')">
                      <i class="bi bi-x-lg me-1"></i> Desmarcar / Recusar
                    </button>
                  <?php } else { ?>
                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 flex-grow-1" onclick="confirmarPresencaAjax(<?= $esc->id_escala_voluntario ?>)">
                      <i class="bi bi-arrow-repeat me-1"></i> Alterar para Confirmado
                    </button>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="text-muted small fst-italic">
                  <i class="bi bi-clock-history me-1"></i> Culto já realizado
                </div>
              <?php } ?>

            </div>

          </div>
        <?php } ?>
      </div>
    <?php } ?>

  </div>

  <!-- Modal / Bottom Sheet de Recusa / Justificativa -->
  <div class="modal fade" id="modalRecusarEscala" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 shadow border-0">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold text-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Desmarcar Escala
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <form id="formRecusarEscala" onsubmit="enviarRecusaAjax(event)">
          <input type="hidden" id="modal_recusa_id_escala" value="">

          <div class="modal-body py-3">
            <div class="p-2 rounded-3 bg-danger-subtle text-danger-emphasis border border-danger-subtle small mb-3">
              Você está desmarcando sua participação no <strong id="modal_recusa_culto_nome"></strong> do dia <strong id="modal_recusa_culto_data"></strong>.
            </div>

            <div class="mb-2">
              <label for="modal_recusa_justificativa" class="form-label fw-semibold small">
                Motivo / Justificativa <span class="text-danger">*</span>
              </label>
              <textarea id="modal_recusa_justificativa" class="form-control" rows="3" placeholder="Ex: Estarei em viagem familiar / compromisso profissional inadiável..." required></textarea>
              <small class="text-muted">A sua liderança receberá essa justificativa para ajustar a escala.</small>
            </div>
          </div>

          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Voltar</button>
            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold" id="btnConfirmarRecusaSubmit">
              <i class="bi bi-send-fill me-1"></i> Enviar Justificativa
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="portalToast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body fw-semibold" id="portalToastBody"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let modalRecusaInstance = null;

    function showToast(tipo, mensagem) {
      const toastEl = document.getElementById('portalToast');
      const toastBody = document.getElementById('portalToastBody');
      toastEl.className = 'toast align-items-center text-white border-0 shadow-lg rounded-3 ' + (tipo === 'success' ? 'bg-success' : 'bg-danger');
      toastBody.innerHTML = (tipo === 'success' ? '<i class="bi bi-check-circle-fill me-2"></i>' : '<i class="bi bi-exclamation-octagon-fill me-2"></i>') + mensagem;
      const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
      bsToast.show();
    }

    function confirmarPresencaAjax(id_escala) {
      const btnContainer = document.getElementById(`acoes_escala_${id_escala}`);
      const originalHtml = btnContainer ? btnContainer.innerHTML : '';
      if (btnContainer) {
        btnContainer.innerHTML = '<div class="text-center py-2 text-primary small"><div class="spinner-border spinner-border-sm me-2"></div>Confirmando presença...</div>';
      }

      const formData = new FormData();
      formData.append('id_escala_voluntario', id_escala);

      fetch('<?= base_url('portal/confirmarEscala') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          showToast('success', data.message);
          setTimeout(() => window.location.reload(), 600);
        } else {
          showToast('error', data.message || 'Erro ao confirmar presença.');
          if (btnContainer) btnContainer.innerHTML = originalHtml;
        }
      })
      .catch(err => {
        showToast('error', 'Falha na conexão com o servidor.');
        if (btnContainer) btnContainer.innerHTML = originalHtml;
      });
    }

    function abrirModalRecusa(id_escala, nomeCulto, dataCulto) {
      document.getElementById('modal_recusa_id_escala').value = id_escala;
      document.getElementById('modal_recusa_culto_nome').textContent = nomeCulto;
      document.getElementById('modal_recusa_culto_data').textContent = dataCulto;
      document.getElementById('modal_recusa_justificativa').value = '';

      if (!modalRecusaInstance) {
        modalRecusaInstance = new bootstrap.Modal(document.getElementById('modalRecusarEscala'));
      }
      modalRecusaInstance.show();
    }

    function enviarRecusaAjax(e) {
      e.preventDefault();
      const id_escala = document.getElementById('modal_recusa_id_escala').value;
      const justificativa = document.getElementById('modal_recusa_justificativa').value.trim();
      const btn = document.getElementById('btnConfirmarRecusaSubmit');

      if (!justificativa) {
        showToast('error', 'Informe uma justificativa.');
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Enviando...';

      const formData = new FormData();
      formData.append('id_escala_voluntario', id_escala);
      formData.append('justificativa', justificativa);

      fetch('<?= base_url('portal/recusarEscala') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Enviar Justificativa';
        if (modalRecusaInstance) modalRecusaInstance.hide();

        if (data.status === 'success') {
          showToast('success', data.message);
          setTimeout(() => window.location.reload(), 600);
        } else {
          showToast('error', data.message || 'Erro ao desmarcar escala.');
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Enviar Justificativa';
        showToast('error', 'Falha na conexão com o servidor.');
      });
    }
  </script>
</body>
</html>
