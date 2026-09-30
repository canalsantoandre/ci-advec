<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
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
    .badge-level-aprendiz { background-color: #64748b; color: #fff; }
    .badge-level-junior   { background-color: #0284c7; color: #fff; }
    .badge-level-pleno    { background-color: #7c3aed; color: #fff; }
    .badge-level-senior   { background-color: #d97706; color: #fff; }
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
        <small class="text-muted">Acompanhe seu desempenho e sua posição no ranking de voluntários</small>
      </div>

      <div class="btn-group shadow-xs rounded-pill overflow-hidden bg-white p-1 border" role="group">
        <a href="<?= base_url('portal/metricas?periodo=mes_atual') ?>" class="btn btn-sm <?= ($periodo === 'mes_atual') ? 'btn-primary text-white fw-bold' : 'btn-light text-muted' ?> rounded-pill px-3">Mês Atual</a>
        <a href="<?= base_url('portal/metricas?periodo=ano_atual') ?>" class="btn btn-sm <?= ($periodo === 'ano_atual') ? 'btn-primary text-white fw-bold' : 'btn-light text-muted' ?> rounded-pill px-3">Ano Atual</a>
        <a href="<?= base_url('portal/metricas?periodo=tudo') ?>" class="btn btn-sm <?= ($periodo === 'tudo') ? 'btn-primary text-white fw-bold' : 'btn-light text-muted' ?> rounded-pill px-3">Geral</a>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- HERO CARD PREMIUM: MEU SCORE & POSIÇÃO     -->
    <!-- ========================================== -->
    <?php
      $meu = $rankingData->meuRank;
      $posicaoMeu = $meu ? $meu->posicao : 1;
      $pontosMeu  = $meu ? $meu->pontos : 0;
      $totalAtivos= $rankingData->totalAtivos;
      $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($voluntario->nome) . '&background=2563eb&color=fff&size=120&bold=true';
      $fotoSrc = !empty($voluntario->foto_url) ? $voluntario->foto_url : $defaultAvatar;
    ?>
    <div class="hero-ranking-card shadow-lg p-4 mb-4">
      <div class="row align-items-center g-4">
        
        <!-- Identificação e Foto -->
        <div class="col-md-7 d-flex align-items-center gap-3 gap-md-4">
          <div class="position-relative">
            <img src="<?= esc($fotoSrc) ?>" class="rounded-circle border border-3 border-warning shadow" style="width: 84px; height: 84px; object-fit: cover;" alt="avatar" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
            <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-warning text-dark fw-black px-2 shadow">
              #<?= $posicaoMeu ?>
            </span>
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
              <span class="text-white-50 small">&bull;</span>
              <span class="text-white-50 small">Top <strong><?= $rankingData->percentil ?>%</strong> dos mais dedicados</span>
            </div>

            <div class="text-white-50 small">
              <i class="bi bi-star-fill text-warning me-1"></i> <?= $pontosMeu ?> Pontos de Fidelidade acumulados
            </div>
          </div>
        </div>

        <!-- Posição no Pódio & Assiduidade -->
        <div class="col-md-5 text-md-end">
          <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-10 d-inline-block text-center text-md-end w-100">
            <div class="d-flex justify-content-around justify-content-md-end gap-4 align-items-center">
              <div>
                <span class="d-block text-white-50 small text-uppercase fw-bold" style="font-size: 0.72rem;">Sua Posição</span>
                <span class="fs-2 fw-black text-warning">#<?= $posicaoMeu ?> <span class="fs-6 text-white-50">/ <?= $totalAtivos ?></span></span>
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
            <i class="bi bi-x-circle-fill"></i> Seus Cancelamentos no Período (<?= count($metricas->listaCancelamentos) ?>)
          </h6>
          <span class="badge bg-danger rounded-pill px-3">Registrado no Histórico</span>
        </div>
        <div class="card-body p-3 p-md-4">
          <div class="row g-3">
            <?php foreach ($metricas->listaCancelamentos as $c) { ?>
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
                  <div class="text-muted small mb-2"><?= esc($c->nome_departamento) ?> &bull; <?= esc($c->nome_area) ?></div>
                  
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
      <div class="card-header bg-body py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold mb-0 text-primary d-flex align-items-center gap-2">
          <i class="bi bi-award-fill text-warning"></i> Ranking de Fidelidade & Atendimento
        </h5>
        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3">
          <?= count($rankingData->leaderboard) ?> Voluntários
        </span>
      </div>

      <div class="card-body p-0">
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
      </div>
    </div>

  </main>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
