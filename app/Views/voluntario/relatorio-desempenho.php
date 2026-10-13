<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-person-lines-fill text-primary me-2"></i>Desempenho & Cancelamentos
        </h3>
        <p class="text-secondary small mb-0">Controle de assiduidade, presenças, recusas e justificativas de cancelamento dos voluntários</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('voluntario') ?>">Voluntários</a></li>
          <li class="breadcrumb-item active" aria-current="page">Desempenho</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Card de Filtros Avançados -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
      <div class="card-header bg-body py-3">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="card-title fw-bold mb-0 text-primary">
            <i class="bi bi-funnel-fill me-2"></i> Filtros do Relatório
          </h5>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="window.print()" title="Imprimir Relatório">
              <i class="bi bi-printer me-1"></i> Imprimir
            </button>
            <a href="<?= base_url('voluntario/ranking') ?>" class="btn btn-sm btn-warning text-dark rounded-pill fw-semibold shadow-xs">
              <i class="bi bi-trophy-fill me-1"></i> Ranking
            </a>
            <a href="<?= base_url('voluntario') ?>" class="btn btn-sm btn-outline-primary rounded-pill">
              <i class="bi bi-people me-1"></i> Lista de Voluntários
            </a>
          </div>
        </div>
      </div>
      <div class="card-body p-3 p-md-4">
        <form action="<?= base_url('voluntario/desempenho') ?>" method="GET" id="formFiltroDesempenho">

          <div class="row g-3">

            <!-- Período Rápido -->
            <div class="col-lg-2 col-md-6">
              <label class="form-label small fw-bold text-muted">Período</label>
              <select name="periodo" id="selectPeriodo" class="form-select rounded-3 shadow-none" onchange="toggleDatasCustom(this.value)">
                <option value="mes_atual" <?= ($filtros['periodo'] === 'mes_atual') ? 'selected' : '' ?>>Mês Atual (<?= date('m/Y') ?>)</option>
                <option value="mes_anterior" <?= ($filtros['periodo'] === 'mes_anterior') ? 'selected' : '' ?>>Mês Anterior</option>
                <option value="ultimos_3_meses" <?= ($filtros['periodo'] === 'ultimos_3_meses') ? 'selected' : '' ?>>Últimos 3 Meses</option>
                <option value="ano_atual" <?= ($filtros['periodo'] === 'ano_atual') ? 'selected' : '' ?>>Ano Atual (<?= date('Y') ?>)</option>
                <option value="tudo" <?= ($filtros['periodo'] === 'tudo') ? 'selected' : '' ?>>Todo o Histórico</option>
                <option value="custom" <?= ($filtros['periodo'] === 'custom') ? 'selected' : '' ?>>Personalizado</option>
              </select>
            </div>

            <!-- Departamento -->
            <div class="col-lg-3 col-md-6">
              <label class="form-label small fw-bold text-muted">Departamento</label>
              <select name="id_departamento" class="form-select rounded-3 shadow-none">
                <option value="">Todos os Departamentos</option>
                <?php if (!empty($departamentos)) { ?>
                  <?php foreach ($departamentos as $dep) { ?>
                    <option value="<?= $dep->id_departamento ?>" <?= ($filtros['id_departamento'] == $dep->id_departamento) ? 'selected' : '' ?>>
                      <?= esc($dep->nome) ?>
                    </option>
                  <?php } ?>
                <?php } ?>
              </select>
            </div>

            <!-- Status da Análise da Justificativa -->
            <div class="col-lg-2 col-md-6">
              <label class="form-label small fw-bold text-muted">Análise de Recusas</label>
              <select name="status_analise" class="form-select rounded-3 shadow-none">
                <option value="todos" <?= (($filtros['status_analise'] ?? 'todos') === 'todos') ? 'selected' : '' ?>>Todas as Recusas</option>
                <option value="PENDENTE" <?= (($filtros['status_analise'] ?? '') === 'PENDENTE') ? 'selected' : '' ?>>⏳ Pendentes (<?= $relatorio->kpis->totalCancelamentosPendentes ?? 0 ?>)</option>
                <option value="ABONADO" <?= (($filtros['status_analise'] ?? '') === 'ABONADO') ? 'selected' : '' ?>>🛡️ Abonadas (<?= $relatorio->kpis->totalCancelamentosAbonados ?? 0 ?>)</option>
                <option value="DESCONTADO" <?= (($filtros['status_analise'] ?? '') === 'DESCONTADO') ? 'selected' : '' ?>>📉 Com Desconto (<?= $relatorio->kpis->totalCancelamentosDescontados ?? 0 ?>)</option>
              </select>
            </div>

            <!-- Busca por Nome / Nick / Whats -->
            <div class="col-lg-3 col-md-6">
              <label class="form-label small fw-bold text-muted">Buscar Voluntário</label>
              <input type="text" name="busca" class="form-control rounded-3 shadow-none" placeholder="Nome, apelido ou fone..." value="<?= esc($filtros['busca'] ?? '') ?>">
            </div>

            <!-- Ordenação -->
            <div class="col-lg-2 col-md-6">
              <label class="form-label small fw-bold text-muted">Ordenar Por</label>
              <select name="ordem" class="form-select rounded-3 shadow-none">
                <option value="cancelamentos_desc" <?= ($filtros['ordem'] === 'cancelamentos_desc') ? 'selected' : '' ?>>Mais Recusas</option>
                <option value="assiduidade_asc" <?= ($filtros['ordem'] === 'assiduidade_asc') ? 'selected' : '' ?>>Menor Assiduidade</option>
                <option value="assiduidade_desc" <?= ($filtros['ordem'] === 'assiduidade_desc') ? 'selected' : '' ?>>Maior Assiduidade</option>
                <option value="escalas_desc" <?= ($filtros['ordem'] === 'escalas_desc') ? 'selected' : '' ?>>Mais Escalações</option>
                <option value="nome_asc" <?= ($filtros['ordem'] === 'nome_asc') ? 'selected' : '' ?>>Nome (A-Z)</option>
              </select>
            </div>

            <!-- Intervalo Personalizado de Datas -->
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

            <!-- Filtros Extras & Botões -->
            <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="apenasCancelamentos" name="apenas_cancelamentos" value="1" <?= (!empty($filtros['apenas_cancelamentos'])) ? 'checked' : '' ?>>
                <label class="form-check-label fw-bold text-danger" for="apenasCancelamentos">
                  <i class="bi bi-exclamation-triangle-fill me-1"></i> Exibir apenas voluntários com cancelamentos / recusas
                </label>
              </div>

              <div class="d-flex gap-2">
                <a href="<?= base_url('voluntario/desempenho') ?>" class="btn btn-light rounded-pill px-3">Limpar</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                  <i class="bi bi-search me-1"></i> Filtrar
                </button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
      <div class="col-lg-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Total de Escalações</p>
                <h3 class="fw-bold mb-0"><?= $relatorio->kpis->totalEscalas ?></h3>
                <small class="text-white-50"><?= $relatorio->labelPeriodo ?></small>
              </div>
              <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                <i class="bi bi-calendar-check fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-success text-white">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Presenças / Confirmações</p>
                <h3 class="fw-bold mb-0"><?= $relatorio->kpis->totalConfirmados ?></h3>
                <small class="text-white-50"><?= $relatorio->kpis->totalPresencas ?> presenças executadas</small>
              </div>
              <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                <i class="bi bi-check-circle-fill fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-danger text-white">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <p class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Cancelamentos / Recusas</p>
                <h3 class="fw-bold mb-0"><?= $relatorio->kpis->totalCancelamentos ?></h3>
              </div>
              <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                <i class="bi bi-x-circle-fill fs-4"></i>
              </div>
            </div>
            <div class="d-flex flex-wrap gap-1 mt-1 small">
              <span class="badge bg-white text-danger fw-bold" title="Aguardando análise da liderança"><?= $relatorio->kpis->totalCancelamentosPendentes ?? 0 ?> pendentes</span>
              <span class="badge bg-black bg-opacity-25 text-white" title="Abonadas sem desconto no ranking"><?= $relatorio->kpis->totalCancelamentosAbonados ?? 0 ?> abonadas</span>
              <span class="badge bg-black bg-opacity-25 text-white" title="Com desconto de pontos confirmado"><?= $relatorio->kpis->totalCancelamentosDescontados ?? 0 ?> c/ desconto</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-info text-white">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Taxa de Assiduidade</p>
                <h3 class="fw-bold mb-0"><?= $relatorio->kpis->taxaAssiduidadeGeral ?>%</h3>
                <small class="text-white-50"><?= count($relatorio->listaDesempenho) ?> voluntários avaliados</small>
              </div>
              <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                <i class="bi bi-pie-chart-fill fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Abas de Visualização -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-body p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs m-0 border-bottom-0" id="desempenhoTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold py-3 px-4" id="tabela-tab" data-bs-toggle="tab" data-bs-target="#tabela-pane" type="button" role="tab">
              <i class="bi bi-table me-2 text-primary"></i>Desempenho por Voluntário (<?= count($relatorio->listaDesempenho) ?>)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold py-3 px-4 position-relative" id="cancelamentos-tab" data-bs-toggle="tab" data-bs-target="#cancelamentos-pane" type="button" role="tab">
              <i class="bi bi-chat-left-quote-fill me-2 text-danger"></i>Mural de Justificativas de Cancelamento
              <?php if (!empty($relatorio->kpis->totalCancelamentosPendentes)) { ?>
                <span class="badge bg-warning text-dark rounded-pill ms-2" title="Justificativas aguardando sua análise para não descontar pontos">
                  <?= $relatorio->kpis->totalCancelamentosPendentes ?> pendentes
                </span>
              <?php } elseif (!empty($relatorio->kpis->totalCancelamentos)) { ?>
                <span class="badge bg-secondary rounded-pill ms-2"><?= $relatorio->kpis->totalCancelamentos ?></span>
              <?php } ?>
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body p-0">
        <div class="tab-content" id="desempenhoTabsContent">

          <!-- ========================================== -->
          <!-- ABA 1: TABELA DE DESEMPENHO POR VOLUNTÁRIO -->
          <!-- ========================================== -->
          <div class="tab-pane fade show active" id="tabela-pane" role="tabpanel" tabindex="0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-4">Voluntário</th>
                    <th>Departamentos & Áreas</th>
                    <th class="text-center">Escalas</th>
                    <th class="text-center">Confirmadas</th>
                    <th class="text-center">Canceladas / Recusas</th>
                    <th class="text-center">Pendentes</th>
                    <th class="text-center" style="min-width: 140px;">Assiduidade</th>
                    <th class="text-end pe-4">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($relatorio->listaDesempenho)) { ?>
                    <?php foreach ($relatorio->listaDesempenho as $d) {
                      $v = $d->voluntario;
                      $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($v->nome) . '&background=2563eb&color=fff&size=80&bold=true';
                      $avatarSrc = !empty($v->foto_url) ? $v->foto_url : $defaultAvatar;
                      $corTaxa = ($d->taxaAssiduidade >= 80) ? 'success' : (($d->taxaAssiduidade >= 50) ? 'warning' : 'danger');
                    ?>
                      <tr class="<?= ($d->totalCancelamentos > 0) ? 'table-danger-subtle' : '' ?>">

                        <!-- Voluntário -->
                        <td class="ps-4">
                          <div class="d-flex align-items-center gap-3">
                            <img src="<?= esc($avatarSrc) ?>" class="rounded-circle border shadow-sm" style="width: 44px; height: 44px; object-fit: cover;" alt="avatar" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
                            <div>
                              <div class="fw-bold text-body">
                                <?= esc($v->nome) ?>
                                <?php if (!empty($v->nickname)) { ?>
                                  <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-0 small ms-1">"<?= esc($v->nickname) ?>"</span>
                                <?php } ?>
                              </div>
                              <div class="d-flex align-items-center gap-2 small text-muted">
                                <span><i class="bi bi-mortarboard me-1"></i><?= esc($v->nivel_conhecimento ?? 'JUNIOR') ?></span>
                                &bull;
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $v->telefone_whatsapp) ?>" target="_blank" class="text-success text-decoration-none fw-semibold">
                                  <i class="bi bi-whatsapp me-1"></i><?= esc($v->telefone_whatsapp) ?>
                                </a>
                              </div>
                            </div>
                          </div>
                        </td>

                        <!-- Departamentos & Áreas -->
                        <td>
                          <?php if (!empty($d->areas)) { ?>
                            <div class="d-flex flex-wrap gap-1">
                              <?php foreach ($d->areas as $va) {
                                $corDep = !empty($va->cor_identificacao) ? $va->cor_identificacao : '#2563eb';
                              ?>
                                <span class="badge rounded-pill text-white px-2 py-1 small" style="background-color: <?= esc($corDep) ?>; font-size: 0.72rem;">
                                  <?= esc($va->nome_area) ?>
                                </span>
                              <?php } ?>
                            </div>
                          <?php } else { ?>
                            <span class="text-muted small">Sem áreas vinculadas</span>
                          <?php } ?>
                        </td>

                        <!-- Total Escalas -->
                        <td class="text-center fw-bold fs-6">
                          <?= $d->totalEscalas ?>
                        </td>

                        <!-- Confirmadas -->
                        <td class="text-center">
                          <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                            <?= $d->totalConfirmados ?>
                          </span>
                        </td>

                        <!-- Canceladas / Recusas -->
                        <td class="text-center">
                          <?php if ($d->totalCancelamentos > 0) { ?>
                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold shadow-sm" onclick="abrirModalJustificativas(<?= $v->id_voluntario ?>)" title="Ver justificativas de recusa">
                              <i class="bi bi-x-circle me-1"></i> <?= $d->totalCancelamentos ?> recusa(s)
                            </button>
                            <div class="d-flex flex-column align-items-center gap-1 mt-1">
                              <?php if (!empty($d->totalCancelamentosAbonados)) { ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 small" title="Justificativas abonadas: sem desconto de pontos" style="font-size: 0.68rem;">
                                  <i class="bi bi-shield-check me-0.5"></i> <?= $d->totalCancelamentosAbonados ?> abon.
                                </span>
                              <?php } ?>
                              <?php if (!empty($d->totalCancelamentosPendentes)) { ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 small" title="Aguardando análise da liderança" style="font-size: 0.68rem;">
                                  <i class="bi bi-hourglass-split me-0.5"></i> <?= $d->totalCancelamentosPendentes ?> pend.
                                </span>
                              <?php } ?>
                            </div>
                          <?php } else { ?>
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-1">0</span>
                          <?php } ?>
                        </td>

                        <!-- Pendentes -->
                        <td class="text-center">
                          <?php if ($d->totalPendentes > 0) { ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">
                              <?= $d->totalPendentes ?>
                            </span>
                          <?php } else { ?>
                            <span class="text-muted small">0</span>
                          <?php } ?>
                        </td>

                        <!-- Assiduidade -->
                        <td class="text-center">
                          <div class="d-flex flex-column align-items-center">
                            <span class="fw-bold text-<?= $corTaxa ?> mb-1"><?= $d->taxaAssiduidade ?>%</span>
                            <div class="progress w-100" style="height: 6px;">
                              <div class="progress-bar bg-<?= $corTaxa ?>" role="progressbar" style="width: <?= $d->taxaAssiduidade ?>%;"></div>
                            </div>
                          </div>
                        </td>

                        <!-- Ações -->
                        <td class="text-end pe-4">
                          <div class="d-inline-flex gap-1">
                            <?php if ($d->totalCancelamentos > 0) { ?>
                              <button type="button" class="btn btn-action btn-outline-danger" onclick="abrirModalJustificativas(<?= $v->id_voluntario ?>)" title="Ver Motivos de Cancelamento">
                                <i class="bi bi-chat-left-dots-fill"></i>
                              </button>
                            <?php } ?>
                            <a href="<?= base_url('voluntario/dashVoluntario/' . $v->id_voluntario) ?>" class="btn btn-action btn-outline-primary" title="Dashboard do Voluntário">
                              <i class="bi bi-graph-up-arrow"></i>
                            </a>
                            <a href="<?= base_url('voluntario/editar/' . ($v->hash_voluntario ?: $v->id_voluntario)) ?>" class="btn btn-action btn-outline-secondary" title="Editar Cadastro">
                              <i class="bi bi-pencil-square"></i>
                            </a>
                          </div>
                        </td>

                      </tr>
                    <?php } ?>
                  <?php } else { ?>
                    <tr>
                      <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                        Nenhum registro de desempenho encontrado para os filtros aplicados.
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- ========================================== -->
          <!-- ABA 2: MURAL DE JUSTIFICATIVAS DE RECUSA   -->
          <!-- ========================================== -->
          <div class="tab-pane fade" id="cancelamentos-pane" role="tabpanel" tabindex="0">
            <div class="p-4">
              <?php if (!empty($relatorio->todosCancelamentos)) { ?>
                <div class="row g-3">
                  <?php foreach ($relatorio->todosCancelamentos as $tc) {
                    $vNome = $tc->voluntario ? $tc->voluntario->nome : 'Voluntário';
                    $vNick = $tc->voluntario ? $tc->voluntario->nickname : '';
                    $vFone = $tc->voluntario ? $tc->voluntario->telefone_whatsapp : '';
                    $vFoto = ($tc->voluntario && !empty($tc->voluntario->foto_url)) ? $tc->voluntario->foto_url : 'https://ui-avatars.com/api/?name=' . urlencode($vNome) . '&background=dc2626&color=fff&size=80&bold=true';
                    $stAnalise = strtoupper((string)($tc->status_analise_justificativa ?: 'PENDENTE'));
                  ?>
                    <div class="col-lg-6" id="mural-card-<?= $tc->id_escala_voluntario ?>">
                      <div class="card border border-danger-subtle shadow-sm rounded-4 h-100 bg-body">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                          <div>
                            <!-- Header do Card -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                              <div class="d-flex align-items-center gap-3">
                                <img src="<?= esc($vFoto) ?>" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;" alt="avatar">
                                <div>
                                  <h6 class="fw-bold mb-0 text-body">
                                    <?= esc($vNome) ?>
                                    <?php if ($vNick) { ?>
                                      <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-0 small">"<?= esc($vNick) ?>"</span>
                                    <?php } ?>
                                  </h6>
                                  <div class="text-muted small">
                                    <a href="https://wa.me/55<?= preg_replace('/\D/', '', $vFone) ?>" target="_blank" class="text-success text-decoration-none fw-semibold">
                                      <i class="bi bi-whatsapp me-1"></i><?= esc($vFone) ?>
                                    </a>
                                  </div>
                                </div>
                              </div>

                              <div class="text-end">
                                <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1 fw-bold d-block mb-1">
                                  <i class="bi bi-x-circle-fill me-1"></i> Cancelado
                                </span>
                                <?php if ($stAnalise === 'ABONADO') { ?>
                                  <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                    <i class="bi bi-shield-check me-1"></i>Abonada (Sem Desconto)
                                  </span>
                                <?php } elseif ($stAnalise === 'DESCONTADO') { ?>
                                  <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small fw-bold">
                                    <i class="bi bi-dash-circle-fill me-1"></i>Desconto Confirmado (-10 pts)
                                  </span>
                                <?php } else { ?>
                                  <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small fw-bold">
                                    <i class="bi bi-hourglass-split me-1"></i>Aguardando Análise
                                  </span>
                                <?php } ?>
                              </div>
                            </div>

                            <!-- Detalhes do Culto -->
                            <div class="p-3 bg-body-tertiary rounded-3 border mb-3">
                              <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-primary font-monospace">
                                  <i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y', strtotime($tc->data_culto)) ?>
                                </span>
                                <span class="badge rounded-pill text-white px-2 py-1 small" style="background-color: <?= esc($tc->cor_departamento ?: '#2563eb') ?>;">
                                  <?= esc($tc->nome_departamento) ?>: <?= esc($tc->nome_area) ?>
                                </span>
                              </div>
                              <div class="fw-semibold text-body"><?= esc($tc->titulo_culto) ?></div>
                              <small class="text-muted"><i class="bi bi-clock me-1"></i><?= substr($tc->horario_inicio, 0, 5) ?> às <?= substr($tc->horario_termino, 0, 5) ?></small>
                            </div>

                            <!-- Justificativa do Voluntário -->
                            <div class="mb-3">
                              <div class="text-danger fw-bold small text-uppercase mb-1">
                                <i class="bi bi-chat-quote-fill me-1"></i> Motivo / Justificativa Informada:
                              </div>
                              <blockquote class="blockquote fs-6 p-3 bg-danger-subtle rounded-3 border border-danger-subtle text-danger-emphasis mb-2 fst-italic">
                                "<?= nl2br(esc($tc->justificativa_recusa ?: 'Nenhuma justificativa textual preenchida.')) ?>"
                              </blockquote>
                              <?php if (!empty($tc->data_resposta)) { ?>
                                <div class="text-end text-muted" style="font-size: 0.75rem;">
                                  <i class="bi bi-clock-history me-1"></i> Respondido em: <?= date('d/m/Y H:i', strtotime($tc->data_resposta)) ?>
                                </div>
                              <?php } ?>
                            </div>

                            <!-- Parecer da Gestão / Analisador -->
                            <?php if ($stAnalise !== 'PENDENTE') { ?>
                              <div class="p-3 bg-body-secondary rounded-3 border small mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                  <span class="fw-bold text-body">
                                    <i class="bi bi-person-check-fill text-primary me-1"></i> Analisado por: <?= esc($tc->nome_analisador ?: 'Gestor') ?>
                                  </span>
                                  <?php if (!empty($tc->justificativa_analisado_em)) { ?>
                                    <span class="text-muted" style="font-size: 0.72rem;"><?= date('d/m/Y H:i', strtotime($tc->justificativa_analisado_em)) ?></span>
                                  <?php } ?>
                                </div>
                                <?php if (!empty($tc->justificativa_parecer_gestor)) { ?>
                                  <div class="text-secondary fst-italic">
                                    <i class="bi bi-chat-left-text me-1"></i> Parecer: "<?= esc($tc->justificativa_parecer_gestor) ?>"
                                  </div>
                                <?php } ?>
                              </div>
                            <?php } ?>
                          </div>

                          <!-- Barra de Ações de Análise -->
                          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 border-top mt-2">
                            <div class="small">
                              <span class="text-muted">Impacto no Ranking:</span>
                              <?php if ($stAnalise === 'ABONADO') { ?>
                                <strong class="text-success ms-1"><i class="bi bi-shield-check"></i> 0 pts descontados</strong>
                              <?php } else { ?>
                                <strong class="text-danger ms-1"><i class="bi bi-dash-circle"></i> -10 pts</strong>
                              <?php } ?>
                            </div>
                            <div class="d-flex gap-2">
                              <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-semibold" onclick="abrirModalParecer(<?= $tc->id_escala_voluntario ?>, '<?= esc(addslashes($vNome)) ?>', '<?= date('d/m/Y', strtotime($tc->data_culto)) ?> - <?= esc(addslashes($tc->titulo_culto)) ?>', 'ABONADO', '<?= esc(addslashes($tc->justificativa_parecer_gestor ?? '')) ?>')">
                                <i class="bi bi-shield-check me-1"></i> <?= ($stAnalise === 'ABONADO') ? 'Editar Parecer' : 'Abonar (0 pts)' ?>
                              </button>
                              <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold" onclick="abrirModalParecer(<?= $tc->id_escala_voluntario ?>, '<?= esc(addslashes($vNome)) ?>', '<?= date('d/m/Y', strtotime($tc->data_culto)) ?> - <?= esc(addslashes($tc->titulo_culto)) ?>', 'DESCONTADO', '<?= esc(addslashes($tc->justificativa_parecer_gestor ?? '')) ?>')">
                                <i class="bi bi-dash-circle me-1"></i> <?= ($stAnalise === 'DESCONTADO') ? 'Editar Parecer' : 'Descontar (-10 pts)' ?>
                              </button>
                              <?php if ($stAnalise !== 'PENDENTE') { ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="reverterAnalise(<?= $tc->id_escala_voluntario ?>)" title="Reverter para pendente">
                                  <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                              <?php } ?>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="text-center py-5 text-muted">
                  <i class="bi bi-check2-circle fs-1 text-success d-block mb-2"></i>
                  <h5 class="fw-bold text-success">Nenhum cancelamento registrado</h5>
                  <p class="small mb-0">Não foram registradas recusas de escala no período selecionado.</p>
                </div>
              <?php } ?>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal Justificativas do Voluntário -->
<div class="modal fade" id="modalJustificativasVoluntario" tabindex="-1" aria-labelledby="modalJustificativasLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 border-0 shadow">

      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title fw-bold" id="modalJustificativasLabel">
          <i class="bi bi-chat-square-quote-fill me-2"></i> Motivos de Cancelamento do Voluntário
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body p-4">

        <!-- Header do Voluntário Selecionado -->
        <div class="d-flex align-items-center gap-3 p-3 bg-body-tertiary rounded-3 border mb-4" id="modalVoluntarioHeader">
          <img src="" id="modalVolFoto" class="rounded-circle border" style="width: 54px; height: 54px; object-fit: cover;" alt="avatar">
          <div class="flex-grow-1">
            <h5 class="fw-bold mb-0 text-body" id="modalVolNome">-</h5>
            <div class="text-muted small" id="modalVolSub">-</div>
          </div>
          <div>
            <a href="#" target="_blank" id="modalVolWhatsLink" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
              <i class="bi bi-whatsapp me-1"></i> Contatar
            </a>
          </div>
        </div>

        <!-- Lista de Cancelamentos -->
        <div id="modalLoadingCancelamentos" class="text-center py-4">
          <div class="spinner-border text-danger" role="status"></div>
          <p class="text-muted small mt-2">Carregando histórico de recusas...</p>
        </div>

        <div id="modalListaCancelamentos" class="d-none">
          <!-- Conteúdo gerado via JS -->
        </div>

      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Decisão e Parecer da Análise da Liderança -->
<div class="modal fade" id="modalParecerAnalise" tabindex="-1" aria-labelledby="modalParecerLabel" aria-hidden="true" style="z-index: 1065;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <form id="formAnaliseJustificativa" onsubmit="event.preventDefault(); submeterAnaliseJustificativa();">
        <input type="hidden" name="id_escala_voluntario" id="analise_id_escala">

        <div class="modal-header bg-primary text-white border-0">
          <h5 class="modal-title fw-bold" id="modalParecerLabel">
            <i class="bi bi-shield-check me-2"></i> Analisar Justificativa de Cancelamento
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body p-4">
          <div class="p-3 bg-body-tertiary rounded-3 border mb-3">
            <div class="small text-muted text-uppercase fw-semibold mb-1">Voluntário & Escala</div>
            <div class="fw-bold text-body" id="analise_nome_voluntario">-</div>
            <div class="small text-muted" id="analise_info_culto">-</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold small text-uppercase text-muted">Decisão da Liderança:</label>
            <div class="vstack gap-2">
              <label class="d-flex align-items-start gap-3 p-3 border rounded-3 bg-body" style="cursor: pointer;">
                <input class="form-check-input mt-1" type="radio" name="decisao" id="decisao_abonado" value="ABONADO" checked>
                <div>
                  <div class="fw-bold text-success"><i class="bi bi-shield-check me-1"></i> Abonar (Não Descontar Pontos)</div>
                  <small class="text-muted d-block">A justificativa foi considerada válida (saúde, trabalho, luto, etc.). O cancelamento <strong>NÃO</strong> retirará pontos do ranking.</small>
                </div>
              </label>

              <label class="d-flex align-items-start gap-3 p-3 border rounded-3 bg-body" style="cursor: pointer;">
                <input class="form-check-input mt-1" type="radio" name="decisao" id="decisao_descontado" value="DESCONTADO">
                <div>
                  <div class="fw-bold text-danger"><i class="bi bi-dash-circle-fill me-1"></i> Confirmar Desconto (-10 pts)</div>
                  <small class="text-muted d-block">A justificativa não foi acolhida ou foi feita fora do prazo acordado. A penalidade de 10 pontos será <strong>mantida</strong> no ranking.</small>
                </div>
              </label>
            </div>
          </div>

          <div class="mb-0">
            <label for="analise_parecer" class="form-label fw-bold small text-uppercase text-muted">Parecer / Observação da Liderança (Opcional):</label>
            <textarea class="form-control rounded-3" id="analise_parecer" name="parecer" rows="3" placeholder="Ex: Justificativa médica apresentada e aceita pelo coordenador..."></textarea>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnSalvarAnalise">
            <i class="bi bi-check-lg me-1"></i> Confirmar Decisão
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  let modalParecerInstance = null;

  function notificar(tipo, titulo, msg) {
    if (typeof USToast !== 'undefined' && USToast.show) {
      USToast.show(tipo, titulo, msg);
    } else if (typeof usShowToast === 'function') {
      usShowToast(tipo, titulo, msg);
    } else {
      console.log(tipo, titulo, msg);
    }
  }

  function toggleDatasCustom(val) {
    const div = document.getElementById('divDatasCustom');
    if (val === 'custom') {
      div.classList.remove('d-none');
    } else {
      div.classList.add('d-none');
    }
  }

  function abrirModalJustificativas(idVoluntario) {
    const modalEl = document.getElementById('modalJustificativasVoluntario');
    const modal = new bootstrap.Modal(modalEl);

    document.getElementById('modalLoadingCancelamentos').classList.remove('d-none');
    document.getElementById('modalListaCancelamentos').classList.add('d-none');
    document.getElementById('modalListaCancelamentos').innerHTML = '';

    modal.show();

    fetch('<?= base_url('voluntario/getJustificativas') ?>/' + idVoluntario, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(r => r.json())
      .then(data => {
        document.getElementById('modalLoadingCancelamentos').classList.add('d-none');
        const container = document.getElementById('modalListaCancelamentos');
        container.classList.remove('d-none');

        if (data.status === 'success') {
          const v = data.voluntario;
          const defaultAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(v.nome) + '&background=dc2626&color=fff&size=80&bold=true';
          document.getElementById('modalVolFoto').src = v.foto_url || defaultAvatar;
          document.getElementById('modalVolNome').textContent = v.nome + (v.nickname ? ` ("${v.nickname}")` : '');
          document.getElementById('modalVolSub').textContent = v.telefone || 'Sem telefone';

          const whatsLimpo = (v.telefone || '').replace(/\D/g, '');
          document.getElementById('modalVolWhatsLink').href = 'https://wa.me/55' + whatsLimpo;

          if (data.cancelamentos && data.cancelamentos.length > 0) {
            let html = '<div class="vstack gap-3">';
            data.cancelamentos.forEach(c => {
              const dtCulto = c.data_culto ? c.data_culto.split('-').reverse().join('/') : '-';
              const hora = (c.horario_inicio || '').substring(0, 5) + ' às ' + (c.horario_termino || '').substring(0, 5);
              const corDep = c.cor_departamento || '#2563eb';
              const just = c.justificativa_recusa || 'Sem justificativa informada.';
              const dtResp = c.data_resposta ? new Date(c.data_resposta).toLocaleString('pt-BR') : 'Não registrada';

              const stAn = (c.status_analise_justificativa || 'PENDENTE').toUpperCase();
              let badgeStatus = '';
              let pontosTexto = '';
              if (stAn === 'ABONADO') {
                badgeStatus = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-bold"><i class="bi bi-shield-check me-1"></i>Abonada (Sem Desconto)</span>';
                pontosTexto = '<strong class="text-success"><i class="bi bi-shield-check"></i> 0 pts descontados</strong>';
              } else if (stAn === 'DESCONTADO') {
                badgeStatus = '<span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small fw-bold"><i class="bi bi-dash-circle-fill me-1"></i>Desconto Confirmado (-10 pts)</span>';
                pontosTexto = '<strong class="text-danger"><i class="bi bi-dash-circle"></i> -10 pts</strong>';
              } else {
                badgeStatus = '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small fw-bold"><i class="bi bi-hourglass-split me-1"></i>Aguardando Análise</span>';
                pontosTexto = '<strong class="text-warning-emphasis"><i class="bi bi-hourglass-split"></i> Provisório (-10 pts)</strong>';
              }

              let parecerBox = '';
              if (stAn !== 'PENDENTE') {
                parecerBox = `
                  <div class="p-2.5 bg-body-secondary rounded-3 border small mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="fw-bold text-body"><i class="bi bi-person-check-fill text-primary me-1"></i> Analisado por: ${c.nome_analisador || 'Gestor'}</span>
                      ${c.justificativa_analisado_em ? `<span class="text-muted" style="font-size: 0.72rem;">${new Date(c.justificativa_analisado_em).toLocaleString('pt-BR')}</span>` : ''}
                    </div>
                    ${c.justificativa_parecer_gestor ? `<div class="text-secondary fst-italic">"${c.justificativa_parecer_gestor}"</div>` : ''}
                  </div>
                `;
              }

              const safeVolNome = (v.nome || '').replace(/'/g, "\\'");
              const safeCultoInfo = `${dtCulto} - ${(c.titulo_culto || '').replace(/'/g, "\\'")}`;
              const safeParecer = (c.justificativa_parecer_gestor || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');

              html += `
              <div class="card border border-danger-subtle rounded-3 shadow-none bg-body">
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <span class="fw-bold text-primary font-monospace">
                      <i class="bi bi-calendar-event me-1"></i> ${dtCulto} &bull; ${hora}
                    </span>
                    <div class="d-flex align-items-center gap-2">
                      <span class="badge rounded-pill text-white px-2 py-1 small" style="background-color: ${corDep};">
                        ${c.nome_departamento}: ${c.nome_area}
                      </span>
                      ${badgeStatus}
                    </div>
                  </div>
                  <div class="fw-semibold text-body mb-2">${c.titulo_culto}</div>
                  <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle text-danger-emphasis small fst-italic mb-2">
                    <i class="bi bi-chat-quote-fill me-1 text-danger"></i> "${just}"
                  </div>
                  <div class="text-end text-muted mb-2" style="font-size: 0.72rem;">
                    <i class="bi bi-clock-history me-1"></i> Cancelado em: ${dtResp}
                  </div>
                  ${parecerBox}
                  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
                    <div class="small">
                      <span class="text-muted">Ranking:</span> ${pontosTexto}
                    </div>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-semibold" onclick="abrirModalParecer(${c.id_escala_voluntario}, '${safeVolNome}', '${safeCultoInfo}', 'ABONADO', '${safeParecer}')">
                        <i class="bi bi-shield-check me-1"></i> ${stAn === 'ABONADO' ? 'Editar Parecer' : 'Abonar (0 pts)'}
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold" onclick="abrirModalParecer(${c.id_escala_voluntario}, '${safeVolNome}', '${safeCultoInfo}', 'DESCONTADO', '${safeParecer}')">
                        <i class="bi bi-dash-circle me-1"></i> ${stAn === 'DESCONTADO' ? 'Editar Parecer' : 'Descontar (-10 pts)'}
                      </button>
                      ${stAn !== 'PENDENTE' ? `
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="reverterAnalise(${c.id_escala_voluntario})" title="Reverter para pendente">
                          <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                      ` : ''}
                    </div>
                  </div>
                </div>
              </div>
            `;
            });
            html += '</div>';
            container.innerHTML = html;
          } else {
            container.innerHTML = `
            <div class="text-center py-4 text-muted">
              <i class="bi bi-check-circle fs-2 text-success d-block mb-1"></i>
              Nenhum cancelamento encontrado para este voluntário.
            </div>
          `;
          }
        } else {
          container.innerHTML = `<div class="alert alert-danger">${data.message || 'Erro ao carregar dados.'}</div>`;
        }
      })
      .catch(err => {
        document.getElementById('modalLoadingCancelamentos').classList.add('d-none');
        document.getElementById('modalListaCancelamentos').classList.remove('d-none');
        document.getElementById('modalListaCancelamentos').innerHTML = `<div class="alert alert-danger">Erro de conexão ao carregar justificativas.</div>`;
      });
  }

  function abrirModalParecer(idEscala, nomeVoluntario, infoCulto, decisaoPrevia, parecerPrevio) {
    document.getElementById('analise_id_escala').value = idEscala;
    document.getElementById('analise_nome_voluntario').textContent = nomeVoluntario;
    document.getElementById('analise_info_culto').textContent = infoCulto;
    document.getElementById('analise_parecer').value = parecerPrevio || '';

    if (decisaoPrevia === 'DESCONTADO') {
      document.getElementById('decisao_descontado').checked = true;
    } else {
      document.getElementById('decisao_abonado').checked = true;
    }

    const modalEl = document.getElementById('modalParecerAnalise');
    modalParecerInstance = new bootstrap.Modal(modalEl);
    modalParecerInstance.show();
  }

  function submeterAnaliseJustificativa() {
    const form = document.getElementById('formAnaliseJustificativa');
    const btn = document.getElementById('btnSalvarAnalise');
    const formData = new FormData(form);

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

    fetch('<?= base_url('voluntario/analisarJustificativa') ?>', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Confirmar Decisão';

      if (data.status === 'success') {
        if (modalParecerInstance) modalParecerInstance.hide();
        notificar('success', 'Decisão Salva!', data.message);
        setTimeout(() => {
          window.location.reload();
        }, 700);
      } else {
        notificar('error', 'Atenção', data.message || 'Não foi possível salvar a decisão.');
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Confirmar Decisão';
      notificar('error', 'Erro de Conexão', 'Falha ao comunicar com o servidor.');
    });
  }

  function reverterAnalise(idEscala) {
    const formData = new FormData();
    formData.append('id_escala_voluntario', idEscala);
    formData.append('decisao', 'PENDENTE');

    fetch('<?= base_url('voluntario/analisarJustificativa') ?>', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(r => r.json())
    .then(data => {
      if (data.status === 'success') {
        notificar('success', 'Status Revertido', data.message);
        setTimeout(() => {
          window.location.reload();
        }, 700);
      } else {
        notificar('error', 'Atenção', data.message || 'Não foi possível reverter a análise.');
      }
    })
    .catch(err => {
      notificar('error', 'Erro de Conexão', 'Falha ao comunicar com o servidor.');
    });
  }
</script>