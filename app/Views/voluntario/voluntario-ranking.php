<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-trophy-fill text-warning"></i>
          <span>Ranking & Gamificação dos Voluntários</span>
        </h3>
        <p class="text-secondary small mb-0">Classificação por assiduidade, presenças e pontuação por departamento, sub-área e visão global multi-departamento</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <div class="d-flex justify-content-sm-end align-items-center gap-2 mt-2 mt-sm-0 flex-wrap">
          <a href="<?= base_url('voluntario') ?>" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-people-fill"></i>
            <span>Lista de Voluntários</span>
          </a>
          <a href="<?= base_url('voluntario/desempenho') ?>" class="btn btn-outline-info rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-person-lines-fill"></i>
            <span>Desempenho & Cancelamentos</span>
          </a>
          <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" onclick="window.print()">
            <i class="bi bi-printer"></i>
            <span>Imprimir</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  :root {
    --gold-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --silver-gradient: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    --bronze-gradient: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    --accent-glow: rgba(245, 158, 11, 0.25);
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
    background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .podium-card {
    border-radius: 1.25rem;
    border: 1px solid rgba(226, 232, 240, 0.8);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background: #ffffff;
    position: relative;
    overflow: hidden;
  }

  [data-bs-theme="dark"] .podium-card {
    background-color: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
  }

  .podium-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.12);
  }

  .podium-card.first-place {
    border: 2px solid #f59e0b;
    box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.25);
  }

  .podium-badge {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1rem;
  }

  .podium-1 {
    background: var(--gold-gradient);
    color: #fff;
    box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
  }

  .podium-2 {
    background: var(--silver-gradient);
    color: #fff;
    box-shadow: 0 0 10px rgba(148, 163, 184, 0.4);
  }

  .podium-3 {
    background: var(--bronze-gradient);
    color: #fff;
    box-shadow: 0 0 10px rgba(217, 119, 6, 0.4);
  }

  .score-badge {
    background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
    color: #ffffff;
    font-weight: 800;
    letter-spacing: 0.3px;
    padding: 0.45rem 0.85rem;
    border-radius: 50rem;
    box-shadow: 0 2px 8px rgba(234, 88, 12, 0.25);
  }

  .score-badge-sub {
    background: rgba(245, 158, 11, 0.12);
    color: #b45309;
    font-weight: 700;
    border: 1px solid rgba(245, 158, 11, 0.3);
  }

  [data-bs-theme="dark"] .score-badge-sub {
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.4);
  }

  .kpi-stat-card {
    border-radius: 1rem;
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.9);
    transition: transform 0.15s ease;
  }

  [data-bs-theme="dark"] .kpi-stat-card {
    background: #1e293b;
    border-color: #334155;
  }

  .kpi-stat-card:hover {
    transform: translateY(-2px);
  }

  .multi-badge {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.3px;
  }

  .level-badge-senior {
    background-color: #7c3aed !important;
    color: #fff;
  }
  .level-badge-pleno {
    background-color: #0284c7 !important;
    color: #fff;
  }
  .level-badge-junior {
    background-color: #10b981 !important;
    color: #fff;
  }
  .level-badge-aprendiz {
    background-color: #64748b !important;
    color: #fff;
  }

  .dep-pill {
    font-size: 0.72rem;
    font-weight: 600;
    border-radius: 50rem;
    padding: 0.2rem 0.6rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }
</style>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Card Hero Informativo de Gamificação -->
    <div class="hero-ranking-card shadow-sm p-4 mb-4">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1.5">
              <i class="bi bi-stars me-1"></i> Gamificação & Fidelidade
            </span>
            <span class="text-white-50 small">&bull; <?= esc($rankingData->labelPeriodo) ?></span>
          </div>
          <h2 class="fw-bold mb-2">
            <?php if (!empty($rankingData->departamento)) { ?>
              Ranking: <?= esc($rankingData->departamento->nome) ?>
              <?php if (!empty($rankingData->area)) { ?>
                <span class="fs-4 text-warning fw-normal">/ <?= esc($rankingData->area->nome_area) ?></span>
              <?php } ?>
            <?php } else { ?>
              Visão Geral Consolidada de Voluntários
            <?php } ?>
          </h2>
          <p class="text-white-50 mb-0 small" style="max-width: 700px;">
            Acompanhe o engajamento e a constância da equipe. O score gamificado valoriza a fidelidade e a prontidão:
            <span class="text-warning fw-semibold">+15 pts por presença cumprida</span>,
            <span class="text-info fw-semibold">+10 pts por confirmação</span>,
            <span class="text-success fw-semibold">+5 pts bônus de agilidade</span> (aceite em até 24h),
            <span class="text-danger fw-semibold">-10 pts por cancelamento</span> (exceto se abonado pela liderança) e
            <span class="text-white fw-semibold">-25 pts por omissão/falta</span>.
            <span class="d-block mt-1 text-warning fw-semibold">
              <i class="bi bi-stopwatch-fill me-1"></i>Divisor de Águas: em caso de empate na pontuação, a rapidez em que o voluntário responde e aceita as escalas define as posições no ranking!
            </span>
          </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
          <div class="d-inline-flex flex-column align-items-lg-end">
            <div class="d-flex align-items-center gap-3">
              <div class="text-center">
                <h3 class="fw-bold font-monospace mb-0 text-warning"><?= $rankingData->totalClassificados ?></h3>
                <small class="text-white-50" style="font-size: 0.72rem;">VOLUNTÁRIOS</small>
              </div>
              <div class="vr bg-white opacity-25" style="height: 38px;"></div>
              <div class="text-center">
                <h3 class="fw-bold font-monospace mb-0 text-white"><?= $rankingData->totalMultidepartamento ?></h3>
                <small class="text-white-50" style="font-size: 0.72rem;">MULTIDEPARTAMENTO</small>
              </div>
              <div class="vr bg-white opacity-25" style="height: 38px;"></div>
              <div class="text-center">
                <h3 class="fw-bold font-monospace mb-0 text-success"><?= $rankingData->kpis->mediaAssiduidade ?>%</h3>
                <small class="text-white-50" style="font-size: 0.72rem;">ASSIDUIDADE</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Alerta de Justificativas Pendentes de Análise -->
    <?php if (!empty($rankingData->kpis->cancelados_pendentes) && $rankingData->kpis->cancelados_pendentes > 0) { ?>
      <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center justify-content-between p-3 mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="p-2 bg-warning bg-opacity-25 rounded-circle text-warning-emphasis">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
          </div>
          <div>
            <div class="fw-bold text-warning-emphasis">
              <?= $rankingData->kpis->cancelados_pendentes ?> justificativa(s) de cancelamento aguardando sua análise
            </div>
            <div class="small text-muted">
              Você pode analisar e abonar as justificativas aceitas para que os pontos desses cancelamentos não sejam descontados do ranking dos voluntários.
            </div>
          </div>
        </div>
        <a href="<?= base_url('voluntario/desempenho?status_analise=PENDENTE') ?>" class="btn btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm text-nowrap">
          <i class="bi bi-shield-check me-1"></i> Analisar Justificativas
        </a>
      </div>
    <?php } ?>

    <!-- Barra de Filtros Avançados -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
      <div class="card-body p-3 p-md-4">
        <form method="get" action="<?= base_url('voluntario/ranking') ?>" id="formFiltroRanking">

          <div class="row g-3 align-items-end">

            <!-- Período -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="form-label small fw-bold text-muted mb-1">
                <i class="bi bi-calendar3 me-1 text-primary"></i> Período
              </label>
              <select name="periodo" id="selectPeriodoRanking" class="form-select rounded-3 shadow-none" onchange="toggleDatasCustom(this.value)">
                <option value="mes_atual" <?= ($filtros['periodo'] === 'mes_atual') ? 'selected' : '' ?>>Mês Atual (<?= date('m/Y') ?>)</option>
                <option value="mes_anterior" <?= ($filtros['periodo'] === 'mes_anterior') ? 'selected' : '' ?>>Mês Anterior</option>
                <option value="ultimos_3_meses" <?= ($filtros['periodo'] === 'ultimos_3_meses') ? 'selected' : '' ?>>Últimos 3 Meses</option>
                <option value="ano_atual" <?= ($filtros['periodo'] === 'ano_atual') ? 'selected' : '' ?>>Ano Atual (<?= date('Y') ?>)</option>
                <option value="tudo" <?= ($filtros['periodo'] === 'tudo') ? 'selected' : '' ?>>Todo o Histórico</option>
                <option value="custom" <?= ($filtros['periodo'] === 'custom') ? 'selected' : '' ?>>Personalizado</option>
              </select>
            </div>

            <!-- Departamento -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="form-label small fw-bold text-muted mb-1">
                <i class="bi bi-building me-1 text-primary"></i> Departamento
              </label>
              <select name="id_departamento" id="selectDepartamentoRanking" class="form-select rounded-3 shadow-none" onchange="carregarSubareasDoDepartamento(this.value)">
                <option value="">🎯 Todos os Departamentos (Visão Geral)</option>
                <?php foreach ($departamentos as $d) { ?>
                  <option value="<?= $d->id_departamento ?>" <?= (isset($filtros['id_departamento']) && $filtros['id_departamento'] == $d->id_departamento) ? 'selected' : '' ?>>
                    <?= esc($d->nome) ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <!-- Sub-área (Dinâmica conforme Departamento) -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="form-label small fw-bold text-muted mb-1">
                <i class="bi bi-grid-fill me-1 text-primary"></i> Sub-área
              </label>
              <select name="id_area" id="selectAreaRanking" class="form-select rounded-3 shadow-none" <?= empty($filtros['id_departamento']) ? 'disabled' : '' ?>>
                <option value="">Todas as Sub-áreas</option>
                <?php if (!empty($areasDoDepartamento)) { ?>
                  <?php foreach ($areasDoDepartamento as $area) { ?>
                    <option value="<?= $area->id_area ?>" <?= (isset($filtros['id_area']) && $filtros['id_area'] == $area->id_area) ? 'selected' : '' ?>>
                      <?= esc($area->nome_area) ?>
                    </option>
                  <?php } ?>
                <?php } ?>
              </select>
            </div>

            <!-- Busca por Voluntário -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="form-label small fw-bold text-muted mb-1">
                <i class="bi bi-search me-1 text-primary"></i> Buscar Voluntário
              </label>
              <input type="text" name="busca" class="form-control rounded-3 shadow-none" placeholder="Nome, apelido..." value="<?= esc($filtros['busca'] ?? '') ?>">
            </div>

            <!-- Intervalo Personalizado (se custom) -->
            <div class="col-12 row g-3 m-0 p-0 <?= ($filtros['periodo'] === 'custom') ? '' : 'd-none' ?>" id="divDatasCustom">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Data Inicial</label>
                <input type="date" name="data_inicio" class="form-control rounded-3 shadow-none" value="<?= esc($filtros['data_inicio'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Data Final</label>
                <input type="date" name="data_fim" class="form-control rounded-3 shadow-none" value="<?= esc($filtros['data_fim'] ?? '') ?>">
              </div>
            </div>

            <!-- Filtros Especiais & Botões de Ação -->
            <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2 border-top">
              <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Toggle Apenas Multidepartamento -->
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" role="switch" name="apenas_multi" id="switchApenasMulti" value="1" <?= !empty($filtros['apenas_multi']) ? 'checked' : '' ?> onchange="this.form.submit()">
                  <label class="form-check-label small fw-semibold text-body user-select-none" for="switchApenasMulti">
                    <span class="badge multi-badge rounded-pill px-2 py-0.5 me-1">Multidep</span> Apenas voluntários que atuam em 2+ departamentos
                  </label>
                </div>

                <!-- Toggle Apenas Escalados no Período -->
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" role="switch" name="apenas_escalados" id="switchApenasEscalados" value="1" <?= !empty($filtros['apenas_escalados']) ? 'checked' : '' ?> onchange="this.form.submit()">
                  <label class="form-check-label small fw-semibold text-body user-select-none" for="switchApenasEscalados">
                    Apenas com escalas no período
                  </label>
                </div>
              </div>

              <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                  <i class="bi bi-funnel-fill me-1"></i> Filtrar
                </button>
                <a href="<?= base_url('voluntario/ranking') ?>" class="btn btn-outline-secondary rounded-pill px-3" title="Limpar Filtros">
                  <i class="bi bi-x-lg"></i> Limpar
                </a>
              </div>
            </div>

          </div>

        </form>
      </div>
    </div>

    <!-- Pódio Visual dos Top 3 (Se houver classificados) -->
    <?php if (!empty($rankingData->top3)) { ?>
      <div class="row g-3 mb-4">
        <?php foreach ($rankingData->top3 as $topVol) {
          $posClass = $topVol->posicao === 1 ? 'podium-1' : ($topVol->posicao === 2 ? 'podium-2' : 'podium-3');
          $posEmoji = $topVol->posicao === 1 ? '🥇' : ($topVol->posicao === 2 ? '🥈' : '🥉');
          $isLeader = $topVol->posicao === 1;
        ?>
          <div class="col-12 col-md-4">
            <div class="podium-card p-3 h-100 <?= $isLeader ? 'first-place' : '' ?>">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                  <span class="podium-badge <?= $posClass ?>"><?= $topVol->posicao ?>º</span>
                  <span class="fs-4"><?= $posEmoji ?></span>
                </div>
                <span class="score-badge">
                  <i class="bi bi-fire me-1"></i><?= $topVol->pontos ?> pts
                </span>
              </div>

              <div class="d-flex align-items-center gap-3 mb-3">
                <img src="<?= esc($topVol->foto_url) ?>" alt="<?= esc($topVol->nome_completo) ?>" class="rounded-circle shadow-sm" style="width: 58px; height: 58px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($topVol->nome_completo) ?>&background=2563eb&color=fff&size=80&bold=true'">
                <div>
                  <h6 class="fw-bold mb-0 text-truncate" style="max-width: 170px;" title="<?= esc($topVol->nome_completo) ?>">
                    <?= esc($topVol->nome_exibicao) ?>
                  </h6>
                  <span class="badge level-badge-<?= strtolower($topVol->nivel_conhecimento) ?> rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.68rem;">
                    <?= esc($topVol->nivel_conhecimento) ?>
                  </span>
                  <?php if ($topVol->is_multidepartamento) { ?>
                    <span class="badge multi-badge rounded-pill px-2 py-0.5 ms-1" style="font-size: 0.68rem;">
                      <?= $topVol->total_departamentos_atuacao ?> Deptos
                    </span>
                  <?php } ?>
                </div>
              </div>

              <!-- Departamentos onde atua -->
              <div class="d-flex flex-wrap gap-1 mb-3">
                <?php foreach ($topVol->departamentos_vinculados as $depVinc) { ?>
                  <span class="dep-pill border" style="background-color: <?= esc($depVinc['cor_identificacao']) ?>15; color: <?= esc($depVinc['cor_identificacao']) ?>; border-color: <?= esc($depVinc['cor_identificacao']) ?>40 !important;">
                    <i class="bi bi-dot" style="font-size: 1.2rem; margin: -0.4rem;"></i>
                    <?= esc($depVinc['nome']) ?>
                  </span>
                <?php } ?>
              </div>

              <!-- Métricas Rápidas com Agilidade / Tempo de Aceite -->
              <div class="row g-2 pt-2 border-top text-center">
                <div class="col-3">
                  <small class="text-muted d-block" style="font-size: 0.65rem;">ESCALAS</small>
                  <span class="fw-bold font-monospace"><?= $topVol->total_escalas ?></span>
                </div>
                <div class="col-3">
                  <small class="text-muted d-block" style="font-size: 0.65rem;">PRESENÇAS</small>
                  <span class="fw-bold font-monospace text-success"><?= $topVol->presencas ?></span>
                </div>
                <div class="col-3">
                  <small class="text-muted d-block" style="font-size: 0.65rem;">ASSIDUIDADE</small>
                  <span class="fw-bold font-monospace text-primary"><?= $topVol->taxa_assiduidade ?>%</span>
                </div>
                <div class="col-3" title="Tempo médio de resposta (critério de desempate no ranking)">
                  <small class="text-muted d-block" style="font-size: 0.65rem;">AGILIDADE</small>
                  <span class="fw-bold font-monospace text-warning-emphasis d-inline-flex align-items-center justify-content-center gap-0.5">
                    <i class="bi bi-stopwatch text-warning" style="font-size: 0.72rem;"></i><?= $topVol->tempo_medio_resposta_formatado ?>
                  </span>
                </div>
              </div>

              <?php if (!empty($topVol->bonus_agilidade_count)) { ?>
                <div class="mt-2 text-center">
                  <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                    <i class="bi bi-lightning-charge-fill"></i> +<?= $topVol->bonus_agilidade_pontos ?> pts bônus de rapidez (<?= $topVol->bonus_agilidade_count ?>x)
                  </span>
                </div>
              <?php } ?>

              <!-- Botão Ver Ficha do Voluntário -->
              <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-semibold" onclick="abrirModalDetalhesRanking(<?= $topVol->id_voluntario ?>)">
                  <i class="bi bi-info-circle me-1"></i> Ver Posição em Todos os Departamentos
                </button>
              </div>

            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>

    <!-- Tabela Matriz Leaderboard Completa -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <h5 class="card-title fw-bold mb-0 text-primary d-flex align-items-center gap-2">
            <i class="bi bi-list-ol"></i>
            Tabela de Classificação
          </h5>
          <span class="badge bg-primary-subtle text-primary rounded-pill px-3">
            <?= $rankingData->totalClassificados ?> Voluntários
          </span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <small class="text-muted">Ordenado por:</small>
          <span class="badge bg-body-secondary text-body border rounded-pill px-2 py-1 small">
            Pontos &bull; Agilidade / Tempo de Resposta (Desempate) &bull; Presenças &bull; Assiduidade
          </span>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="tblLeaderboardGestor">
            <thead class="table-dark small text-uppercase">
              <tr>
                <th scope="col" class="text-center py-3" style="width: 75px;">Pos.</th>
                <th scope="col" class="py-3" style="min-width: 240px;">Voluntário</th>
                <th scope="col" class="py-3" style="min-width: 220px;">Onde Atua (Departamentos)</th>
                <th scope="col" class="text-center py-3" style="width: 80px;" title="Total de escalas no período">Escalas</th>
                <th scope="col" class="text-center py-3" style="width: 90px;" title="Presenças confirmadas e cumpridas">Presenças</th>
                <th scope="col" class="text-center py-3" style="width: 90px;" title="Escalas aceitas/confirmadas">Aceitos</th>
                <th scope="col" class="text-center py-3" style="width: 90px;" title="Cancelamentos e recusas no período">Recusas</th>
                <th scope="col" class="text-center py-3" style="width: 90px;" title="Faltas sem justificativa">Faltas</th>
                <th scope="col" class="py-3" style="min-width: 130px;">Assiduidade</th>
                <th scope="col" class="text-center py-3" style="min-width: 120px;" title="Tempo médio de resposta às convocações e bônus de agilidade conquistados (+5 pts por aceite em até 24h)">Agilidade</th>
                <th scope="col" class="text-center py-3" style="width: 110px;">Pontos</th>
                <th scope="col" class="text-end py-3 pe-4" style="width: 100px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($rankingData->leaderboard)) { ?>
                <?php foreach ($rankingData->leaderboard as $vol) {
                  $isTop1 = ($vol->posicao === 1);
                  $isTop2 = ($vol->posicao === 2);
                  $isTop3 = ($vol->posicao === 3);
                  $rowBg  = $isTop1 ? 'table-warning-subtle' : '';
                ?>
                  <tr class="<?= $rowBg ?>">
                    <!-- Posição -->
                    <td class="text-center fw-bold fs-6">
                      <?php if ($isTop1) { ?>
                        <span class="badge podium-badge podium-1 shadow-sm">1</span>
                      <?php } elseif ($isTop2) { ?>
                        <span class="badge podium-badge podium-2 shadow-sm">2</span>
                      <?php } elseif ($isTop3) { ?>
                        <span class="badge podium-badge podium-3 shadow-sm">3</span>
                      <?php } else { ?>
                        <span class="text-muted font-monospace">#<?= $vol->posicao ?></span>
                      <?php } ?>
                    </td>

                    <!-- Voluntário -->
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img src="<?= esc($vol->foto_url) ?>" alt="<?= esc($vol->nome_completo) ?>" class="rounded-circle shadow-xs" style="width: 42px; height: 42px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($vol->nome_completo) ?>&background=2563eb&color=fff&size=80&bold=true'">
                        <div>
                          <div class="fw-bold text-body">
                            <?= esc($vol->nome_completo) ?>
                          </div>
                          <div class="d-flex align-items-center gap-1 flex-wrap mt-0.5">
                            <?php if (!empty($vol->nickname)) { ?>
                              <small class="text-muted fw-semibold">"<?= esc($vol->nickname) ?>"</small>
                            <?php } ?>
                            <span class="badge level-badge-<?= strtolower($vol->nivel_conhecimento) ?> rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                              <?= esc($vol->nivel_conhecimento) ?>
                            </span>
                            <?php if ($vol->is_multidepartamento) { ?>
                              <span class="badge multi-badge rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Atua em <?= $vol->total_departamentos_atuacao ?> departamentos diferentes">
                                <i class="bi bi-diagram-3-fill me-0.5"></i> <?= $vol->total_departamentos_atuacao ?> Deptos
                              </span>
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Departamentos onde atua -->
                    <td>
                      <div class="d-flex flex-wrap gap-1">
                        <?php foreach ($vol->departamentos_vinculados as $dep) { ?>
                          <span class="dep-pill border" style="background-color: <?= esc($dep['cor_identificacao']) ?>15; color: <?= esc($dep['cor_identificacao']) ?>; border-color: <?= esc($dep['cor_identificacao']) ?>40 !important;" title="Sub-áreas: <?= esc(implode(', ', $dep['areas'] ?? [])) ?>">
                            <?= esc($dep['nome']) ?>
                            <?php if (!empty($dep['areas'])) { ?>
                              <small class="opacity-75">(<?= count($dep['areas']) ?>)</small>
                            <?php } ?>
                          </span>
                        <?php } ?>
                      </div>
                    </td>

                    <!-- Escalas -->
                    <td class="text-center font-monospace fw-semibold">
                      <?= $vol->total_escalas ?>
                    </td>

                    <!-- Presenças -->
                    <td class="text-center">
                      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 font-monospace fw-bold">
                        <i class="bi bi-check-lg me-0.5"></i><?= $vol->presencas ?>
                      </span>
                    </td>

                    <!-- Aceitos -->
                    <td class="text-center font-monospace text-muted">
                      <?= $vol->cultos_aceitos ?>
                    </td>

                    <!-- Recusas -->
                    <td class="text-center">
                      <?php if ($vol->cultos_cancelados > 0) { ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 font-monospace fw-bold">
                          <?= $vol->cultos_cancelados ?>
                        </span>
                        <?php if (!empty($vol->cultos_cancelados_abonados)) { ?>
                          <div class="small text-success mt-1" style="font-size: 0.68rem;" title="Justificativa abonada: sem perda de pontos">
                            <i class="bi bi-shield-check"></i> <?= $vol->cultos_cancelados_abonados ?> abon.
                          </div>
                        <?php } ?>
                      <?php } else { ?>
                        <span class="text-muted font-monospace">0</span>
                      <?php } ?>
                    </td>

                    <!-- Faltas/Omissões -->
                    <td class="text-center">
                      <?php if ($vol->nao_confirmados > 0) { ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 font-monospace fw-bold" data-bs-toggle="tooltip" title="<?= $vol->nao_confirmados ?> omissão(ões) (-<?= $vol->nao_confirmados * 25 ?> pts no ranking)">
                          <?= $vol->nao_confirmados ?>
                        </span>
                      <?php } else { ?>
                        <span class="text-muted font-monospace">0</span>
                      <?php } ?>
                    </td>

                    <!-- Assiduidade -->
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1 rounded-pill" style="height: 6px;">
                          <?php
                          $barClass = $vol->taxa_assiduidade >= 80 ? 'bg-success' : ($vol->taxa_assiduidade >= 50 ? 'bg-warning' : 'bg-danger');
                          ?>
                          <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $vol->taxa_assiduidade ?>%;"></div>
                        </div>
                        <span class="fw-bold font-monospace small" style="min-width: 42px;">
                          <?= $vol->taxa_assiduidade ?>%
                        </span>
                      </div>
                    </td>

                    <!-- Agilidade e Tempo de Resposta (Divisor de Águas) -->
                    <td class="text-center">
                      <div class="d-inline-flex flex-column align-items-center">
                        <span class="badge bg-body-secondary text-body border rounded-pill px-2 py-0.5 font-monospace small" title="Tempo médio de resposta às convocações">
                          <i class="bi bi-stopwatch text-warning me-1"></i><?= $vol->tempo_medio_resposta_formatado ?>
                        </span>
                        <?php if (!empty($vol->bonus_agilidade_count)) { ?>
                          <small class="text-success fw-bold mt-0.5" style="font-size: 0.68rem;" title="<?= $vol->bonus_agilidade_count ?> aceite(s) em até 24h (+<?= $vol->bonus_agilidade_pontos ?> pts)">
                            <i class="bi bi-lightning-charge-fill"></i>+<?= $vol->bonus_agilidade_pontos ?> pts
                          </small>
                        <?php } ?>
                      </div>
                    </td>

                    <!-- Pontos Gamificados -->
                    <td class="text-center">
                      <span class="badge score-badge-sub rounded-pill px-2.5 py-1 fs-6 font-monospace">
                        <?= $vol->pontos ?> <small style="font-size: 0.65rem;">pts</small>
                      </span>
                    </td>

                    <!-- Ações -->
                    <td class="text-end pe-4">
                      <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5" title="Ver ranking comparativo por departamento e sub-área" onclick="abrirModalDetalhesRanking(<?= $vol->id_voluntario ?>)">
                        <i class="bi bi-graph-up-arrow me-1"></i> Ficha
                      </button>
                    </td>

                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="12" class="text-center py-5 text-muted">
                    <i class="bi bi-trophy fs-1 d-block mb-2 opacity-50"></i>
                    <h6 class="fw-bold">Nenhum voluntário classificado no período</h6>
                    <p class="small mb-0">Tente ajustar o período ou desmarcar o filtro de "Apenas com escalas no período".</p>
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

<!-- Modal Detalhado de Comparativo do Voluntário (Posição no Departamento, Sub-área e Geral) -->
<div class="modal fade" id="modalDetalhesRankingVoluntario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 shadow border-0 overflow-hidden">
      <div class="modal-header bg-dark text-white border-0 py-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-trophy-fill text-warning fs-5"></i>
          <h5 class="modal-title fw-bold mb-0">Ficha Comparativa de Ranking do Voluntário</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body p-4" id="modalDetalhesRankingCorpo">
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
          <p class="small text-muted mt-2">Carregando dados de ranking do voluntário...</p>
        </div>
      </div>

      <div class="modal-footer border-0 bg-body-tertiary">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
  let modalDetalhesRankingInstance = null;

  function toggleDatasCustom(periodo) {
    const div = document.getElementById('divDatasCustom');
    if (!div) return;
    if (periodo === 'custom') {
      div.classList.remove('d-none');
    } else {
      div.classList.add('d-none');
    }
  }

  function carregarSubareasDoDepartamento(idDepartamento) {
    const selectArea = document.getElementById('selectAreaRanking');
    if (!selectArea) return;

    selectArea.innerHTML = '<option value="">Todas as Sub-áreas</option>';

    if (!idDepartamento) {
      selectArea.disabled = true;
      return;
    }

    selectArea.disabled = false;
    fetch('<?= base_url('voluntario/getSubareasPorDepartamento') ?>?id_departamento=' + idDepartamento)
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success' && Array.isArray(data.subareas)) {
          data.subareas.forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id_area;
            opt.textContent = a.nome_area;
            selectArea.appendChild(opt);
          });
        }
      })
      .catch(err => {
        console.error('Erro ao buscar sub-áreas:', err);
      });
  }

  function abrirModalDetalhesRanking(idVoluntario) {
    const periodo = document.getElementById('selectPeriodoRanking')?.value || 'mes_atual';
    const corpo = document.getElementById('modalDetalhesRankingCorpo');
    corpo.innerHTML = `
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="small text-muted mt-2">Carregando métricas e ranking do voluntário...</p>
      </div>
    `;

    if (!modalDetalhesRankingInstance) {
      modalDetalhesRankingInstance = new bootstrap.Modal(document.getElementById('modalDetalhesRankingVoluntario'));
    }
    modalDetalhesRankingInstance.show();

    fetch('<?= base_url('voluntario/ranking-voluntario') ?>/' + idVoluntario + '?periodo=' + periodo)
      .then(r => r.json())
      .then(resp => {
        if (resp.status === 'success' && resp.dados) {
          renderizarDetalhesModalRanking(resp.dados);
        } else {
          corpo.innerHTML = `
            <div class="alert alert-warning border-0 rounded-3 mb-0">
              <i class="bi bi-exclamation-triangle-fill me-1"></i> Não foi possível carregar as informações deste voluntário.
            </div>
          `;
        }
      })
      .catch(err => {
        corpo.innerHTML = `
          <div class="alert alert-danger border-0 rounded-3 mb-0">
            <i class="bi bi-x-circle-fill me-1"></i> Falha de conexão ao buscar o ranking.
          </div>
        `;
      });
  }

  function renderizarDetalhesModalRanking(dados) {
    const v = dados.voluntario;
    const corpo = document.getElementById('modalDetalhesRankingCorpo');

    let htmlDeps = '';
    if (v.breakdown_departamentos && v.breakdown_departamentos.length > 0) {
      v.breakdown_departamentos.forEach(dep => {
        let subareasHtml = '';
        if (dep.subareas && dep.subareas.length > 0) {
          subareasHtml = `
            <div class="mt-2 pt-2 border-top">
              <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.72rem;">CLASSIFICAÇÃO NAS SUB-ÁREAS DESTE DEPARTAMENTO:</small>
              <div class="d-flex flex-wrap gap-2">
                ${dep.subareas.map(sa => `
                  <div class="p-2 bg-body border rounded-3" style="min-width: 140px;">
                    <div class="fw-bold small text-body">${sa.nome_area}</div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                      <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.7rem;">
                        ${sa.posicao ? '#' + sa.posicao + ' de ' + sa.total_subarea : 'N/C'}
                      </span>
                      <small class="font-monospace fw-bold text-warning-emphasis">${sa.pontos} pts</small>
                    </div>
                    <small class="text-muted d-block mt-0.5" style="font-size: 0.68rem;">${sa.presencas}/${sa.total_escalas} presenças (${sa.taxa_assiduidade}%)</small>
                  </div>
                `).join('')}
              </div>
            </div>
          `;
        }

        htmlDeps += `
          <div class="card border mb-3 rounded-3 shadow-none overflow-hidden">
            <div class="card-header py-2.5 d-flex align-items-center justify-content-between" style="background-color: ${dep.cor_identificacao}15;">
              <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="width: 12px; height: 12px; background-color: ${dep.cor_identificacao};"></span>
                <h6 class="fw-bold mb-0 text-body">${dep.nome_departamento}</h6>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark fw-bold rounded-pill px-2.5 py-1">
                  ${dep.posicao ? '#' + dep.posicao + ' no Departamento' : 'Sem Escalas'}
                </span>
                <span class="badge bg-dark rounded-pill px-2.5 py-1 font-monospace">
                  ${dep.pontos} pts
                </span>
              </div>
            </div>
            <div class="card-body p-3">
              <div class="row g-2 text-center">
                <div class="col-3">
                  <small class="text-muted d-block" style="font-size: 0.7rem;">ESCALAS</small>
                  <span class="fw-bold font-monospace">${dep.total_escalas}</span>
                </div>
                <div class="col-3">
                  <small class="text-muted d-block" style="font-size: 0.7rem;">PRESENÇAS</small>
                  <span class="fw-bold font-monospace text-success">${dep.presencas}</span>
                </div>
                <div class="col-3">
                  <small class="text-muted d-block" style="font-size: 0.7rem;">CANCELAMENTOS</small>
                  <span class="fw-bold font-monospace ${dep.cancelados > 0 ? 'text-danger' : 'text-muted'}">${dep.cancelados}</span>
                </div>
                <div class="col-3">
                  <small class="text-muted d-block" style="font-size: 0.7rem;">ASSIDUIDADE</small>
                  <span class="fw-bold font-monospace text-primary">${dep.taxa_assiduidade}%</span>
                </div>
              </div>
              ${subareasHtml}
            </div>
          </div>
        `;
      });
    } else {
      htmlDeps = `
        <div class="alert alert-light border rounded-3 p-3 text-muted small">
          Este voluntário não possui departamentos ou histórico vinculado no período selecionado.
        </div>
      `;
    }

    corpo.innerHTML = `
      <!-- Perfil do Voluntário no Topo -->
      <div class="d-flex align-items-center gap-3 p-3 bg-body-tertiary rounded-4 mb-4">
        <img src="${v.foto_url}" class="rounded-circle shadow-sm" style="width: 64px; height: 64px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(v.nome_completo)}&background=2563eb&color=fff&size=80&bold=true'">
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <h5 class="fw-bold mb-0 text-body">${v.nome_completo}</h5>
            ${v.nickname ? `<span class="badge bg-secondary-subtle text-secondary rounded-pill">"${v.nickname}"</span>` : ''}
            <span class="badge level-badge-${v.nivel_conhecimento.toLowerCase()} rounded-pill px-2.5 py-1">${v.nivel_conhecimento}</span>
            ${v.is_multidepartamento ? `<span class="badge multi-badge rounded-pill px-2.5 py-1">Atua em ${v.total_departamentos_atuacao} Departamentos</span>` : ''}
          </div>
          <div class="text-muted small mt-1">
            <i class="bi bi-calendar-event me-1"></i> Período Analisado: <strong>${dados.labelPeriodo}</strong>
          </div>
        </div>
        <div class="text-end">
          <div class="small text-muted fw-bold">POSIÇÃO GERAL</div>
          <h3 class="fw-bold text-warning mb-0 font-monospace">
            ${v.posicao_global ? '#' + v.posicao_global : 'N/C'}
          </h3>
          <small class="text-muted" style="font-size: 0.72rem;">de ${v.total_geral_voluntarios} voluntários</small>
        </div>
      </div>

      <!-- Resumo Geral de Pontuação & Agilidade -->
      <div class="row g-2 mb-4 text-center">
        <div class="col-6 col-md-2">
          <div class="p-2.5 bg-body border rounded-3 h-100">
            <small class="text-muted d-block" style="font-size: 0.7rem;">PONTOS GERAIS</small>
            <h4 class="fw-bold text-warning mb-0 font-monospace">${v.pontos_globais || v.pontos} <span class="fs-6">pts</span></h4>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="p-2.5 bg-body border rounded-3 h-100">
            <small class="text-muted d-block" style="font-size: 0.7rem;">ESCALAS</small>
            <h4 class="fw-bold text-body mb-0 font-monospace">${v.total_escalas}</h4>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="p-2.5 bg-body border rounded-3 h-100">
            <small class="text-muted d-block" style="font-size: 0.7rem;">PRESENÇAS</small>
            <h4 class="fw-bold text-success mb-0 font-monospace">${v.presencas}</h4>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="p-2.5 bg-body border rounded-3 h-100">
            <small class="text-muted d-block" style="font-size: 0.7rem;">ASSIDUIDADE</small>
            <h4 class="fw-bold text-primary mb-0 font-monospace">${v.taxa_assiduidade}%</h4>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="p-2.5 bg-body border rounded-3 h-100" title="Tempo médio de resposta">
            <small class="text-muted d-block" style="font-size: 0.7rem;">TEMPO RESPOSTA</small>
            <h4 class="fw-bold text-info mb-0 font-monospace">${v.tempo_medio_resposta_formatado || '--'}</h4>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="p-2.5 bg-body border rounded-3 h-100" title="Bônus conquistados por aceites em até 24h">
            <small class="text-muted d-block" style="font-size: 0.7rem;">BÔNUS AGILIDADE</small>
            <h4 class="fw-bold text-success mb-0 font-monospace">+${v.bonus_agilidade_pontos || 0} <span class="fs-6">pts</span></h4>
          </div>
        </div>
      </div>

      <!-- Desempenho e Ranking nos Departamentos -->
      <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-diagram-3-fill"></i>
        <span>Ranking nos Departamentos onde o Voluntário Serve</span>
      </h6>

      ${htmlDeps}
    `;
  }
</script>
