<?php
  /**
   * Helper: Retorna a cor de texto ideal (#ffffff ou #0f172a) com base na luminância da cor de fundo (WCAG YIQ)
   */
  if (!function_exists('getContrasteTexto')) {
      function getContrasteTexto($hexColor) {
          if (empty($hexColor)) return '#ffffff';
          $hex = ltrim((string)$hexColor, '#');
          if (strlen($hex) === 3) {
              $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
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

  $nomeDepAtual = $departamentoAtual ? ($departamentoAtual->nome ?? $departamentoAtual->nome_departamento ?? 'Departamento') : 'Consolidado (Todos)';
  $corDepAtual  = !empty($departamentoAtual->cor_identificacao) ? $departamentoAtual->cor_identificacao : '#2563eb';
  $txtDepAtual  = getContrasteTexto($corDepAtual);
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
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

  <title><?= esc($title ?? 'Métricas & Ranking - Portal do Voluntário') ?></title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --primary-color: #2563eb;
      --primary-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
      --gold-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
      --silver-gradient: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
      --bronze-gradient: linear-gradient(135deg, #d97706 0%, #92400e 100%);
      --dark-card: #0f172a;
    }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: #f1f5f9;
      color: #1e293b;
      min-height: 100dvh;
    }
    [data-bs-theme="dark"] body {
      background-color: #0b1329;
      color: #e2e8f0;
    }
    .hero-ranking-card {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1e3a8a 100%);
      color: #ffffff;
      border-radius: 1.5rem;
      position: relative;
      overflow: hidden;
    }
    .hero-ranking-card::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 320px;
      height: 320px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }
    .kpi-card {
      border-radius: 1.25rem;
      border: 1px solid rgba(226, 232, 240, 0.8);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      background: #ffffff;
    }
    [data-bs-theme="dark"] .kpi-card {
      background-color: #1e293b;
      border-color: #334155;
      color: #f1f5f9;
    }
    .kpi-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
    }
    .podium-badge {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.9rem;
    }
    .podium-1 { background: var(--gold-gradient); color: #fff; box-shadow: 0 0 15px rgba(245, 158, 11, 0.5); }
    .podium-2 { background: var(--silver-gradient); color: #fff; }
    .podium-3 { background: var(--bronze-gradient); color: #fff; }
    .leaderboard-row-me {
      background: rgba(37, 99, 235, 0.08) !important;
      border-left: 4px solid #2563eb !important;
    }
    [data-bs-theme="dark"] .leaderboard-row-me {
      background: rgba(59, 130, 246, 0.18) !important;
      border-left: 4px solid #60a5fa !important;
    }
    .badge-level-aprendiz { background-color: #64748b !important; color: #ffffff !important; font-weight: 700; }
    .badge-level-junior   { background-color: #0284c7 !important; color: #ffffff !important; font-weight: 700; }
    .badge-level-pleno    { background-color: #7c3aed !important; color: #ffffff !important; font-weight: 700; }
    .badge-level-senior   { background-color: #d97706 !important; color: #ffffff !important; font-weight: 700; }

    [data-bs-theme="dark"] .card {
      background-color: #1e293b;
      border-color: #334155;
      color: #f1f5f9;
    }
    [data-bs-theme="dark"] .card-header {
      background-color: #0f172a !important;
      border-color: #334155 !important;
    }
    [data-bs-theme="dark"] .table {
      --bs-table-bg: transparent;
      --bs-table-color: #e2e8f0;
      --bs-table-hover-bg: rgba(255, 255, 255, 0.05);
    }
    [data-bs-theme="dark"] .table-light {
      --bs-table-bg: #0f172a;
      --bs-table-color: #94a3b8;
    }
    [data-bs-theme="dark"] .btn-light {
      background-color: #1e293b;
      border-color: #334155;
      color: #e2e8f0;
    }
    [data-bs-theme="dark"] .btn-light:hover {
      background-color: #334155;
      color: #fff;
    }
  </style>
</head>
<body>

  <!-- Navegação do Portal -->
  <?= view('portal_voluntario/_nav', ['voluntario' => $voluntario, 'menuAtivo' => 'metricas']) ?>

  <main class="container max-w-portal py-3 py-md-4">

    <!-- Seletor de Período -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div>
        <h4 class="fw-black mb-0 text-body d-flex align-items-center gap-2">
          <i class="bi bi-trophy-fill text-warning"></i> Métricas & Competição de Fidelidade
        </h4>
        <small class="text-muted">Acompanhe seu desempenho e sua posição no ranking por departamento</small>
      </div>

      <?php
        $depParamUrl = ($id_departamento_selecionado !== null) ? '&id_departamento=' . $id_departamento_selecionado : '&id_departamento=todos';
      ?>
      <div class="btn-group shadow-xs rounded-pill overflow-hidden bg-white p-1 border" role="group">
        <a href="<?= base_url('portal/metricas?periodo=mes_atual' . $depParamUrl) ?>" class="btn btn-sm <?= ($periodo === 'mes_atual') ? 'btn-primary text-white fw-bold' : 'btn-light text-muted' ?> rounded-pill px-3">Mês Atual</a>
        <a href="<?= base_url('portal/metricas?periodo=ano_atual' . $depParamUrl) ?>" class="btn btn-sm <?= ($periodo === 'ano_atual') ? 'btn-primary text-white fw-bold' : 'btn-light text-muted' ?> rounded-pill px-3">Ano Atual</a>
        <a href="<?= base_url('portal/metricas?periodo=tudo' . $depParamUrl) ?>" class="btn btn-sm <?= ($periodo === 'tudo') ? 'btn-primary text-white fw-bold' : 'btn-light text-muted' ?> rounded-pill px-3">Geral</a>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- SELETOR DE DEPARTAMENTO: RANQUEAMENTO JUSTO E SEPARADO   -->
    <!-- ======================================================== -->
    <?php if (!empty($meusDepartamentos) && count($meusDepartamentos) > 1) { ?>
      <div class="card border-0 shadow-xs rounded-4 mb-3 bg-body">
        <div class="card-body p-2 p-md-3">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-diagram-3-fill text-primary"></i>
              <span class="fw-bold text-body small">Selecione o Departamento para ver o Ranking:</span>
            </div>
            <small class="text-muted" style="font-size: 0.75rem;">
              <i class="bi bi-shield-check text-success me-1"></i> Ranqueamento segregado por departamento para pontuação justa
            </small>
          </div>
          <div class="d-flex flex-wrap gap-2">
            <?php foreach ($meusDepartamentos as $dep) { 
              $isDepActive = ($id_departamento_selecionado !== null && (int)$id_departamento_selecionado === (int)$dep->id_departamento);
              $depCor = !empty($dep->cor_identificacao) ? $dep->cor_identificacao : '#2563eb';
              $depTxt = getContrasteTexto($depCor);
              $depNome = $dep->nome ?? $dep->nome_departamento ?? 'Departamento';
            ?>
              <a href="<?= base_url('portal/metricas?id_departamento=' . $dep->id_departamento . '&periodo=' . $periodo) ?>" 
                 class="btn btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 fw-semibold shadow-xs <?= $isDepActive ? 'border' : 'btn-light border text-body' ?>"
                 style="<?= $isDepActive ? 'background-color: ' . esc($depCor) . ' !important; color: ' . esc($depTxt) . ' !important; border-color: ' . esc($depCor) . ' !important;' : '' ?>">
                <span class="rounded-circle shadow-xs" style="width: 10px; height: 10px; background-color: <?= esc($depCor) ?>; display: inline-block; border: 1px solid <?= $isDepActive ? esc($depTxt) : 'rgba(0,0,0,0.15)' ?>;"></span>
                <span><?= esc($depNome) ?></span>
                <?php if ($isDepActive) { ?>
                  <i class="bi bi-check-circle-fill ms-1" style="color: <?= esc($depTxt) ?>;"></i>
                <?php } ?>
              </a>
            <?php } ?>

            <!-- Visão Consolidada Geral (Opcional) -->
            <a href="<?= base_url('portal/metricas?id_departamento=todos&periodo=' . $periodo) ?>" 
               class="btn btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 fw-semibold shadow-xs <?= ($id_departamento_selecionado === null) ? 'btn-primary text-white fw-bold' : 'btn-light border text-body' ?>">
              <i class="bi bi-grid-fill"></i>
              <span>Consolidado (Todos)</span>
            </a>
          </div>
        </div>
      </div>
    <?php } elseif (!empty($departamentoAtual)) { ?>
      <!-- Voluntário em 1 único departamento -->
      <div class="d-flex align-items-center gap-2 mb-3">
        <span class="badge rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-2 fw-bold" 
              style="background-color: <?= esc($corDepAtual) ?> !important; color: <?= esc($txtDepAtual) ?> !important; font-size: 0.82rem; border: 1px solid rgba(0,0,0,0.15);">
          <i class="bi bi-bookmark-check-fill"></i> Departamento: <?= esc($nomeDepAtual) ?>
        </span>
        <span class="text-muted small d-none d-sm-inline">&bull; Métricas e ranking exclusivos deste departamento</span>
      </div>
    <?php } ?>

    <!-- ========================================== -->
    <!-- HERO CARD PREMIUM: MEU SCORE & POSIÇÃO     -->
    <!-- ========================================== -->
    <?php
      $meu = $rankingData->meuRank;
      $isClassificado = !empty($meu) && !empty($meu->classificado) && !empty($meu->posicao);
      $posicaoMeu = $isClassificado ? '#' . $meu->posicao : 'S/C';
      $pontosMeu  = $meu ? $meu->pontos : 0;
      $totalClassificados = $rankingData->totalClassificados ?? count($rankingData->leaderboard);
      $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($voluntario->nome) . '&background=2563eb&color=fff&size=120&bold=true';
      $fotoSrc = !empty($voluntario->foto_url) ? $voluntario->foto_url : $defaultAvatar;
    ?>
    <div class="hero-ranking-card shadow-lg p-4 mb-4">
      <div class="row align-items-center g-4">
        
        <!-- Identificação e Foto -->
        <div class="col-md-7 d-flex align-items-center gap-3 gap-md-4">
          <div class="position-relative">
            <img src="<?= esc($fotoSrc) ?>" class="rounded-circle border border-3 border-warning shadow" style="width: 84px; height: 84px; object-fit: cover;" alt="avatar" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
            <?php if ($isClassificado) { ?>
              <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-warning text-dark fw-black px-2 shadow">
                <?= $posicaoMeu ?>
              </span>
            <?php } else { ?>
              <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-secondary text-white fw-bold px-2 shadow" style="font-size: 0.65rem;" title="Sem escalas no período">
                S/C
              </span>
            <?php } ?>
          </div>

          <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
              <h4 class="fw-black mb-0 text-white"><?= esc($voluntario->nome) ?></h4>
              <?php if (!empty($voluntario->nickname)) { ?>
                <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-2 py-0">"<?= esc($voluntario->nickname) ?>"</span>
              <?php } ?>
            </div>
            
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
              <span class="badge badge-level-<?= strtolower($voluntario->nivel_conhecimento ?? 'junior') ?> rounded-pill px-2 py-1 small">
                <i class="bi bi-mortarboard-fill me-1"></i><?= esc($voluntario->nivel_conhecimento ?? 'JUNIOR') ?>
              </span>

              <?php if ($departamentoAtual) { ?>
                <span class="badge rounded-pill px-2.5 py-1 small fw-bold shadow-xs d-inline-flex align-items-center gap-1" 
                      style="background-color: <?= esc($corDepAtual) ?> !important; color: <?= esc($txtDepAtual) ?> !important; border: 1px solid rgba(255,255,255,0.25);">
                  <i class="bi bi-diagram-3-fill"></i> <?= esc($nomeDepAtual) ?>
                </span>
              <?php } else { ?>
                <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1 small fw-bold shadow-xs d-inline-flex align-items-center gap-1">
                  <i class="bi bi-grid-fill"></i> Consolidado Geral
                </span>
              <?php } ?>

              <span class="text-white-50 small">&bull;</span>
              <?php if ($isClassificado && !empty($rankingData->percentil)) { ?>
                <span class="text-white-50 small">Top <strong><?= $rankingData->percentil ?>%</strong> do departamento</span>
              <?php } else { ?>
                <span class="text-white-50 small">Sem escalas neste departamento no período</span>
              <?php } ?>
            </div>

            <div class="text-white-50 small">
              <i class="bi bi-star-fill text-warning me-1"></i> <?= $pontosMeu ?> Pontos de Fidelidade <?= $isClassificado ? 'acumulados' : 'no período' ?>
            </div>
          </div>
        </div>

        <!-- Posição no Pódio & Assiduidade -->
        <div class="col-md-5 text-md-end">
          <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-10 d-inline-block text-center text-md-end w-100">
            <div class="d-flex justify-content-around justify-content-md-end gap-4 align-items-center">
              <div>
                <span class="d-block text-white-50 small text-uppercase fw-bold" style="font-size: 0.72rem;">Sua Posição <?= $departamentoAtual ? 'no Depto' : '' ?></span>
                <?php if ($isClassificado) { ?>
                  <span class="fs-2 fw-black text-warning"><?= $posicaoMeu ?> <span class="fs-6 text-white-50">/ <?= $totalClassificados ?></span></span>
                <?php } else { ?>
                  <span class="fs-3 fw-black text-white-50">S/C <span class="fs-6 text-white-50">(Sem escalas)</span></span>
                <?php } ?>
              </div>
              <div class="vr bg-white opacity-25"></div>
              <div>
                <span class="d-block text-white-50 small text-uppercase fw-bold" style="font-size: 0.72rem;">Assiduidade</span>
                <span class="fs-2 fw-black text-<?= ($metricas->taxaAssiduidade >= 80 ? 'success' : ($metricas->taxaAssiduidade >= 50 ? 'warning' : 'danger')) ?>"><?= $metricas->taxaAssiduidade ?>%</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ========================================== -->
    <!-- CARDS DE METRICAS COMPARATIVAS (4 CARDS)   -->
    <!-- ========================================== -->
    <div class="row g-3 mb-4">
      
      <!-- 1. Cultos Aceitos / Confirmados -->
      <div class="col-6 col-lg-3">
        <div class="kpi-card p-3 p-md-4 h-100 shadow-xs">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Cultos Aceitos</span>
            <span class="badge bg-success-subtle text-success-emphasis rounded-circle p-2">
              <i class="bi bi-check-lg fs-6"></i>
            </span>
          </div>
          <h3 class="fw-black text-success mb-1"><?= $metricas->totalConfirmados ?></h3>
          <small class="text-muted" style="font-size: 0.75rem;">
            <?= $metricas->totalPresencas ?> presenças cumpridas
          </small>
        </div>
      </div>

      <!-- 2. Cancelamentos / Recusas (Monitorado) -->
      <div class="col-6 col-lg-3">
        <div class="kpi-card p-3 p-md-4 h-100 shadow-xs border-danger-subtle <?= ($metricas->totalCancelamentos > 0) ? 'bg-danger-subtle bg-opacity-25' : '' ?>">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="text-danger small fw-bold text-uppercase" style="font-size: 0.72rem;">Cancelamentos</span>
            <span class="badge bg-danger-subtle text-danger-emphasis rounded-circle p-2">
              <i class="bi bi-x-lg fs-6"></i>
            </span>
          </div>
          <h3 class="fw-black text-danger mb-1"><?= $metricas->totalCancelamentos ?></h3>
          <small class="text-danger" style="font-size: 0.75rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Monitorado (-10 pts)
          </small>
        </div>
      </div>

      <!-- 3. Taxa de Assiduidade -->
      <div class="col-6 col-lg-3">
        <div class="kpi-card p-3 p-md-4 h-100 shadow-xs">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Assiduidade</span>
            <span class="badge bg-info-subtle text-info-emphasis rounded-circle p-2">
              <i class="bi bi-pie-chart-fill fs-6"></i>
            </span>
          </div>
          <h3 class="fw-black text-body mb-1"><?= $metricas->taxaAssiduidade ?>%</h3>
          <div class="progress mt-1" style="height: 5px;">
            <div class="progress-bar bg-<?= ($metricas->taxaAssiduidade >= 80 ? 'success' : ($metricas->taxaAssiduidade >= 50 ? 'warning' : 'danger')) ?>" style="width: <?= $metricas->taxaAssiduidade ?>%;"></div>
          </div>
        </div>
      </div>

      <!-- 4. Total de Escalações Atribuídas -->
      <div class="col-6 col-lg-3">
        <div class="kpi-card p-3 p-md-4 h-100 shadow-xs">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Atribuído</span>
            <span class="badge bg-primary-subtle text-primary-emphasis rounded-circle p-2">
              <i class="bi bi-calendar-check fs-6"></i>
            </span>
          </div>
          <h3 class="fw-black text-primary mb-1"><?= $metricas->totalEscalas ?></h3>
          <small class="text-muted" style="font-size: 0.75rem;">
            <?= $metricas->totalPendentes ?> aguardando resposta
          </small>
        </div>
      </div>

    </div>

    <!-- ========================================== -->
    <!-- ALERTA DE MONITORAMENTO DE CANCELAMENTOS   -->
    <!-- ========================================== -->
    <div class="card border-0 shadow-xs rounded-4 mb-4 bg-body border-start border-4 border-<?= ($metricas->totalCancelamentos > 0 ? 'danger' : 'primary') ?>">
      <div class="card-body p-3 p-md-4">
        <div class="d-flex align-items-start gap-3">
          <div class="p-2 rounded-circle bg-<?= ($metricas->totalCancelamentos > 0 ? 'danger' : 'primary') ?>-subtle text-<?= ($metricas->totalCancelamentos > 0 ? 'danger' : 'primary') ?> fs-4">
            <i class="bi bi-shield-check"></i>
          </div>
          <div>
            <h6 class="fw-bold text-body mb-1">Transparência & Monitoramento de Escalas</h6>
            <p class="text-secondary small mb-0">
              A liderança da ADVEC acompanha em tempo real a confirmação e as justificativas de cancelamento das agendas. Cada culto atendido soma pontos no seu score e fortalece a equipe. Evite desmarcar em cima da hora para manter a excelência do serviço.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- SEÇÃO: SEUS CANCELAMENTOS REGISTRADOS      -->
    <!-- ========================================== -->
    <?php if (!empty($metricas->listaCancelamentos)) { ?>
      <div class="card card-outline card-danger shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-danger-subtle py-3 d-flex justify-content-between align-items-center">
          <h6 class="card-title fw-bold mb-0 text-danger-emphasis d-flex align-items-center gap-2">
            <i class="bi bi-x-circle-fill"></i> Seus Cancelamentos <?= $departamentoAtual ? 'em ' . esc($nomeDepAtual) : '' ?> no Período (<?= count($metricas->listaCancelamentos) ?>)
          </h6>
          <span class="badge bg-danger rounded-pill px-3">Registrado no Histórico</span>
        </div>
        <div class="card-body p-3 p-md-4">
          <div class="row g-3">
            <?php foreach ($metricas->listaCancelamentos as $c) { 
              $corDepCanc = !empty($c->cor_departamento) ? $c->cor_departamento : '#2563eb';
              $txtDepCanc = getContrasteTexto($corDepCanc);
            ?>
              <div class="col-md-6">
                <div class="p-3 bg-body border border-danger-subtle rounded-3 shadow-none">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-primary font-monospace small">
                      <i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y', strtotime($c->data_culto)) ?>
                    </span>
                    <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2 py-1 small">
                      Cancelado
                    </span>
                  </div>
                  <div class="fw-semibold text-body mb-1"><?= esc($c->titulo_culto) ?></div>
                  <div class="d-flex align-items-center gap-1 text-muted small mb-2 flex-wrap">
                    <span class="badge rounded-pill px-2 py-0.5 small fw-bold" style="background-color: <?= esc($corDepCanc) ?> !important; color: <?= esc($txtDepCanc) ?> !important;">
                      <?= esc($c->nome_departamento) ?>
                    </span>
                    <span>&bull;</span>
                    <span><?= esc($c->nome_area) ?></span>
                  </div>
                  
                  <div class="p-2 bg-danger-subtle rounded-3 text-danger-emphasis small fst-italic">
                    <i class="bi bi-chat-quote-fill me-1"></i> "<?= nl2br(esc($c->justificativa_recusa ?: 'Sem justificativa preenchida.')) ?>"
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>

    <!-- ========================================== -->
    <!-- RANKING & LEADERBOARD DE VOLUNTÁRIOS       -->
    <!-- ========================================== -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="card-title fw-bold mb-0 text-body d-flex align-items-center gap-2">
          <i class="bi bi-award-fill text-warning"></i> Ranking de Fidelidade:
          <?php if ($departamentoAtual) { ?>
            <span class="badge rounded-pill px-3 py-1 shadow-xs fw-bold" 
                  style="background-color: <?= esc($corDepAtual) ?> !important; color: <?= esc($txtDepAtual) ?> !important;">
              <?= esc($nomeDepAtual) ?>
            </span>
          <?php } else { ?>
            <span class="badge bg-secondary text-white rounded-pill px-3 py-1 shadow-xs fw-bold">
              Consolidado Geral
            </span>
          <?php } ?>
        </h5>
        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2 fw-semibold">
          <?= count($rankingData->leaderboard) ?> Voluntário(s) Classificado(s)
        </span>
      </div>

      <div class="card-body p-0">
        <?php if (!empty($rankingData->leaderboard)) { ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-3 ps-md-4 text-center" style="width: 60px;">Posição</th>
                  <th>Voluntário</th>
                  <th class="text-center d-none d-md-table-cell">Nível</th>
                  <th class="text-center d-none d-md-table-cell">Cultos Aceitos</th>
                  <th class="text-center d-none d-md-table-cell">Cancelamentos</th>
                  <th class="text-center d-none d-md-table-cell">Assiduidade</th>
                  <th class="pe-4 text-end d-none d-md-table-cell">Pontuação</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rankingData->leaderboard as $item) { 
                  $isMe = $item->is_current_user;
                ?>
                  <tr class="<?= $isMe ? 'leaderboard-row-me' : '' ?>">
                    
                    <!-- Posição / Troféus Top 3 -->
                    <td class="ps-3 ps-md-4 text-center">
                      <?php if ($item->posicao === 1) { ?>
                        <span class="podium-badge podium-1" title="1º Lugar">🥇</span>
                      <?php } elseif ($item->posicao === 2) { ?>
                        <span class="podium-badge podium-2" title="2º Lugar">🥈</span>
                      <?php } elseif ($item->posicao === 3) { ?>
                        <span class="podium-badge podium-3" title="3º Lugar">🥉</span>
                      <?php } else { ?>
                        <span class="fw-bold text-muted">#<?= $item->posicao ?></span>
                      <?php } ?>
                    </td>

                    <!-- Voluntário -->
                    <td class="pe-3 pe-md-0">
                      <div class="d-flex align-items-center gap-2 gap-md-3">
                        <img src="<?= esc($item->foto_url) ?>" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;" alt="avatar" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($item->nome_completo) ?>&background=2563eb&color=fff&size=80&bold=true';">
                        <div class="text-truncate">
                          <div class="fw-bold text-body text-truncate">
                            <?= esc($item->nome_exibicao) ?>
                            <?php if ($isMe) { ?>
                              <span class="badge bg-primary text-white rounded-pill px-2 py-0 small ms-1">VOCÊ</span>
                            <?php } ?>
                          </div>
                          <small class="text-muted d-none d-sm-block text-truncate"><?= esc($item->nome_completo) ?></small>
                        </div>
                      </div>
                    </td>

                    <!-- Nível de Conhecimento -->
                    <td class="text-center d-none d-md-table-cell">
                      <span class="badge badge-level-<?= strtolower($item->nivel_conhecimento) ?> rounded-pill px-2 py-1 small" style="font-size: 0.72rem;">
                        <?= esc($item->nivel_conhecimento) ?>
                      </span>
                    </td>

                    <!-- Cultos Aceitos -->
                    <td class="text-center d-none d-md-table-cell">
                      <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> <?= $item->cultos_aceitos ?>
                      </span>
                    </td>

                    <!-- Cancelamentos -->
                    <td class="text-center d-none d-md-table-cell">
                      <?php if ($item->cultos_cancelados > 0) { ?>
                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2 py-1 fw-bold">
                          <?= $item->cultos_cancelados ?>
                        </span>
                      <?php } else { ?>
                        <span class="text-muted small">0</span>
                      <?php } ?>
                    </td>

                    <!-- Assiduidade -->
                    <td class="text-center d-none d-md-table-cell">
                      <span class="fw-bold text-<?= ($item->taxa_assiduidade >= 80 ? 'success' : ($item->taxa_assiduidade >= 50 ? 'warning' : 'danger')) ?>">
                        <?= $item->taxa_assiduidade ?>%
                      </span>
                    </td>

                    <!-- Pontuação -->
                    <td class="pe-4 text-end d-none d-md-table-cell">
                      <span class="fw-black fs-6 text-primary"><?= $item->pontos ?> <small class="text-muted fs-8">pts</small></span>
                    </td>

                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <?php } else { ?>
          <div class="text-center py-5 px-3">
            <i class="bi bi-trophy text-muted opacity-50 display-4 d-block mb-3"></i>
            <h6 class="fw-bold mb-1 text-body">Nenhum voluntário classificado neste período/departamento</h6>
            <p class="text-muted small mb-0">Para ingressar no ranking, os voluntários devem ter escalas atribuídas e confirmadas neste departamento durante o período selecionado.</p>
          </div>
        <?php } ?>
      </div>
    </div>

  </main>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
