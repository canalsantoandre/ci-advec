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

  <!-- Anti-flicker Theme Init -->
  <script>
    (function() {
      const t = localStorage.getItem('portal_theme') || localStorage.getItem('theme');
      let resolved = t;
      if (!t || t === 'auto') {
        resolved = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      }
      document.documentElement.setAttribute('data-bs-theme', resolved);
    })();
  </script>

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

    /* Cards de Estatísticas Interativos (Filtros Premium) */
    .stat-filter-card {
      cursor: pointer;
      user-select: none;
      -webkit-user-select: none;
      position: relative;
      border-radius: 1.15rem;
      padding: 0.75rem 0.6rem;
      text-align: center;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      border: 1.5px solid rgba(0, 0, 0, 0.08);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 2px;
    }

    .stat-filter-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.1);
    }

    .stat-filter-card:active {
      transform: translateY(-1px) scale(0.98);
    }

    .stat-card-total {
      background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .stat-card-total.active {
      background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%) !important;
      border-color: #3b82f6 !important;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2), 0 8px 20px -4px rgba(59, 130, 246, 0.25) !important;
    }

    .stat-card-aceitas {
      background: linear-gradient(180deg, #f0fdf4 0%, #e8fbee 100%);
      border-color: #bbf7d0;
    }

    .stat-card-aceitas.active {
      background: linear-gradient(180deg, #dcfce7 0%, #bbf7d0 100%) !important;
      border-color: #16a34a !important;
      box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2), 0 8px 20px -4px rgba(22, 163, 74, 0.25) !important;
    }

    .stat-card-pendentes {
      background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%);
      border-color: #fde68a;
    }

    .stat-card-pendentes.active {
      background: linear-gradient(180deg, #fef3c7 0%, #fde68a 100%) !important;
      border-color: #d97706 !important;
      box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.2), 0 8px 20px -4px rgba(217, 119, 6, 0.25) !important;
    }

    [data-bs-theme="dark"] .stat-card-total {
      background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
      border-color: rgba(255, 255, 255, 0.08);
    }

    [data-bs-theme="dark"] .stat-card-total.active {
      background: linear-gradient(180deg, rgba(30, 58, 138, 0.4) 0%, #1e293b 100%) !important;
      border-color: #3b82f6 !important;
    }

    [data-bs-theme="dark"] .stat-card-aceitas {
      background: linear-gradient(180deg, rgba(20, 83, 45, 0.25) 0%, #1e293b 100%);
      border-color: rgba(34, 197, 94, 0.3);
    }

    [data-bs-theme="dark"] .stat-card-aceitas.active {
      background: linear-gradient(180deg, rgba(20, 83, 45, 0.5) 0%, #1e293b 100%) !important;
      border-color: #22c55e !important;
    }

    [data-bs-theme="dark"] .stat-card-pendentes {
      background: linear-gradient(180deg, rgba(120, 53, 15, 0.25) 0%, #1e293b 100%);
      border-color: rgba(245, 158, 11, 0.3);
    }

    [data-bs-theme="dark"] .stat-card-pendentes.active {
      background: linear-gradient(180deg, rgba(120, 53, 15, 0.5) 0%, #1e293b 100%) !important;
      border-color: #f59e0b !important;
    }

    .stat-filter-indicator {
      font-size: 0.68rem;
      font-weight: 800;
      letter-spacing: 0.6px;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 3px;
    }

    .stat-filter-count {
      font-size: 1.45rem;
      font-weight: 800;
      line-height: 1.1;
    }

    /* Swipe Container & Gestos Touch (Estilo iOS / Google) */
    .swipe-container {
      position: relative;
      overflow: hidden;
      border-radius: 1.25rem;
      margin-bottom: 0.85rem;
      touch-action: pan-y;
      user-select: none;
      -webkit-user-select: none;
      transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .swipe-bg {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      padding: 0 1.5rem;
      border-radius: 1.25rem;
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
      border-radius: 1.25rem;
      border: 1px solid rgba(0, 0, 0, 0.08);
      box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
      transition: transform 0.28s cubic-bezier(0.2, 0.9, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
      z-index: 2;
      cursor: grab;
    }

    .swipe-content:active {
      cursor: grabbing;
    }

    [data-bs-theme="dark"] .swipe-content {
      border-color: rgba(255, 255, 255, 0.08);
      background: #1e293b;
      box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.3);
    }

    .swipe-content:hover {
      box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.1);
      transform: translateY(-2px);
    }

    .swipe-content.swiping {
      transition: none !important;
    }

    .calendar-badge {
      border: 1px solid rgba(0, 0, 0, 0.07);
      background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
      transition: transform 0.15s ease;
    }

    [data-bs-theme="dark"] .calendar-badge {
      background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
      border-color: #334155;
    }

    .status-badge-pendente {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      color: #92400e;
      border: 1px solid #fcd34d;
    }

    .status-badge-confirmado {
      background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
      color: #166534;
      border: 1px solid #86efac;
    }

    .status-badge-recusado {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      color: #991b1b;
      border: 1px solid #fca5a5;
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

    /* ========================================================
       AVATAR STACK (FACEPILE) & BOTTOM SHEET PARTICIPANTES
       ======================================================== */
    .facepile-stack-wrapper {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      cursor: pointer;
      padding: 2px 7px 2px 3px;
      border-radius: 9999px;
      background: rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(0, 0, 0, 0.08);
      transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
    }

    .facepile-stack-wrapper:hover {
      background: rgba(37, 99, 235, 0.08);
      border-color: rgba(37, 99, 235, 0.3);
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    }

    .facepile-stack-wrapper:active {
      transform: scale(0.97);
    }

    [data-bs-theme="dark"] .facepile-stack-wrapper {
      background: rgba(255, 255, 255, 0.05);
      border-color: rgba(255, 255, 255, 0.1);
    }

    [data-bs-theme="dark"] .facepile-stack-wrapper:hover {
      background: rgba(59, 130, 246, 0.18);
      border-color: rgba(59, 130, 246, 0.4);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .facepile-avatars {
      display: inline-flex;
      align-items: center;
      flex-direction: row;
    }

    .facepile-avatar-item {
      position: relative;
      width: 26px;
      height: 26px;
      border-radius: 50%;
      border: 2px solid #ffffff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
      object-fit: cover;
      margin-left: -8px;
      transition: transform 0.2s ease, z-index 0.2s ease, box-shadow 0.2s ease;
      background-color: #e2e8f0;
      cursor: pointer;
    }

    .facepile-avatar-item:first-child {
      margin-left: 0;
    }

    .facepile-avatar-item:hover {
      transform: scale(1.18);
      z-index: 15 !important;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    [data-bs-theme="dark"] .facepile-avatar-item {
      border-color: #1e293b;
      background-color: #334155;
    }

    .facepile-counter-bubble {
      position: relative;
      width: 26px;
      height: 26px;
      border-radius: 50%;
      border: 2px solid #ffffff;
      margin-left: -8px;
      background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
      color: #1e40af;
      font-size: 0.65rem;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease;
    }

    [data-bs-theme="dark"] .facepile-counter-bubble {
      border-color: #1e293b;
      background: linear-gradient(135deg, #1e293b 0%, #172554 100%);
      color: #93c5fd;
    }

    .facepile-label-hint {
      font-size: 0.68rem;
      font-weight: 700;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 2px;
      padding-right: 2px;
      margin-left: 2px;
    }

    [data-bs-theme="dark"] .facepile-label-hint {
      color: #94a3b8;
    }

    /* Modal / Bottom Sheet Mobile-First */
    @media (max-width: 767.98px) {
      .modal-bottom-sheet-mobile {
        padding-right: 0 !important;
        padding-left: 0 !important;
      }

      .modal-bottom-sheet-mobile .modal-dialog {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        margin: 0;
        max-width: 100%;
        transform: translateY(100%);
        transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
      }

      .modal-bottom-sheet-mobile.show .modal-dialog {
        transform: translateY(0);
      }

      .modal-bottom-sheet-mobile .modal-content {
        border-radius: 1.5rem 1.5rem 0 0 !important;
        border-bottom: 0;
        max-height: 85vh;
        box-shadow: 0 -10px 35px rgba(0, 0, 0, 0.4);
      }
    }

    .bottom-sheet-drag-handle {
      width: 42px;
      height: 5px;
      border-radius: 999px;
      background-color: rgba(0, 0, 0, 0.18);
      margin: 8px auto 2px auto;
    }

    [data-bs-theme="dark"] .bottom-sheet-drag-handle {
      background-color: rgba(255, 255, 255, 0.25);
    }

    .participante-card-item {
      background: #ffffff;
      border: 1px solid rgba(0, 0, 0, 0.07);
      border-radius: 1rem;
      padding: 0.75rem 0.85rem;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      cursor: pointer;
      user-select: none;
      -webkit-user-select: none;
    }

    .participante-card-item:hover {
      border-color: rgba(37, 99, 235, 0.35);
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
      background-color: #f8fafc;
    }

    [data-bs-theme="dark"] .participante-card-item {
      background: #1e293b;
      border-color: rgba(255, 255, 255, 0.08);
    }

    [data-bs-theme="dark"] .participante-card-item:hover {
      background-color: #24344d;
      border-color: rgba(59, 130, 246, 0.4);
    }

    .participante-nome-expand {
      max-height: 0;
      opacity: 0;
      overflow: hidden;
      transition: max-height 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.22s ease;
    }

    .participante-card-item.expanded .participante-nome-expand {
      max-height: 180px;
      opacity: 1;
    }

    .participante-detalhes-clean {
      border-top: 1px solid rgba(0, 0, 0, 0.06);
      padding-top: 0.45rem;
      margin-top: 0.5rem;
    }

    [data-bs-theme="dark"] .participante-detalhes-clean {
      border-top-color: rgba(255, 255, 255, 0.08);
    }

    .social-icon-btn {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.76rem;
      text-decoration: none;
      transition: transform 0.2s ease, opacity 0.2s ease;
      opacity: 0.9;
    }

    .social-icon-btn:hover {
      transform: translateY(-2px) scale(1.1);
      opacity: 1;
    }

    .btn-whatsapp-action {
      background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
      color: #ffffff !important;
      border: none;
      font-weight: 700;
      border-radius: 9999px;
      padding: 0.38rem 0.72rem;
      font-size: 0.75rem;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3);
      transition: all 0.2s ease;
      text-decoration: none;
      white-space: nowrap;
    }

    .btn-whatsapp-action:hover {
      background: linear-gradient(135deg, #20ba5a 0%, #0e7266 100%);
      box-shadow: 0 4px 12px rgba(37, 211, 102, 0.45);
      transform: translateY(-1px);
    }

    .btn-whatsapp-action:active {
      transform: scale(0.96);
    }
  </style>
</head>

<body>

  <?php
  // Pré-cálculos de Vigência e Status
  $hojeIso = date('Y-m-d');
  $totalVigentes = 0;
  $totalPassadas = 0;

  if (!empty($escalas)) {
    foreach ($escalas as $escCheck) {
      $dataIso = date('Y-m-d', strtotime($escCheck->data_culto));
      if ($dataIso >= $hojeIso) {
        $totalVigentes++;
      } else {
        $totalPassadas++;
      }
    }
  }
  // Se houver escalas vigentes no mês, inicia mostrando apenas as vigentes. Se todas forem passadas, mostra todas.
  $modoInicial = ($totalVigentes > 0) ? 'vigentes' : 'todos';
  ?>

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

        <!-- Cards de Estatísticas Interativos com Filtro por Clique -->
        <div class="row g-2 mt-2 pt-2 border-top">
          <!-- TOTAL -->
          <div class="col-4">
            <div class="stat-filter-card stat-card-total <?= ($modoInicial === 'todos' ? 'active' : '') ?>"
              id="filter_card_todos"
              onclick="aplicarFiltro('todos')"
              role="button"
              tabindex="0"
              title="Clique para ver todas as escalas">
              <span class="stat-filter-indicator text-secondary">
                <i class="bi bi-grid-fill"></i> TOTAL
              </span>
              <strong class="stat-filter-count text-body"><?= $totalMes ?></strong>
            </div>
          </div>

          <!-- ACEITAS -->
          <div class="col-4">
            <div class="stat-filter-card stat-card-aceitas"
              id="filter_card_aceitas"
              onclick="aplicarFiltro('aceitas')"
              role="button"
              tabindex="0"
              title="Clique para filtrar apenas escalas aceitas">
              <span class="stat-filter-indicator text-success-emphasis">
                <i class="bi bi-check-circle-fill"></i> ACEITAS
              </span>
              <strong class="stat-filter-count text-success-emphasis"><?= $totalConfirmadas ?></strong>
            </div>
          </div>

          <!-- PENDENTES -->
          <div class="col-4">
            <div class="stat-filter-card stat-card-pendentes <?= ($totalPendentes > 0 ? 'pulse-warning' : '') ?>"
              id="filter_card_pendentes"
              onclick="aplicarFiltro('pendentes')"
              role="button"
              tabindex="0"
              title="Clique para filtrar apenas escalas pendentes">
              <span class="stat-filter-indicator text-warning-emphasis">
                <i class="bi bi-hourglass-split"></i> PENDENTES
              </span>
              <strong class="stat-filter-count text-warning-emphasis"><?= $totalPendentes ?></strong>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Barra Informativa de Filtro Ativo & Ação de Gestos -->
    <?php if (!empty($escalas)) { ?>
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 px-1">
        <!-- Status do Filtro e Opção de Ver Passadas -->
        <div class="d-flex align-items-center gap-1.5 flex-wrap">
          <span class="badge bg-body text-secondary border rounded-pill px-2.5 py-1.5 small fw-semibold shadow-xs d-flex align-items-center gap-1.5" id="filtroAtivoBadge">
            <i class="bi bi-funnel-fill text-primary" id="filtroAtivoIcon"></i>
            <span id="filtroAtivoTexto">Exibindo escalas vigentes</span>
          </span>

          <?php if ($totalPassadas > 0) { ?>
            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold text-primary small" id="btnTogglePassadas" onclick="togglePassadas()" style="font-size: 0.76rem;">
              <i class="bi bi-clock-history me-1"></i> Ver anteriores (<?= $totalPassadas ?>)
            </button>
          <?php } ?>
        </div>

        <!-- Botão de Ajuda Swipe -->
        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-primary" style="font-size: 0.76rem;" onclick="reabrirOnboardingSwipe()">
          <i class="bi bi-question-circle me-1"></i> Como usar?
        </button>
      </div>
    <?php } ?>

    <!-- Lista de Escalas Minimalistas e Premium -->
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
      <div class="d-flex flex-column gap-2" id="listaEscalasContainer">
        <?php foreach ($escalas as $esc) {
          $conf = strtoupper((string)($esc->status_confirmacao ?: 'PENDENTE'));
          $corDep = !empty($esc->cor_departamento) ? $esc->cor_departamento : '#2563eb';
          $txtDep = getContrasteTexto($corDep);
          $corCulto = !empty($esc->cor_evento) ? $esc->cor_evento : '#2563eb';

          $tsData = strtotime($esc->data_culto);
          $dataCultoIso = date('Y-m-d', $tsData);
          $diaNum = date('d', $tsData);
          $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
          $diaSemanaNome = $diasSemana[(int)date('w', $tsData)] ?? '';
          $isPassado = ($dataCultoIso < $hojeIso);
          $isHoje = ($dataCultoIso === $hojeIso);
          $isAmanha = ($dataCultoIso === date('Y-m-d', strtotime('+1 day')));
        ?>
          <div class="swipe-container <?= ($isPassado && $modoInicial === 'vigentes') ? 'd-none' : '' ?>"
            id="swipe_wrapper_<?= $esc->id_escala_voluntario ?>"
            data-escala-item="1"
            data-passado="<?= $isPassado ? '1' : '0' ?>"
            data-status="<?= $conf ?>">

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

            <!-- Card Minimalista Interativo Premium -->
            <div class="swipe-content p-3"
              id="card_escala_<?= $esc->id_escala_voluntario ?>"
              data-id="<?= $esc->id_escala_voluntario ?>"
              data-titulo="<?= esc($esc->titulo_culto) ?>"
              data-data="<?= date('d/m/Y', $tsData) ?>"
              data-status="<?= $conf ?>"
              data-passado="<?= $isPassado ? '1' : '0' ?>"
              style="border-left: 6px solid <?= esc($corCulto) ?> !important;">

              <div class="d-flex align-items-center gap-3">

                <!-- Coluna Esquerda: Calendário (2 linhas) + Status debaixo -->
                <div class="d-flex flex-column align-items-center flex-shrink-0" style="min-width: 74px; width: 74px;">
                  <!-- Box do Calendário com respiro interno sem encostar nas bordas -->
                  <div class="calendar-badge text-center w-100 shadow-xs mb-1.5" style="padding: 6px 4px 5px 4px; border-radius: 0.85rem;">
                    <span class="d-block text-uppercase fw-bold text-primary font-monospace" style="font-size: 0.66rem; line-height: 1.2; letter-spacing: 0.6px; margin-top: 1px;"><?= $diaSemanaNome ?></span>
                    <strong class="text-body d-block font-monospace" style="font-size: 1.38rem; line-height: 1; font-weight: 800; margin-top: 3px; margin-bottom: 2px;"><?= $diaNum ?></strong>
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

                  <!-- Linha 2: Horário e Badges de Tempo -->
                  <div class="text-muted small mb-1.5 d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 0.78rem;">
                    <div class="d-flex align-items-center gap-1">
                      <i class="bi bi-clock"></i>
                      <span><?= substr($esc->horario_inicio, 0, 5) ?> - <?= substr($esc->horario_termino, 0, 5) ?></span>
                    </div>

                    <?php if ($isHoje) { ?>
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0 fw-bold" style="font-size: 0.64rem;">
                        <i class="bi bi-record-circle-fill me-1"></i>Hoje
                      </span>
                    <?php } elseif ($isAmanha) { ?>
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0 fw-bold" style="font-size: 0.64rem;">
                        Amanhã
                      </span>
                    <?php } elseif ($isPassado) { ?>
                      <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0" style="font-size: 0.64rem;">
                        Realizado
                      </span>
                    <?php } ?>
                  </div>

                  <!-- Linha 3: Departamento, Sub-área e Avatar Stack (Facepile) -->
                  <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mt-0.5">
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                      <span class="badge rounded-pill px-2.5 py-1 small fw-bold shadow-xs text-nowrap"
                        style="background-color: <?= esc($corDep) ?> !important; color: <?= esc($txtDep) ?> !important; font-size: 0.72rem; border: 1px solid rgba(0,0,0,0.1);">
                        <?= esc($esc->nome_departamento) ?>
                      </span>
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold text-nowrap" style="font-size: 0.72rem;">
                        <?= esc($esc->nome_area) ?>
                      </span>
                    </div>

                    <?php
                    $meuIdVol = (int)($voluntario->id_voluntario ?? 0);
                    $participantes = array_values(array_filter($esc->participantes ?? [], function ($p) use ($meuIdVol) {
                      return (int)$p->id_voluntario !== $meuIdVol;
                    }));
                    $totalPart = count($participantes);
                    if ($totalPart > 0) {
                      $participantesJson = htmlspecialchars(json_encode($participantes), ENT_QUOTES, 'UTF-8');
                      $maxAvatares = 3;
                      $exibirAvatares = array_slice($participantes, 0, $maxAvatares);
                      $restantes = $totalPart - $maxAvatares;
                      $tituloCultoEsc = esc(addslashes($esc->titulo_culto));
                      $nomeDepEsc = esc(addslashes($esc->nome_departamento));
                    ?>
                      <!-- Avatar Stack (Facepile) -->
                      <div class="facepile-stack-wrapper shadow-xs"
                        onclick="event.stopPropagation(); abrirModalParticipantes(<?= $participantesJson ?>, '<?= $tituloCultoEsc ?>', '<?= date('d/m/Y', $tsData) ?>', '<?= $nomeDepEsc ?>', '<?= esc($corDep) ?>', '<?= esc($txtDep) ?>')"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Ver <?= $totalPart ?> <?= $totalPart === 1 ? 'outro voluntário escalado' : 'outros voluntários escalados' ?>">
                        <div class="facepile-avatars">
                          <?php
                          $zIndex = 5;
                          foreach ($exibirAvatares as $p) {
                            $fotoPart = !empty($p->foto_url) ? $p->foto_url : ('https://ui-avatars.com/api/?name=' . urlencode($p->nome_completo) . '&background=2563eb&color=fff&size=64&bold=true');
                            $nomeTooltip = esc($p->nome_completo) . ' (' . esc($p->sub_area) . ')';
                          ?>
                            <img src="<?= esc($fotoPart) ?>"
                              alt="<?= esc($p->primeiro_nome) ?>"
                              class="facepile-avatar-item"
                              style="z-index: <?= $zIndex ?>;"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              title="<?= $nomeTooltip ?>"
                              onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($p->nome_completo) ?>&background=2563eb&color=fff&size=64&bold=true';">
                          <?php
                            $zIndex--;
                          }
                          ?>
                          <?php if ($restantes > 0) { ?>
                            <span class="facepile-counter-bubble" style="z-index: <?= $zIndex ?>;" data-bs-toggle="tooltip" data-bs-placement="top" title="Mais <?= $restantes ?> <?= $restantes === 1 ? 'voluntário' : 'voluntários' ?>">
                              +<?= $restantes ?>
                            </span>
                          <?php } ?>
                        </div>
                        <span class="facepile-label-hint">
                          <i class="bi bi-people-fill text-primary" style="font-size: 0.72rem;"></i>
                          <span><?= $totalPart ?></span>
                        </span>
                      </div>
                    <?php } ?>
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

              <!-- Materiais de Apoio / Músicas da Escala (Consumo do Voluntário) -->
              <?php if (!empty($esc->recursos)) { ?>
                <div class="mt-3 pt-2.5 border-top">
                  <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="fw-bold text-primary small d-flex align-items-center gap-1" style="font-size: 0.76rem;">
                      <i class="bi bi-collection-play-fill text-primary"></i> Materiais de Apoio (<?= count($esc->recursos) ?>)
                    </span>
                  </div>
                  <div class="d-flex flex-wrap gap-1.5">
                    <?php foreach ($esc->recursos as $resItem) {
                      $resJson = htmlspecialchars(json_encode($resItem), ENT_QUOTES, 'UTF-8');
                    ?>
                      <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-start d-inline-flex align-items-center gap-1.5 shadow-xs" onclick="abrirPlayerVoluntario(<?= $resJson ?>)" style="font-size: 0.72rem;">
                        <i class="<?= esc($resItem->type_icon) ?> text-<?= esc($resItem->type_color) ?>"></i>
                        <span class="text-truncate fw-semibold" style="max-width: 140px;">&nbsp;<?= esc($resItem->title) ?></span>
                        &nbsp;<i class="bi bi-play-circle text-primary ms-0.5"></i>
                      </button>
                    <?php } ?>
                  </div>
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

        <!-- Empty State de Filtro -->
        <div id="emptyFilterState" class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-body" style="display: none;">
          <div class="mb-3">
            <i class="bi bi-funnel text-muted" style="font-size: 3rem;"></i>
          </div>
          <h6 class="fw-bold text-body mb-1" id="emptyFilterTitle">Nenhuma escala encontrada</h6>
          <p class="text-secondary small mb-3" id="emptyFilterDesc">Nenhuma escala coincide com o filtro selecionado.</p>
          <div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-semibold" onclick="aplicarFiltro('todos')">
              <i class="bi bi-calendar-range me-1"></i> Ver Todas as Escalas
            </button>
          </div>
        </div>

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

  <!-- Modal / Bottom Sheet de Participantes da Escala (Facepile Modal) -->
  <div class="modal fade modal-bottom-sheet-mobile" id="modalParticipantesEscala" tabindex="-1" aria-labelledby="modalPartTituloCulto" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 500px;">
      <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
        <!-- Barra visual de arrasto no Mobile (Bottom Sheet Handle) -->
        <div class="d-block d-md-none pt-1">
          <div class="bottom-sheet-drag-handle"></div>
        </div>

        <div class="modal-header border-0 pb-1 pt-3 px-3.5">
          <div class="w-100">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <span class="badge rounded-pill px-2.5 py-1 small fw-bold" id="modalPartDepBadge" style="font-size: 0.72rem;">Departamento</span>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <h5 class="modal-title fw-bold text-body fs-5 mb-0" id="modalPartTituloCulto">Equipe Escalada</h5>
            <div class="text-secondary small mt-1 d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 0.78rem;">
              <span class="d-flex align-items-center gap-1">
                <i class="bi bi-calendar-check text-primary"></i>
                <span id="modalPartDataCulto">Data</span>
              </span>
              <span class="text-muted">•</span>
              <span id="modalPartTotalCount" class="fw-bold text-primary">0 voluntários</span>
            </div>
          </div>
        </div>

        <div class="modal-body py-2.5 px-3.5" style="max-height: 60vh; overflow-y: auto;">


          <div id="modalParticipantesLista" class="d-flex flex-column gap-2">
            <!-- Participantes renderizados dinamicamente via JS -->
          </div>
        </div>

        <div class="modal-footer border-0 pt-2 pb-3 px-3.5 bg-body-tertiary">
          <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-4 w-100 fw-semibold" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i> Fechar
          </button>
        </div>
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
    // FILTRAGEM INTERATIVA (TOTAL, ACEITAS, PENDENTES, VIGENTES)
    // ========================================================
    let filtroAtual = '<?= $modoInicial ?>';
    const totalPassadas = <?= (int)$totalPassadas ?>;
    const totalVigentes = <?= (int)$totalVigentes ?>;

    function aplicarFiltro(tipo) {
      filtroAtual = tipo;
      const items = document.querySelectorAll('.swipe-container[data-escala-item="1"]');
      const emptyState = document.getElementById('emptyFilterState');
      const emptyTitle = document.getElementById('emptyFilterTitle');
      const emptyDesc = document.getElementById('emptyFilterDesc');
      const badgeTexto = document.getElementById('filtroAtivoTexto');
      const badgeIcon = document.getElementById('filtroAtivoIcon');
      const btnToggle = document.getElementById('btnTogglePassadas');

      // Limpa classes ativas dos cards de filtro
      document.getElementById('filter_card_todos')?.classList.remove('active');
      document.getElementById('filter_card_aceitas')?.classList.remove('active');
      document.getElementById('filter_card_pendentes')?.classList.remove('active');

      let visiveis = 0;

      items.forEach(item => {
        const status = item.getAttribute('data-status');
        const isPassado = item.getAttribute('data-passado') === '1';
        let exibir = false;

        if (tipo === 'todos') {
          exibir = true;
        } else if (tipo === 'vigentes') {
          exibir = !isPassado;
        } else if (tipo === 'aceitas') {
          exibir = (status === 'CONFIRMADO');
        } else if (tipo === 'pendentes') {
          exibir = (status === 'PENDENTE');
        }

        if (exibir) {
          item.classList.remove('d-none');
          visiveis++;
        } else {
          item.classList.add('d-none');
        }
      });

      // Atualiza o card de filtro ativo
      if (tipo === 'todos') {
        document.getElementById('filter_card_todos')?.classList.add('active');
        if (badgeTexto) badgeTexto.textContent = `Exibindo todas as escalas (${visiveis})`;
        if (badgeIcon) badgeIcon.className = 'bi bi-grid-fill text-primary';
        if (btnToggle) {
          btnToggle.innerHTML = '<i class="bi bi-eye-slash me-1"></i> Ocultar anteriores';
        }
      } else if (tipo === 'aceitas') {
        document.getElementById('filter_card_aceitas')?.classList.add('active');
        if (badgeTexto) badgeTexto.textContent = `Exibindo escalas aceitas (${visiveis})`;
        if (badgeIcon) badgeIcon.className = 'bi bi-check-circle-fill text-success';
      } else if (tipo === 'pendentes') {
        document.getElementById('filter_card_pendentes')?.classList.add('active');
        if (badgeTexto) badgeTexto.textContent = `Exibindo escalas pendentes (${visiveis})`;
        if (badgeIcon) badgeIcon.className = 'bi bi-hourglass-split text-warning';
      } else if (tipo === 'vigentes') {
        if (badgeTexto) badgeTexto.textContent = `Exibindo escalas vigentes (${visiveis})`;
        if (badgeIcon) badgeIcon.className = 'bi bi-funnel-fill text-primary';
        if (btnToggle) {
          btnToggle.innerHTML = `<i class="bi bi-clock-history me-1"></i> Ver anteriores (${totalPassadas})`;
        }
      }

      // Controle do Empty State
      if (emptyState) {
        if (visiveis === 0) {
          emptyState.style.display = 'block';
          if (tipo === 'pendentes') {
            if (emptyTitle) emptyTitle.textContent = 'Nenhuma escala pendente';
            if (emptyDesc) emptyDesc.textContent = 'Você não possui escalas aguardando resposta neste mês. Parabéns!';
          } else if (tipo === 'aceitas') {
            if (emptyTitle) emptyTitle.textContent = 'Nenhuma escala aceita';
            if (emptyDesc) emptyDesc.textContent = 'Nenhuma escala confirmada para este mês.';
          } else if (tipo === 'vigentes') {
            if (emptyTitle) emptyTitle.textContent = 'Nenhuma escala vigente';
            if (emptyDesc) emptyDesc.textContent = 'Todas as escalas deste mês já foram concluídas.';
          } else {
            if (emptyTitle) emptyTitle.textContent = 'Nenhuma escala encontrada';
            if (emptyDesc) emptyDesc.textContent = 'Não há registros para exibição com o filtro atual.';
          }
        } else {
          emptyState.style.display = 'none';
        }
      }
    }

    function togglePassadas() {
      if (filtroAtual === 'vigentes') {
        aplicarFiltro('todos');
      } else {
        aplicarFiltro('vigentes');
      }
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

    // Modal de Visualização de Materiais de Apoio / Player Embed
    let modalPlayerVoluntarioInstance = null;

    function abrirPlayerVoluntario(resource) {
      if (!modalPlayerVoluntarioInstance) {
        modalPlayerVoluntarioInstance = new bootstrap.Modal(document.getElementById('modalPlayerVoluntario'));
      }

      document.getElementById('vol_player_badge_type').textContent = resource.type_name || 'Material';
      document.getElementById('vol_player_title').textContent = resource.title || '';
      document.getElementById('vol_player_desc').textContent = resource.description || '';

      const linkBtn = document.getElementById('vol_player_external_link');
      if (resource.url) {
        linkBtn.href = resource.url;
        linkBtn.classList.remove('d-none');
      } else {
        linkBtn.classList.add('d-none');
      }

      const container = document.getElementById('vol_player_body_container');
      container.innerHTML = '';

      if (resource.provider === 'youtube' && resource.embed_url) {
        container.innerHTML = `
          <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow">
            <iframe src="${resource.embed_url}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        `;
      } else if (resource.provider === 'spotify' && resource.embed_url) {
        container.innerHTML = `
          <iframe src="${resource.embed_url}" class="w-100" style="height: 352px; border: 0; border-radius: 12px;" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        `;
      } else if (resource.provider === 'drive' && resource.embed_url) {
        container.innerHTML = `
          <iframe src="${resource.embed_url}" class="w-100" style="height: 420px; border: 0;" allow="autoplay"></iframe>
        `;
      } else if (resource.type_code === 'text' || resource.content_text) {
        container.innerHTML = `
          <div class="p-3 bg-body text-body rounded-3 border overflow-auto font-monospace text-start" style="max-height: 400px; white-space: pre-wrap; font-size: 0.88rem;">
            ${escapeHtmlAgenda(resource.content_text || resource.description || '')}
          </div>
        `;
      } else if (resource.type_code === 'pdf' && resource.url) {
        container.innerHTML = `
          <iframe src="${resource.url}" class="w-100" style="height: 420px; border: 0;"></iframe>
        `;
      } else {
        container.innerHTML = `
          <div class="text-center p-4">
            <i class="bi bi-box-arrow-up-right display-4 text-primary mb-2"></i>
            <p class="small text-secondary mb-3">Material disponível através de link externo.</p>
            <a href="${resource.url}" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-4">
              <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Página
            </a>
          </div>
        `;
      }

      modalPlayerVoluntarioInstance.show();
    }

    function escapeHtmlAgenda(text) {
      if (!text) return '';
      return String(text).replace(/[&<>"']/g, function(m) {
        return {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        } [m];
      });
    }

    // ========================================================
    // MODAL / BOTTOM SHEET DE PARTICIPANTES DA ESCALA (FACEPILE)
    // ========================================================
    const CURRENT_VOLUNTARIO_ID = <?= (int)($voluntario->id_voluntario ?? 0) ?>;
    let modalParticipantesInstance = null;

    // Inicializa todos os tooltips da página
    function inicializarTooltipsBootstrap() {
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      tooltipTriggerList.forEach(el => {
        if (!bootstrap.Tooltip.getInstance(el)) {
          new bootstrap.Tooltip(el);
        }
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      inicializarTooltipsBootstrap();
      inicializarGestosBottomSheetParticipantes();
    });

    // Helpers para Redes Sociais, Senioridade e Formatação de Telefone
    function getSocialLinkInfo(plataforma, valor) {
      const p = (plataforma || '').toLowerCase().trim();
      const v = (valor || '').trim();
      const cleanNick = v.replace(/^@/, '');

      const config = {
        'instagram': {
          icon: 'bi-instagram',
          color: '#E1306C',
          bg: 'rgba(225,48,108,0.12)',
          border: 'rgba(225,48,108,0.3)',
          base: 'https://instagram.com/'
        },
        'facebook': {
          icon: 'bi-facebook',
          color: '#1877F2',
          bg: 'rgba(24,119,242,0.12)',
          border: 'rgba(24,119,242,0.3)',
          base: 'https://facebook.com/'
        },
        'youtube': {
          icon: 'bi-youtube',
          color: '#FF0000',
          bg: 'rgba(255,0,0,0.12)',
          border: 'rgba(255,0,0,0.3)',
          base: 'https://youtube.com/@'
        },
        'tiktok': {
          icon: 'bi-tiktok',
          color: '#000000',
          bg: 'rgba(0,0,0,0.12)',
          border: 'rgba(0,0,0,0.3)',
          base: 'https://tiktok.com/@'
        },
        'linkedin': {
          icon: 'bi-linkedin',
          color: '#0A66C2',
          bg: 'rgba(10,102,194,0.12)',
          border: 'rgba(10,102,194,0.3)',
          base: 'https://linkedin.com/in/'
        },
        'twitter': {
          icon: 'bi-twitter-x',
          color: '#000000',
          bg: 'rgba(0,0,0,0.12)',
          border: 'rgba(0,0,0,0.3)',
          base: 'https://x.com/'
        },
        'x': {
          icon: 'bi-twitter-x',
          color: '#000000',
          bg: 'rgba(0,0,0,0.12)',
          border: 'rgba(0,0,0,0.3)',
          base: 'https://x.com/'
        },
        'threads': {
          icon: 'bi-threads',
          color: '#000000',
          bg: 'rgba(0,0,0,0.12)',
          border: 'rgba(0,0,0,0.3)',
          base: 'https://threads.net/@'
        },
        'github': {
          icon: 'bi-github',
          color: '#24292e',
          bg: 'rgba(36,41,46,0.12)',
          border: 'rgba(36,41,46,0.3)',
          base: 'https://github.com/'
        },
        'spotify': {
          icon: 'bi-spotify',
          color: '#1DB954',
          bg: 'rgba(29,185,84,0.12)',
          border: 'rgba(29,185,84,0.3)',
          base: 'https://open.spotify.com/'
        },
        'pinterest': {
          icon: 'bi-pinterest',
          color: '#BD081C',
          bg: 'rgba(189,8,28,0.12)',
          border: 'rgba(189,8,28,0.3)',
          base: 'https://pinterest.com/'
        }
      };

      const info = config[p] || {
        icon: 'bi-globe',
        color: '#64748b',
        bg: 'rgba(100,116,139,0.12)',
        border: 'rgba(100,116,139,0.3)',
        base: 'https://'
      };
      let finalUrl = '#';
      if (!v) {
        finalUrl = '#';
      } else if (/^https?:\/\//i.test(v)) {
        finalUrl = v;
      } else {
        finalUrl = info.base + cleanNick;
      }

      return {
        icon: info.icon,
        color: info.color,
        bg: info.bg,
        border: info.border,
        url: finalUrl,
        name: p.charAt(0).toUpperCase() + p.slice(1)
      };
    }

    function formatarTelefoneBR(tel) {
      if (!tel) return '';
      const r = tel.replace(/\D/g, '');
      if (r.length === 11) {
        return `(${r.substring(0, 2)}) ${r.substring(2, 7)}-${r.substring(7)}`;
      } else if (r.length === 10) {
        return `(${r.substring(0, 2)}) ${r.substring(2, 6)}-${r.substring(6)}`;
      } else if (r.length === 13 && r.startsWith('55')) {
        return `(${r.substring(2, 4)}) ${r.substring(4, 9)}-${r.substring(9)}`;
      } else if (r.length === 12 && r.startsWith('55')) {
        return `(${r.substring(2, 4)}) ${r.substring(4, 8)}-${r.substring(8)}`;
      }
      return tel;
    }

    function getSenioridadeBadge(nivel) {
      const n = (nivel || '').toUpperCase().trim();
      if (n === 'SENIOR' || n === 'SÊNIOR') {
        return `<span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.64rem; letter-spacing: 0.2px;"><i class="bi bi-star-fill text-warning me-1"></i>Sênior</span>`;
      } else if (n === 'PLENO') {
        return `<span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.64rem; letter-spacing: 0.2px;"><i class="bi bi-award-fill me-1"></i>Pleno</span>`;
      } else if (n === 'JUNIOR' || n === 'JÚNIOR') {
        return `<span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.64rem; letter-spacing: 0.2px;"><i class="bi bi-patch-check-fill me-1"></i>Júnior</span>`;
      } else if (n === 'APRENDIZ') {
        return `<span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.64rem; letter-spacing: 0.2px;"><i class="bi bi-mortarboard-fill me-1"></i>Aprendiz</span>`;
      }
      return '';
    }

    function abrirModalParticipantes(participantesRaw, nomeCulto, dataCulto, nomeDepartamento, corDep, txtDep) {
      const modalEl = document.getElementById('modalParticipantesEscala');
      if (!modalEl) return;

      if (!modalParticipantesInstance) {
        modalParticipantesInstance = new bootstrap.Modal(modalEl);
      }

      // Filtra para garantir que apenas outros participantes sejam exibidos
      const participantes = (participantesRaw || []).filter(p => parseInt(p.id_voluntario, 10) !== CURRENT_VOLUNTARIO_ID);

      // Configura Header
      document.getElementById('modalPartTituloCulto').textContent = nomeCulto || 'Equipe Escalada';
      document.getElementById('modalPartDataCulto').textContent = dataCulto || '';

      const badgeDep = document.getElementById('modalPartDepBadge');
      if (badgeDep) {
        badgeDep.textContent = nomeDepartamento || 'Departamento';
        badgeDep.style.backgroundColor = corDep || '#2563eb';
        badgeDep.style.color = txtDep || '#ffffff';
      }

      const totalCount = participantes.length;
      document.getElementById('modalPartTotalCount').textContent = totalCount === 1 ? '1 voluntário com você' : `${totalCount} voluntários com você`;

      // Monta Lista de Participantes
      const listaContainer = document.getElementById('modalParticipantesLista');
      listaContainer.innerHTML = '';

      if (!totalCount) {
        listaContainer.innerHTML = `
          <div class="text-center py-4 text-muted">
            <i class="bi bi-people fs-2 mb-1 d-block opacity-50"></i>
            <p class="small mb-0">Você é o único voluntário escalado até o momento nesta área/culto.</p>
          </div>
        `;
      } else {
        participantes.forEach((p) => {
          const nomeExibicao = p.primeiro_nome || p.apelido || (p.nome_completo ? p.nome_completo.split(' ')[0] : 'Voluntário');
          const nomeCompleto = p.nome_completo || nomeExibicao;
          const subArea = p.sub_area || 'Geral';
          const fotoUrl = p.foto_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(nomeCompleto)}&background=2563eb&color=fff&size=80&bold=true`;

          const isConfirmado = (String(p.status_confirmacao).toUpperCase() === 'CONFIRMADO');

          // Tratamento Telefone WhatsApp
          let rawPhone = (p.telefone_whatsapp || '').replace(/\D/g, '');
          let temWhats = false;
          let linkWhats = '#';
          let telefoneFormatado = formatarTelefoneBR(p.telefone_whatsapp || '');
          if (rawPhone.length >= 8) {
            let phoneComDDI = rawPhone;
            if (rawPhone.length <= 11) {
              phoneComDDI = '55' + rawPhone;
            }
            const msgWhats = `Oi ${nomeExibicao} tudo bem? Vi que vamos servir juntos no mesmo dia no culto ${nomeCulto} dia ${dataCulto} na área ${nomeDepartamento}. Podemos combinar como podemos nos ajudar nessa escala?`;
            linkWhats = `https://wa.me/${phoneComDDI}?text=${encodeURIComponent(msgWhats)}`;
            temWhats = true;
          }

          // Tratamento de Redes Sociais
          let redesList = [];
          if (p.redes_sociais) {
            try {
              const parsed = typeof p.redes_sociais === 'string' ? JSON.parse(p.redes_sociais) : p.redes_sociais;
              if (Array.isArray(parsed)) {
                redesList = parsed;
              }
            } catch (err) {
              redesList = [];
            }
          }

          let redesHtml = '';
          if (redesList.length > 0) {
            redesList.forEach(r => {
              const platName = r.plataforma || r.rede || 'Outro';
              const rUrl = r.url || r.link || '';
              if (!rUrl) return;
              const sInfo = getSocialLinkInfo(platName, rUrl);
              redesHtml += `
                <a href="${escapeHtmlAgenda(sInfo.url)}" target="_blank" rel="noopener noreferrer" 
                  class="social-icon-btn" 
                  style="background-color: ${sInfo.bg}; color: ${sInfo.color};"
                  onclick="event.stopPropagation();" 
                  data-bs-toggle="tooltip" 
                  data-bs-placement="top" 
                  title="${escapeHtmlAgenda(sInfo.name)}: ${escapeHtmlAgenda(rUrl)}">
                  <i class="bi ${sInfo.icon}"></i>
                </a>
              `;
            });
          }

          const card = document.createElement('div');
          card.className = 'participante-card-item shadow-xs';
          card.setAttribute('data-id-vol', p.id_voluntario);
          card.setAttribute('data-bs-toggle', 'tooltip');
          card.setAttribute('data-bs-placement', 'top');
          card.setAttribute('title', `${nomeCompleto}`);

          // Interação Mobile: Toque / Clique expande os dados do voluntário
          card.onclick = function(e) {
            if (e.target.closest('.btn-whatsapp-action') || e.target.closest('.social-icon-btn') || e.target.closest('a')) return;
            this.classList.toggle('expanded');
          };

          card.innerHTML = `
            <div class="d-flex align-items-center justify-content-between gap-2.5">
              <!-- Avatar e Informações Básicas -->
              <div class="d-flex align-items-center gap-2.5 overflow-hidden flex-grow-1">
                <div class="position-relative flex-shrink-0">
                  <img src="${escapeHtmlAgenda(fotoUrl)}" 
                    alt="${escapeHtmlAgenda(nomeExibicao)}" 
                    class="rounded-circle border" 
                    style="width: 42px; height: 42px; object-fit: cover;"
                    onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(nomeCompleto)}&background=2563eb&color=fff&size=80&bold=true';">
                  <span class="position-absolute bottom-0 end-0 p-1 rounded-circle border border-2 ${isConfirmado ? 'bg-success' : 'bg-warning'}" 
                    style="transform: translate(15%, 15%); width: 11px; height: 11px;" 
                    title="${isConfirmado ? 'Confirmado' : 'Pendente'}">
                  </span>
                </div>
                
                <div class="overflow-hidden flex-grow-1">
                  <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="fw-bold text-body" style="font-size: 0.92rem;">
                      ${escapeHtmlAgenda(nomeExibicao)}
                    </span>
                  </div>
                  
                  <div class="d-flex align-items-center gap-1.5 mt-0.5 flex-wrap">
                    <span class="badge bg-body-secondary text-secondary rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.70rem;">
                      <i class="bi bi-tag-fill me-0.5 opacity-75"></i>${escapeHtmlAgenda(subArea)}
                    </span>
                    <span class="badge ${isConfirmado ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis'} rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.66rem;">
                      ${isConfirmado ? '<i class="bi bi-check-circle-fill me-0.5"></i>Confirmado' : '<i class="bi bi-clock-fill me-0.5"></i>Pendente'}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Ação do WhatsApp Direto -->
              <div class="flex-shrink-0">
                ${temWhats ? `
                  <a href="${linkWhats}" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-action" onclick="event.stopPropagation();" data-bs-toggle="tooltip" data-bs-placement="left" title="Mandar mensagem no WhatsApp">
                    <i class="bi bi-whatsapp"></i>
                    <span>Conversar</span>
                  </a>
                ` : `
                  <span class="badge bg-body-tertiary text-secondary border rounded-pill px-2 py-1" style="font-size: 0.70rem;" title="Telefone não informado">
                    <i class="bi bi-telephone-x me-1"></i>Sem Whats
                  </span>
                `}
              </div>
            </div>

            <!-- Expansão Detalhada Minimalista (Sem bordas de tabela, tipografia suave & clean) -->
            <div class="participante-nome-expand">
              <div class="participante-detalhes-clean text-start">
                <!-- Linha 1: Nome Completo + Senioridade -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                  <span class="text-body fw-medium text-truncate" style="font-size: 0.81rem; letter-spacing: -0.01em;">
                    ${escapeHtmlAgenda(nomeCompleto)}
                  </span>
                  ${getSenioridadeBadge(p.nivel_conhecimento)}
                </div>

                <!-- Linha 2: Telefone/WhatsApp + Redes Sociais -->
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                  <!-- Número do WhatsApp -->
                  <div class="d-flex align-items-center">
                    ${temWhats ? `
                      <a href="${linkWhats}" target="_blank" rel="noopener noreferrer" 
                        class="d-inline-flex align-items-center gap-1 text-decoration-none text-success" 
                        style="font-size: 0.74rem; font-weight: 500;"
                        onclick="event.stopPropagation();" 
                        title="Conversar no WhatsApp">
                        <i class="bi bi-whatsapp" style="font-size: 0.75rem;"></i>
                        <span>${escapeHtmlAgenda(telefoneFormatado)}</span>
                      </a>
                    ` : `
                      <span class="text-muted d-inline-flex align-items-center gap-1" style="font-size: 0.73rem;">
                        <i class="bi bi-telephone text-secondary" style="font-size: 0.72rem;"></i>
                        <span>${telefoneFormatado || 'Sem telefone'}</span>
                      </span>
                    `}
                  </div>

                  <!-- Ícones de Redes Sociais -->
                  ${redesHtml ? `
                    <div class="d-flex align-items-center gap-1 ms-auto">
                      ${redesHtml}
                    </div>
                  ` : ''}
                </div>
              </div>
            </div>
          `;

          listaContainer.appendChild(card);
        });
      }

      // Reinicializa Tooltips no conteúdo recém-criado
      const modalTooltips = modalEl.querySelectorAll('[data-bs-toggle="tooltip"]');
      modalTooltips.forEach(el => new bootstrap.Tooltip(el));

      modalParticipantesInstance.show();
    }

    // Suporte a swipe down no Bottom Sheet no Mobile para fechar
    function inicializarGestosBottomSheetParticipantes() {
      const modalEl = document.getElementById('modalParticipantesEscala');
      if (!modalEl) return;

      let startY = 0;
      let currentY = 0;
      let isDragging = false;

      const content = modalEl.querySelector('.modal-content');
      if (!content) return;

      content.addEventListener('touchstart', function(e) {
        if (window.innerWidth >= 768) return;
        const modalBody = modalEl.querySelector('.modal-body');
        if (modalBody && modalBody.scrollTop > 0) return;

        startY = e.touches[0].clientY;
        currentY = startY;
        isDragging = true;
      }, {
        passive: true
      });

      content.addEventListener('touchmove', function(e) {
        if (!isDragging || window.innerWidth >= 768) return;
        currentY = e.touches[0].clientY;
        const diff = currentY - startY;
        if (diff > 0) {
          content.style.transform = `translateY(${diff}px)`;
          content.style.transition = 'none';
        }
      }, {
        passive: true
      });

      content.addEventListener('touchend', function() {
        if (!isDragging || window.innerWidth >= 768) return;
        isDragging = false;
        const diff = currentY - startY;
        content.style.transition = 'transform 0.25s ease';
        if (diff > 75) {
          if (modalParticipantesInstance) {
            modalParticipantesInstance.hide();
          }
          setTimeout(() => {
            content.style.transform = '';
          }, 300);
        } else {
          content.style.transform = '';
        }
      });
    }
  </script>

  <!-- Modal / Bottom Sheet de Visualização de Materiais do Voluntário -->
  <div class="modal fade" id="modalPlayerVoluntario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 shadow border-0 overflow-hidden">
        <div class="modal-header border-0 pb-0">
          <div>
            <span class="badge bg-primary-subtle text-primary rounded-pill mb-1" id="vol_player_badge_type">Tipo</span>
            <h5 class="modal-title fw-bold text-dark-emphasis mb-0" id="vol_player_title">Título</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body py-3">
          <div id="vol_player_body_container" class="mb-2"></div>
          <p class="text-secondary small mb-2" id="vol_player_desc"></p>
          <div class="pt-2 border-top d-flex justify-content-end">
            <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="vol_player_external_link">
              <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Link Completo
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>