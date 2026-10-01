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

  [data-bs-theme="dark"] .bg-purple-subtle {
    background-color: rgba(168, 85, 247, 0.2) !important;
    color: #c084fc !important;
    border-color: rgba(168, 85, 247, 0.3) !important;
  }

  [data-bs-theme="dark"] .text-purple {
    color: #c084fc !important;
  }

  [data-bs-theme="dark"] .vol-text-muted {
    color: #94a3b8 !important;
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
    <!-- 2. NAVEGAÇÃO DE MESES E ANO (ESTILO TABS) -->
    <!-- ========================================== -->
    <div class="card bg-body-tertiary border-0 rounded-4 shadow-sm mb-4">
      <div class="card-body p-2 overflow-auto">
        <div class="d-flex align-items-center justify-content-between flex-nowrap gap-2">

          <!-- Abas dos Meses -->
          <div class="d-flex align-items-center gap-1 flex-nowrap">
            <?php for ($m = 1; $m <= 12; $m++) {
              $isActive = ($m == $mes);
            ?>
              <a href="<?= base_url("escala/grade/{$id_departamento}/{$ano}/{$m}") ?>" class="btn btn-sm rounded-pill px-3 fw-bold text-nowrap <?= $isActive ? 'btn-primary shadow-sm' : 'btn-outline-secondary border-0' ?>">
                <?= $mesesNomes[$m] ?>-<?= $ano ?>
              </a>
            <?php } ?>
          </div>

          <!-- Seletor de Ano -->
          <div class="d-flex align-items-center gap-2 ms-3 flex-nowrap">
            <a href="<?= base_url("escala/grade/{$id_departamento}/" . ($ano - 1) . "/{$mes}") ?>" class="btn btn-outline-secondary btn-sm rounded-circle" title="Ano Anterior">
              <i class="bi bi-chevron-left"></i>
            </a>
            <span class="fw-bold fs-6 text-primary"><?= $ano ?></span>
            <a href="<?= base_url("escala/grade/{$id_departamento}/" . ($ano + 1) . "/{$mes}") ?>" class="btn btn-outline-secondary btn-sm rounded-circle" title="Próximo Ano">
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

          <!-- Filtros de Linhas e Seletor de Culto -->
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="small fw-bold text-secondary me-1"><i class="bi bi-funnel-fill me-1"></i> Filtrar Grade:</span>

            <!-- Seletor de Culto Específico -->
            <div class="me-1">
              <select id="selectFiltroCulto" class="form-select form-select-sm rounded-pill px-3 fw-semibold bg-body border-primary shadow-sm" style="min-width: 220px;" title="Filtrar ocorrências por tipo de culto">
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

                if ($temCulto) {
                  foreach ($dia['cultos_agendados'] as $c) {
                    $corCulto = !empty($c->cor_evento) ? $c->cor_evento : '#2563eb';
                    $temEscalaNoCulto = !empty($c->escalasPorArea);
                    $statusFiltro = $temEscalaNoCulto ? 'com_escala' : 'sem_escala';
                    $dataCultosAttr = trim((string)$c->titulo_culto);
              ?>
                    <tr class="grade-row <?= $bgClass ?>" data-status-escala="<?= $statusFiltro ?>" data-is-weekend="<?= $isFimSemana ? '1' : '0' ?>" data-cultos="<?= esc($dataCultosAttr) ?>">

                      <!-- Dia -->
                      <td class="text-center fw-bold font-monospace fs-6">
                        <?= $dia['data_formatada'] ?>
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
                          ?>
                            <div class="slot-area-box <?= $isPreenchido ? 'filled' : '' ?>">
                              <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small text-primary-emphasis" style="font-size: 0.75rem;">
                                  <i class="bi bi-grid me-1"></i><?= esc($area->nome_area) ?>
                                </strong>
                                <?php if ($isPreenchido && !empty($sys_action->create)) { ?>
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
                                          <span class="badge btn-presence-toggle <?= $isPresente ? 'bg-success' : 'bg-danger' ?>" onclick="togglePresenca(<?= $esc->id_escala_voluntario ?>, <?= $isPresente ? 0 : 1 ?>)" title="Clique para alternar presença">
                                            <?= $isPresente ? 'Pres.' : 'Aus.' ?>
                                          </span>
                                        <?php } else { ?>
                                          <!-- Voluntário Pendente: Ampulheta com Efeito Pulsante -->
                                          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-1.5 py-0.5 d-inline-flex align-items-center justify-content-center pulse-pending" data-bs-toggle="tooltip" title="Pendente de confirmação do voluntário">
                                            <i class="bi bi-hourglass-split text-warning" style="font-size: 0.78rem;"></i>
                                          </span>
                                        <?php } ?>

                                        <?php if (!empty($sys_action->delete)) { ?>
                                          <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1" onclick="removerEscalaAjax(<?= $esc->id_escala_voluntario ?>)" title="Remover da Escala">
                                            <i class="bi bi-x-circle-fill"></i>
                                          </button>
                                        <?php } ?>
                                      </div>
                                    </div>
                                  <?php } ?>
                                </div>

                                <?php if (!empty($sys_action->create)) { ?>
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
                                <?php if (!empty($sys_action->create)) { ?>
                                  <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 small w-100 text-nowrap" onclick="abrirModalEscalar('<?= $c->data_culto ?>', <?= $c->id_culto_padrao ?>, '<?= esc($c->titulo_culto) ?> - <?= $dia['data_formatada'] ?>', <?= $area->id_area ?>)">
                                    <i class="bi bi-plus me-1"></i> Escalar
                                  </button>
                                <?php } else { ?>
                                  <small class="text-muted fst-italic">Vago</small>
                                <?php } ?>
                              <?php } ?>

                              <!-- Registro Discreto de Recusas para este slot (Joia para baixo sem poluir o calendário) -->
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
                                      <?php if (!empty($sys_action->delete)) { ?>
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
                      <td class="text-end pe-4">
                        <?php if (!empty($sys_action->create)) { ?>
                          <button type="button" class="btn btn-outline-primary btn-action" title="Escalar Voluntário no Culto" onclick="abrirModalEscalar('<?= $c->data_culto ?>', <?= $c->id_culto_padrao ?>, '<?= esc($c->titulo_culto) ?> - <?= $dia['data_formatada'] ?>')">
                            <i class="bi bi-person-plus-fill"></i>
                          </button>
                        <?php } ?>
                      </td>

                    </tr>
                  <?php
                  } // Fim foreach cultos
                } else { // Sem culto no dia
                  ?>
                  <tr class="grade-row <?= $bgClass ?>" data-status-escala="sem_culto" data-is-weekend="<?= $isFimSemana ? '1' : '0' ?>" data-cultos="">

                    <!-- Dia -->
                    <td class="text-center fw-bold font-monospace fs-6">
                      <?= $dia['data_formatada'] ?>
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
                    <td class="text-end pe-4">
                      <span class="text-muted small">-</span>
                    </td>

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
        <button type="button" id="btnConfirmarRemoverEscalaSubmit" class="btn btn-danger rounded-pill px-4 fw-bold">
          <i class="bi bi-trash3-fill me-1"></i> Confirmar Remoção
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
        'JUNIOR':   'bg-info-subtle text-info border border-info-subtle',
        'PLENO':    'bg-success-subtle text-success border border-success-subtle',
        'SENIOR':   'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold'
      };
      const badgeNivelClass = badgeNivelMap[nivel] || 'bg-light text-dark';

      // Disponibilidade tag (Topo)
      let dispTagHtml = '';
      if (v.tipo_disponibilidade === 'TOTAL') {
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
        limiteTagHtml = `<span class="badge-stacked-limit">${v.total_escalas_mes}/${v.max_escalas_mes} Monthly</span>`;
      } else {
        limiteTagHtml = `<span class="badge-stacked-limit">${v.total_escalas_mes} Monthly</span>`;
      }

      const itemClass = isSelected ?
        'vol-card-selected' :
        (v.atingiu_limite ? 'border-danger-subtle bg-danger-subtle opacity-75' : '');

      const jsonVolStr = JSON.stringify(v).replace(/"/g, '&quot;');

      html += `
        <div class="vol-card-item d-flex align-items-center justify-content-between ${itemClass}" 
             style="cursor: ${v.atingiu_limite && !isSelected ? 'not-allowed' : 'pointer'};"
             onclick="clickVoluntarioItem(${v.id_voluntario}, ${v.atingiu_limite ? 'true' : 'false'}, ${jsonVolStr})">
          
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

  function clickVoluntarioItem(id_voluntario, atingiuLimite, voluntarioObj) {
    if (voluntariosSelecionadosModal[id_voluntario]) {
      delete voluntariosSelecionadosModal[id_voluntario];
    } else {
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

  // Função unificada de filtros da Grade (Status + Tipo de Culto)
  function aplicarFiltrosGrade() {
    const activeBtn = document.querySelector('.btnFilterRow.active');
    const filter = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
    const cultoSelecionado = (document.getElementById('selectFiltroCulto')?.value || '').toLowerCase().trim();

    document.querySelectorAll('#tblGradeEscala tbody tr.grade-row').forEach(row => {
      const status = row.getAttribute('data-status-escala');
      const isWeekend = row.getAttribute('data-is-weekend') === '1';
      const cultosNaLinha = (row.getAttribute('data-cultos') || '').toLowerCase();

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

  function escapeHtml(text) {
    if (!text) return '';
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
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