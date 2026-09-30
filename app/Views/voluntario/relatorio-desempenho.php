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
            <div class="col-lg-3 col-md-6">
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

            <!-- Busca por Nome / Nick / Whats -->
            <div class="col-lg-3 col-md-6">
              <label class="form-label small fw-bold text-muted">Buscar Voluntário</label>
              <input type="text" name="busca" class="form-control rounded-3 shadow-none" placeholder="Nome, apelido ou fone..." value="<?= esc($filtros['busca'] ?? '') ?>">
            </div>

            <!-- Ordenação -->
            <div class="col-lg-3 col-md-6">
              <label class="form-label small fw-bold text-muted">Ordenar Por</label>
              <select name="ordem" class="form-select rounded-3 shadow-none">
                <option value="cancelamentos_desc" <?= ($filtros['ordem'] === 'cancelamentos_desc') ? 'selected' : '' ?>>Mais Cancelamentos / Recusas</option>
                <option value="assiduidade_asc" <?= ($filtros['ordem'] === 'assiduidade_asc') ? 'selected' : '' ?>>Menor Assiduidade (%)</option>
                <option value="assiduidade_desc" <?= ($filtros['ordem'] === 'assiduidade_desc') ? 'selected' : '' ?>>Maior Assiduidade (%)</option>
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
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.75rem;">Cancelamentos / Recusas</p>
                <h3 class="fw-bold mb-0"><?= $relatorio->kpis->totalCancelamentos ?></h3>
                <small class="text-white-50">Taxa de recusa: <?= $relatorio->kpis->taxaCancelamentoGeral ?>%</small>
              </div>
              <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                <i class="bi bi-x-circle-fill fs-4"></i>
              </div>
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
              <?php if ($relatorio->kpis->totalCancelamentos > 0) { ?>
                <span class="badge bg-danger rounded-pill ms-2"><?= $relatorio->kpis->totalCancelamentos ?></span>
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
                  ?>
                    <div class="col-lg-6">
                      <div class="card border border-danger-subtle shadow-sm rounded-4 h-100 bg-body">
                        <div class="card-body p-4">

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

                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                              <i class="bi bi-x-circle-fill me-1"></i> Cancelado
                            </span>
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
                          <div>
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

<script>
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

              html += `
              <div class="card border border-danger-subtle rounded-3 shadow-none bg-body">
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-primary font-monospace">
                      <i class="bi bi-calendar-event me-1"></i> ${dtCulto} &bull; ${hora}
                    </span>
                    <span class="badge rounded-pill text-white px-2 py-1 small" style="background-color: ${corDep};">
                      ${c.nome_departamento}: ${c.nome_area}
                    </span>
                  </div>
                  <div class="fw-semibold text-body mb-2">${c.titulo_culto}</div>
                  <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle text-danger-emphasis small fst-italic mb-2">
                    <i class="bi bi-chat-quote-fill me-1 text-danger"></i> "${just}"
                  </div>
                  <div class="text-end text-muted" style="font-size: 0.72rem;">
                    <i class="bi bi-clock-history me-1"></i> Cancelado em: ${dtResp}
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
</script>