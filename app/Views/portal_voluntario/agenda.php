<?php

/**
 * Helper: Retorna a cor de texto ideal (#ffffff ou #0f172a) com base na luminância da cor de fundo (WCAG YIQ)
 */
if (!function_exists('getContrasteTexto')) {
  function getContrasteTexto($hexColor)
  {
    if (empty($hexColor)) return '#ffffff';
    $hex = ltrim((string)$hexColor, '#');
    if (strlen($hex) === 3) {
      $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (strlen($hex) !== 6) {
      return '#ffffff';
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 150) ? '#0f172a' : '#ffffff';
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

  <!-- PWA & Mobile Fullscreen Settings -->
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
      color: #1e293b;
      min-height: 100dvh;
    }

    [data-bs-theme="dark"] body {
      background-color: #0b1120;
      color: #e2e8f0;
    }

    /* Swipe Container & Gestos Touch (Estilo iOS / Google) */
    .swipe-container {
      position: relative;
      overflow: hidden;
      border-radius: 1.15rem;
      margin-bottom: 0.85rem;
      touch-action: pan-y;
      user-select: none;
      -webkit-user-select: none;
    }

    .swipe-bg {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      padding: 0 1.5rem;
      border-radius: 1.15rem;
      font-weight: 700;
      color: #fff;
      pointer-events: none;
      transition: opacity 0.15s ease;
    }

    .swipe-bg-accept {
      background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
      justify-content: flex-start;
    }

    .swipe-bg-reject {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      justify-content: flex-end;
    }

    .swipe-content {
      position: relative;
      background: #ffffff;
      border-radius: 1.15rem;
      border: 1px solid rgba(0, 0, 0, 0.08);
      transition: transform 0.28s cubic-bezier(0.2, 0.9, 0.3, 1), box-shadow 0.2s ease;
      z-index: 2;
      cursor: grab;
    }

    .swipe-content:active {
      cursor: grabbing;
    }

    [data-bs-theme="dark"] .swipe-content {
      border-color: rgba(255, 255, 255, 0.08);
      background: #1e293b;
    }

    .swipe-content:hover {
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .swipe-content.swiping {
      transition: none !important;
    }

    .calendar-badge {
      border: 1px solid rgba(0, 0, 0, 0.08);
      background: #f8fafc;
      transition: transform 0.15s ease;
    }

    [data-bs-theme="dark"] .calendar-badge {
      background: #0f172a;
      border-color: #334155;
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
      0% {
        opacity: 1;
      }

      50% {
        opacity: 0.65;
      }

      100% {
        opacity: 1;
      }
    }

    /* Overlay Onboarding Premium com Fundo Embaçado (Frosted Glass) */
    .swipe-onboarding-backdrop {
      position: fixed;
      inset: 0;
      z-index: 1080;
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.25rem;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.35s ease, visibility 0.35s ease;
    }

    .swipe-onboarding-backdrop.active {
      opacity: 1;
      visibility: visible;
    }

    .swipe-onboarding-modal {
      background: var(--bs-body-bg, #ffffff);
      max-width: 460px;
      width: 100%;
      border-radius: 1.5rem;
      border: 1px solid rgba(255, 255, 255, 0.25);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      transform: scale(0.92) translateY(18px);
      transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
      overflow: hidden;
    }

    .swipe-onboarding-backdrop.active .swipe-onboarding-modal {
      transform: scale(1) translateY(0);
    }

    [data-bs-theme="dark"] .swipe-onboarding-modal {
      background: #1e293b;
      border-color: rgba(255, 255, 255, 0.12);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
    }

    /* Animação da Demonstração de Gesto */
    .swipe-demo-box {
      background: rgba(0, 0, 0, 0.03);
      border: 1.5px dashed rgba(0, 0, 0, 0.12);
      border-radius: 1.2rem;
      position: relative;
      overflow: hidden;
      height: 100px;
    }

    [data-bs-theme="dark"] .swipe-demo-box {
      background: rgba(255, 255, 255, 0.04);
      border-color: rgba(255, 255, 255, 0.15);
    }

    .swipe-demo-card {
      position: absolute;
      top: 12px;
      bottom: 12px;
      left: 14px;
      right: 14px;
      background: var(--bs-card-bg, #ffffff);
      border: 1px solid var(--bs-border-color, #e2e8f0);
      border-radius: 0.85rem;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      animation: swipeDemoLoop 4.2s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
    }

    [data-bs-theme="dark"] .swipe-demo-card {
      background: #0f172a;
      border-color: #334155;
    }

    .swipe-demo-indicator-right {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #16a34a;
      font-weight: 800;
      font-size: 0.75rem;
      opacity: 0;
      animation: indicatorRightLoop 4.2s ease-in-out infinite;
    }

    .swipe-demo-indicator-left {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #dc2626;
      font-weight: 800;
      font-size: 0.75rem;
      opacity: 0;
      animation: indicatorLeftLoop 4.2s ease-in-out infinite;
    }

    .swipe-hand-cursor {
      display: inline-block;
      font-size: 1.5rem;
      color: #2563eb;
      filter: drop-shadow(0 2px 4px rgba(37, 99, 235, 0.3));
    }

    @keyframes swipeDemoLoop {

      0%,
      100% {
        transform: translateX(0);
      }

      22% {
        transform: translateX(65px);
      }

      44% {
        transform: translateX(0);
      }

      66% {
        transform: translateX(-65px);
      }

      88% {
        transform: translateX(0);
      }
    }

    @keyframes indicatorRightLoop {

      0%,
      42%,
      100% {
        opacity: 0;
      }

      18%,
      30% {
        opacity: 1;
      }
    }

    @keyframes indicatorLeftLoop {

      0%,
      45%,
      86%,
      100% {
        opacity: 0;
      }

      60%,
      74% {
        opacity: 1;
      }
    }
  </style>
</head>

<body>

  <!-- Navigation -->
  <?= view('portal_voluntario/_nav', ['voluntario' => $voluntario, 'menuAtivo' => 'agenda']) ?>

  <div class="container max-w-portal py-3 px-3">

    <!-- Seletor de Mês & Header (Centralizado no Mobile) -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-body">
      <div class="card-body p-3 p-md-4">

        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-center text-center text-md-start gap-3">
          <!-- Título Centralizado no Mobile -->
          <div>
            <h4 class="fw-bold mb-0 text-body d-flex align-items-center justify-content-center justify-content-md-start gap-2">
              <i class="bi bi-calendar2-week text-primary"></i> Minhas Escalas
            </h4>
            <p class="text-secondary small mb-0">Confirme ou cancele suas escalas</p>
          </div>

          <!-- Navegador de Mês Centralizado no Mobile -->
          <div class="d-flex align-items-center justify-content-center gap-2">
            <?php
            $prevMes = ($mes == 1) ? 12 : $mes - 1;
            $prevAno = ($mes == 1) ? $ano - 1 : $ano;
            $nextMes = ($mes == 12) ? 1 : $mes + 1;
            $nextAno = ($mes == 12) ? $ano + 1 : $ano;
            ?>
            <a href="<?= base_url("portal/agenda/{$prevAno}/{$prevMes}") ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 shadow-xs" title="Mês Anterior">
              <i class="bi bi-chevron-left"></i>
            </a>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3.5 py-2 fw-bold fs-6 shadow-xs">
              <?= esc($nomeMes) ?> / <?= $ano ?>
            </span>
            <a href="<?= base_url("portal/agenda/{$nextAno}/{$nextMes}") ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 shadow-xs" title="Próximo Mês">
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
              <span class="d-block text-success-emphasis small fw-semibold" style="font-size: 0.72rem;">ACEITAS</span>
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

    <!-- Botão de Ajuda Rápida / Tutorial Gestos -->
    <?php if (!empty($escalas)) { ?>
      <div class="d-flex align-items-center justify-content-between mb-3 text-muted small px-1">
        <span style="font-size: 0.78rem;">
          <i class="bi bi-phone-fill me-1 text-primary"></i> No celular, arraste o card para responder
        </span>
        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-primary" style="font-size: 0.76rem;" onclick="reabrirOnboardingSwipe()">
          <i class="bi bi-question-circle me-1"></i> Como usar?
        </button>
      </div>
    <?php } ?>

    <!-- Lista de Escalas Minimalistas com Suporte a Swipe -->
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
      <div class="d-flex flex-column gap-2.5">
        <?php foreach ($escalas as $esc) {
          $conf = strtoupper((string)($esc->status_confirmacao ?: 'PENDENTE'));
          $corDep = !empty($esc->cor_departamento) ? $esc->cor_departamento : '#2563eb';
          $txtDep = getContrasteTexto($corDep);
          $corCulto = !empty($esc->cor_evento) ? $esc->cor_evento : '#2563eb';

          $tsData = strtotime($esc->data_culto);
          $diaNum = date('d', $tsData);
          $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
          $diaSemanaNome = $diasSemana[(int)date('w', $tsData)] ?? '';
          $isPassado = (strtotime($esc->data_culto . ' 23:59:59') < time());
        ?>
          <div class="swipe-container" id="swipe_wrapper_<?= $esc->id_escala_voluntario ?>">

            <!-- Fundo de Ação Esquerda: Aceitar / Confirmar (Verde) -->
            <div class="swipe-bg swipe-bg-accept" id="bg_accept_<?= $esc->id_escala_voluntario ?>" style="opacity: 0;">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-3"></i>
                <span class="fs-6 fw-bold">Confirmar Presença</span>
              </div>
            </div>

            <!-- Fundo de Ação Direita: Recusar / Desmarcar (Vermelho) -->
            <div class="swipe-bg swipe-bg-reject" id="bg_reject_<?= $esc->id_escala_voluntario ?>" style="opacity: 0;">
              <div class="d-flex align-items-center gap-2">
                <span class="fs-6 fw-bold">Desmarcar / Recusar</span>
                <i class="bi bi-x-circle-fill fs-3"></i>
              </div>
            </div>

            <!-- Card Minimalista Interativo -->
            <div class="swipe-content p-3"
              id="card_escala_<?= $esc->id_escala_voluntario ?>"
              data-id="<?= $esc->id_escala_voluntario ?>"
              data-titulo="<?= esc($esc->titulo_culto) ?>"
              data-data="<?= date('d/m/Y', $tsData) ?>"
              data-status="<?= $conf ?>"
              data-passado="<?= $isPassado ? '1' : '0' ?>"
              style="border-left: 5px solid <?= esc($corCulto) ?> !important;">

              <div class="d-flex align-items-center gap-3">

                <!-- Coluna Esquerda: Calendário (2 linhas) + Status debaixo -->
                <div class="d-flex flex-column align-items-center flex-shrink-0" style="min-width: 72px; width: 72px;">
                  <!-- Box do Calendário com respiro interno sem encostar nas bordas -->
                  <div class="calendar-badge text-center w-100 shadow-xs mb-1.5" style="padding: 6px 4px 5px 4px; border-radius: 0.75rem;">
                    <span class="d-block text-uppercase fw-bold text-primary font-monospace" style="font-size: 0.65rem; line-height: 1.2; letter-spacing: 0.5px; margin-top: 1px;"><?= $diaSemanaNome ?></span>
                    <strong class="text-body d-block font-monospace" style="font-size: 1.32rem; line-height: 1; font-weight: 800; margin-top: 3px; margin-bottom: 2px;"><?= $diaNum ?></strong>
                  </div>

                  <!-- Status Encaixado Debaixo do Calendário com respiro -->
                  <div id="badge_status_<?= $esc->id_escala_voluntario ?>" class="w-100 text-center">
                    <?php if ($conf === 'CONFIRMADO') { ?>
                      <span class="badge status-badge-confirmado rounded-pill w-100 py-1 px-1 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-1 text-nowrap" style="font-size: 0.64rem;">
                        <i class="bi bi-check-circle-fill"></i> Aceito
                      </span>
                    <?php } elseif ($conf === 'RECUSADO') { ?>
                      <span class="badge status-badge-recusado rounded-pill w-100 py-1 px-1 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-1 text-nowrap" style="font-size: 0.64rem;" title="Justificativa: <?= esc($esc->justificativa_recusa) ?>">
                        <i class="bi bi-x-circle-fill"></i> Recusado
                      </span>
                    <?php } else { ?>
                      <span class="badge status-badge-pendente pulse-warning rounded-pill w-100 py-1 px-1 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-1 text-nowrap" style="font-size: 0.64rem;">
                        <i class="bi bi-hourglass-split"></i> Pendente
                      </span>
                    <?php } ?>
                  </div>
                </div>

                <!-- Coluna Direita: Informações em 3 Linhas -->
                <div class="flex-grow-1 overflow-hidden">
                  <!-- Linha 1: Nome do Culto -->
                  <h6 class="fw-bold mb-1 text-body fs-6 text-truncate" title="<?= esc($esc->titulo_culto) ?>">
                    <?= esc($esc->titulo_culto) ?>
                  </h6>

                  <!-- Linha 2: Horário -->
                  <div class="text-muted small mb-1.5 d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                    <i class="bi bi-clock"></i>
                    <span><?= substr($esc->horario_inicio, 0, 5) ?> - <?= substr($esc->horario_termino, 0, 5) ?></span>
                    <?php if ($isPassado) { ?>
                      <span class="badge bg-secondary-subtle text-secondary rounded-pill px-1.5 py-0 ms-1" style="font-size: 0.62rem;">Realizado</span>
                    <?php } ?>
                  </div>

                  <!-- Linha 3: Departamento e Sub-área Lado a Lado -->
                  <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="badge rounded-pill px-2.5 py-1 small fw-bold shadow-xs text-nowrap"
                      style="background-color: <?= esc($corDep) ?> !important; color: <?= esc($txtDep) ?> !important; font-size: 0.72rem; border: 1px solid rgba(0,0,0,0.1);">
                      <?= esc($esc->nome_departamento) ?>
                    </span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold text-nowrap" style="font-size: 0.72rem;">
                      <?= esc($esc->nome_area) ?>
                    </span>
                  </div>
                </div>

              </div>

              <!-- Mensagens / Justificativas Opcionais -->
              <?php if (!empty($esc->observacao)) { ?>
                <div class="mt-2 pt-2 border-top small text-secondary d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                  <i class="bi bi-info-circle-fill text-info"></i> <strong>Instrução:</strong> <?= esc($esc->observacao) ?>
                </div>
              <?php } ?>

              <?php if ($conf === 'RECUSADO' && !empty($esc->justificativa_recusa)) { ?>
                <div class="mt-2 pt-2 border-top small text-danger d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                  <i class="bi bi-chat-quote-fill"></i> <strong>Motivo da recusa:</strong> "<?= esc($esc->justificativa_recusa) ?>"
                </div>
              <?php } ?>

              <!-- Botões de Ação para Tablet / Desktop (Ocultos no Mobile para foco total no Swipe) -->
              <?php if (!$isPassado) { ?>
                <div class="d-none d-md-flex gap-2 mt-2 pt-2 border-top" id="acoes_escala_<?= $esc->id_escala_voluntario ?>">
                  <?php if ($conf !== 'CONFIRMADO') { ?>
                    <button type="button" class="btn btn-sm btn-confirmar rounded-pill px-3 py-1 flex-grow-1 small" onclick="confirmarPresencaAjax(<?= $esc->id_escala_voluntario ?>)">
                      <i class="bi bi-check-lg me-1"></i> Confirmar Presença
                    </button>
                  <?php } ?>

                  <?php if ($conf !== 'RECUSADO') { ?>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 flex-grow-1 small" onclick="abrirModalRecusa(<?= $esc->id_escala_voluntario ?>, '<?= esc($esc->titulo_culto) ?>', '<?= date('d/m/Y', $tsData) ?>')">
                      <i class="bi bi-x-lg me-1"></i> Desmarcar / Recusar
                    </button>
                  <?php } else { ?>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 flex-grow-1 small" onclick="confirmarPresencaAjax(<?= $esc->id_escala_voluntario ?>)">
                      <i class="bi bi-arrow-repeat me-1"></i> Alterar para Confirmado
                    </button>
                  <?php } ?>
                </div>
              <?php } ?>

            </div>

          </div>
        <?php } ?>
      </div>
    <?php } ?>

  </div>

  <!-- Modal de Recusa / Justificativa (Mantido) -->
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

  <!-- Modal / Overlay de Onboarding Swipe com Fundo Embaçado (Blur) -->
  <div id="swipeOnboardingBackdrop" class="swipe-onboarding-backdrop" role="dialog" aria-modal="true" onclick="if(event.target===this) fecharOnboardingSwipe(false)">
    <div class="swipe-onboarding-modal p-4 text-center">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="badge bg-primary-subtle text-primary-emphasis px-2.5 py-1 rounded-pill small fw-bold">
          <i class="bi bi-magic me-1"></i> Nova Funcionalidade
        </span>
        <button type="button" class="btn-close" onclick="fecharOnboardingSwipe(false)" aria-label="Fechar"></button>
      </div>

      <h5 class="fw-bold text-body mt-2 mb-1" style="letter-spacing: -0.02em;">Gerencie suas Escalas com Facilidade</h5>
      <p class="text-secondary small mb-3">Arraste os cards lateralmente para responder em instantes:</p>

      <!-- Ilustração Interativa com Demonstração Animada -->
      <div class="swipe-demo-box mb-3 d-flex align-items-center justify-content-center">
        <div class="swipe-demo-indicator-right">
          <i class="bi bi-check-circle-fill me-1"></i> ACEITAR
        </div>
        <div class="swipe-demo-indicator-left">
          RECUSAR <i class="bi bi-x-circle-fill ms-1"></i>
        </div>
        <div class="swipe-demo-card">
          <i class="bi bi-calendar-event text-primary fs-5"></i>
          <div class="text-start lh-1">
            <span class="d-block fw-bold text-body" style="font-size: 0.8rem;">Escala de Culto</span>
            <small class="text-muted" style="font-size: 0.68rem;">Domingo • 18:00</small>
          </div>
          <i class="bi bi-hand-index-thumb-fill swipe-hand-cursor ms-1"></i>
        </div>
      </div>

      <!-- Detalhamento Visual das Ações (Centralizado: Ícone > Título > Descrição) -->
      <div class="row g-2.5 text-center mb-3">
        <div class="col-6">
          <div class="p-3 rounded-4 bg-success-subtle border border-success-subtle h-100 d-flex flex-column align-items-center text-center">
            <div class="mb-2">
              <i class="bi bi-arrow-right-circle-fill text-success" style="font-size: 1.85rem;"></i>
            </div>
            <strong class="text-success-emphasis mb-1 d-block" style="font-size: 0.85rem; letter-spacing: -0.01em;">Para a Direita</strong>
            <p class="text-success-emphasis mb-0" style="font-size: 0.74rem; line-height: 1.35;">
              <strong>Aceita</strong> e confirma sua presença imediatamente.
            </p>
          </div>
        </div>
        <div class="col-6">
          <div class="p-3 rounded-4 bg-danger-subtle border border-danger-subtle h-100 d-flex flex-column align-items-center text-center">
            <div class="mb-2">
              <i class="bi bi-arrow-left-circle-fill text-danger" style="font-size: 1.85rem;"></i>
            </div>
            <strong class="text-danger-emphasis mb-1 d-block" style="font-size: 0.85rem; letter-spacing: -0.01em;">Para a Esquerda</strong>
            <p class="text-danger-emphasis mb-0" style="font-size: 0.74rem; line-height: 1.35;">
              <strong>Recusa</strong> e abre o motivo para informar a liderança.
            </p>
          </div>
        </div>
      </div>

      <!-- Opção de Não Exibir Novamente -->
      <div class="form-check text-start mb-3 ms-1">
        <input class="form-check-input" type="checkbox" id="chkNaoExibirMaisSwipe" checked>
        <label class="form-check-label text-secondary small fw-medium" for="chkNaoExibirMaisSwipe" style="font-size: 0.8rem;">
          Não exibir este guia novamente
        </label>
      </div>

      <!-- Botão de Ação -->
      <button type="button" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-sm" onclick="fecharOnboardingSwipe(true)">
        Entendi, Vamos Lá! <i class="bi bi-arrow-right ms-1"></i>
      </button>
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
      const bsToast = new bootstrap.Toast(toastEl, {
        delay: 3500
      });
      bsToast.show();
    }

    function confirmarPresencaAjax(id_escala) {
      const btnContainer = document.getElementById(`acoes_escala_${id_escala}`);
      const originalHtml = btnContainer ? btnContainer.innerHTML : '';
      if (btnContainer) {
        btnContainer.innerHTML = '<div class="text-center py-1 text-success small"><div class="spinner-border spinner-border-sm me-2"></div>Confirmando presença...</div>';
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
            setTimeout(() => window.location.reload(), 500);
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
            setTimeout(() => window.location.reload(), 500);
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

    // ========================================================
    // GESTOS DE SWIPE / ARRASTE (iOS / Google Messages Style)
    // ========================================================
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.swipe-content');

      cards.forEach(card => {
        const isPassado = card.getAttribute('data-passado') === '1';
        if (isPassado) return;

        const id = card.getAttribute('data-id');
        const titulo = card.getAttribute('data-titulo');
        const data = card.getAttribute('data-data');
        const bgAccept = document.getElementById(`bg_accept_${id}`);
        const bgReject = document.getElementById(`bg_reject_${id}`);

        let startX = 0;
        let startY = 0;
        let isSwiping = false;
        let isHorizontal = false;
        const threshold = 85; // Limiar de pixels para disparar a ação

        function onStart(e) {
          const touch = e.touches ? e.touches[0] : e;
          startX = touch.clientX;
          startY = touch.clientY;
          isSwiping = true;
          isHorizontal = false;
          card.classList.add('swiping');
        }

        function onMove(e) {
          if (!isSwiping) return;
          const touch = e.touches ? e.touches[0] : e;
          const dx = touch.clientX - startX;
          const dy = touch.clientY - startY;

          // Se o movimento for predominantemente horizontal
          if (!isHorizontal) {
            if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 8) {
              isHorizontal = true;
            } else if (Math.abs(dy) > 8) {
              isSwiping = false;
              card.classList.remove('swiping');
              return;
            }
          }

          if (isHorizontal) {
            if (e.cancelable) e.preventDefault();

            // Amortecimento de limite
            const resistDx = (dx > 0) ? Math.min(dx, 150) : Math.max(dx, -150);
            card.style.transform = `translateX(${resistDx}px)`;

            if (dx > 0) {
              // Deslizando para a Direita -> Aceitar
              if (bgAccept) bgAccept.style.opacity = Math.min(1, dx / threshold);
              if (bgReject) bgReject.style.opacity = '0';
            } else {
              // Deslizando para a Esquerda -> Recusar
              if (bgReject) bgReject.style.opacity = Math.min(1, Math.abs(dx) / threshold);
              if (bgAccept) bgAccept.style.opacity = '0';
            }
          }
        }

        function onEnd(e) {
          if (!isSwiping) return;
          isSwiping = false;
          card.classList.remove('swiping');

          const touch = e.changedTouches ? e.changedTouches[0] : e;
          const dx = touch.clientX - startX;

          if (isHorizontal) {
            if (dx > threshold) {
              // Ação Direita: Aceitar / Confirmar
              card.style.transform = 'translateX(100%)';
              setTimeout(() => {
                confirmarPresencaAjax(id);
              }, 150);
            } else if (dx < -threshold) {
              // Ação Esquerda: Recusar / Abrir Modal
              card.style.transform = 'translateX(-100%)';
              setTimeout(() => {
                card.style.transform = 'translateX(0)';
                if (bgReject) bgReject.style.opacity = '0';
                abrirModalRecusa(id, titulo, data);
              }, 200);
            } else {
              // Reset suave se não atingiu o limiar
              card.style.transform = 'translateX(0)';
              if (bgAccept) bgAccept.style.opacity = '0';
              if (bgReject) bgReject.style.opacity = '0';
            }
          } else {
            card.style.transform = 'translateX(0)';
            if (bgAccept) bgAccept.style.opacity = '0';
            if (bgReject) bgReject.style.opacity = '0';
          }
        }

        // Eventos Touch (Mobile)
        card.addEventListener('touchstart', onStart, {
          passive: true
        });
        card.addEventListener('touchmove', onMove, {
          passive: false
        });
        card.addEventListener('touchend', onEnd);
        card.addEventListener('touchcancel', onEnd);

        // Eventos Mouse (Desktop drag)
        card.addEventListener('mousedown', onStart);
        window.addEventListener('mousemove', function(e) {
          if (isSwiping) onMove(e);
        });
        window.addEventListener('mouseup', function(e) {
          if (isSwiping) onEnd(e);
        });
      });

      // Inicializa Onboarding com Fundo Embaçado
      inicializarOnboardingSwipe();
    });

    function inicializarOnboardingSwipe() {
      const modal = document.getElementById('swipeOnboardingBackdrop');
      if (!modal) return;
      const ocultar = localStorage.getItem('advec_swipe_onboarding_v1');
      if (ocultar !== 'dismissed') {
        setTimeout(() => {
          modal.classList.add('active');
        }, 350);
      }
    }

    function fecharOnboardingSwipe(daAcaoBotao = false) {
      const modal = document.getElementById('swipeOnboardingBackdrop');
      const chk = document.getElementById('chkNaoExibirMaisSwipe');

      if (chk && chk.checked) {
        localStorage.setItem('advec_swipe_onboarding_v1', 'dismissed');
      }

      if (modal) {
        modal.classList.remove('active');
      }
    }

    function reabrirOnboardingSwipe() {
      const modal = document.getElementById('swipeOnboardingBackdrop');
      if (modal) {
        modal.classList.add('active');
      }
    }
  </script>
</body>

</html>