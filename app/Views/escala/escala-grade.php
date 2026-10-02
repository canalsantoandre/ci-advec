<?php
$corDep = !empty($departamentoSelecionado->cor_identificacao) ? $departamentoSelecionado->cor_identificacao : '#2563eb';
$mesesNomes = [
  1 => 'JAN',
  2 => 'FEV',
  3 => 'MAR',
  4 => 'ABR',
  5 => 'MAI',
  6 => 'JUN',
  7 => 'JUL',
  8 => 'AGO',
  9 => 'SET',
  10 => 'OUT',
  11 => 'NOV',
  12 => 'DEZ'
];

// Extrai lista única de nomes/títulos de cultos no mês
$titulosCultosMes = [];
if (!empty($diasGrade)) {
  foreach ($diasGrade as $dia) {
    if (!empty($dia['cultos_agendados'])) {
      foreach ($dia['cultos_agendados'] as $c) {
        $t = trim((string)$c->titulo_culto);
        if (!empty($t) && !in_array($t, $titulosCultosMes)) {
          $titulosCultosMes[] = $t;
        }
      }
    }
  }
}
sort($titulosCultosMes);

$hojeIso           = !empty($hojeIso) ? $hojeIso : date('Y-m-d');
$isMesAtual        = isset($isMesAtual) ? $isMesAtual : ($ano == (int)date('Y') && $mes == (int)date('m'));
$isMesPassado      = isset($isMesPassado) ? $isMesPassado : ($ano < (int)date('Y') || ($ano == (int)date('Y') && $mes < (int)date('m')));
$podeEditarPassado = !empty($podeEditarPassado) || !empty($sys_action->update_past);
$mesesPassadosComEscala = isset($mesesPassadosComEscala) && is_array($mesesPassadosComEscala) ? $mesesPassadosComEscala : [];

// Contagem de dias passados no mês visualizado
$totalDiasPassados = 0;
if (!empty($diasGrade)) {
  foreach ($diasGrade as $dItem) {
    if ($dItem['data_iso'] < $hojeIso) {
      $totalDiasPassados++;
    }
  }
}

if (!function_exists('formatarNomeExibicaoGrade')) {
  function formatarNomeExibicaoGrade($nomeCompleto, $nickname = null)
  {
    $nick = trim((string)$nickname);
    if (!empty($nick)) {
      return $nick;
    }
    $partes = preg_split('/\s+/', trim((string)$nomeCompleto));
    if (count($partes) <= 1) {
      return $partes[0] ?? '';
    }
    return $partes[0] . ' ' . end($partes);
  }
}
?>

<style>
  .table-warning-custom {
    background-color: rgba(254, 243, 199, 0.45) !important;
  }

  [data-bs-theme="dark"] .table-warning-custom {
    background-color: rgba(120, 53, 15, 0.2) !important;
  }

  /* Estilos para Dias Passados (Histórico) */
  .linha-dia-passado {
    background-color: rgba(241, 245, 249, 0.65) !important;
    transition: opacity 0.25s ease, background-color 0.25s ease;
  }

  [data-bs-theme="dark"] .linha-dia-passado {
    background-color: rgba(15, 23, 42, 0.45) !important;
  }

  .linha-dia-passado td {
    opacity: 0.90;
  }

  .linha-dia-passado:hover td {
    opacity: 1;
  }

  .badge-past-locked {
    background: rgba(100, 116, 139, 0.12);
    color: #64748b;
    border: 1px solid rgba(100, 116, 139, 0.25);
    font-size: 0.72rem;
  }

  [data-bs-theme="dark"] .badge-past-locked {
    background: rgba(148, 163, 184, 0.12);
    color: #94a3b8;
    border-color: rgba(148, 163, 184, 0.25);
  }

  /* Estilos para Botão e Menu de Meses Anteriores com Alto Contraste */
  .btn-meses-anteriores {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
    color: #f8fafc !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.18);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .btn-meses-anteriores:hover {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
    border-color: #38bdf8 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.35);
  }

  .btn-meses-anteriores .icon-history {
    color: #38bdf8 !important;
    /* Cyan vibrante de altíssimo contraste */
    font-size: 0.95rem;
    transition: transform 0.25s ease;
  }

  .btn-meses-anteriores:hover .icon-history {
    transform: rotate(-30deg) scale(1.15);
  }

  .btn-meses-anteriores .badge-count {
    background-color: #38bdf8 !important;
    color: #0f172a !important;
    font-weight: 800;
  }

  /* Dropdown Menu de Histórico */
  .dropdown-history-menu {
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    border-radius: 0.85rem !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18) !important;
    min-width: 220px;
    padding: 0.4rem;
  }

  [data-bs-theme="dark"] .dropdown-history-menu {
    background-color: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
  }

  .dropdown-history-menu .dropdown-header {
    font-size: 0.72rem;
    letter-spacing: 0.5px;
    font-weight: 700;
    color: #64748b;
    padding: 0.4rem 0.75rem 0.25rem;
  }

  [data-bs-theme="dark"] .dropdown-history-menu .dropdown-header {
    color: #94a3b8;
  }

  .dropdown-history-item {
    border-radius: 0.6rem;
    padding: 0.55rem 0.85rem;
    font-weight: 600;
    color: #1e293b;
    transition: all 0.15s ease;
  }

  [data-bs-theme="dark"] .dropdown-history-item {
    color: #f1f5f9;
  }

  .dropdown-history-item:hover {
    background-color: #e0e7ff !important;
    color: #3730a3 !important;
  }

  [data-bs-theme="dark"] .dropdown-history-item:hover {
    background-color: rgba(99, 102, 241, 0.25) !important;
    color: #c7d2fe !important;
  }

  .dropdown-history-item.active {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    color: #ffffff !important;
  }

  /* Aba de Mês Passado Selecionado */
  .btn-mes-passado-ativo {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 3px 10px rgba(79, 70, 229, 0.35);
  }

  .slot-area-box {
    border: 1px dashed rgba(0, 0, 0, 0.15);
    background: rgba(255, 255, 255, 0.7);
    border-radius: 0.6rem;
    padding: 0.45rem 0.65rem;
    min-width: 170px;
    transition: all 0.2s ease;
  }

  [data-bs-theme="dark"] .slot-area-box {
    border-color: rgba(255, 255, 255, 0.15);
    background: rgba(30, 41, 59, 0.6);
  }

  .slot-area-box.filled {
    border-style: solid;
    border-color: rgba(37, 99, 235, 0.3);
    background: rgba(239, 246, 255, 0.85);
  }

  [data-bs-theme="dark"] .slot-area-box.filled {
    border-color: rgba(59, 130, 246, 0.4);
    background: rgba(30, 58, 138, 0.3);
  }

  .dep-tab-pill {
    transition: all 0.2s ease;
    border: 2px solid transparent;
  }

  .dep-tab-pill:hover {
    transform: translateY(-2px);
  }

  .dep-tab-pill.active {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  }

  .slot-vol-badge {
    background-color: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.12);
    color: #1e293b;
    transition: all 0.15s ease;
  }

  .slot-vol-name {
    color: #1e293b;
  }

  [data-bs-theme="dark"] .slot-vol-badge {
    background-color: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
  }

  [data-bs-theme="dark"] .slot-vol-name {
    color: #f8fafc !important;
  }

  [data-bs-theme="dark"] .slot-vol-badge:hover {
    background-color: #273549 !important;
    border-color: #60a5fa !important;
  }

  .btn-presence-toggle {
    cursor: pointer;
    transition: transform 0.15s ease;
  }

  .btn-presence-toggle:hover {
    transform: scale(1.05);
  }

  /* Ampulheta Pulsante para Escalas Pendentes */
  .pulse-pending {
    animation: pulse-pend 1.6s infinite ease-in-out;
  }

  @keyframes pulse-pend {
    0% {
      transform: scale(1);
      opacity: 1;
    }

    50% {
      transform: scale(1.15);
      opacity: 0.6;
    }

    100% {
      transform: scale(1);
      opacity: 1;
    }
  }

  .vol-card-item {
    background-color: #ffffff;
    border: 1px solid #e2e8f0 !important;
    color: #1e293b;
    padding: 0.65rem 0.95rem !important;
    border-radius: 1rem !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  }

  .vol-card-item:hover:not(.opacity-75) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: #93c5fd !important;
  }

  .vol-card-item.vol-card-selected {
    background: #f0f7ff !important;
    border: 1.5px solid #2563eb !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15) !important;
  }

  /* Suporte a Visão Noturna (Dark Mode) */
  [data-bs-theme="dark"] .vol-card-item {
    background-color: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: #f8fafc !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  }

  [data-bs-theme="dark"] .vol-card-item:hover:not(.opacity-75) {
    background-color: #24344d !important;
    border-color: #60a5fa !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
  }

  [data-bs-theme="dark"] .vol-card-item.vol-card-selected {
    background: rgba(37, 99, 235, 0.22) !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.3) !important;
  }

  /* Check Indicator */
  .vol-check-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #cbd5e1;
    color: transparent;
    transition: all 0.2s ease;
    font-size: 0.72rem;
    flex-shrink: 0;
    background-color: #ffffff;
  }

  .vol-check-indicator.checked {
    background-color: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
  }

  [data-bs-theme="dark"] .vol-check-indicator {
    background-color: #1e293b;
    border-color: #475569;
  }

  [data-bs-theme="dark"] .vol-check-indicator.checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
  }

  /* Avatar com borda dourada */
  .avatar-gold-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid #f59e0b;
    box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.35);
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.82rem;
    letter-spacing: 0.5px;
    flex-shrink: 0;
    cursor: pointer;
    margin: 0 5px;
    padding: 1.5px;
    transition: transform 0.2s ease;
  }

  .avatar-gold-circle:hover {
    transform: scale(1.08);
  }

  /* Badges Empilhados (Stacked Badges) */
  .badge-stacked-disp {
    font-size: 0.67rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    line-height: 1.2;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
  }

  .badge-stacked-limit {
    font-size: 0.67rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 5px;
    background-color: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    line-height: 1.2;
  }

  [data-bs-theme="dark"] .badge-stacked-limit {
    background-color: #334155;
    color: #cbd5e1;
    border-color: #475569;
  }

  /* Badge Role */
  .badge-role-pill {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1px 6px;
    border-radius: 4px;
    background-color: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    display: inline-block;
    width: fit-content;
    line-height: 1.2;
  }

  [data-bs-theme="dark"] .badge-role-pill {
    background-color: #334155;
    color: #cbd5e1;
    border-color: #475569;
  }

  .badge-chip-voluntario {
    padding: 7px 14px !important;
    font-size: 0.82rem !important;
    font-weight: 500 !important;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    border-radius: 50rem !important;
    line-height: 1.2 !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  .badge-chip-voluntario:hover {
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25) !important;
  }

  .badge-chip-voluntario .chip-close-btn {
    cursor: pointer;
    opacity: 0.85;
    transition: transform 0.15s ease, opacity 0.15s ease;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .badge-chip-voluntario .chip-close-btn:hover {
    opacity: 1;
    transform: scale(1.2);
  }

  #voluntario_selecionado_preview {
    padding: 14px 16px !important;
    border-radius: 0.85rem !important;
    background-color: rgba(37, 99, 235, 0.08) !important;
    border: 1.5px solid rgba(37, 99, 235, 0.3) !important;
    margin-top: 14px !important;
    margin-bottom: 6px !important;
  }

  [data-bs-theme="dark"] #voluntario_selecionado_preview {
    background-color: rgba(30, 58, 138, 0.25) !important;
    border-color: rgba(59, 130, 246, 0.4) !important;
  }

  .chips-container-wrapper {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 8px 10px !important;
    align-items: center !important;
    padding: 4px 0 2px 0 !important;
    margin-top: 6px !important;
  }

  .modal-input-search-addon {
    background-color: #f8fafc;
    border-color: #dee2e6;
    color: #64748b;
  }

  [data-bs-theme="dark"] .modal-input-search-addon {
    background-color: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    color: #94a3b8 !important;
  }

  [data-bs-theme="dark"] #filtro_busca_voluntario {
    background-color: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    color: #f8fafc !important;
  }

  [data-bs-theme="dark"] #filtro_busca_voluntario::placeholder {
    color: #64748b !important;
  }

  /* ============================================================
     ESTILOS PREMIUM - BIBLIOTECA & PLAYERS (SPOTIFY / APPLE MUSIC)
     ============================================================ */
  .music-modal-content {
    border-radius: 20px !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3) !important;
    backdrop-filter: blur(16px);
  }

  .music-track-card {
    border-radius: 14px;
    padding: 10px 14px;
    border: 1px solid rgba(0, 0, 0, 0.06);
    background: var(--bs-body-bg);
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
  }

  .music-track-card:hover {
    transform: translateY(-2px);
    border-color: rgba(var(--bs-primary-rgb), 0.35);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
    background: rgba(var(--bs-primary-rgb), 0.025);
  }

  [data-bs-theme="dark"] .music-track-card {
    border-color: rgba(255, 255, 255, 0.08);
    background: #181b20;
  }

  [data-bs-theme="dark"] .music-track-card:hover {
    border-color: rgba(99, 102, 241, 0.5);
    background: #1f232b;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
  }

  .music-cover-art {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }

  .cover-gradient-video {
    background: linear-gradient(135deg, #ff0033 0%, #b3001e 100%);
    color: #fff;
  }

  .cover-gradient-audio {
    background: linear-gradient(135deg, #1db954 0%, #107c34 100%);
    color: #fff;
  }

  .cover-gradient-spotify {
    background: linear-gradient(135deg, #1db954 0%, #191414 100%);
    color: #fff;
  }

  .cover-gradient-pdf {
    background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
    color: #fff;
  }

  .cover-gradient-link {
    background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
    color: #fff;
  }

  .cover-gradient-text {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    color: #fff;
  }

  .cover-gradient-collection {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #fff;
  }

  .badge-global-origin {
    background: rgba(16, 185, 129, 0.12);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
    font-weight: 600;
  }

  [data-bs-theme="dark"] .badge-global-origin {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.35);
  }

  .badge-dep-origin {
    background: rgba(99, 102, 241, 0.1);
    color: #4f46e5;
    border: 1px solid rgba(99, 102, 241, 0.2);
    font-weight: 600;
  }

  [data-bs-theme="dark"] .badge-dep-origin {
    background: rgba(99, 102, 241, 0.2);
    color: #a5b4fc;
    border-color: rgba(99, 102, 241, 0.35);
  }

  .pill-filter-btn {
    border-radius: 50rem;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 5px 14px;
    border: 1px solid rgba(0, 0, 0, 0.08);
    background: var(--bs-body-bg);
    color: var(--bs-body-color);
    transition: all 0.2s ease;
  }

  .pill-filter-btn:hover,
  .pill-filter-btn.active {
    background: var(--bs-primary);
    color: #fff;
    border-color: var(--bs-primary);
    box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.3);
  }

  [data-bs-theme="dark"] .pill-filter-btn {
    border-color: rgba(255, 255, 255, 0.1);
    background: #181b20;
  }

  .btn-attach-music {
    border-radius: 50rem;
    padding: 5px 14px;
    font-size: 0.80rem;
    font-weight: 600;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .btn-attach-music:hover {
    transform: scale(1.05);
  }
</style>

<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-calendar-check-fill text-primary me-2"></i>Grade Mensal de Escalas
        </h3>
        <p class="text-secondary small mb-0">Gestão visual e alocação de voluntários nas sub-áreas dos cultos</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('escala') ?>">Escalas</a></li>
          <li class="breadcrumb-item active" aria-current="page">Grade Mensal</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- ========================================== -->
    <!-- 1. FILTRO PRINCIPAL: SELETOR DE DEPARTAMENTO -->
    <!-- ========================================== -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
      <div class="card-header bg-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-diagram-3-fill fs-5 text-primary"></i>
          <span class="fw-bold text-body">Selecione o Departamento para Gerenciar:</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="<?= base_url("escala/imprimir/{$id_departamento}/{$ano}/{$mes}") ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
            <i class="bi bi-printer me-1"></i> Imprimir Escala
          </a>
          <a href="<?= base_url('voluntario') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
            <i class="bi bi-person-hearts me-1"></i> Voluntários
          </a>
          <a href="<?= base_url('departamento') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
            <i class="bi bi-building me-1"></i> Departamentos
          </a>
        </div>
      </div>

      <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($departamentos as $d) {
            $isActive = ($d->id_departamento == $id_departamento);
            $c = !empty($d->cor_identificacao) ? $d->cor_identificacao : '#2563eb';
          ?>
            <a href="<?= base_url("escala/grade/{$d->id_departamento}/{$ano}/{$mes}") ?>" class="btn dep-tab-pill d-flex align-items-center gap-2 rounded-4 px-3 py-2 text-decoration-none <?= $isActive ? 'active' : 'bg-body border' ?>" style="<?= $isActive ? "background-color: {$c}; color: #fff; border-color: {$c};" : "border-left: 4px solid {$c} !important;" ?>">
              <span class="badge rounded-circle p-1" style="background-color: <?= $isActive ? '#fff' : $c ?>; width: 10px; height: 10px;"></span>
              <span class="fw-bold"><?= esc($d->nome) ?></span>
              <span class="badge rounded-pill <?= $isActive ? 'bg-white text-dark' : 'bg-secondary-subtle text-secondary-emphasis' ?>" style="font-size: 0.7rem;">
                <?= esc($d->responsavel_nome) ?>
              </span>
            </a>
          <?php } ?>
        </div>

        <!-- Sub-áreas ativas do departamento selecionado -->
        <?php if (!empty($areasDepartamento)) { ?>
          <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">
              <i class="bi bi-grid-fill me-1" style="color: <?= esc($corDep) ?>;"></i> Sub-áreas deste Departamento:
            </small>
            <?php foreach ($areasDepartamento as $area) { ?>
              <span class="badge bg-body-secondary text-body border rounded-pill px-3 py-1 fw-semibold small">
                <?= esc($area->nome_area) ?>
              </span>
            <?php } ?>
          </div>
        <?php } else { ?>
          <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 py-2 small">
            <i class="bi bi-exclamation-triangle me-1"></i> Este departamento ainda não possui sub-áreas cadastradas.
            <a href="<?= base_url('departamento/editar/' . $id_departamento) ?>" class="fw-bold text-decoration-underline">Clique aqui para adicionar sub-áreas</a>.
          </div>
        <?php } ?>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. NAVEGAÇÃO DE MESES E ANO (ESTILO TABS INTELIGENTE) -->
    <!-- ========================================== -->
    <?php
    $anoAtualReal = (int)date('Y');
    $mesAtualReal = (int)date('m');
    $isAnoCorrente = ($ano === $anoAtualReal);
    ?>
    <div class="card bg-body-tertiary border-0 rounded-4 shadow-sm mb-4" style="overflow: visible; position: relative; z-index: 20;">
      <div class="card-body p-2" style="overflow: visible;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

          <!-- Abas dos Meses e Dropdown -->
          <div class="d-flex align-items-center gap-1 flex-wrap">

            <?php if ($isAnoCorrente) { ?>
              <!-- Dropdown de Meses Anteriores com Escala Cadastrada (Alto Contraste) -->
              <div class="dropdown me-1 position-relative" style="z-index: 25;">
                <?php if (!empty($mesesPassadosComEscala)) { ?>
                  <button class="btn btn-sm btn-meses-anteriores rounded-pill px-3 fw-semibold dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="Visualizar meses passados que possuem escalas cadastradas">
                    <i class="bi bi-clock-history icon-history"></i> Meses anteriores
                    <span class="badge badge-count rounded-pill px-2 py-0.5 ms-0.5" style="font-size: 0.72rem;"><?= count($mesesPassadosComEscala) ?></span>
                  </button>
                  <ul class="dropdown-menu dropdown-history-menu mt-1">
                    <li class="dropdown-header text-uppercase d-flex align-items-center gap-1">
                      <i class="bi bi-folder2-open text-warning"></i> Meses com Escala (<?= $ano ?>)
                    </li>
                    <?php foreach ($mesesPassadosComEscala as $mPassado) {
                      $isItemActive = ($mPassado == $mes);
                    ?>
                      <li>
                        <a class="dropdown-item dropdown-history-item d-flex align-items-center justify-content-between gap-3 <?= $isItemActive ? 'active' : '' ?>" href="<?= base_url("escala/grade/{$id_departamento}/{$ano}/{$mPassado}") ?>">
                          <span class="d-inline-flex align-items-center">
                            <i class="bi bi-calendar2-range me-2 <?= $isItemActive ? 'text-white' : 'text-primary' ?>"></i>
                            <?= $mesesNomes[$mPassado] ?> - <?= $ano ?>
                          </span>
                          <span class="badge <?= $isItemActive ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' ?> rounded-pill" style="font-size: 0.68rem;">Histórico</span>
                        </a>
                      </li>
                    <?php } ?>
                  </ul>
                <?php } else { ?>
                  <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold opacity-60 d-inline-flex align-items-center gap-1" type="button" disabled title="Nenhum mês anterior possui escalas cadastradas neste departamento">
                    <i class="bi bi-clock-history"></i> Meses anteriores
                  </button>
                <?php } ?>
              </div>

              <!-- Se o gestor navegou para um mês passado, exibe a aba desse mês passado em destaque com cor diferenciada -->
              <?php if ($mes < $mesAtualReal) { ?>
                <a href="<?= base_url("escala/grade/{$id_departamento}/{$ano}/{$mes}") ?>" class="btn btn-sm btn-mes-passado-ativo rounded-pill px-3 fw-bold text-nowrap d-inline-flex align-items-center gap-1">
                  <i class="bi bi-archive-fill"></i> <?= $mesesNomes[$mes] ?>-<?= $ano ?>
                  <span class="badge bg-white text-dark rounded-pill ms-1" style="font-size: 0.65rem;">Histórico</span>
                </a>
                <div class="vr mx-1 opacity-25" style="height: 24px;"></div>
              <?php } ?>

              <!-- Meses a partir do Mês Atual até o fim do ano -->
              <?php for ($m = $mesAtualReal; $m <= 12; $m++) {
                $isActive = ($m == $mes);
                $isCurrentMonth = ($m == $mesAtualReal);
              ?>
                <a href="<?= base_url("escala/grade/{$id_departamento}/{$ano}/{$m}") ?>" class="btn btn-sm rounded-pill px-3 fw-bold text-nowrap <?= $isActive ? 'btn-primary shadow-sm' : 'btn-outline-secondary border-0' ?>">
                  <?= $mesesNomes[$m] ?>-<?= $ano ?>
                  <?php if ($isCurrentMonth && !$isActive) { ?>
                    <span class="badge bg-primary-subtle text-primary rounded-pill ms-1" style="font-size: 0.65rem;">Atual</span>
                  <?php } ?>
                </a>
              <?php } ?>

            <?php } else { ?>
              <!-- Ano Passado ou Futuro: Exibe os 12 meses normalmente -->
              <?php for ($m = 1; $m <= 12; $m++) {
                $isActive = ($m == $mes);
              ?>
                <a href="<?= base_url("escala/grade/{$id_departamento}/{$ano}/{$m}") ?>" class="btn btn-sm rounded-pill px-3 fw-bold text-nowrap <?= $isActive ? 'btn-primary shadow-sm' : 'btn-outline-secondary border-0' ?>">
                  <?= $mesesNomes[$m] ?>-<?= $ano ?>
                </a>
              <?php } ?>
            <?php } ?>

          </div>

          <!-- Seletor de Ano -->
          <div class="d-flex align-items-center gap-2 ms-auto flex-nowrap">
            <a href="<?= base_url("escala/grade/{$id_departamento}/" . ($ano - 1) . "/{$mes}") ?>" class="btn btn-outline-secondary btn-sm rounded-circle shadow-xs" title="Ano Anterior">
              <i class="bi bi-chevron-left"></i>
            </a>
            <span class="fw-bold fs-6 text-primary"><?= $ano ?></span>
            <a href="<?= base_url("escala/grade/{$id_departamento}/" . ($ano + 1) . "/{$mes}") ?>" class="btn btn-outline-secondary btn-sm rounded-circle shadow-xs" title="Próximo Ano">
              <i class="bi bi-chevron-right"></i>
            </a>
          </div>

        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. DASHBOARD KPI WIDGETS -->
    <!-- ========================================== -->
    <div class="row g-3 mb-4">
      <!-- Card 1: Total de Cultos no Mês -->
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-primary bg-gradient text-white">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">Cultos no Mês</span>
                <h2 class="mb-0 fw-bold font-monospace"><?= $totalCultosMes ?></h2>
              </div>
              <div class="rounded-circle bg-white bg-opacity-25 p-3 text-white">
                <i class="bi bi-calendar-event fs-3"></i>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">
              <i class="bi bi-clock me-1"></i> Agendamentos do mês <?= sprintf('%02d', $mes) ?>/<?= $ano ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: Escalações Preenchidas -->
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-success bg-gradient text-white">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">Vagas Preenchidas</span>
                <h2 class="mb-0 fw-bold font-monospace"><?= $totalEscalasPreenchidas ?></h2>
              </div>
              <div class="rounded-circle bg-white bg-opacity-25 p-3 text-white">
                <i class="bi bi-check-circle-fill fs-3"></i>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">
              <span class="badge bg-white text-success fw-bold rounded-pill px-2 py-0 me-1"><?= $percentualPreenchimento ?>%</span> Preenchimento
            </div>
          </div>
        </div>
      </div>

      <!-- Card 3: Voluntários Únicos Escalados -->
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden text-white" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">Voluntários Escalados</span>
                <h2 class="mb-0 fw-bold font-monospace"><?= $totalVoluntariosUnicos ?></h2>
              </div>
              <div class="rounded-circle bg-white bg-opacity-25 p-3 text-white">
                <i class="bi bi-people-fill fs-3"></i>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">
              <i class="bi bi-person-badge me-1"></i> Membros atuando no mês
            </div>
          </div>
        </div>
      </div>

      <!-- Card 4: Cultos Pendentes -->
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden <?= $cultosSemEscala > 0 ? 'bg-danger bg-gradient text-white' : 'bg-secondary bg-gradient text-white' ?>">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">Cultos sem Escala</span>
                <h2 class="mb-0 fw-bold font-monospace"><?= $cultosSemEscala ?></h2>
              </div>
              <div class="rounded-circle bg-white bg-opacity-25 p-3 text-white">
                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">
              <?php if ($cultosSemEscala > 0) { ?>
                <span class="badge bg-white text-danger fw-bold rounded-pill px-2 py-0 me-1">Atenção!</span> Preencher equipe
              <?php } else { ?>
                <span class="badge bg-white text-secondary fw-bold rounded-pill px-2 py-0 me-1">100%</span> Todos com voluntários
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Banner de Modo Histórico / Mês Encerrado -->
    <?php if ($isMesPassado) { ?>
      <div class="alert alert-secondary border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-archive-fill text-primary fs-5"></i>
          <div>
            <strong class="text-body d-block small fw-bold">Mês Encerrado - Visualização de Histórico</strong>
            <small class="text-muted">Exibindo todos os registros passados de <?= sprintf('%02d', $mes) ?>/<?= $ano ?>.</small>
          </div>
        </div>
        <div>
          <?php if (!$podeEditarPassado) { ?>
            <span class="badge bg-body-secondary text-secondary border rounded-pill px-3 py-1.5 small fw-semibold">
              <i class="bi bi-lock-fill me-1"></i> Modo Somente Leitura
            </span>
          <?php } else { ?>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1.5 small fw-bold">
              <i class="bi bi-shield-check me-1"></i> Edição Retroativa Liberada (SysAdm)
            </span>
          <?php } ?>
        </div>
      </div>
    <?php } ?>

    <!-- Barra de Filtros Rápidos da Tabela -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

          <!-- Progresso -->
          <div class="flex-grow-1" style="min-width: 250px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="fw-bold small text-body">
                <i class="bi bi-bar-chart-line-fill text-primary me-1"></i> Preenchimento da Escala do Departamento:
                <span class="text-primary fw-bold"><?= $percentualPreenchimento ?>%</span>
              </span>
              <small class="text-secondary font-monospace"><?= $totalEscalasPreenchidas ?> vagas preenchidas</small>
            </div>
            <div class="progress rounded-pill" style="height: 10px;">
              <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $percentualPreenchimento ?>%;"></div>
            </div>
          </div>

          <!-- Filtros de Linhas, Seletor de Culto e Toggle de Dias Passados -->
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="small fw-bold text-secondary me-1"><i class="bi bi-funnel-fill me-1"></i> Filtrar Grade:</span>

            <!-- Seletor de Culto Específico -->
            <div class="me-1">
              <select id="selectFiltroCulto" class="form-select form-select-sm rounded-pill px-3 fw-semibold bg-body border-primary shadow-sm" style="min-width: 200px;" title="Filtrar ocorrências por tipo de culto">
                <option value="">🎯 Todos os Cultos (<?= $totalCultosMes ?>)</option>
                <?php foreach ($titulosCultosMes as $nomeCulto) { ?>
                  <option value="<?= esc($nomeCulto) ?>"><?= esc($nomeCulto) ?></option>
                <?php } ?>
              </select>
            </div>

            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btnFilterRow active" data-filter="all">
              Todos (<?= $totalDias ?>d)
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 btnFilterRow" data-filter="com_culto">
              Com Culto (<?= $totalCultosMes ?>)
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 btnFilterRow" data-filter="com_escala">
              <i class="bi bi-person-check-fill me-1 text-success"></i> Escalados (<?= $cultosComEscala ?>)
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 btnFilterRow" data-filter="sem_escala">
              <i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i> Sem Escala (<?= $cultosSemEscala ?>)
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 btnFilterRow" data-filter="fim_semana">
              Fins de Semana
            </button>

            <!-- Toggle Inteligente de Dias Anteriores no Mês Atual -->
            <?php if ($isMesAtual && $totalDiasPassados > 0) { ?>
              <div class="form-check form-switch d-inline-flex align-items-center gap-2 mb-0 px-3 py-1 bg-body border rounded-pill shadow-xs ms-lg-2" style="min-height: 31px;">
                <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="toggleDiasAnteriores" style="cursor: pointer; width: 2.2em; height: 1.15em;">
                <label class="form-check-label small fw-semibold text-secondary user-select-none" for="toggleDiasAnteriores" style="cursor: pointer;">
                  <i class="bi bi-clock-history me-1 text-primary"></i> Exibir dias anteriores
                  <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1" style="font-size: 0.7rem;"><?= $totalDiasPassados ?></span>
                </label>
              </div>
            <?php } ?>
          </div>

        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. TABELA MATRIZ DINÂMICA DA GRADE -->
    <!-- ========================================== -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title fw-bold mb-0 text-primary d-flex align-items-center gap-2">
          <i class="bi bi-grid-3x3-gap-fill"></i>
          Escala de <?= esc($departamentoSelecionado->nome) ?> - <?= sprintf('%02d', $mes) ?>/<?= $ano ?>
        </h5>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3"><?= count($areasDepartamento) ?> Sub-áreas</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0" id="tblGradeEscala">
            <thead class="table-dark text-uppercase small">
              <tr>
                <th scope="col" class="text-center py-3" style="width: 110px;">Dia</th>
                <th scope="col" class="py-3" style="width: 130px;">Dia Semana</th>
                <th scope="col" class="py-3" style="width: 220px;">Culto / Evento</th>
                <th scope="col" class="py-3">Sub-áreas & Voluntários Escalados</th>
                <th scope="col" class="text-end py-3 pe-4" style="width: 100px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($diasGrade as $dia) {
                $isFimSemana = $dia['is_fim_semana'];
                $bgClass = $isFimSemana ? 'table-warning-custom' : '';
                $temCulto = !empty($dia['cultos_agendados']);
                $isDiaPassado = ($dia['data_iso'] < $hojeIso);
                $isOcultoInicial = ($isMesAtual && $isDiaPassado);
                $rowPastClass = $isDiaPassado ? 'linha-dia-passado' : '';
                $styleOculto = $isOcultoInicial ? 'display: none;' : '';

                if ($temCulto) {
                  foreach ($dia['cultos_agendados'] as $c) {
                    $corCulto = !empty($c->cor_evento) ? $c->cor_evento : '#2563eb';
                    $temEscalaNoCulto = !empty($c->escalasPorArea);
                    $statusFiltro = $temEscalaNoCulto ? 'com_escala' : 'sem_escala';
                    $dataCultosAttr = trim((string)$c->titulo_culto);
              ?>
                    <tr class="grade-row <?= $bgClass ?> <?= $rowPastClass ?>" data-status-escala="<?= $statusFiltro ?>" data-is-weekend="<?= $isFimSemana ? '1' : '0' ?>" data-cultos="<?= esc($dataCultosAttr) ?>" data-is-past="<?= $isDiaPassado ? '1' : '0' ?>" style="<?= $styleOculto ?>">

                      <!-- Dia -->
                      <td class="text-center fw-bold font-monospace fs-6">
                        <?= $dia['data_formatada'] ?>
                        <?php if ($isDiaPassado) { ?>
                          <div class="mt-1">
                            <?php if ($podeEditarPassado) { ?>
                              <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Edição retroativa permitida">
                                <i class="bi bi-pencil-square me-0.5"></i> Retroativo
                              </span>
                            <?php } else { ?>
                              <span class="badge bg-body-secondary text-muted rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Data encerrada (somente leitura)">
                                Encerrado
                              </span>
                            <?php } ?>
                          </div>
                        <?php } ?>
                      </td>

                      <!-- Dia Semana -->
                      <td class="fw-semibold text-capitalize">
                        <?php if ($isFimSemana) { ?>
                          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-bold">
                            <i class="bi bi-star-fill me-1 text-warning"></i><?= $dia['nome_dia_semana'] ?>
                          </span>
                        <?php } else { ?>
                          <span class="text-secondary"><?= $dia['nome_dia_semana'] ?></span>
                        <?php } ?>
                      </td>

                      <!-- Culto / Evento -->
                      <td>
                        <div class="p-2 border rounded-3 bg-body shadow-sm" style="border-left: 4px solid <?= esc($corCulto) ?> !important;">
                          <strong class="text-body d-block"><?= esc($c->titulo_culto) ?></strong>
                          <small class="text-muted">
                            <i class="bi bi-clock me-1"></i><?= substr($c->horario_inicio, 0, 5) ?> - <?= substr($c->horario_termino, 0, 5) ?>
                          </small>
                        </div>
                      </td>

                      <!-- Sub-áreas & Voluntários Escalados -->
                      <td>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                          <?php foreach ($areasDepartamento as $area) {
                            $escalasDestaArea = $c->escalasPorArea[$area->id_area] ?? [];

                            $escalasAtivas = [];
                            $escalasRecusadas = [];
                            foreach ($escalasDestaArea as $escItem) {
                              $confStatus = strtoupper((string)($escItem->status_confirmacao ?: 'PENDENTE'));
                              if ($confStatus === 'RECUSADO') {
                                $escalasRecusadas[] = $escItem;
                              } else {
                                $escalasAtivas[] = $escItem;
                              }
                            }

                            $isPreenchido = !empty($escalasAtivas);
                            $permiteEditarEsteSlot = (!$isDiaPassado || $podeEditarPassado);
                          ?>
                            <div class="slot-area-box <?= $isPreenchido ? 'filled' : '' ?>">
                              <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small text-primary-emphasis" style="font-size: 0.75rem;">
                                  <i class="bi bi-grid me-1"></i><?= esc($area->nome_area) ?>
                                </strong>
                                <?php if ($isPreenchido && !empty($sys_action->create) && $permiteEditarEsteSlot) { ?>
                                  <button type="button" class="btn btn-xs btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
                                    style="width: 18px; height: 18px; font-size: 0.75rem;"
                                    onclick="abrirModalEscalar('<?= $c->data_culto ?>', <?= $c->id_culto_padrao ?>, '<?= esc($c->titulo_culto) ?> - <?= $dia['data_formatada'] ?>', <?= $area->id_area ?>)"
                                    title="Adicionar mais voluntários nesta sub-área">
                                    <i class="bi bi-plus"></i>
                                  </button>
                                <?php } ?>
                              </div>

                              <?php if ($isPreenchido) { ?>
                                <div class="d-flex flex-column gap-1">
                                  <?php foreach ($escalasAtivas as $esc) {
                                    $defaultAv = 'https://ui-avatars.com/api/?name=' . urlencode($esc->nome_voluntario) . '&background=2563eb&color=fff&size=50';
                                    $av = !empty($esc->foto_url) ? $esc->foto_url : $defaultAv;
                                    $isPresente = ($esc->status_presenca == 1);
                                    $conf = strtoupper((string)($esc->status_confirmacao ?: 'PENDENTE'));
                                    $nomeDisplay = formatarNomeExibicaoGrade($esc->nome_voluntario, $esc->nickname);
                                    $nivelVol = !empty($esc->nivel_conhecimento) ? $esc->nivel_conhecimento : 'JUNIOR';
                                  ?>
                                    <div class="d-flex align-items-center justify-content-between gap-1 p-1 rounded shadow-sm slot-vol-badge" id="boxEscala_<?= $esc->id_escala_voluntario ?>">
                                      <div class="d-flex align-items-center gap-1 text-truncate" style="max-width: 140px;">
                                        <img src="<?= esc($av) ?>" class="rounded-circle border" style="width: 22px; height: 22px; object-fit: cover;" alt="avatar" onerror="this.onerror=null;this.src='<?= $defaultAv ?>';">
                                        <span class="small fw-semibold text-truncate slot-vol-name" title="<?= esc($esc->nome_voluntario) ?><?= !empty($esc->nickname) ? ' (' . esc($esc->nickname) . ')' : '' ?> [<?= esc($nivelVol) ?>]">
                                          <?= esc($nomeDisplay) ?>
                                        </span>
                                      </div>

                                      <div class="d-flex align-items-center gap-1">
                                        <?php if ($conf === 'CONFIRMADO') { ?>
                                          <!-- Voluntário Aceitou: Joinha e Toggle de Presença -->
                                          <span class="text-success small" data-bs-toggle="tooltip" title="Agenda Aceita pelo Voluntário" style="font-size: 0.85rem;">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                          </span>
                                          <?php if ($permiteEditarEsteSlot) { ?>
                                            <span class="badge btn-presence-toggle <?= $isPresente ? 'bg-success' : 'bg-danger' ?>" onclick="togglePresenca(<?= $esc->id_escala_voluntario ?>, <?= $isPresente ? 0 : 1 ?>)" title="Clique para alternar presença">
                                              <?= $isPresente ? 'Pres.' : 'Aus.' ?>
                                            </span>
                                          <?php } else { ?>
                                            <span class="badge <?= $isPresente ? 'bg-success' : 'bg-danger' ?> opacity-75" style="cursor: not-allowed;" title="Presença finalizada (Histórico bloqueado)">
                                              <?= $isPresente ? 'Pres.' : 'Aus.' ?>
                                            </span>
                                          <?php } ?>
                                        <?php } elseif ($conf === 'NAO_CONFIRMADO') { ?>
                                          <!-- Voluntário Omitiu / Não Confirmou no Passado (Falta) -->
                                          <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-1.5 py-0.5 d-inline-flex align-items-center gap-1" data-bs-toggle="tooltip" title="Não confirmou até a data do evento (Omissão/Falta)">
                                            <i class="bi bi-exclamation-octagon-fill text-danger" style="font-size: 0.75rem;"></i> Falta
                                          </span>
                                        <?php } else { ?>
                                          <!-- Voluntário Pendente: Ampulheta com Efeito Pulsante -->
                                          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-1.5 py-0.5 d-inline-flex align-items-center justify-content-center pulse-pending" data-bs-toggle="tooltip" title="Pendente de confirmação do voluntário">
                                            <i class="bi bi-hourglass-split text-warning" style="font-size: 0.78rem;"></i>
                                          </span>
                                        <?php } ?>

                                        <?php if (!empty($sys_action->delete) && $permiteEditarEsteSlot) { ?>
                                          <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1" onclick="removerEscalaAjax(<?= $esc->id_escala_voluntario ?>)" title="Remover da Escala">
                                            <i class="bi bi-x-circle-fill"></i>
                                          </button>
                                        <?php } ?>
                                      </div>
                                    </div>
                                  <?php } ?>
                                </div>

                                <?php if (!empty($sys_action->create) && $permiteEditarEsteSlot) { ?>
                                  <div class="mt-1 pt-1 text-center">
                                    <button type="button" class="btn btn-sm btn-link text-primary text-decoration-none p-0 small fw-semibold d-inline-flex align-items-center gap-1"
                                      style="font-size: 0.72rem;"
                                      onclick="abrirModalEscalar('<?= $c->data_culto ?>', <?= $c->id_culto_padrao ?>, '<?= esc($c->titulo_culto) ?> - <?= $dia['data_formatada'] ?>', <?= $area->id_area ?>)"
                                      title="Escalar outro voluntário nesta mesma sub-área">
                                      <i class="bi bi-plus-circle"></i> + Escalar outro
                                    </button>
                                  </div>
                                <?php } ?>
                              <?php } else { ?>
                                <!-- Slot Vago / Disponível para Escalar -->
                                <?php if (!empty($sys_action->create) && $permiteEditarEsteSlot) { ?>
                                  <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 small w-100 text-nowrap" onclick="abrirModalEscalar('<?= $c->data_culto ?>', <?= $c->id_culto_padrao ?>, '<?= esc($c->titulo_culto) ?> - <?= $dia['data_formatada'] ?>', <?= $area->id_area ?>)">
                                    <i class="bi bi-plus me-1"></i> Escalar
                                  </button>
                                <?php } else { ?>
                                  <?php if ($isDiaPassado) { ?>
                                    <span class="badge badge-past-locked rounded-pill px-2 py-1"><i class="bi bi-lock-fill me-1"></i>Encerrado</span>
                                  <?php } else { ?>
                                    <small class="text-muted fst-italic">Vago</small>
                                  <?php } ?>
                                <?php } ?>
                              <?php } ?>

                              <!-- Registro Discreto de Recusas para este slot -->
                              <?php if (!empty($escalasRecusadas)) { ?>
                                <div class="d-flex flex-wrap gap-1 mt-1 pt-1 border-top border-secondary-subtle">
                                  <?php foreach ($escalasRecusadas as $escRec) {
                                    $nomeRec = formatarNomeExibicaoGrade($escRec->nome_voluntario, $escRec->nickname);
                                    $justif = !empty($escRec->justificativa_recusa) ? ' Motivo: ' . esc($escRec->justificativa_recusa) : '';
                                  ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill py-0.5 px-1.5 small d-flex align-items-center gap-1"
                                      data-bs-toggle="tooltip"
                                      data-bs-placement="top"
                                      title="Recusado por <?= esc($escRec->nome_voluntario) ?>.<?= $justif ?>"
                                      style="font-size: 0.65rem;">
                                      <i class="bi bi-hand-thumbs-down-fill text-danger"></i>
                                      <span class="text-truncate" style="max-width: 85px; text-decoration: line-through;"><?= esc($nomeRec) ?></span>
                                      <?php if (!empty($sys_action->delete) && $permiteEditarEsteSlot) { ?>
                                        <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-0.5" onclick="removerEscalaAjax(<?= $escRec->id_escala_voluntario ?>)" title="Excluir Registro de Recusa">
                                          <i class="bi bi-x" style="font-size: 0.75rem;"></i>
                                        </button>
                                      <?php } ?>
                                    </span>
                                  <?php } ?>
                                </div>
                              <?php } ?>

                            </div>
                          <?php } ?>
                        </div>
                      </td>

                      <!-- Ações -->
                      <?php if ($isDiaPassado && !$podeEditarPassado) { ?>
                        <td class="text-center">
                          <span class="badge badge-past-locked rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" title="Data encerrada (somente leitura)">
                            <i class="bi bi-lock-fill"></i> Encerrado
                          </span>
                        </td>
                      <?php } else { ?>
                        <td class="text-end pe-4">
                          <div class="d-inline-flex gap-1 align-items-center">
                            <?php if (!empty($sys_action->create)) { ?>
                              <?php
                              $totalMateriaisCulto = $recursosContagemPorCulto[$c->data_culto][$c->id_culto_padrao] ?? 0;
                              $btnClassMat = $totalMateriaisCulto > 0 ? 'btn-info text-white' : 'btn-outline-info';
                              ?>
                              <button type="button" id="btn_mat_culto_<?= $c->data_culto ?>_<?= $c->id_culto_padrao ?>" class="btn <?= $btnClassMat ?> btn-action position-relative" title="Biblioteca de Materiais (<?= $totalMateriaisCulto ?> anexado(s))" onclick="abrirModalMateriaisCulto('<?= $c->data_culto ?>', <?= $c->id_culto_padrao ?>, '<?= esc($c->titulo_culto) ?> - <?= $dia['data_formatada'] ?>')">
                                <i class="bi bi-collection-play-fill"></i>
                                <span id="badge_mat_culto_<?= $c->data_culto ?>_<?= $c->id_culto_padrao ?>" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light <?= $totalMateriaisCulto > 0 ? '' : 'd-none' ?>" style="font-size: 0.60rem; padding: 0.25em 0.45em;" title="<?= $totalMateriaisCulto ?> material(is) anexado(s)">
                                  <?= $totalMateriaisCulto ?>
                                </span>
                              </button>
                              <button type="button" class="btn btn-outline-primary btn-action" title="Escalar Voluntário no Culto" onclick="abrirModalEscalar('<?= $c->data_culto ?>', <?= $c->id_culto_padrao ?>, '<?= esc($c->titulo_culto) ?> - <?= $dia['data_formatada'] ?>')">
                                <i class="bi bi-person-plus-fill"></i>
                              </button>
                            <?php } ?>
                          </div>
                        </td>
                      <?php } ?>
                    </tr>
                  <?php
                  } // Fim foreach cultos
                } else { // Sem culto no dia
                  ?>
                  <tr class="grade-row <?= $bgClass ?> <?= $rowPastClass ?>" data-status-escala="sem_culto" data-is-weekend="<?= $isFimSemana ? '1' : '0' ?>" data-cultos="" data-is-past="<?= $isDiaPassado ? '1' : '0' ?>" style="<?= $styleOculto ?>">

                    <!-- Dia -->
                    <td class="text-center fw-bold font-monospace fs-6">
                      <?= $dia['data_formatada'] ?>
                      <?php if ($isDiaPassado) { ?>
                        <div class="mt-1">
                          <span class="badge bg-body-secondary text-muted rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                            Encerrado
                          </span>
                        </div>
                      <?php } ?>
                    </td>

                    <!-- Dia Semana -->
                    <td class="fw-semibold text-capitalize">
                      <?php if ($isFimSemana) { ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-bold">
                          <i class="bi bi-star-fill me-1 text-warning"></i><?= $dia['nome_dia_semana'] ?>
                        </span>
                      <?php } else { ?>
                        <span class="text-secondary"><?= $dia['nome_dia_semana'] ?></span>
                      <?php } ?>
                    </td>

                    <!-- Culto / Evento -->
                    <td>
                      <span class="text-muted small fst-italic">Sem culto agendado</span>
                    </td>

                    <!-- Sub-áreas e Voluntários Escalados -->
                    <td>
                      <span class="text-muted small">-</span>
                    </td>

                    <!-- Ações -->
                    <?php if ($isDiaPassado && !$podeEditarPassado) { ?>
                      <td class="text-center">
                        <span class="badge badge-past-locked rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" title="Data encerrada (somente leitura)">
                          <i class="bi bi-lock-fill"></i> Encerrado
                        </span>
                      </td>
                    <?php } else { ?>
                      <td class="text-center">
                        <span class="text-muted small">-</span>
                      </td>
                    <?php } ?>

                  </tr>
                <?php } ?>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal Escalação Rápida de Voluntário -->
<div class="modal fade" id="modalEscalarVoluntario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-primary">
          <i class="bi bi-calendar-check-fill me-2"></i> Escalar Voluntário
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <form id="formSalvarEscala" onsubmit="salvarEscalaAjax(event)">
        <input type="hidden" name="data_culto" id="modal_escala_data_culto" value="">
        <input type="hidden" name="id_culto_padrao" id="modal_escala_id_culto_padrao" value="0">
        <input type="hidden" name="id_departamento" value="<?= $id_departamento ?>">

        <div class="modal-body py-3">

          <div class="mb-3">
            <label class="form-label fw-semibold">Culto / Data</label>
            <input type="text" id="modal_escala_culto_display" class="form-control bg-body-tertiary fw-bold text-primary" readonly>
          </div>

          <div class="mb-3">
            <label for="modal_escala_id_area" class="form-label fw-semibold">Sub-área de Atuação <span class="text-danger">*</span></label>
            <select name="id_area" id="modal_escala_id_area" class="form-select" required onchange="carregarVoluntariosPorArea(this.value)">
              <option value="">Selecione a sub-área...</option>
              <?php foreach ($areasDepartamento as $area) { ?>
                <option value="<?= $area->id_area ?>"><?= esc($area->nome_area) ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label fw-semibold mb-0">
                Voluntário <span class="text-danger">*</span>
              </label>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="filtro_apenas_vinculados" checked onchange="filtrarListaVoluntariosModal()">
                <label class="form-check-label small fw-semibold text-primary" for="filtro_apenas_vinculados" id="label_apenas_vinculados">
                  Apenas vinculados (<span id="count_vinculados">0</span>)
                </label>
              </div>
            </div>

            <!-- Campo de Busca em Tempo Real -->
            <div class="input-group input-group-sm mb-2 shadow-sm">
              <span class="input-group-text modal-input-search-addon border-end-0"><i class="bi bi-search"></i></span>
              <input type="text" id="filtro_busca_voluntario" class="form-control border-start-0 ps-0" placeholder="Digitar nome, apelido ou nível (ex: Senior)..." oninput="filtrarListaVoluntariosModal()" autocomplete="off">
              <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('filtro_busca_voluntario').value=''; filtrarListaVoluntariosModal();" title="Limpar busca">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>

            <!-- Input Hidden com os IDs dos Voluntários Selecionados (separados por vírgula) -->
            <input type="hidden" name="id_voluntarios" id="modal_escala_id_voluntarios" value="" required>

            <!-- Container da Lista de Voluntários -->
            <div id="container_voluntarios_lista" class="border rounded-3 p-2 bg-body-tertiary shadow-inner" style="max-height: 240px; overflow-y: auto;">
              <div class="text-center text-muted py-4 small">
                <i class="bi bi-arrow-up-circle me-1"></i> Selecione uma sub-área para listar os voluntários
              </div>
            </div>

            <!-- Banner de Voluntários Selecionados (Múltipla Seleção) -->
            <div id="voluntario_selecionado_preview" class="d-none">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-primary-emphasis d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                  <i class="bi bi-people-fill text-primary"></i> &nbsp;Voluntários Selecionados:
                </span>
                <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none small fw-semibold d-flex align-items-center gap-1" onclick="deselecionarTodosVoluntariosModal()" title="Limpar todos">
                  <i class="bi bi-trash3"></i> Limpar todos
                </button>
              </div>
              <div class="chips-container-wrapper" id="voluntarios_selecionados_chips"></div>
            </div>
          </div>

          <div class="mb-2">
            <label for="modal_escala_observacao" class="form-label fw-semibold">Observações / Instruções (Opcional)</label>
            <input type="text" name="observacao" id="modal_escala_observacao" class="form-control" placeholder="Ex: Chegar 30 minutos antes para alinhamento...">
          </div>

        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnSubmitEscala" disabled>
            <i class="bi bi-check-lg me-1"></i> Confirmar Escalação
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Confirmação de Remoção de Voluntário da Escala -->
<div class="modal fade" id="modalConfirmarRemoverEscala" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Remover da Escala
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body py-3">
        <p class="mb-0">Deseja realmente remover este voluntário da escala?</p>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="btnConfirmarRemoverEscalaSubmit">
          <i class="bi bi-trash-fill me-1"></i> Confirmar Remoção
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Materiais de Apoio / Biblioteca do Culto -->
<div class="modal fade" id="modalMateriaisCulto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content music-modal-content shadow-lg border-0">
      <div class="modal-header border-0 pb-1 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-primary text-white shadow-sm" style="width: 38px; height: 38px;">
            <i class="bi bi-disc-fill fs-5"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-1.5 mb-0">
              <span class="badge bg-primary-subtle text-primary rounded-pill px-2" style="font-size: 0.70rem; font-weight: 700; letter-spacing: 0.5px;">BIBLIOTECA</span>
            </div>
            <h5 class="modal-title fw-bold text-dark-emphasis mb-0" id="modal_mat_culto_titulo" style="font-size: 1.15rem;">Materiais do Culto</h5>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body py-3">
        <!-- Sub-área de Destino & Ações Rápidas -->
        <div class="p-3 rounded-4 bg-body-tertiary border mb-3 shadow-xs">
          <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-5">
              <label for="modal_mat_id_area" class="form-label small fw-bold text-secondary mb-1">
                <i class="bi bi-pin-map-fill me-1 text-primary"></i> Destino / Sub-área:
              </label>
              <select id="modal_mat_id_area" class="form-select form-select-sm fw-semibold rounded-pill px-3" onchange="aoMudarSubareaModalMateriais()">
                <option value="0">🌐 Geral (Todas as Sub-áreas)</option>
                <?php foreach ($areasDepartamento as $area) { ?>
                  <option value="<?= $area->id_area ?>">📍 <?= esc($area->nome_area) ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="col-md-7 d-flex justify-content-md-end gap-2 align-self-end">
              <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-xs fw-semibold" onclick="abrirPickerColecoesEscala()">
                <i class="bi bi-folder2-open me-1"></i> + Repertório Completo
              </button>
              <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs fw-semibold" onclick="abrirPickerMateriaisEscala()">
                <i class="bi bi-plus-circle-fill me-1"></i> + Material Avulso
              </button>
            </div>
          </div>
        </div>

        <!-- Filtro Rápido / Visualização da Lista -->
        <div class="d-flex align-items-center justify-content-between mb-2.5 px-1">
          <div class="d-flex align-items-center gap-1.5">
            <i class="bi bi-music-note-list text-primary"></i>
            <span class="small fw-bold text-dark-emphasis" id="modal_mat_lista_info">
              Faixas & Materiais Escalados
            </span>
          </div>
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary active pill-filter-btn" id="btn_mat_view_all" onclick="filtrarVisualizacaoMateriais('all')">Todos</button>
            <button type="button" class="btn btn-sm btn-outline-secondary pill-filter-btn" id="btn_mat_view_selected" onclick="filtrarVisualizacaoMateriais('current')">Apenas Destino Selecionado</button>
          </div>
        </div>

        <!-- Lista de Materiais Anexados -->
        <div id="container_materiais_escala_lista" class="d-flex flex-column gap-2 overflow-auto p-1" style="max-height: 380px;">
          <div class="text-center text-muted py-4 small">
            <div class="spinner-border spinner-border-sm text-primary me-2"></div> Carregando materiais...
          </div>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Concluir</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Picker de Materiais e Coleções para Anexar na Escala (Estilo Spotify) -->
<div class="modal fade" id="modalPickerMateriaisEscala" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content music-modal-content shadow-lg border-0">
      <div class="modal-header border-0 pb-1 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-light btn-sm rounded-circle p-1.5 d-flex align-items-center justify-content-center shadow-xs" onclick="voltarParaModalMateriais()" title="Voltar aos materiais do culto" style="width: 32px; height: 32px;">
            <i class="bi bi-arrow-left fs-6"></i>
          </button>
          <div>
            <div class="d-flex align-items-center gap-1.5 mb-0">
              <span class="badge bg-success-subtle text-success rounded-pill px-2" style="font-size: 0.70rem; font-weight: 700; letter-spacing: 0.5px;">BIBLIOTECA DE MÍDIA</span>
            </div>
            <h5 class="modal-title fw-bold text-dark-emphasis mb-0" id="picker_modal_title" style="font-size: 1.15rem;">
              Selecionar Materiais
            </h5>
          </div>
        </div>
        <button type="button" class="btn-close" onclick="fecharTodosModaisMateriais()" aria-label="Fechar"></button>
      </div>

      <div class="modal-body py-3">
        <!-- Destino Banner -->
        <div class="p-2.5 rounded-3 bg-body-tertiary border text-secondary small mb-3 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt-fill text-primary"></i>
            <span>Destino na Escala: <strong id="picker_destino_label" class="text-primary-emphasis">Geral (Todas as Sub-áreas)</strong></span>
          </div>
          <span class="badge bg-secondary-subtle text-secondary rounded-pill">Ao clicar em Anexar, o item entra na escala</span>
        </div>

        <!-- Barra de Busca Spotify Style -->
        <div class="position-relative mb-3">
          <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 rounded-start-pill ps-3 text-muted">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="filtro_picker_escala_busca" class="form-control bg-body-tertiary border-start-0 rounded-end-pill pe-3" placeholder="Buscar por título, autor, cifra ou link..." oninput="filtrarPickerEscala()">
          </div>
        </div>

        <!-- Filtros Rápidos por Tipo de Mídia -->
        <div class="d-flex align-items-center gap-1.5 overflow-x-auto pb-2 mb-2" id="picker_quick_filter_pills">
          <button type="button" class="btn pill-filter-btn active" data-type="" onclick="aplicarFiltroTipoPicker('')">
            <i class="bi bi-grid-fill me-1"></i> Todos
          </button>
          <button type="button" class="btn pill-filter-btn" data-type="audio" onclick="aplicarFiltroTipoPicker('audio')">
            <i class="bi bi-music-note-beamed text-success me-1"></i> Músicas & Áudios
          </button>
          <button type="button" class="btn pill-filter-btn" data-type="video" onclick="aplicarFiltroTipoPicker('video')">
            <i class="bi bi-play-circle-fill text-danger me-1"></i> Vídeos
          </button>
          <button type="button" class="btn pill-filter-btn" data-type="pdf" onclick="aplicarFiltroTipoPicker('pdf')">
            <i class="bi bi-file-earmark-pdf-fill text-warning me-1"></i> Cifras / PDFs
          </button>
          <button type="button" class="btn pill-filter-btn" data-type="text" onclick="aplicarFiltroTipoPicker('text')">
            <i class="bi bi-file-text-fill text-secondary me-1"></i> Letras / Textos
          </button>
        </div>

        <!-- Container de Itens Carregados -->
        <div id="picker_escala_itens_container" class="d-flex flex-column gap-2 overflow-auto p-1" style="max-height: 360px;">
          <!-- Carregado via AJAX -->
        </div>
      </div>

      <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" onclick="voltarParaModalMateriais()">
          <i class="bi bi-arrow-left me-1"></i> Voltar à Escala
        </button>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" onclick="voltarParaModalMateriais()">
          Pronto / Ver Escala
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  let modalEscalarInstance = null;
  let modalRemoverEscalaInstance = null;
  let idEscalaParaRemover = null;
  let cacheVoluntariosModal = {
    vinculados: [],
    outros: []
  };
  let voluntariosSelecionadosModal = {};

  function abrirModalEscalar(data_culto, id_culto_padrao, cultoDisplay, id_area_pre_select = null) {
    document.getElementById('modal_escala_data_culto').value = data_culto;
    document.getElementById('modal_escala_id_culto_padrao').value = id_culto_padrao;
    document.getElementById('modal_escala_culto_display').value = cultoDisplay;
    document.getElementById('modal_escala_observacao').value = '';
    document.getElementById('filtro_busca_voluntario').value = '';
    document.getElementById('filtro_apenas_vinculados').checked = true;
    deselecionarTodosVoluntariosModal();

    const selectArea = document.getElementById('modal_escala_id_area');
    if (id_area_pre_select) {
      selectArea.value = id_area_pre_select;
      carregarVoluntariosPorArea(id_area_pre_select);
    } else {
      selectArea.value = selectArea.options.length > 1 ? selectArea.options[1].value : '';
      if (selectArea.value) {
        carregarVoluntariosPorArea(selectArea.value);
      }
    }

    if (!modalEscalarInstance) {
      modalEscalarInstance = new bootstrap.Modal(document.getElementById('modalEscalarVoluntario'));
    }
    modalEscalarInstance.show();
  }

  function carregarVoluntariosPorArea(id_area) {
    const container = document.getElementById('container_voluntarios_lista');
    container.innerHTML = '<div class="text-center text-muted py-3 small"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Carregando voluntários...</div>';
    deselecionarTodosVoluntariosModal();

    if (!id_area) {
      container.innerHTML = '<div class="text-center text-muted py-3 small">Selecione uma sub-área primeiro...</div>';
      return;
    }

    const dataCulto = document.getElementById('modal_escala_data_culto')?.value || '';
    const idCultoPadrao = document.getElementById('modal_escala_id_culto_padrao')?.value || '';

    fetch(`<?= base_url("escala/getVoluntariosPorArea?id_departamento={$id_departamento}") ?>&id_area=${id_area}&data_culto=${dataCulto}&id_culto_padrao=${idCultoPadrao}`)
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          cacheVoluntariosModal.vinculados = data.vinculados_area || [];
          cacheVoluntariosModal.outros = data.outros_departamento || [];

          document.getElementById('count_vinculados').textContent = cacheVoluntariosModal.vinculados.length;
          filtrarListaVoluntariosModal();
        } else {
          container.innerHTML = '<div class="text-center text-danger py-3 small">Erro ao carregar voluntários.</div>';
        }
      })
      .catch(err => {
        container.innerHTML = '<div class="text-center text-danger py-3 small">Falha na requisição ao servidor.</div>';
      });
  }

  function getInitials(name) {
    if (!name) return 'V';
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  }

  function filtrarListaVoluntariosModal() {
    const container = document.getElementById('container_voluntarios_lista');
    const apenasVinculados = document.getElementById('filtro_apenas_vinculados').checked;
    const termoBusca = (document.getElementById('filtro_busca_voluntario').value || '').trim().toLowerCase();

    let lista = [];

    cacheVoluntariosModal.vinculados.forEach(v => {
      lista.push({
        ...v,
        is_vinculado: true
      });
    });

    if (!apenasVinculados) {
      cacheVoluntariosModal.outros.forEach(v => {
        lista.push({
          ...v,
          is_vinculado: false
        });
      });
    }

    if (termoBusca !== '') {
      lista = lista.filter(v => {
        const nome = (v.nome || '').toLowerCase();
        const nick = (v.nickname || '').toLowerCase();
        const nivel = (v.nivel_conhecimento || '').toLowerCase();
        const fone = (v.telefone_whatsapp || '').toLowerCase();
        return nome.includes(termoBusca) || nick.includes(termoBusca) || nivel.includes(termoBusca) || fone.includes(termoBusca);
      });
    }

    if (lista.length === 0) {
      container.innerHTML = `
        <div class="text-center text-muted py-4 small">
          <i class="bi bi-search me-1"></i> Nenhum voluntário encontrado ${termoBusca ? 'para "<b>' + escapeHtml(termoBusca) + '</b>"' : ''}.
          ${apenasVinculados ? '<div class="mt-1"><a href="javascript:void(0)" onclick="document.getElementById(\'filtro_apenas_vinculados\').checked=false; filtrarListaVoluntariosModal();" class="text-primary text-decoration-none">Ver outros voluntários do departamento</a></div>' : ''}
        </div>`;
      return;
    }

    let html = '<div class="d-flex flex-column gap-1.5">';

    lista.forEach(v => {
      const isSelected = !!voluntariosSelecionadosModal[v.id_voluntario];
      const initials = getInitials(v.nome);
      const nivel = (v.nivel_conhecimento || 'JUNIOR').toUpperCase();
      const cleanNick = (v.nickname || '').trim();
      const nickHtml = cleanNick !== '' ? `<span class="text-body-secondary fw-medium" style="font-size: 0.80rem;">&nbsp;(${escapeHtml(cleanNick)})</span>` : '';
      const foto = v.foto_url ? v.foto_url : '';

      // Badge Nível de Conhecimento (Padrão da tela de voluntários)
      const badgeNivelMap = {
        'APRENDIZ': 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        'JUNIOR': 'bg-info-subtle text-info border border-info-subtle',
        'PLENO': 'bg-success-subtle text-success border border-success-subtle',
        'SENIOR': 'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold'
      };
      const badgeNivelClass = badgeNivelMap[nivel] || 'bg-light text-dark';

      // Disponibilidade tag (Topo)
      let dispTagHtml = '';
      if (v.tem_conflito_agenda) {
        dispTagHtml = `<span class="badge-stacked-disp bg-danger text-white fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Conflito</span>`;
      } else if (v.tipo_disponibilidade === 'TOTAL') {
        dispTagHtml = `<span class="badge-stacked-disp bg-primary text-white"><i class="bi bi-snow"></i> Total Disp.</span>`;
      } else if (v.disponivel_culto) {
        dispTagHtml = `<span class="badge-stacked-disp bg-success text-white"><i class="bi bi-check2"></i> Disponível</span>`;
      } else {
        dispTagHtml = `<span class="badge-stacked-disp bg-secondary text-white"><i class="bi bi-clock-history"></i> Indisp.</span>`;
      }

      // Mensal tag (Abaixo)
      let limiteTagHtml = '';
      if (v.atingiu_limite) {
        limiteTagHtml = `<span class="badge-stacked-limit bg-danger-subtle text-danger border-danger-subtle fw-bold"><i class="bi bi-exclamation-circle me-1"></i>${v.total_escalas_mes}/${v.max_escalas_mes} Limite</span>`;
      } else if (v.max_escalas_mes > 0) {
        limiteTagHtml = `<span class="badge-stacked-limit">${v.total_escalas_mes}/${v.max_escalas_mes} por mês</span>`;
      } else {
        limiteTagHtml = `<span class="badge-stacked-limit">${v.total_escalas_mes} por mês</span>`;
      }

      const itemClass = isSelected ?
        'vol-card-selected' :
        (v.tem_conflito_agenda ? 'border-danger-subtle bg-danger-subtle opacity-75' : (v.atingiu_limite ? 'border-danger-subtle bg-danger-subtle opacity-75' : ''));

      const jsonVolStr = JSON.stringify(v).replace(/"/g, '&quot;');

      html += `
        <div class="vol-card-item d-flex align-items-center justify-content-between ${itemClass}" 
             style="cursor: ${(v.tem_conflito_agenda || v.atingiu_limite) && !isSelected ? 'not-allowed' : 'pointer'};"
             onclick="clickVoluntarioItem(${v.id_voluntario}, ${v.atingiu_limite ? 'true' : 'false'}, ${v.tem_conflito_agenda ? 'true' : 'false'}, ${jsonVolStr})">
          
          <!-- Lado Esquerdo: Checkbox + Avatar + Informações -->
          <div class="d-flex align-items-center gap-2.5 flex-grow-1 min-w-0 me-2">
            <!-- Status Check -->
            <div class="vol-check-indicator ${isSelected ? 'checked' : ''}">
              <i class="bi bi-check-lg"></i>
            </div>

            <!-- Avatar com borda dourada e tooltip -->
            <div class="avatar-gold-circle position-relative overflow-hidden flex-shrink-0" data-bs-toggle="tooltip" data-bs-placement="top" title="${escapeHtml(v.nome)}">
              <span>${escapeHtml(initials)}</span>
              ${foto ? `<img src="${escapeHtml(foto)}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover rounded-circle" onerror="this.remove();">` : ''}
            </div>
            
            <!-- Informações do Voluntário -->
            <div class="d-flex flex-column min-w-0 flex-grow-1">
              <!-- Linha 1: Nome + (Apelido) + Nível -->
              <div class="d-flex align-items-center gap-1.5 flex-wrap">
                <span class="fw-bold text-body text-truncate" style="font-size: 0.86rem; line-height: 1.2;">
                  ${escapeHtml(v.nome)}
                </span>
                ${nickHtml}
                &nbsp;<span class="badge rounded-pill px-2 py-0 ${badgeNivelClass} ms-0.5" style="font-size: 0.65rem;" title="Nível de conhecimento">${escapeHtml(nivel)}</span>
              </div>
              
              <!-- Linha 2: Contato + Linked -->
              <div class="d-flex align-items-center gap-2 text-muted small mt-0.5" style="font-size: 0.72rem; line-height: 1.1;">
                <span class="d-flex align-items-center gap-1 text-nowrap">
                  <i class="bi bi-whatsapp me-1 text-success" style="font-size: 0.70rem;"></i>
                  <span>${escapeHtml(v.telefone_whatsapp || 'Sem telefone')}</span>
                </span>
                <span class="text-muted opacity-40">•</span>
                ${v.is_vinculado ? `
                  <span class="d-flex align-items-center gap-1 text-nowrap text-body-secondary fw-semibold">
                    <span class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Voluntário Vinculado à ${escapeHtml(v.nome_area || 'Área')}">⭐</span>
                  </span>
                ` : `
                  <span class="d-flex align-items-center gap-1 text-nowrap text-muted" ${v.nome_area ? `data-bs-toggle="tooltip" data-bs-placement="top" title="Vinculado à: ${escapeHtml(v.nome_area)}"` : ''}>
                    Outra área
                  </span>
                `}
              </div>

              ${v.tem_conflito_agenda ? `
                <div class="d-flex align-items-center gap-1 text-danger fw-semibold mt-0.5" style="font-size: 0.70rem; line-height: 1.1;">
                  <i class="bi bi-calendar-x-fill text-danger"></i>
                  <span>Já escalado em: <strong>${escapeHtml(v.conflito_departamento)} ${escapeHtml(v.conflito_subarea)}</strong></span>
                </div>
              ` : ''}
            </div>
          </div>

          <!-- Lado Direito: Badges Empilhados (Um sobre o outro) -->
          <div class="d-flex flex-column align-items-end justify-content-center gap-1 flex-shrink-0 text-nowrap">
            ${dispTagHtml}
            ${limiteTagHtml}
          </div>

        </div>
      `;
    });

    html += '</div>';
    container.innerHTML = html;

    // Inicializar tooltips nos avatares
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
      container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
      });
    }
  }

  function clickVoluntarioItem(id_voluntario, atingiuLimite, temConflitoAgenda, voluntarioObj) {
    if (voluntariosSelecionadosModal[id_voluntario]) {
      delete voluntariosSelecionadosModal[id_voluntario];
    } else {
      if (temConflitoAgenda) {
        const depto = (voluntarioObj.conflito_departamento || '').toUpperCase();
        const area = (voluntarioObj.conflito_subarea || '').toUpperCase();
        const msg = `Este voluntário não pode ser escalado, pois já tem uma agenda confirmada para este dia/culto:<br>• <strong>${escapeHtml(depto)} ${escapeHtml(area)}</strong>`;
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('warning', 'Conflito de Agenda', msg);
        } else if (typeof usShowToast === 'function') {
          usShowToast('warning', 'Conflito de Agenda', msg);
        }
        return;
      }
      if (atingiuLimite) {
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('warning', 'Limite Atingido', `Este voluntário já atingiu o limite de ${voluntarioObj.max_escalas_mes} escalas neste mês.`);
        } else if (typeof usShowToast === 'function') {
          usShowToast('warning', 'Limite Atingido', `Este voluntário já atingiu o limite de ${voluntarioObj.max_escalas_mes} escalas neste mês.`);
        }
        return;
      }
      voluntariosSelecionadosModal[id_voluntario] = voluntarioObj;
    }

    atualizarPreviewVoluntariosSelecionados();
    filtrarListaVoluntariosModal();
  }

  function atualizarPreviewVoluntariosSelecionados() {
    const keys = Object.keys(voluntariosSelecionadosModal);
    const count = keys.length;
    const hiddenInput = document.getElementById('modal_escala_id_voluntarios');
    const submitBtn = document.getElementById('btnSubmitEscala');
    const preview = document.getElementById('voluntario_selecionado_preview');
    const container = document.getElementById('voluntarios_selecionados_chips');

    if (hiddenInput) hiddenInput.value = keys.join(',');

    if (count === 0) {
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Confirmar Escalação';
      }
      if (preview) preview.classList.add('d-none');
      if (container) container.innerHTML = '';
    } else {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="bi bi-check-lg me-1"></i> Confirmar Escalação (${count})`;
      }
      if (preview) preview.classList.remove('d-none');

      let chipsHtml = '';
      keys.forEach(k => {
        const v = voluntariosSelecionadosModal[k];
        const nome = v.nickname ? `${v.nome} (${v.nickname})` : v.nome;
        chipsHtml += `
          <span class="badge badge-chip-voluntario text-white">
            <span class="text-truncate" style="max-width: 140px;">${escapeHtml(nome)}</span>
            <i class="bi bi-x-circle-fill chip-close-btn" onclick="event.stopPropagation(); deselecionarVoluntarioModal(${v.id_voluntario})" title="Remover este voluntário"></i>
          </span>
        `;
      });
      if (container) container.innerHTML = chipsHtml;
    }
  }

  function deselecionarVoluntarioModal(id_voluntario) {
    if (id_voluntario) {
      delete voluntariosSelecionadosModal[id_voluntario];
    }
    atualizarPreviewVoluntariosSelecionados();
    filtrarListaVoluntariosModal();
  }

  function deselecionarTodosVoluntariosModal() {
    voluntariosSelecionadosModal = {};
    atualizarPreviewVoluntariosSelecionados();
    filtrarListaVoluntariosModal();
  }

  function salvarEscalaAjax(e) {
    e.preventDefault();
    const form = document.getElementById('formSalvarEscala');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSubmitEscala');
    btn.disabled = true;

    fetch('<?= base_url('escala/salvarEscala') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        if (data.status === 'success') {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Escalação Salva', data.message);
          } else if (typeof usShowToast === 'function') {
            usShowToast('success', 'Escalação Salva', data.message);
          }
          modalEscalarInstance.hide();
          setTimeout(() => window.location.reload(), 600);
        } else {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Erro na Escalação', data.message || 'Erro ao salvar escala.');
          } else if (typeof usShowToast === 'function') {
            usShowToast('error', 'Erro na Escalação', data.message || 'Erro ao salvar escala.');
          }
        }
      })
      .catch(err => {
        btn.disabled = false;
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Erro de Conexão', 'Falha ao salvar escala.');
        }
      });
  }

  function removerEscalaAjax(id_escala_voluntario) {
    idEscalaParaRemover = id_escala_voluntario;

    if (!modalRemoverEscalaInstance) {
      modalRemoverEscalaInstance = new bootstrap.Modal(document.getElementById('modalConfirmarRemoverEscala'));
    }
    modalRemoverEscalaInstance.show();
  }

  document.getElementById('btnConfirmarRemoverEscalaSubmit')?.addEventListener('click', function() {
    if (!idEscalaParaRemover) return;

    const formData = new FormData();
    formData.append('id_escala_voluntario', idEscalaParaRemover);

    fetch('<?= base_url('escala/removerEscala') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (modalRemoverEscalaInstance) modalRemoverEscalaInstance.hide();
        if (data.status === 'success') {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Escala Atualizada', data.message);
          } else if (typeof usShowToast === 'function') {
            usShowToast('success', 'Escala Atualizada', data.message);
          }
          setTimeout(() => window.location.reload(), 600);
        } else {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Não foi possível remover', data.message || 'Erro ao remover voluntário.');
          } else if (typeof usShowToast === 'function') {
            usShowToast('error', 'Não foi possível remover', data.message || 'Erro ao remover voluntário.');
          }
        }
      })
      .catch(err => {
        if (modalRemoverEscalaInstance) modalRemoverEscalaInstance.hide();
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Erro de Conexão', 'Falha ao comunicar com o servidor.');
        }
      });
  });

  function togglePresenca(id_escala_voluntario, status_presenca) {
    const formData = new FormData();
    formData.append('id_escala_voluntario', id_escala_voluntario);
    formData.append('status_presenca', status_presenca);

    fetch('<?= base_url('escala/alternarPresenca') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('info', 'Presença Atualizada', data.message);
          } else if (typeof usShowToast === 'function') {
            usShowToast('info', 'Presença Atualizada', data.message);
          }
          setTimeout(() => window.location.reload(), 500);
        } else {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Atenção', data.message || 'Erro ao alternar presença.');
          } else if (typeof usShowToast === 'function') {
            usShowToast('error', 'Atenção', data.message || 'Erro ao alternar presença.');
          }
        }
      })
      .catch(err => {
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Erro de Conexão', 'Falha ao atualizar presença.');
        }
      });
  }

  // Função unificada de filtros da Grade (Status + Tipo de Culto + Dias Anteriores)
  function aplicarFiltrosGrade() {
    const activeBtn = document.querySelector('.btnFilterRow.active');
    const filter = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
    const cultoSelecionado = (document.getElementById('selectFiltroCulto')?.value || '').toLowerCase().trim();
    const toggleDiasAnteriores = document.getElementById('toggleDiasAnteriores');
    const exibirDiasAnteriores = toggleDiasAnteriores ? toggleDiasAnteriores.checked : true;

    document.querySelectorAll('#tblGradeEscala tbody tr.grade-row').forEach(row => {
      const status = row.getAttribute('data-status-escala');
      const isWeekend = row.getAttribute('data-is-weekend') === '1';
      const cultosNaLinha = (row.getAttribute('data-cultos') || '').toLowerCase();
      const isPast = row.getAttribute('data-is-past') === '1';

      // Se for linha de dia passado no mês corrente e o toggle estiver desligado, oculta
      if (isPast && !exibirDiasAnteriores) {
        row.style.display = 'none';
        return;
      }

      let atendeStatus = true;
      if (filter === 'all') {
        atendeStatus = true;
      } else if (filter === 'com_culto') {
        atendeStatus = (status !== 'sem_culto');
      } else if (filter === 'com_escala') {
        atendeStatus = (status === 'com_escala');
      } else if (filter === 'sem_escala') {
        atendeStatus = (status === 'sem_escala');
      } else if (filter === 'fim_semana') {
        atendeStatus = isWeekend;
      }

      let atendeCulto = true;
      if (cultoSelecionado !== '') {
        atendeCulto = (status !== 'sem_culto') && cultosNaLinha.includes(cultoSelecionado);
      }

      row.style.display = (atendeStatus && atendeCulto) ? '' : 'none';
    });
  }

  // Event listener dos botões rápidos
  document.querySelectorAll('.btnFilterRow').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.btnFilterRow').forEach(b => {
        b.classList.remove('btn-primary', 'active');
        b.classList.add('btn-outline-secondary');
      });
      this.classList.add('btn-primary', 'active');
      this.classList.remove('btn-outline-secondary');

      aplicarFiltrosGrade();
    });
  });

  // Event listener da mudança no dropdown de Cultos
  document.getElementById('selectFiltroCulto')?.addEventListener('change', function() {
    aplicarFiltrosGrade();
  });

  // Event listener do Toggle de Dias Anteriores
  document.getElementById('toggleDiasAnteriores')?.addEventListener('change', function() {
    aplicarFiltrosGrade();
  });

  function safeDecode(text) {
    if (!text) return '';
    const doc = new DOMParser().parseFromString(String(text), 'text/html');
    return doc.body.textContent || '';
  }

  function escapeHtml(text) {
    if (!text) return '';
    const clean = safeDecode(text);
    return clean
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function getMediaCoverHtml(r, isCollection = false) {
    if (isCollection) {
      return `
        <div class="music-cover-art cover-gradient-collection flex-shrink-0">
          <i class="bi bi-folder2-open fs-5"></i>
        </div>
      `;
    }
    if (r.thumbnail_url && (r.provider === 'youtube' || r.provider === 'spotify')) {
      return `
        <div class="music-cover-art flex-shrink-0 position-relative">
          <img src="${escapeHtml(r.thumbnail_url)}" alt="Capa" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
      `;
    }
    let gradClass = 'cover-gradient-audio';
    let iconClass = r.type_icon || 'bi bi-music-note-beamed';
    const code = (r.type_code || '').toLowerCase();
    const provider = (r.provider || '').toLowerCase();

    if (provider === 'youtube' || code === 'video') {
      gradClass = 'cover-gradient-video';
      iconClass = 'bi bi-play-circle-fill';
    } else if (provider === 'spotify') {
      gradClass = 'cover-gradient-spotify';
      iconClass = 'bi bi-spotify';
    } else if (code === 'pdf') {
      gradClass = 'cover-gradient-pdf';
      iconClass = 'bi bi-file-earmark-pdf-fill';
    } else if (code === 'link') {
      gradClass = 'cover-gradient-link';
      iconClass = 'bi bi-link-45deg';
    } else if (code === 'text') {
      gradClass = 'cover-gradient-text';
      iconClass = 'bi bi-file-text-fill';
    }

    return `
      <div class="music-cover-art ${gradClass} flex-shrink-0">
        <i class="${iconClass} fs-5"></i>
      </div>
    `;
  }

  // ============================================================
  // BIBLIOTECA / MATERIAIS DE APOIO NA ESCALA DO CULTO COM SUB-ÁREAS
  // ============================================================
  let modalMateriaisCultoInstance = null;
  let modalPickerMateriaisEscalaInstance = null;
  let contextoMateriaisAtual = {
    data_culto: '',
    id_culto_padrao: 0,
    id_departamento: <?= (int)$id_departamento ?>,
    id_area: 0
  };
  let cacheMateriaisEscalaCulto = [];
  let cacheItensPickerDisponiveis = [];
  let modoPickerAtual = 'recursos'; // 'recursos' ou 'colecoes'
  let filtroVisualizacaoMateriais = 'all';
  let filtroTipoPickerAtual = '';

  function abrirModalMateriaisCulto(data_culto, id_culto_padrao, cultoDisplay, id_area_pre_select = 0) {
    contextoMateriaisAtual.data_culto = data_culto;
    contextoMateriaisAtual.id_culto_padrao = id_culto_padrao;
    contextoMateriaisAtual.id_area = parseInt(id_area_pre_select) || 0;

    document.getElementById('modal_mat_culto_titulo').textContent = `Biblioteca • ${cultoDisplay}`;

    const selectArea = document.getElementById('modal_mat_id_area');
    if (selectArea) {
      selectArea.value = contextoMateriaisAtual.id_area;
    }

    if (!modalMateriaisCultoInstance) {
      modalMateriaisCultoInstance = new bootstrap.Modal(document.getElementById('modalMateriaisCulto'));
    }

    carregarMateriaisEscala(data_culto, id_culto_padrao);
    modalMateriaisCultoInstance.show();
  }

  function voltarParaModalMateriais() {
    if (modalPickerMateriaisEscalaInstance) {
      modalPickerMateriaisEscalaInstance.hide();
    }
    if (modalMateriaisCultoInstance) {
      modalMateriaisCultoInstance.show();
    }
  }

  function fecharTodosModaisMateriais() {
    if (modalPickerMateriaisEscalaInstance) modalPickerMateriaisEscalaInstance.hide();
    if (modalMateriaisCultoInstance) modalMateriaisCultoInstance.hide();
  }

  function aoMudarSubareaModalMateriais() {
    const selectArea = document.getElementById('modal_mat_id_area');
    contextoMateriaisAtual.id_area = parseInt(selectArea.value) || 0;
    if (filtroVisualizacaoMateriais === 'current') {
      renderizarListaMateriaisEscala(cacheMateriaisEscalaCulto);
    }
  }

  function filtrarVisualizacaoMateriais(modo) {
    filtroVisualizacaoMateriais = modo;
    document.getElementById('btn_mat_view_all')?.classList.toggle('active', modo === 'all');
    document.getElementById('btn_mat_view_selected')?.classList.toggle('active', modo === 'current');
    renderizarListaMateriaisEscala(cacheMateriaisEscalaCulto);
  }

  function carregarMateriaisEscala(data_culto, id_culto_padrao) {
    const container = document.getElementById('container_materiais_escala_lista');
    container.innerHTML = '<div class="text-center text-muted py-4 small"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Carregando materiais...</div>';

    fetch(`<?= base_url('resource/getRecursosEscala') ?>?data_culto=${data_culto}&id_culto_padrao=${id_culto_padrao}&id_departamento=${contextoMateriaisAtual.id_departamento}`)
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success' && res.data) {
          cacheMateriaisEscalaCulto = res.data;
          renderizarListaMateriaisEscala(res.data);
        } else {
          cacheMateriaisEscalaCulto = [];
          container.innerHTML = `
            <div class="text-center py-4 text-secondary small bg-body rounded-4 border p-4">
              <i class="bi bi-music-note-beamed display-6 text-muted d-block mb-2"></i>
              Nenhum material anexado a este culto. Clique nos botões acima para adicionar materiais avulsos ou uma coleção completa.
            </div>
          `;
        }
      })
      .catch(() => {
        container.innerHTML = '<div class="text-center text-danger py-3 small">Erro ao carregar materiais da escala.</div>';
      });
  }

  function renderizarListaMateriaisEscala(lista) {
    const container = document.getElementById('container_materiais_escala_lista');
    const idAreaFiltro = contextoMateriaisAtual.id_area;

    let exibiveis = lista || [];
    if (filtroVisualizacaoMateriais === 'current') {
      exibiveis = exibiveis.filter(r => (parseInt(r.id_area) === idAreaFiltro || parseInt(r.id_area) === 0));
    }

    if (exibiveis.length === 0) {
      container.innerHTML = `
        <div class="text-center py-4 text-secondary small bg-body rounded-4 border p-4">
          <i class="bi bi-disc display-6 text-muted d-block mb-2 opacity-50"></i>
          Nenhum material anexado ${filtroVisualizacaoMateriais === 'current' ? 'para o destino selecionado' : 'a este culto'}.
        </div>
      `;
      return;
    }

    let html = '';
    exibiveis.forEach(r => {
      const isGeral = (parseInt(r.id_area) === 0);
      const tagDestino = isGeral ?
        `<span class="badge badge-global-origin rounded-pill small"><i class="bi bi-globe2 me-1"></i>Geral (Todas)</span>` :
        `<span class="badge badge-dep-origin rounded-pill small"><i class="bi bi-pin-map-fill me-1"></i>${escapeHtml(r.nome_area || 'Sub-área')}</span>`;

      const coverHtml = getMediaCoverHtml(r);

      html += `
        <div class="music-track-card d-flex align-items-center justify-content-between scale-mat-item" data-id="${r.id}">
          <div class="d-flex align-items-center gap-3 min-w-0">
            ${coverHtml}
            <div class="min-w-0">
              <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                <h6 class="fw-bold mb-0 text-dark-emphasis text-truncate" style="font-size: 0.90rem;">${escapeHtml(r.title)}</h6>
                ${tagDestino}
                ${r.provider === 'youtube' ? '<span class="badge bg-danger text-white rounded-pill" style="font-size: 0.65rem;">YouTube</span>' : ''}
                ${r.provider === 'spotify' ? '<span class="badge bg-success text-white rounded-pill" style="font-size: 0.65rem;">Spotify</span>' : ''}
                ${r.type_name ? `<span class="badge bg-body-secondary text-secondary rounded-pill" style="font-size: 0.65rem;">${escapeHtml(r.type_name)}</span>` : ''}
              </div>
              <div class="text-secondary small text-truncate" style="font-size: 0.78rem;">
                ${escapeHtml(r.description || r.url || 'Material de apoio para a equipe')}
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center gap-1.5 flex-shrink-0 ms-2">
            ${r.url ? `
              <a href="${r.url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light border-0 rounded-circle p-2 d-flex align-items-center justify-content-center shadow-xs text-primary" title="Abrir Link Externo" style="width: 34px; height: 34px;">
                <i class="bi bi-box-arrow-up-right"></i>
              </a>
            ` : ''}
            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2 d-flex align-items-center justify-content-center shadow-xs" onclick="removerRecursoEscalaAjax(${r.id}, ${r.id_area})" title="Desanexar deste culto" style="width: 34px; height: 34px;">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
  }

  function abrirPickerMateriaisEscala() {
    modoPickerAtual = 'recursos';
    const selectArea = document.getElementById('modal_mat_id_area');
    const selectedText = selectArea ? selectArea.options[selectArea.selectedIndex]?.text : 'Geral';
    const labelDestino = document.getElementById('picker_destino_label');
    if (labelDestino) labelDestino.textContent = selectedText;

    if (modalMateriaisCultoInstance) {
      modalMateriaisCultoInstance.hide();
    }

    if (!modalPickerMateriaisEscalaInstance) {
      modalPickerMateriaisEscalaInstance = new bootstrap.Modal(document.getElementById('modalPickerMateriaisEscala'));
    }

    document.getElementById('picker_modal_title').innerHTML = 'Selecionar Materiais Avulsos';
    document.getElementById('picker_quick_filter_pills').style.display = 'flex';
    document.getElementById('filtro_picker_escala_busca').value = '';
    filtroTipoPickerAtual = '';
    aplicarFiltroTipoPicker('');

    const container = document.getElementById('picker_escala_itens_container');
    container.innerHTML = '<div class="text-center py-4 small text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Carregando materiais da biblioteca (Departamento + Globais)...</div>';

    modalPickerMateriaisEscalaInstance.show();

    fetch(`<?= base_url('resource/apiList') ?>?department_id=${contextoMateriaisAtual.id_departamento}`)
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success' && res.data) {
          cacheItensPickerDisponiveis = res.data;
          renderizarItensPicker(res.data);
        } else {
          cacheItensPickerDisponiveis = [];
          container.innerHTML = '<div class="text-center text-muted py-4 small">Nenhum material encontrado na biblioteca.</div>';
        }
      });
  }

  function abrirPickerColecoesEscala() {
    modoPickerAtual = 'colecoes';
    const selectArea = document.getElementById('modal_mat_id_area');
    const selectedText = selectArea ? selectArea.options[selectArea.selectedIndex]?.text : 'Geral';
    const labelDestino = document.getElementById('picker_destino_label');
    if (labelDestino) labelDestino.textContent = selectedText;

    if (modalMateriaisCultoInstance) {
      modalMateriaisCultoInstance.hide();
    }

    if (!modalPickerMateriaisEscalaInstance) {
      modalPickerMateriaisEscalaInstance = new bootstrap.Modal(document.getElementById('modalPickerMateriaisEscala'));
    }

    document.getElementById('picker_modal_title').innerHTML = 'Selecionar Coleção / Repertório';
    document.getElementById('picker_quick_filter_pills').style.display = 'none';
    document.getElementById('filtro_picker_escala_busca').value = '';

    const container = document.getElementById('picker_escala_itens_container');
    container.innerHTML = '<div class="text-center py-4 small text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Carregando coleções e repertórios...</div>';

    modalPickerMateriaisEscalaInstance.show();

    fetch(`<?= base_url('resource/apiCollections') ?>?department_id=${contextoMateriaisAtual.id_departamento}`)
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success' && res.data) {
          cacheItensPickerDisponiveis = res.data;
          renderizarItensPicker(res.data);
        } else {
          cacheItensPickerDisponiveis = [];
          container.innerHTML = '<div class="text-center text-muted py-4 small">Nenhuma coleção cadastrada.</div>';
        }
      });
  }

  function aplicarFiltroTipoPicker(typeCode) {
    filtroTipoPickerAtual = typeCode;
    document.querySelectorAll('#picker_quick_filter_pills .pill-filter-btn').forEach(btn => {
      btn.classList.toggle('active', btn.getAttribute('data-type') === typeCode);
    });
    filtrarPickerEscala();
  }

  function filtrarPickerEscala() {
    const termo = (document.getElementById('filtro_picker_escala_busca').value || '').trim().toLowerCase();

    let filtrados = cacheItensPickerDisponiveis.filter(item => {
      const matchBusca = (item.title || '').toLowerCase().includes(termo) ||
        (item.description || '').toLowerCase().includes(termo);

      if (!matchBusca) return false;

      if (modoPickerAtual === 'recursos' && filtroTipoPickerAtual !== '') {
        const itemCode = (item.type_code || '').toLowerCase();
        const itemProv = (item.provider || '').toLowerCase();
        if (filtroTipoPickerAtual === 'audio' && itemCode !== 'audio' && itemProv !== 'spotify') return false;
        if (filtroTipoPickerAtual === 'video' && itemCode !== 'video' && itemProv !== 'youtube') return false;
        if (filtroTipoPickerAtual === 'pdf' && itemCode !== 'pdf') return false;
        if (filtroTipoPickerAtual === 'text' && itemCode !== 'text') return false;
      }

      return true;
    });

    renderizarItensPicker(filtrados);
  }

  function renderizarItensPicker(itens) {
    const container = document.getElementById('picker_escala_itens_container');
    if (!itens || itens.length === 0) {
      container.innerHTML = '<div class="text-center text-muted py-4 small">Nenhum item encontrado com os filtros selecionados.</div>';
      return;
    }

    let html = '';
    if (modoPickerAtual === 'recursos') {
      itens.forEach(r => {
        const isGlobal = (r.department_id === null || parseInt(r.department_id) === 0);
        const originBadge = isGlobal ?
          `<span class="badge badge-global-origin rounded-pill small"><i class="bi bi-globe2 me-1"></i>Global</span>` :
          `<span class="badge badge-dep-origin rounded-pill small"><i class="bi bi-building me-1"></i>${escapeHtml(r.department_name || 'Departamento')}</span>`;

        const coverHtml = getMediaCoverHtml(r);

        html += `
          <div class="music-track-card d-flex align-items-center justify-content-between picker-item-row" data-id="${r.id}">
            <div class="d-flex align-items-center gap-3 min-w-0">
              ${coverHtml}
              <div class="min-w-0">
                <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                  <h6 class="fw-bold mb-0 text-dark-emphasis text-truncate" style="font-size: 0.90rem;">${escapeHtml(r.title)}</h6>
                  ${originBadge}
                  ${r.provider === 'youtube' ? '<span class="badge bg-danger text-white rounded-pill" style="font-size: 0.65rem;">YouTube</span>' : ''}
                  ${r.provider === 'spotify' ? '<span class="badge bg-success text-white rounded-pill" style="font-size: 0.65rem;">Spotify</span>' : ''}
                  ${r.type_name ? `<span class="badge bg-body-secondary text-secondary rounded-pill" style="font-size: 0.65rem;">${escapeHtml(r.type_name)}</span>` : ''}
                </div>
                <div class="text-secondary small text-truncate" style="font-size: 0.78rem;">
                  ${escapeHtml(r.description || r.url || 'Disponível na Biblioteca')}
                </div>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary btn-attach-music flex-shrink-0 ms-2" id="btn_anexar_rec_${r.id}" onclick="anexarRecursoNaEscalaAjax(${r.id})">
              <i class="bi bi-plus-lg me-1"></i> Anexar
            </button>
          </div>
        `;
      });
    } else {
      itens.forEach(c => {
        const isGlobal = (c.department_id === null || parseInt(c.department_id) === 0);
        const originBadge = isGlobal ?
          `<span class="badge badge-global-origin rounded-pill small"><i class="bi bi-globe2 me-1"></i>Global</span>` :
          `<span class="badge badge-dep-origin rounded-pill small"><i class="bi bi-building me-1"></i>${escapeHtml(c.department_name || 'Departamento')}</span>`;

        const coverHtml = getMediaCoverHtml(c, true);

        html += `
          <div class="music-track-card d-flex align-items-center justify-content-between picker-item-row" data-id="${c.id}">
            <div class="d-flex align-items-center gap-3 min-w-0">
              ${coverHtml}
              <div class="min-w-0">
                <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                  <h6 class="fw-bold mb-0 text-dark-emphasis text-truncate" style="font-size: 0.92rem;">${escapeHtml(c.title)}</h6>
                  ${originBadge}
                  <span class="badge bg-primary text-white rounded-pill" style="font-size: 0.65rem;">${c.total_resources || 0} materiais inclusos</span>
                </div>
                <div class="text-secondary small text-truncate" style="font-size: 0.78rem;">
                  ${escapeHtml(c.description || 'Repertório completo pronto para ser escalado')}
                </div>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary btn-attach-music flex-shrink-0 ms-2" id="btn_anexar_col_${c.id}" onclick="anexarColecaoNaEscalaAjax(${c.id})">
              <i class="bi bi-box-arrow-in-down me-1"></i> Inserir Repertório
            </button>
          </div>
        `;
      });
    }

    container.innerHTML = html;
  }

  function atualizarBulletMateriaisGrade(data_culto, id_culto_padrao, total) {
    const btn = document.getElementById(`btn_mat_culto_${data_culto}_${id_culto_padrao}`);
    const badge = document.getElementById(`badge_mat_culto_${data_culto}_${id_culto_padrao}`);
    const count = parseInt(total) || 0;

    if (btn) {
      if (count > 0) {
        btn.className = 'btn btn-info text-white btn-action position-relative';
        btn.setAttribute('title', `Biblioteca de Materiais (${count} anexado(s))`);
      } else {
        btn.className = 'btn btn-outline-info btn-action position-relative';
        btn.setAttribute('title', 'Biblioteca de Materiais (0 anexado(s))');
      }
    }

    if (badge) {
      badge.textContent = count;
      badge.setAttribute('title', `${count} material(is) anexado(s)`);
      if (count > 0) {
        badge.classList.remove('d-none');
      } else {
        badge.classList.add('d-none');
      }
    }
  }

  function anexarRecursoNaEscalaAjax(resource_id) {
    const id_area = parseInt(document.getElementById('modal_mat_id_area')?.value) || 0;
    const btn = document.getElementById(`btn_anexar_rec_${resource_id}`);
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
    }

    const formData = new FormData();
    formData.append('data_culto', contextoMateriaisAtual.data_culto);
    formData.append('id_culto_padrao', contextoMateriaisAtual.id_culto_padrao);
    formData.append('id_departamento', contextoMateriaisAtual.id_departamento);
    formData.append('id_area', id_area);
    formData.append('resource_ids', resource_id);

    fetch('<?= base_url('resource/anexarNaEscala') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success') {
          if (btn) {
            btn.className = 'btn btn-sm btn-success btn-attach-music flex-shrink-0 ms-2';
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Anexado';
          }
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Material Anexado', res.message);
          } else if (typeof usShowToast === 'function') {
            usShowToast('success', 'Material Anexado', res.message);
          }
          if (res.data) {
            cacheMateriaisEscalaCulto = res.data;
            renderizarListaMateriaisEscala(res.data);
            atualizarBulletMateriaisGrade(contextoMateriaisAtual.data_culto, contextoMateriaisAtual.id_culto_padrao, res.data.length);
          }
        } else {
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Anexar';
          }
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Erro', res.message || 'Erro ao anexar.');
          }
        }
      })
      .catch(() => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Anexar';
        }
      });
  }

  function anexarColecaoNaEscalaAjax(collection_id) {
    const id_area = parseInt(document.getElementById('modal_mat_id_area')?.value) || 0;
    const btn = document.getElementById(`btn_anexar_col_${collection_id}`);
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
    }

    const formData = new FormData();
    formData.append('data_culto', contextoMateriaisAtual.data_culto);
    formData.append('id_culto_padrao', contextoMateriaisAtual.id_culto_padrao);
    formData.append('id_departamento', contextoMateriaisAtual.id_departamento);
    formData.append('id_area', id_area);
    formData.append('collection_id', collection_id);

    fetch('<?= base_url('resource/anexarNaEscala') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success') {
          if (btn) {
            btn.className = 'btn btn-sm btn-success btn-attach-music flex-shrink-0 ms-2';
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Inserido';
          }
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Coleção Inserida', res.message);
          } else if (typeof usShowToast === 'function') {
            usShowToast('success', 'Coleção Inserida', res.message);
          }
          if (res.data) {
            cacheMateriaisEscalaCulto = res.data;
            renderizarListaMateriaisEscala(res.data);
            atualizarBulletMateriaisGrade(contextoMateriaisAtual.data_culto, contextoMateriaisAtual.id_culto_padrao, res.data.length);
          }
        } else {
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-box-arrow-in-down me-1"></i> Inserir Repertório';
          }
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Erro', res.message || 'Erro ao anexar coleção.');
          }
        }
      })
      .catch(() => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-box-arrow-in-down me-1"></i> Inserir Repertório';
        }
      });
  }

  function removerRecursoEscalaAjax(resource_id, id_area = null) {
    const formData = new FormData();
    formData.append('data_culto', contextoMateriaisAtual.data_culto);
    formData.append('id_culto_padrao', contextoMateriaisAtual.id_culto_padrao);
    formData.append('id_departamento', contextoMateriaisAtual.id_departamento);
    formData.append('resource_id', resource_id);
    if (id_area !== null) {
      formData.append('id_area', id_area);
    }

    fetch('<?= base_url('resource/removerDaEscala') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success') {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Material Removido', res.message);
          } else if (typeof usShowToast === 'function') {
            usShowToast('success', 'Material Removido', res.message);
          }
          if (res.data) {
            cacheMateriaisEscalaCulto = res.data;
            renderizarListaMateriaisEscala(res.data);
            atualizarBulletMateriaisGrade(contextoMateriaisAtual.data_culto, contextoMateriaisAtual.id_culto_padrao, res.data.length);
          }
        }
      });
  }

  // Inicializa tooltips do Bootstrap
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    }
  });
</script>