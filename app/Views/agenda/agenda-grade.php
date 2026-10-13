<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold text-body">
          <i class="bi bi-table text-primary me-2"></i>Grade Mensal Dinâmica de Agendamento
        </h3>
        <p class="text-secondary small mb-0">Visão em matriz por dia do mês com cultos pré-programados e estatísticas da escala</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <div class="d-flex justify-content-sm-end align-items-center gap-2 mt-2 mt-sm-0 flex-wrap">
          <a href="<?= base_url('agenda') ?>" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-calendar-month fs-6"></i>
            <span>Calendário Visual</span>
          </a>
          <a href="<?= base_url("agenda/imprimir/{$ano}/{$mes}") ?>" target="_blank" class="btn btn-agenda-print rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" title="Imprimir / Salvar PDF do mês">
            <i class="bi bi-printer-fill fs-6 text-warning-emphasis"></i>
            <span>Imprimir Agenda (PDF)</span>
          </a>
          <button type="button" class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-bold d-inline-flex align-items-center gap-2" id="btnGerarGradeMes">
            <i class="bi bi-lightning-charge-fill fs-6"></i>
            <span>Gerar Grade do Mês</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Dashboard de Indicadores do Mês -->
    <div class="row g-3 mb-4">

      <!-- Card 1: Total de Cultos -->
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-primary bg-gradient text-white">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">Total de Cultos</span>
                <h2 class="mb-0 fw-bold font-monospace"><?= $totalCultosMes ?></h2>
              </div>
              <div class="rounded-circle bg-white bg-opacity-25 p-3 text-white">
                <i class="bi bi-calendar-event fs-3"></i>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">
              <i class="bi bi-clock me-1"></i> Agendados no Mês
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: Cultos Com Convidado -->
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-success bg-gradient text-white">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">Com Convidado</span>
                <h2 class="mb-0 fw-bold font-monospace"><?= $cultosComConvidado ?></h2>
              </div>
              <div class="rounded-circle bg-white bg-opacity-25 p-3 text-white">
                <i class="bi bi-person-check-fill fs-3"></i>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">
              <span class="badge bg-white text-success fw-bold rounded-pill px-2 py-0 me-1"><?= $percentualPreenchido ?>%</span> Escala Preenchida
            </div>
          </div>
        </div>
      </div>

      <!-- Card 3: Cultos SEM Convidado (Pendente!) -->
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden <?= $cultosSemConvidado > 0 ? 'bg-danger bg-gradient text-white' : 'bg-secondary bg-gradient text-white' ?>">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">Pendentes de Convidado</span>
                <h2 class="mb-0 fw-bold font-monospace"><?= $cultosSemConvidado ?></h2>
              </div>
              <div class="rounded-circle bg-white bg-opacity-25 p-3 text-white">
                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">
              <?php if ($cultosSemConvidado > 0) { ?>
                <span class="badge bg-white text-danger fw-bold rounded-pill px-2 py-0 me-1">Atenção!</span> Faltam convidados
              <?php } else { ?>
                <span class="badge bg-white text-secondary fw-bold rounded-pill px-2 py-0 me-1">100%</span> Completo
              <?php } ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 4: Total de Convidados Confirmados -->
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden text-white" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-white-50 small fw-bold text-uppercase d-block mb-1">Convidados Na Escala</span>
                <h2 class="mb-0 fw-bold font-monospace"><?= $totalConvidadosConfirmados ?></h2>
              </div>
              <div class="rounded-circle bg-white bg-opacity-25 p-3 text-white">
                <i class="bi bi-people-fill fs-3"></i>
              </div>
            </div>
            <div class="mt-2 text-white-50 small">
              <i class="bi bi-person-badge me-1"></i> Participações na escala
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Barra de Progresso e Filtros Rápidos da Grade -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

          <!-- Progresso da Escala -->
          <div class="flex-grow-1" style="min-width: 250px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="fw-bold small text-body">
                <i class="bi bi-bar-chart-line-fill text-primary me-1"></i> Progresso de Preenchimento da Escala:
                <span class="text-primary fw-bold"><?= $percentualPreenchido ?>%</span>
              </span>
              <small class="text-secondary font-monospace"><?= $cultosComConvidado ?> de <?= $totalCultosMes ?> cultos com convidado</small>
            </div>
            <div class="progress rounded-pill" style="height: 10px;">
              <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $percentualPreenchido ?>%;" aria-valuenow="<?= $percentualPreenchido ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>

          <?php
          // Extrai lista única de nomes/títulos de cultos no mês (para o filtro por culto)
          $titulosCultosMes = [];
          if (!empty($diasGrade)) {
            foreach ($diasGrade as $dItemT) {
              foreach (($dItemT['cultos_agendados'] ?? []) as $cT) {
                $t = trim((string)$cT->titulo_culto);
                if ($t !== '' && !in_array($t, $titulosCultosMes)) {
                  $titulosCultosMes[] = $t;
                }
              }
            }
          }
          sort($titulosCultosMes);

          $hojeIso        = isset($hojeIso) ? $hojeIso : date('Y-m-d');
          $anoAtual       = (int)date('Y');
          $mesAtual       = (int)date('m');
          $isMesAtual     = isset($isMesAtual) ? $isMesAtual : ($ano === $anoAtual && $mes === $mesAtual);
          $totalDiasPassados = 0;
          if (!empty($diasGrade)) {
            foreach ($diasGrade as $dItem) {
              if ($dItem['data_iso'] < $hojeIso) {
                $totalDiasPassados++;
              }
            }
          }
          ?>

          <!-- Filtros Rápidos de Exibição da Tabela -->
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

            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btnFilterGradeRow active" data-filter="all">
              Todos (<?= $totalDias ?>d)
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 btnFilterGradeRow" data-filter="sem_convidado">
              <i class="bi bi-exclamation-triangle-fill me-1"></i> Sem Convidado (<?= $cultosSemConvidado ?>)
            </button>
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 btnFilterGradeRow" data-filter="com_convidado">
              <i class="bi bi-check-circle-fill me-1"></i> Com Convidado (<?= $cultosComConvidado ?>)
            </button>

            <!-- Toggle Inteligente de Dias Anteriores no Mês Atual -->
            <?php if ($isMesAtual && $totalDiasPassados > 0) { ?>
              <div class="form-check form-switch d-inline-flex align-items-center gap-2 mb-0 px-3 py-1 bg-body border rounded-pill shadow-xs ms-lg-2 switch-dias-anteriores-container" style="min-height: 31px;">
                <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="toggleDiasAnteriores">
                <label class="form-check-label small fw-semibold text-body user-select-none" for="toggleDiasAnteriores" style="cursor: pointer;">
                  <i class="bi bi-clock-history me-1 text-primary"></i> Exibir dias anteriores
                  <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1" style="font-size: 0.7rem;"><?= $totalDiasPassados ?></span>
                </label>
              </div>
            <?php } ?>
          </div>

        </div>
      </div>
    </div>

    <!-- Month Navigation Tabs (Estilo Abas da Planilha Excel) -->
    <?php
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
    $anoAtualReal  = (int)date('Y');
    $mesAtualReal  = (int)date('m');
    $isAnoCorrente = ($ano === $anoAtualReal);
    $mesesPassadosComAgenda = isset($mesesPassadosComAgenda) && is_array($mesesPassadosComAgenda) ? $mesesPassadosComAgenda : [];
    ?>
    <div class="card bg-body-tertiary border-0 rounded-4 shadow-sm mb-4" style="overflow: visible; position: relative; z-index: 20;">
      <div class="card-body p-2" style="overflow: visible;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

          <!-- Abas dos Meses e Dropdown -->
          <div class="d-flex align-items-center gap-1 flex-wrap">

            <?php if ($isAnoCorrente) { ?>
              <!-- Dropdown de Meses Anteriores com Agenda Cadastrada -->
              <div class="dropdown me-1 position-relative" style="z-index: 25;">
                <?php if (!empty($mesesPassadosComAgenda)) { ?>
                  <button class="btn btn-sm btn-meses-anteriores rounded-pill px-3 fw-semibold dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="Visualizar meses passados que possuem cultos agendados">
                    <i class="bi bi-clock-history icon-history"></i> Meses anteriores
                    <span class="badge badge-count rounded-pill px-2 py-0.5 ms-0.5" style="font-size: 0.72rem;"><?= count($mesesPassadosComAgenda) ?></span>
                  </button>
                  <ul class="dropdown-menu dropdown-history-menu mt-1">
                    <li class="dropdown-header text-uppercase d-flex align-items-center gap-1">
                      <i class="bi bi-folder2-open text-warning"></i> Meses com Agenda (<?= $ano ?>)
                    </li>
                    <?php foreach ($mesesPassadosComAgenda as $mPassado) {
                      $isItemActive = ($mPassado == $mes);
                    ?>
                      <li>
                        <a class="dropdown-item dropdown-history-item d-flex align-items-center justify-content-between gap-3 <?= $isItemActive ? 'active' : '' ?>" href="<?= base_url("agenda/grade/{$ano}/{$mPassado}") ?>">
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
                  <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold opacity-60 d-inline-flex align-items-center gap-1" type="button" disabled title="Nenhum mês anterior possui cultos agendados">
                    <i class="bi bi-clock-history"></i> Meses anteriores
                  </button>
                <?php } ?>
              </div>

              <!-- Se navegou para um mês passado, exibe a aba desse mês em destaque -->
              <?php if ($mes < $mesAtualReal) { ?>
                <a href="<?= base_url("agenda/grade/{$ano}/{$mes}") ?>" class="btn btn-sm btn-mes-passado-ativo rounded-pill px-3 fw-bold text-nowrap d-inline-flex align-items-center gap-1">
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
                <a href="<?= base_url("agenda/grade/{$ano}/{$m}") ?>" class="btn btn-sm rounded-pill px-3 fw-bold text-nowrap <?= $isActive ? 'btn-primary shadow-sm' : 'btn-outline-secondary border-0' ?>">
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
                <a href="<?= base_url("agenda/grade/{$ano}/{$m}") ?>" class="btn btn-sm rounded-pill px-3 fw-bold text-nowrap <?= $isActive ? 'btn-primary shadow-sm' : 'btn-outline-secondary border-0' ?>">
                  <?= $mesesNomes[$m] ?>-<?= $ano ?>
                </a>
              <?php } ?>
            <?php } ?>

          </div>

          <!-- Seletor de Ano -->
          <div class="d-flex align-items-center gap-2 ms-auto flex-nowrap">
            <a href="<?= base_url("agenda/grade/" . ($ano - 1) . "/{$mes}") ?>" class="btn btn-outline-secondary btn-sm rounded-circle" title="Ano Anterior">
              <i class="bi bi-chevron-left"></i>
            </a>
            <span class="fw-bold fs-6 text-primary"><?= $ano ?></span>
            <a href="<?= base_url("agenda/grade/" . ($ano + 1) . "/{$mes}") ?>" class="btn btn-outline-secondary btn-sm rounded-circle" title="Próximo Ano">
              <i class="bi bi-chevron-right"></i>
            </a>
          </div>

        </div>
      </div>
    </div>

    <!-- Tabela Matriz Estilo Planilha Dinâmica -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title fw-bold mb-0 text-primary">
          <i class="bi bi-grid-3x3-gap-fill me-2"></i>Matriz de Agendamento - <?= sprintf('%02d', $mes) ?>/<?= $ano ?>
        </h5>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3"><?= $totalDias ?> Dias no Mês</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0 text-nowrap" id="tblGradeMensal">
            <thead class="table-dark text-uppercase small">
              <tr>
                <th scope="col" class="text-center py-3 user-select-none" style="width: 120px; cursor: pointer;" id="thSortDia" title="Clique para alternar a ordenação por Dia do Mês">
                  <div class="d-inline-flex align-items-center justify-content-center gap-1">
                    <span>Dia</span>
                    <i class="bi bi-arrow-down-up opacity-50" id="iconSortDia" style="font-size: 0.75rem;"></i>
                  </div>
                </th>
                <th scope="col" class="py-3 user-select-none" style="width: 150px; cursor: pointer;" id="thSortDiaSemana" title="Clique para ordenar todos os eventos por Dia da Semana (ex: Domingos juntos, Quartas juntas)">
                  <div class="d-inline-flex align-items-center gap-1">
                    <span>Dia Semana</span>
                    <i class="bi bi-arrow-down-up text-warning" id="iconSortDiaSemana" style="font-size: 0.75rem;"></i>
                  </div>
                </th>
                <th scope="col" class="py-3">Nome do Culto / Evento</th>
                <th scope="col" class="py-3">Convidado(s) Vinculado(s)</th>
                <th scope="col" class="text-end py-3 pe-4" style="width: 120px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $origRowIndex = 0;
              foreach ($diasGrade as $dia) {
                $isFimSemana = $dia['is_fim_semana'];
                $bgClass = $isFimSemana ? 'table-warning-custom' : '';
                $isDiaPassado = ($dia['data_iso'] < $hojeIso);
                $isOcultoInicial = ($isMesAtual && $isDiaPassado);
                $rowPastClass = $isDiaPassado ? 'linha-dia-passado' : '';
                $styleOculto = $isOcultoInicial ? 'display: none;' : '';

                // Verifica o status dos cultos agendados no dia
                $hasCultoSemConvidado = false;
                $hasCultoComConvidado = false;

                if (!empty($dia['cultos_agendados'])) {
                  foreach ($dia['cultos_agendados'] as $c) {
                    if (empty($c->convidadosVinculados)) {
                      $hasCultoSemConvidado = true;
                    } else {
                      $hasCultoComConvidado = true;
                    }
                  }
                }

                $statusFiltro = $hasCultoSemConvidado ? 'sem_convidado' : ($hasCultoComConvidado ? 'com_convidado' : 'sem_culto');
                $titulosCultosDia = array_map(fn($cc) => trim((string)$cc->titulo_culto), $dia['cultos_agendados'] ?? []);
              ?>
                <tr class="grade-row <?= $bgClass ?> <?= $rowPastClass ?>"
                  data-status-convidado="<?= $statusFiltro ?>"
                  data-cultos="<?= esc(json_encode($titulosCultosDia, JSON_UNESCAPED_UNICODE)) ?>"
                  data-is-past="<?= $isDiaPassado ? '1' : '0' ?>"
                  data-dia-semana="<?= (int)$dia['num_dia_semana'] ?>"
                  data-data-iso="<?= $dia['data_iso'] ?>"
                  data-orig-index="<?= $origRowIndex++ ?>"
                  style="<?= $styleOculto ?>">

                  <!-- Coluna: Dia (DD/MM/YYYY) -->
                  <td class="text-center fw-bold font-monospace fs-6">
                    <?= $dia['data_formatada'] ?>
                    <?php if ($isDiaPassado) { ?>
                      <div class="mt-1">
                        <span class="badge bg-body-secondary text-muted rounded-pill px-2 py-0.5" style="font-size: 0.65rem;" title="Data anterior">
                          Passado
                        </span>
                      </div>
                    <?php } ?>
                  </td>

                  <!-- Coluna: Dia Semana -->
                  <td class="fw-semibold text-capitalize">
                    <?php if ($isFimSemana) { ?>
                      <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-bold">
                        <i class="bi bi-star-fill me-1 text-warning"></i><?= $dia['nome_dia_semana'] ?>
                      </span>
                    <?php } else { ?>
                      <span class="text-secondary"><?= $dia['nome_dia_semana'] ?></span>
                    <?php } ?>
                  </td>

                  <!-- Coluna: Nome do Culto -->
                  <td>
                    <?php if (!empty($dia['cultos_agendados'])) { ?>
                      <div class="d-flex flex-column gap-2">
                        <?php foreach ($dia['cultos_agendados'] as $c) {
                          $cor = !empty($c->cor_evento) ? $c->cor_evento : '#2563eb';
                        ?>
                          <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge rounded-pill px-3 py-2 text-white shadow-sm d-inline-flex align-items-center gap-2 fs-6 fw-bold cursor-pointer btnEditarCultoGrade"
                              style="background-color: <?= esc($cor) ?>;"
                              data-id="<?= $c->id_culto ?>"
                              data-titulo="<?= esc($c->titulo_culto) ?>"
                              data-data="<?= esc($c->data_culto) ?>"
                              data-inicio="<?= esc(substr($c->horario_inicio, 0, 5)) ?>"
                              data-termino="<?= esc(substr($c->horario_termino, 0, 5)) ?>"
                              data-cor="<?= esc($cor) ?>"
                              data-descricao="<?= esc($c->descricao) ?>"
                              title="Clique para editar este evento">
                              <i class="bi bi-pencil-square"></i>
                              <?= esc($c->titulo_culto) ?>
                              <small class="fw-normal opacity-75">(<?= substr($c->horario_inicio, 0, 5) ?> - <?= substr($c->horario_termino, 0, 5) ?>)</small>
                            </span>

                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center btnExcluirCultoGrade"
                              style="width: 26px; height: 26px;"
                              data-id="<?= $c->id_culto ?>"
                              data-titulo="<?= esc($c->titulo_culto) ?>"
                              title="Excluir / Desmarcar este evento">
                              <i class="bi bi-trash"></i>
                            </button>
                          </div>
                        <?php } ?>
                      </div>
                    <?php } else { ?>
                      <!-- Sugestão de Cultos Padrão -->
                      <?php if (!empty($dia['cultos_sugeridos'])) { ?>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                          <?php foreach ($dia['cultos_sugeridos'] as $cs) { ?>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btnQuickAddCulto" data-data="<?= $dia['data_iso'] ?>" data-titulo="<?= esc($cs->nome_culto) ?>" data-inicio="<?= esc(substr($cs->horario_inicio, 0, 5)) ?>" data-termino="<?= esc(substr($cs->horario_termino, 0, 5)) ?>" data-cor="<?= esc($cs->cor_evento) ?>" title="Criar agendamento rápido">
                              <i class="bi bi-plus-circle me-1"></i> <?= esc($cs->nome_culto) ?>
                            </button>
                          <?php } ?>
                        </div>
                      <?php } else { ?>
                        <span class="text-muted small italic">Nenhum culto cadastrado</span>
                      <?php } ?>
                    <?php } ?>
                  </td>

                  <!-- Coluna: Convidado Vinculado -->
                  <td>
                    <?php if (!empty($dia['cultos_agendados'])) { ?>
                      <div class="d-flex flex-column gap-2">
                        <?php foreach ($dia['cultos_agendados'] as $c) { ?>
                          <div class="d-flex flex-wrap align-items-center gap-2">
                            <?php if (!empty($c->convidadosVinculados)) { ?>
                              <?php foreach ($c->convidadosVinculados as $conv) {
                                $isPresente = (isset($conv->status_presenca) && $conv->status_presenca == 1);
                                $nomePartes = explode(' ', trim($conv->nome_convidado));
                                $iniciais   = strtoupper(substr($nomePartes[0], 0, 1) . (isset($nomePartes[1]) && !empty($nomePartes[1]) ? substr($nomePartes[1], 0, 1) : (strlen($nomePartes[0]) > 1 ? substr($nomePartes[0], 1, 1) : '')));
                                $uiAvatar   = 'https://ui-avatars.com/api/?name=' . urlencode($conv->nome_convidado) . '&background=2563eb&color=fff&size=60';
                                $fotoUrl    = !empty($conv->url_foto_instagram) ? esc($conv->url_foto_instagram) : $uiAvatar;
                              ?>
                                <span class="badge bg-white text-body border shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                  <a href="<?= base_url('convidado/editar/' . esc($conv->hash_convidado ?: $conv->id_convidado)) ?>" target="_blank" class="d-inline-flex align-items-center gap-2 text-decoration-none text-body" title="Abrir cadastro do convidado em nova aba">
                                    <?php if (!empty($conv->url_foto_instagram)) { ?>
                                      <img src="<?= $fotoUrl ?>" class="rounded-circle shadow-sm" style="width: 26px; height: 26px; object-fit: cover;" alt="<?= esc($conv->nome_convidado) ?>" onError="this.onerror=null; this.src='<?= $uiAvatar ?>';">
                                    <?php } else { ?>
                                      <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                        <?= esc($iniciais) ?>
                                      </span>
                                    <?php } ?>

                                    <span class="fw-bold text-black"><?= esc($conv->nome_convidado) ?></span>
                                    <?php if (!empty($conv->nm_funcao_eclesiastica)) { ?>
                                      <small class="text-secondary opacity-75">(<?= esc($conv->nm_funcao_eclesiastica) ?>)</small>
                                    <?php } ?>
                                    <i class="bi bi-box-arrow-up-right text-primary opacity-75 ms-1" style="font-size: 0.75rem;"></i>
                                  </a>

                                  <!-- Toggle de Presença -->
                                  <button type="button" class="btn btn-xs rounded-pill px-2 py-0 ms-1 btnAlternarPresencaGrade <?= $isPresente ? 'btn-success text-white' : 'btn-outline-secondary' ?>"
                                    data-id-culto="<?= $c->id_culto ?>"
                                    data-id-convidado="<?= $conv->id_convidado ?>"
                                    title="<?= $isPresente ? 'Presença Confirmada (Clique para alternar)' : 'Marcar como Presente' ?>">
                                    <i class="bi <?= $isPresente ? 'bi-check-circle-fill' : 'bi-circle' ?> me-1"></i>
                                    <?= $isPresente ? 'Presente' : 'Ausente' ?>
                                  </button>

                                  <!-- Desvincular Convidado -->
                                  <button type="button" class="btn btn-link text-danger p-0 ms-1 border-0 btnDesvincularConvidadoGrade"
                                    data-id-culto="<?= $c->id_culto ?>"
                                    data-id-convidado="<?= $conv->id_convidado ?>"
                                    data-nome="<?= esc($conv->nome_convidado) ?>"
                                    title="Desvincular Convidado">
                                    <i class="bi bi-x-circle-fill"></i>
                                  </button>
                                </span>
                              <?php } ?>
                            <?php } else { ?>
                              <!-- Alerta de Convidado Pendente -->
                              <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Convidado Pendente
                              </span>
                            <?php } ?>

                            <!-- Botão + Convidado -->
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill btnQuickVincularConvidado"
                              data-id-culto="<?= $c->id_culto ?>"
                              data-titulo-culto="<?= esc($c->titulo_culto) ?>"
                              data-data-formatada="<?= $dia['data_formatada'] ?>"
                              data-horario="<?= !empty($c->horario_inicio) ? substr($c->horario_inicio, 0, 5) : '' ?>"
                              data-convidados-existentes="<?= esc(json_encode(array_map(function ($cv) {
                                                            return ['nome' => $cv->nome_convidado, 'funcao' => $cv->nm_funcao_eclesiastica ?? ''];
                                                          }, $c->convidadosVinculados ?? []))) ?>"
                              title="Vincular Convidado">
                              <i class="bi bi-person-plus-fill me-1"></i>+ Convidado
                            </button>
                          </div>
                        <?php } ?>
                      </div>
                    <?php } else { ?>
                      <span class="text-muted small">-</span>
                    <?php } ?>
                  </td>

                  <!-- Coluna: Ações -->
                  <td class="text-end pe-4">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btnNovaEntradaGrade" data-data="<?= $dia['data_iso'] ?>" data-data-formatada="<?= $dia['data_formatada'] ?>" title="Novo Agendamento nesta data">
                      <i class="bi bi-plus-lg me-1"></i> Agendar
                    </button>
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

<!-- ========================================== -->
<!-- MODAL: AGENDAMENTO RÁPIDO NA GRADE -->
<!-- ========================================== -->
<div class="modal fade" id="modalAgendamentoGrade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-primary text-white rounded-top-4 py-3">
        <h5 class="modal-title fw-bold" id="modalAgendamentoGradeTitle">
          <i class="bi bi-calendar-plus me-2"></i>Agendar Culto na Grade
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAgendamentoGrade">
        <div class="modal-body p-4">
          <input type="hidden" name="id_culto" id="grade_id_culto" value="0">
          <input type="hidden" name="data_culto" id="grade_data_culto" value="">

          <div class="mb-3">
            <label class="form-label fw-semibold">Data Selecionada</label>
            <input type="text" id="grade_data_formatada_display" class="form-control bg-body-tertiary fw-bold text-primary" readonly>
          </div>

          <?php if (!empty($cultosPadrao)) { ?>
            <div class="mb-3">
              <label for="gradeSelectModelo" class="form-label fw-semibold">Modelo de Culto Padrão</label>
              <select class="form-select" id="gradeSelectModelo">
                <option value="">-- Selecionar Modelo ou Digitar Manualmente --</option>
                <?php foreach ($cultosPadrao as $cp) { ?>
                  <option value="<?= $cp->id_culto_padrao ?>"
                    data-titulo="<?= esc($cp->nome_culto) ?>"
                    data-inicio="<?= esc(substr($cp->horario_inicio, 0, 5)) ?>"
                    data-termino="<?= esc(substr($cp->horario_termino, 0, 5)) ?>"
                    data-cor="<?= esc($cp->cor_evento) ?>">
                    <?= esc($cp->nome_culto) ?> (<?= substr($cp->horario_inicio, 0, 5) ?>)
                  </option>
                <?php } ?>
              </select>
            </div>
          <?php } ?>

          <div class="mb-3">
            <label for="grade_titulo_culto" class="form-label fw-semibold">Nome / Título do Culto <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="titulo_culto" id="grade_titulo_culto" placeholder="Ex: Culto da Vitória" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="grade_horario_inicio" class="form-label fw-semibold">Horário Início</label>
              <input type="time" class="form-control" name="horario_inicio" id="grade_horario_inicio" value="19:00" required>
            </div>
            <div class="col-md-6">
              <label for="grade_horario_termino" class="form-label fw-semibold">Horário Término</label>
              <input type="time" class="form-control" name="horario_termino" id="grade_horario_termino" value="21:00" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Cor no Calendário</label>
            <div class="d-flex flex-wrap gap-2">
              <input type="radio" class="btn-check" name="cor_evento" id="gcor1" value="#2563eb" checked>
              <label class="btn btn-outline-primary rounded-pill px-3" for="gcor1">Azul</label>

              <input type="radio" class="btn-check" name="cor_evento" id="gcor2" value="#16a34a">
              <label class="btn btn-outline-success rounded-pill px-3" for="gcor2">Verde</label>

              <input type="radio" class="btn-check" name="cor_evento" id="gcor3" value="#dc2626">
              <label class="btn btn-outline-danger rounded-pill px-3" for="gcor3">Vermelho</label>

              <input type="radio" class="btn-check" name="cor_evento" id="gcor4" value="#d97706">
              <label class="btn btn-outline-warning rounded-pill px-3" for="gcor4">Laranja</label>

              <input type="radio" class="btn-check" name="cor_evento" id="gcor5" value="#7c3aed">
              <label class="btn btn-outline-secondary rounded-pill px-3" for="gcor5">Roxo</label>
            </div>
          </div>
        </div>

        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-save me-1"></i> Confirmar Agendamento
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: VINCULAÇÃO RÁPIDA DE CONVIDADO (WORKSTATION DA SECRETARIA) -->
<!-- ========================================== -->
<div class="modal fade" id="modalVincularConvidadoRapido" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

      <!-- Cabeçalho Vibrante com Ícone e Estatísticas -->
      <div class="modal-header bg-success text-white py-3 px-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="p-2 bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-person-plus-fill fs-5 text-white"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0">Vincular Convidado ao Culto</h5>
            <small class="text-white-50">Selecione o preletor ou cantor para a escala deste culto</small>
          </div>
        </div>
        <div class="d-flex align-items-center gap-3">
          <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-1 font-monospace small">
            <?= count($convidados ?? []) ?> cadastrados
          </span>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>

      <form id="formVincularConvidadoRapido">
        <div class="modal-body p-4 bg-body">
          <input type="hidden" name="id_culto" id="vincular_rapido_id_culto" value="0">
          <input type="hidden" name="ids_convidados[]" id="selected_rapido_convidado_id" value="">

          <div class="row g-4">

            <!-- COLUNA ESQUERDA: LISTA E BUSCA INTELIGENTE DE CONVIDADOS -->
            <div class="col-12 col-lg-7">

              <!-- Barra de Pesquisa e Filtros -->
              <div class="card border bg-body-tertiary rounded-4 p-3 mb-3 shadow-xs">
                <div class="row g-2 align-items-center">
                  <!-- Input de Busca -->
                  <div class="col-12 col-md-7">
                    <div class="input-group">
                      <span class="input-group-text bg-body border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                      </span>
                      <input type="text" class="form-control border-start-0 border-end-0 ps-0" id="searchRapidoConvidadoInput" placeholder="Buscar por nome, cargo ou @instagram..." autocomplete="off">
                      <button class="btn btn-outline-secondary border-start-0 text-muted" type="button" id="btnClearSearchRapido" title="Limpar busca" style="display: none;">
                        <i class="bi bi-x-circle-fill"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Dropdown de Cargos/Funções -->
                  <div class="col-12 col-md-5">
                    <select class="form-select" id="filterRapidoCargoSelect">
                      <option value="">Todas as Funções (<?= count($convidados ?? []) ?>)</option>
                      <?php if (!empty($funcoesEclesiasticas)) { ?>
                        <?php foreach ($funcoesEclesiasticas as $func) { ?>
                          <option value="<?= $func->id_funcao_eclesiastica ?>"><?= esc($func->nm_funcao_eclesiastica) ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <!-- Chips de Atalhos Rápidos por Cargo -->
                <div class="d-flex flex-wrap gap-1 mt-2 pt-2 border-top align-items-center" id="chipsFuncoesContainer">
                  <span class="small text-muted fw-semibold me-1"><i class="bi bi-funnel me-1"></i>Filtros:</span>
                  <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5 small chip-funcao active" data-cargo-id="">Todos</button>
                  <?php if (!empty($funcoesEclesiasticas)) { ?>
                    <?php foreach (array_slice($funcoesEclesiasticas, 0, 5) as $func) { ?>
                      <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5 small chip-funcao" data-cargo-id="<?= $func->id_funcao_eclesiastica ?>"><?= esc($func->nm_funcao_eclesiastica) ?></button>
                    <?php } ?>
                  <?php } ?>
                </div>
              </div>

              <!-- Contador de Resultados Visíveis -->
              <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                <span class="small text-muted fw-semibold" id="lblContadorRapidoConvidados">
                  Exibindo <?= count($convidados ?? []) ?> de <?= count($convidados ?? []) ?> convidados
                </span>
                <span class="small text-muted">
                  <i class="bi bi-mouse me-1"></i>Clique duplo vincula rápido
                </span>
              </div>

              <!-- Lista Rolável de Cards de Convidados -->
              <div class="d-flex flex-column gap-2 overflow-auto pe-1" style="max-height: 440px;" id="listRapidaConvidados">
                <?php if (!empty($convidados)) { ?>
                  <?php foreach ($convidados as $c) {
                    $nomePartes = explode(' ', trim($c->nome_convidado));
                    $iniciais   = strtoupper(substr($nomePartes[0], 0, 1) . (isset($nomePartes[1]) && !empty($nomePartes[1]) ? substr($nomePartes[1], 0, 1) : (strlen($nomePartes[0]) > 1 ? substr($nomePartes[0], 1, 1) : '')));
                    $uiAvatar   = 'https://ui-avatars.com/api/?name=' . urlencode($c->nome_convidado) . '&background=2563eb&color=fff&size=80';
                    $avatarUrl  = !empty($c->url_foto_instagram) ? esc($c->url_foto_instagram) : $uiAvatar;

                    // Normaliza Instagram (evita @@ duplicado)
                    $rawNick = trim((string)($c->nick_instagram ?? ''));
                    $cleanNick = ltrim($rawNick, '@');

                    // Cor por Cargo
                    $cargoNome = trim((string)($c->nm_funcao_eclesiastica ?? 'Sem Função'));
                    $cargoLower = mb_strtolower($cargoNome);
                    $badgeCargoClass = 'bg-secondary-subtle text-secondary-emphasis';
                    if (str_contains($cargoLower, 'pastor')) {
                      $badgeCargoClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                    } elseif (str_contains($cargoLower, 'cantor') || str_contains($cargoLower, 'louvor') || str_contains($cargoLower, 'músico')) {
                      $badgeCargoClass = 'bg-purple-subtle text-purple border border-purple-subtle';
                    } elseif (str_contains($cargoLower, 'evangelista')) {
                      $badgeCargoClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                    } elseif (str_contains($cargoLower, 'bispo') || str_contains($cargoLower, 'apóstolo')) {
                      $badgeCargoClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                    }
                  ?>
                    <div class="card card-convidado-select p-2.5 rounded-3 border bg-body item-convidado-rapido shadow-xs"
                      data-id="<?= $c->id_convidado ?>"
                      data-id-funcao="<?= $c->id_funcao_eclesiastica ?>"
                      data-nome="<?= esc(mb_strtolower($c->nome_convidado)) ?>"
                      data-cargo="<?= esc(mb_strtolower($cargoNome)) ?>"
                      data-nick="<?= esc(mb_strtolower($cleanNick)) ?>"
                      data-telefone="<?= esc($c->telefone ?? '') ?>"
                      data-link-fotos="<?= esc($c->link_fotos ?? '') ?>"
                      data-avatar="<?= $avatarUrl ?>"
                      data-nome-exibicao="<?= esc($c->nome_convidado) ?>"
                      data-cargo-exibicao="<?= esc($cargoNome) ?>"
                      data-hash="<?= esc($c->hash_convidado ?: $c->id_convidado) ?>"
                      data-total-cultos="<?= (int)($c->total_cultos_agendados ?? 0) ?>">

                      <div class="d-flex align-items-center gap-2.5">
                        <!-- Radio customizado -->
                        <div class="radio-indicator flex-shrink-0 ms-1"></div>
                        <input class="d-none radConvidadoRapido" type="radio" name="radConvidado" value="<?= $c->id_convidado ?>">&nbsp;&nbsp;

                        <!-- Foto / Avatar -->
                        <div class="flex-shrink-0 position-relative">
                          <?php if (!empty($c->url_foto_instagram)) { ?>
                            <img src="<?= $avatarUrl ?>" class="rounded-circle shadow-xs" style="width: 42px; height: 42px; object-fit: cover;" alt="<?= esc($c->nome_convidado) ?>" onError="this.onerror=null;this.src='<?= $uiAvatar ?>';">
                          <?php } else { ?>
                            <div class="rounded-circle bg-primary bg-gradient text-white d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 42px; height: 42px; font-size: 0.95rem;">
                              <?= esc($iniciais) ?>
                            </div>
                          <?php } ?>
                        </div>

                        <!-- Detalhes Centrais -->
                        <div class="flex-grow-1 min-w-0">
                          <div class="d-flex align-items-center justify-content-between gap-1 mb-0.5">
                            <span class="fw-bold text-body text-truncate d-block" style="font-size: 0.95rem;">&nbsp;&nbsp;<?= esc($c->nome_convidado) ?></span>
                            <span class="badge <?= $badgeCargoClass ?> rounded-pill px-2 py-0.5 small fw-semibold flex-shrink-0" style="font-size: 0.72rem;">
                              &nbsp;&nbsp;<?= esc($cargoNome) ?>
                            </span>
                          </div>

                          <div class="d-flex flex-wrap align-items-center gap-2 text-muted small" style="font-size: 0.76rem;">
                            <?php if (!empty($cleanNick)) { ?>
                              <span><i class="bi bi-instagram text-danger me-1"></i>@<?= esc($cleanNick) ?></span>
                            <?php } ?>

                            <?php if (!empty($c->telefone)) { ?>
                              <span><i class="bi bi-whatsapp text-success me-1"></i><?= esc($c->telefone) ?></span>
                            <?php } ?>

                            <?php if (!empty($c->link_fotos)) { ?>
                              <span class="text-info"><i class="bi bi-images me-1"></i>Fotos OK</span>
                            <?php } ?>
                          </div>
                        </div>

                        <!-- Ação de Abrir Cadastro Externo -->
                        <a href="<?= base_url('convidado/editar/' . esc($c->hash_convidado ?: $c->id_convidado)) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle p-1 border-0 flex-shrink-0 text-muted" title="Ver cadastro completo" onclick="event.stopPropagation();">
                          <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                      </div>
                    </div>
                  <?php } ?>
                <?php } else { ?>
                  <div class="card p-4 text-center text-muted rounded-4 border">
                    <i class="bi bi-people fs-1 text-secondary mb-2"></i>
                    Nenhum convidado cadastrado no sistema.
                  </div>
                <?php } ?>
              </div>
            </div>

            <!-- COLUNA DIREITA: PAINEL DE CONTEXTO DO CULTO & PREVIEW DO CONVIDADO -->
            <div class="col-12 col-lg-5">
              <div class="d-flex flex-column gap-3 h-100">

                <!-- CARD DO CULTO SELECIONADO -->
                <div class="card border rounded-4 bg-body-tertiary p-3 shadow-xs">
                  <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-bold">
                      <i class="bi bi-calendar-event me-1"></i>Culto Destino
                    </span>
                    <span class="small text-muted font-monospace" id="vincular_rapido_horario_display"></span>
                  </div>
                  <h6 class="fw-bold text-body mb-1 fs-6" id="vincular_rapido_titulo_display">-</h6>
                  <div class="text-secondary small d-flex align-items-center gap-1" id="vincular_rapido_data_display">
                    <i class="bi bi-clock-history text-primary"></i> Data do culto
                  </div>

                  <!-- Convidados já vinculados a este culto -->
                  <div class="mt-2 pt-2 border-top" id="boxConvidadosJaVinculadosCulto" style="display: none;">
                    <div class="small fw-semibold text-muted mb-1">Convidado(s) já na escala:</div>
                    <div class="d-flex flex-wrap gap-1" id="listaConvidadosJaVinculadosCulto"></div>
                  </div>
                </div>

                <!-- CARD DE INSPEÇÃO DO CONVIDADO SELECIONADO (LIVE PREVIEW) -->
                <div class="card border rounded-4 bg-body p-3 shadow-xs flex-grow-1 d-flex flex-column justify-content-between" id="cardPreviewConvidado">

                  <!-- ESTADO VAZIO (Nenhum convidado selecionado) -->
                  <div id="previewEmptyState" class="text-center py-4 my-auto">
                    <div class="rounded-circle bg-secondary-subtle text-secondary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                      <i class="bi bi-person-bounding-box fs-2"></i>
                    </div>
                    <h6 class="fw-bold text-body mb-1">Nenhum Convidado Selecionado</h6>
                    <p class="text-muted small mb-0 px-2">
                      Clique em um convidado na lista ao lado para conferir os dados de contato, redes sociais e fotos antes de vincular.
                    </p>
                  </div>

                  <!-- ESTADO PREENCHIDO (Convidado Selecionado) -->
                  <div id="previewFilledState" style="display: none;">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                      <img id="previewAvatar" src="" class="rounded-circle shadow-sm border border-2 border-success" style="width: 58px; height: 58px; object-fit: cover;" alt="Avatar">
                      <div class="min-w-0 flex-grow-1">
                        <h6 class="fw-bold text-body mb-0.5 text-truncate fs-6" id="previewNome">-</h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 small fw-semibold" id="previewCargo">-</span>
                      </div>
                    </div>

                    <div class="d-flex flex-column gap-2 small mb-3">
                      <!-- Telefone / WhatsApp -->
                      <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-body-tertiary">
                        <span class="text-muted"><i class="bi bi-whatsapp text-success me-1.5"></i>&nbsp;&nbsp;WhatsApp:</span>
                        <div id="previewTelefoneBox" class="fw-semibold text-body">-</div>
                      </div>

                      <!-- Instagram -->
                      <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-body-tertiary">
                        <span class="text-muted"><i class="bi bi-instagram text-danger me-1.5"></i>&nbsp;&nbsp;Instagram:</span>
                        <div id="previewInstagramBox" class="fw-semibold text-body">-</div>
                      </div>

                      <!-- Fotos / Material Google Drive -->
                      <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-body-tertiary">
                        <span class="text-muted"><i class="bi bi-images text-info me-1.5"></i>&nbsp;&nbsp;Fotos p/ Arte:</span>
                        <div id="previewFotosBox" class="fw-semibold">-</div>
                      </div>

                      <!-- Histórico na Agenda -->
                      <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-body-tertiary">
                        <span class="text-muted"><i class="bi bi-calendar-check text-primary me-1.5"></i>&nbsp;&nbsp;Histórico:</span>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill" id="previewCultosCount">0 cultos agendados</span>
                      </div>
                    </div>

                    <!-- Notificação Automática do WhatsApp (Aviso Informativo) -->
                    <div class="alert alert-info bg-info-subtle border-0 rounded-3 p-2.5 small mb-0 d-flex gap-2 align-items-start">
                      <i class="bi bi-info-circle-fill text-info fs-5 mt-0.5 flex-shrink-0"></i>
                      <div style="font-size: 0.78rem; line-height: 1.35;">
                        <strong>Notificação Automática:</strong> Ao confirmar, o grupo de WhatsApp da equipe de comunicação/artes será acionado instantaneamente com as informações deste convidado.
                      </div>
                    </div>
                  </div>

                  <!-- Ações e Confirmação no Card -->
                  <div class="mt-3 pt-3 border-top d-flex gap-2 justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold" data-bs-dismiss="modal">
                      Cancelar
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 small fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" id="btnSubmitVincularRapido" disabled>
                      <i class="bi bi-check-circle-fill"></i>
                      <span id="lblBtnVincularRapido">Vincular Convidado</span>
                    </button>
                  </div>

                </div>

              </div>
            </div>

          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL DE CONFIRMAÇÃO PARA EXCLUIR CULTO -->
<!-- ========================================== -->
<div class="modal fade" id="modalConfirmExcluirCultoGrade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-danger text-white rounded-top-4 py-3">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>Excluir Culto
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <p class="mb-0 text-body fs-6" id="msgConfirmExcluirCultoGrade"></p>
      </div>
      <div class="modal-footer border-top-0 pt-0 px-4 pb-4 justify-content-center">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold" id="btnConfirmarExcluirCultoGrade">
          <i class="bi bi-trash me-1"></i> Confirmar Exclusão
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL DE CONFIRMAÇÃO PARA DESVINCULAR CONVIDADO -->
<!-- ========================================== -->
<div class="modal fade" id="modalConfirmDesvincularConvidadoGrade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-warning text-dark rounded-top-4 py-3">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-person-dash-fill me-2"></i>Desvincular Convidado
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <p class="mb-0 text-body fs-6" id="msgConfirmDesvincularConvidadoGrade"></p>
      </div>
      <div class="modal-footer border-top-0 pt-0 px-4 pb-4 justify-content-center">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold" id="btnConfirmarDesvincularConvidadoGrade">
          <i class="bi bi-person-x me-1"></i> Confirmar Desvinculação
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  /* Estilo do Fim de Semana Amarelo Ouro (Idêntico à Planilha Excel do Usuário) */
  .table-warning-custom {
    background-color: #fef3c7 !important;
  }

  .table-warning-custom td {
    background-color: #fef3c7 !important;
  }

  [data-bs-theme="dark"] .table-warning-custom {
    background-color: rgba(217, 119, 6, 0.2) !important;
  }

  [data-bs-theme="dark"] .table-warning-custom td {
    background-color: rgba(217, 119, 6, 0.2) !important;
  }

  /* Botão Imprimir Agenda (PDF) */
  .btn-agenda-print {
    background-color: #f8fafc;
    color: #1e293b !important;
    border: 1.5px solid #cbd5e1;
    font-weight: 600;
    font-size: 0.88rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .btn-agenda-print:hover {
    background-color: #e2e8f0;
    color: #0f172a !important;
    border-color: #94a3b8;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
  }

  [data-bs-theme="dark"] .btn-agenda-print {
    background-color: #334155;
    color: #f8fafc !important;
    border-color: #64748b;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
  }

  [data-bs-theme="dark"] .btn-agenda-print:hover {
    background-color: #475569;
    color: #ffffff !important;
    border-color: #94a3b8;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
  }

  /* Linha de dia passado no mês */
  .linha-dia-passado {
    opacity: 0.88;
    background-color: rgba(241, 245, 249, 0.45) !important;
  }

  [data-bs-theme="dark"] .linha-dia-passado {
    opacity: 0.8;
    background-color: rgba(30, 41, 59, 0.35) !important;
  }

  /* Botão e Menu de Meses Anteriores (mesmo padrão da grade de escalas) */
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

  .btn-mes-passado-ativo {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 3px 10px rgba(79, 70, 229, 0.35);
  }

  /* Componente Switch "Exibir dias anteriores" */
  .switch-dias-anteriores-container {
    transition: all 0.2s ease-in-out;
  }

  .switch-dias-anteriores-container:hover {
    border-color: #94a3b8 !important;
  }

  .switch-dias-anteriores-container:has(#toggleDiasAnteriores:checked) {
    border-color: rgba(37, 99, 235, 0.45) !important;
    background-color: rgba(37, 99, 235, 0.06) !important;
  }

  #toggleDiasAnteriores {
    cursor: pointer;
    width: 2.3em !important;
    height: 1.22em !important;
    border-radius: 2em;
    transition: background-color 0.2s ease, border-color 0.2s ease, background-position 0.2s ease;
  }

  #toggleDiasAnteriores:not(:checked) {
    background-color: #cbd5e1 !important;
    border-color: #94a3b8 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%2364748b'/%3e%3c/svg%3e") !important;
    background-position: left center !important;
  }

  [data-bs-theme="dark"] #toggleDiasAnteriores:not(:checked) {
    background-color: #334155 !important;
    border-color: #475569 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23cbd5e1'/%3e%3c/svg%3e") !important;
  }

  #toggleDiasAnteriores:checked {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23ffffff'/%3e%3c/svg%3e") !important;
    background-position: right center !important;
  }

  #toggleDiasAnteriores:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25) !important;
  }

  /* Estilização Moderna do Modal de Vinculação Rápida (Secretaria) */
  .card-convidado-select {
    cursor: pointer;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1.5px solid var(--bs-border-color) !important;
  }

  .card-convidado-select:hover {
    transform: translateY(-1px);
    border-color: #16a34a !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.12) !important;
  }

  .card-convidado-select.selected {
    border-color: #16a34a !important;
    background-color: rgba(22, 163, 74, 0.08) !important;
    box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.3) !important;
  }

  [data-bs-theme="dark"] .card-convidado-select.selected {
    border-color: #22c55e !important;
    background-color: rgba(34, 197, 94, 0.18) !important;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.4) !important;
  }

  .card-convidado-select .radio-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.18s ease;
  }

  [data-bs-theme="dark"] .card-convidado-select .radio-indicator {
    border-color: #475569;
  }

  .card-convidado-select.selected .radio-indicator {
    border-color: #16a34a;
    background-color: #16a34a;
  }

  .card-convidado-select.selected .radio-indicator::after {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #ffffff;
  }

  .chip-funcao {
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    font-size: 0.75rem;
  }

  .chip-funcao:hover {
    transform: translateY(-1px);
  }

  .chip-funcao.active {
    background-color: #16a34a !important;
    color: #ffffff !important;
    border-color: #16a34a !important;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalAgendamentoGradeEl = new bootstrap.Modal(document.getElementById('modalAgendamentoGrade'));
    const modalVincularConvidadoRapidoEl = new bootstrap.Modal(document.getElementById('modalVincularConvidadoRapido'));
    const modalConfirmExcluirCultoGradeEl = new bootstrap.Modal(document.getElementById('modalConfirmExcluirCultoGrade'));
    const modalConfirmDesvincularConvidadoGradeEl = new bootstrap.Modal(document.getElementById('modalConfirmDesvincularConvidadoGrade'));

    function notify(type, title, message) {
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show(type, title, message);
      } else if (typeof $.messageAlert === 'function') {
        var tipoMap = {
          'success': 1,
          'error': 2,
          'warning': 3,
          'info': 4
        };
        $.messageAlert({
          tipoMensagem: tipoMap[type] || 4,
          mensagemDestaque: title,
          mensagem: message,
          temporizador: 5000
        });
      }
    }

    // ==========================================
    // ORDENAÇÃO DINÂMICA DA GRADE (Dia e Dia Semana)
    // ==========================================
    let direcaoDiaSemana = 'none'; // 'none' -> 'asc' -> 'desc'
    let direcaoDia = 'asc'; // 'asc' -> 'desc'

    function ordenarGradeTabela(criterio, direcao) {
      const tbody = document.querySelector('#tblGradeMensal tbody');
      if (!tbody) return;

      const rows = Array.from(tbody.querySelectorAll('tr.grade-row'));

      rows.sort((a, b) => {
        if (criterio === 'dia_semana') {
          const diaSemA = parseInt(a.getAttribute('data-dia-semana') || '0', 10);
          const diaSemB = parseInt(b.getAttribute('data-dia-semana') || '0', 10);
          if (diaSemA !== diaSemB) {
            return direcao === 'asc' ? diaSemA - diaSemB : diaSemB - diaSemA;
          }
          // Critério secundário: data ISO
          const dataA = a.getAttribute('data-data-iso') || '';
          const dataB = b.getAttribute('data-data-iso') || '';
          return dataA.localeCompare(dataB);
        } else if (criterio === 'data') {
          const dataA = a.getAttribute('data-data-iso') || '';
          const dataB = b.getAttribute('data-data-iso') || '';
          return direcao === 'asc' ? dataA.localeCompare(dataB) : dataB.localeCompare(dataA);
        }
        return 0;
      });

      rows.forEach(row => tbody.appendChild(row));
    }

    // Clique no cabeçalho Dia da Semana
    document.getElementById('thSortDiaSemana')?.addEventListener('click', function() {
      const iconSemana = document.getElementById('iconSortDiaSemana');
      const iconDia = document.getElementById('iconSortDia');

      if (direcaoDiaSemana === 'none' || direcaoDiaSemana === 'desc') {
        direcaoDiaSemana = 'asc';
        ordenarGradeTabela('dia_semana', 'asc');
        if (iconSemana) iconSemana.className = 'bi bi-sort-numeric-down text-warning fw-bold';
      } else if (direcaoDiaSemana === 'asc') {
        direcaoDiaSemana = 'desc';
        ordenarGradeTabela('dia_semana', 'desc');
        if (iconSemana) iconSemana.className = 'bi bi-sort-numeric-up text-warning fw-bold';
      }

      // Reseta estado da coluna Dia
      if (iconDia) iconDia.className = 'bi bi-arrow-down-up opacity-50';
      direcaoDia = 'none';
    });

    // Clique no cabeçalho Dia
    document.getElementById('thSortDia')?.addEventListener('click', function() {
      const iconDia = document.getElementById('iconSortDia');
      const iconSemana = document.getElementById('iconSortDiaSemana');

      if (direcaoDia === 'asc') {
        direcaoDia = 'desc';
        ordenarGradeTabela('data', 'desc');
        if (iconDia) iconDia.className = 'bi bi-sort-numeric-up text-white fw-bold';
      } else {
        direcaoDia = 'asc';
        ordenarGradeTabela('data', 'asc');
        if (iconDia) iconDia.className = 'bi bi-sort-numeric-down text-white fw-bold';
      }

      // Reseta estado da coluna Dia Semana
      if (iconSemana) iconSemana.className = 'bi bi-arrow-down-up text-warning opacity-75';
      direcaoDiaSemana = 'none';
    });

    // Função unificada de filtros da Grade (Status Convidado + Dias Anteriores)
    function aplicarFiltrosGrade() {
      const activeBtn = document.querySelector('.btnFilterGradeRow.active');
      const filter = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
      const cultoSelecionado = (document.getElementById('selectFiltroCulto')?.value || '').toLowerCase().trim();
      const toggleDiasAnteriores = document.getElementById('toggleDiasAnteriores');
      const exibirDiasAnteriores = toggleDiasAnteriores ? toggleDiasAnteriores.checked : true;

      document.querySelectorAll('#tblGradeMensal tbody tr.grade-row').forEach(row => {
        const status = row.getAttribute('data-status-convidado');
        const isPast = row.getAttribute('data-is-past') === '1';
        let cultosNaLinha = [];
        try {
          cultosNaLinha = JSON.parse(row.getAttribute('data-cultos') || '[]').map(t => String(t).toLowerCase().trim());
        } catch (e) {
          cultosNaLinha = [];
        }

        // Se for dia passado no mês corrente e toggle desligado, oculta
        if (isPast && !exibirDiasAnteriores) {
          row.style.display = 'none';
          return;
        }

        let atendeStatus = true;
        if (filter === 'all') {
          atendeStatus = true;
        } else if (filter === 'sem_convidado') {
          atendeStatus = (status === 'sem_convidado');
        } else if (filter === 'com_convidado') {
          atendeStatus = (status === 'com_convidado');
        }

        let atendeCulto = true;
        if (cultoSelecionado !== '') {
          atendeCulto = cultosNaLinha.includes(cultoSelecionado);
        }

        row.style.display = (atendeStatus && atendeCulto) ? '' : 'none';
      });
    }

    // Event listener da mudança no dropdown de Cultos
    document.getElementById('selectFiltroCulto')?.addEventListener('change', function() {
      aplicarFiltrosGrade();
    });

    // Filtro Rápido da Tabela da Grade por Status do Convidado
    document.querySelectorAll('.btnFilterGradeRow').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.btnFilterGradeRow').forEach(b => {
          b.classList.remove('btn-primary', 'active');
          b.classList.add('btn-outline-secondary');
        });
        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-primary', 'active');

        aplicarFiltrosGrade();
      });
    });

    // Event listener do Toggle de Dias Anteriores
    document.getElementById('toggleDiasAnteriores')?.addEventListener('change', function() {
      aplicarFiltrosGrade();
    });

    // Botão Gerar Grade Automática do Mês
    document.getElementById('btnGerarGradeMes').addEventListener('click', function() {
      const btn = this;
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Gerando...';

      const formData = new FormData();
      formData.append('ano', '<?= $ano ?>');
      formData.append('mes', '<?= $mes ?>');

      fetch('<?= base_url('agenda/gerarGradeMes') ?>', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          btn.disabled = false;
          btn.innerHTML = originalText;

          if (data.status === 'success') {
            notify('success', 'Grade Mensal Gerada', data.message);
            setTimeout(() => location.reload(), 1200);
          } else {
            notify('error', 'Gerador de Grade', data.message);
          }
        })
        .catch(err => {
          btn.disabled = false;
          btn.innerHTML = originalText;
          notify('error', 'Falha na Requisição', err.message);
        });
    });

    // Clique em Botões de Sugestão Rápida (+ Culto Padrão)
    document.querySelectorAll('.btnQuickAddCulto').forEach(btn => {
      btn.addEventListener('click', function() {
        const dataIso = this.getAttribute('data-data');
        const titulo = this.getAttribute('data-titulo');
        const inicio = this.getAttribute('data-inicio');
        const termino = this.getAttribute('data-termino');
        const cor = this.getAttribute('data-cor');

        const formData = new FormData();
        formData.append('id_culto', '0');
        formData.append('data_culto', dataIso);
        formData.append('titulo_culto', titulo);
        formData.append('horario_inicio', inicio);
        formData.append('horario_termino', termino);
        formData.append('cor_evento', cor);

        fetch('<?= base_url('agenda/salvarRapidoAgenda') ?>', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              notify('success', 'Agendamento Criado', data.message);
              setTimeout(() => location.reload(), 800);
            } else {
              notify('error', 'Agendamento Rápido', data.message);
            }
          });
      });
    });

    // Clique em Nova Entrada de Agendamento
    document.querySelectorAll('.btnNovaEntradaGrade').forEach(btn => {
      btn.addEventListener('click', function() {
        const dataIso = this.getAttribute('data-data');
        const dataFormatada = this.getAttribute('data-data-formatada');

        document.getElementById('grade_id_culto').value = '0';
        document.getElementById('grade_data_culto').value = dataIso;
        document.getElementById('grade_data_formatada_display').value = dataFormatada;
        document.getElementById('grade_titulo_culto').value = '';
        if (document.getElementById('gradeSelectModelo')) document.getElementById('gradeSelectModelo').value = '';
        document.getElementById('modalAgendamentoGradeTitle').innerHTML = '<i class="bi bi-calendar-plus me-2"></i>Agendar Culto na Grade';

        modalAgendamentoGradeEl.show();
      });
    });

    // Clique em Editar Culto Existente
    document.querySelectorAll('.btnEditarCultoGrade').forEach(badge => {
      badge.addEventListener('click', function(e) {
        e.stopPropagation();
        const id = this.getAttribute('data-id');
        const dataIso = this.getAttribute('data-data');
        const titulo = this.getAttribute('data-titulo');
        const inicio = this.getAttribute('data-inicio');
        const termino = this.getAttribute('data-termino');
        const cor = this.getAttribute('data-cor');

        document.getElementById('grade_id_culto').value = id;
        document.getElementById('grade_data_culto').value = dataIso;
        document.getElementById('grade_data_formatada_display').value = dataIso;
        document.getElementById('grade_titulo_culto').value = titulo;
        document.getElementById('grade_horario_inicio').value = inicio;
        document.getElementById('grade_horario_termino').value = termino;

        const radioCor = document.querySelector(`input[name="cor_evento"][value="${cor}"]`);
        if (radioCor) radioCor.checked = true;

        document.getElementById('modalAgendamentoGradeTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Culto / Evento';
        modalAgendamentoGradeEl.show();
      });
    });

    let targetCultoIdExcluir = 0;
    let targetDesvincularCultoId = 0;
    let targetDesvincularConvidadoId = 0;

    // Clique em Excluir / Desmarcar Culto (Abre Modal de Confirmação)
    document.querySelectorAll('.btnExcluirCultoGrade').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        targetCultoIdExcluir = this.getAttribute('data-id');
        const titulo = this.getAttribute('data-titulo');

        document.getElementById('msgConfirmExcluirCultoGrade').innerHTML = `Deseja realmente excluir/desmarcar o evento <strong>"${titulo}"</strong>?`;
        modalConfirmExcluirCultoGradeEl.show();
      });
    });

    // Confirmar Ação de Excluir Culto
    document.getElementById('btnConfirmarExcluirCultoGrade').addEventListener('click', function() {
      if (!targetCultoIdExcluir) return;

      const formData = new FormData();
      fetch(`<?= base_url('agenda/excluirCulto/') ?>${targetCultoIdExcluir}`, {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          modalConfirmExcluirCultoGradeEl.hide();
          if (data.status === 'success') {
            notify('success', 'Evento Excluído', data.message);
            setTimeout(() => location.reload(), 600);
          } else {
            notify('error', 'Excluir Evento', data.message);
          }
        });
    });

    // Alternar Presença do Convidado em Tempo Real
    document.querySelectorAll('.btnAlternarPresencaGrade').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const idCulto = this.getAttribute('data-id-culto');
        const idConvidado = this.getAttribute('data-id-convidado');

        const formData = new FormData();
        formData.append('id_culto', idCulto);
        formData.append('id_convidado', idConvidado);

        fetch('<?= base_url('agenda/alternarPresenca') ?>', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              notify('success', 'Presença Atualizada', data.message);
              setTimeout(() => location.reload(), 500);
            } else {
              notify('error', 'Presença', data.message);
            }
          });
      });
    });

    // Clique em Desvincular Convidado (Abre Modal de Confirmação)
    document.querySelectorAll('.btnDesvincularConvidadoGrade').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        targetDesvincularCultoId = this.getAttribute('data-id-culto');
        targetDesvincularConvidadoId = this.getAttribute('data-id-convidado');
        const nome = this.getAttribute('data-nome');

        document.getElementById('msgConfirmDesvincularConvidadoGrade').innerHTML = `Deseja realmente desvincular o convidado <strong>"${nome}"</strong> deste culto?`;
        modalConfirmDesvincularConvidadoGradeEl.show();
      });
    });

    // Confirmar Ação de Desvincular Convidado
    document.getElementById('btnConfirmarDesvincularConvidadoGrade').addEventListener('click', function() {
      if (!targetDesvincularCultoId || !targetDesvincularConvidadoId) return;

      const formData = new FormData();
      formData.append('id_culto', targetDesvincularCultoId);
      formData.append('id_convidado', targetDesvincularConvidadoId);

      fetch('<?= base_url('agenda/removerConvidado') ?>', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          modalConfirmDesvincularConvidadoGradeEl.hide();
          if (data.status === 'success') {
            notify('success', 'Convidado Desvinculado', data.message);
            setTimeout(() => location.reload(), 500);
          } else {
            notify('error', 'Desvincular Convidado', data.message);
          }
        });
    });

    // Modelo Select Handler no Modal de Agendamento
    const gradeSelectModelo = document.getElementById('gradeSelectModelo');
    if (gradeSelectModelo) {
      gradeSelectModelo.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (!opt || !opt.value) return;

        document.getElementById('grade_titulo_culto').value = opt.getAttribute('data-titulo') || '';
        document.getElementById('grade_horario_inicio').value = opt.getAttribute('data-inicio') || '19:00';
        document.getElementById('grade_horario_termino').value = opt.getAttribute('data-termino') || '21:00';

        const cor = opt.getAttribute('data-cor') || '#2563eb';
        const radioCor = document.querySelector(`input[name="cor_evento"][value="${cor}"]`);
        if (radioCor) radioCor.checked = true;
      });
    }

    // Submit Form Agendamento Grade
    document.getElementById('formAgendamentoGrade').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('<?= base_url('agenda/salvarRapidoAgenda') ?>', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            modalAgendamentoGradeEl.hide();
            notify('success', 'Grade Mensal', data.message);
            setTimeout(() => location.reload(), 800);
          } else {
            notify('error', 'Grade Mensal', data.message);
          }
        });
    });

    // ==========================================
    // WORKSTATION DA SECRETARIA: VINCULAR CONVIDADO
    // ==========================================
    const searchRapidoInput = document.getElementById('searchRapidoConvidadoInput');
    const filterCargoSelect = document.getElementById('filterRapidoCargoSelect');
    const btnClearSearchRapido = document.getElementById('btnClearSearchRapido');
    const lblContadorRapido = document.getElementById('lblContadorRapidoConvidados');
    const btnSubmitVincularRapido = document.getElementById('btnSubmitVincularRapido');

    // Foco automático no input de busca ao abrir o modal
    const modalEl = document.getElementById('modalVincularConvidadoRapido');
    if (modalEl) {
      modalEl.addEventListener('shown.bs.modal', function() {
        if (searchRapidoInput) searchRapidoInput.focus();
      });
    }

    // Clique em + Convidado (Abertura do Modal de Vinculação Rápida)
    document.querySelectorAll('.btnQuickVincularConvidado').forEach(btn => {
      btn.addEventListener('click', function() {
        const idCulto = this.getAttribute('data-id-culto');
        const titulo = this.getAttribute('data-titulo-culto') || 'Culto';
        const dataFmt = this.getAttribute('data-data-formatada') || '';
        const horario = this.getAttribute('data-horario') || '';
        let convidadosExistentes = [];

        try {
          const rawConv = this.getAttribute('data-convidados-existentes');
          if (rawConv) convidadosExistentes = JSON.parse(rawConv);
        } catch (e) {}

        // Atualiza campos ocultos e labels do Culto
        document.getElementById('vincular_rapido_id_culto').value = idCulto;
        document.getElementById('vincular_rapido_titulo_display').innerText = titulo;
        document.getElementById('vincular_rapido_data_display').innerHTML = `<i class="bi bi-clock-history text-primary"></i> ${dataFmt}`;
        document.getElementById('vincular_rapido_horario_display').innerText = horario ? `${horario}h` : '';

        // Convidados já vinculados neste culto
        const boxJaVinculados = document.getElementById('boxConvidadosJaVinculadosCulto');
        const listaJaVinculados = document.getElementById('listaConvidadosJaVinculadosCulto');
        if (convidadosExistentes && convidadosExistentes.length > 0) {
          listaJaVinculados.innerHTML = convidadosExistentes.map(c =>
            `<span class="badge bg-body border text-body shadow-xs rounded-pill px-2.5 py-1 small">
              <i class="bi bi-person-check-fill text-success me-1"></i>${c.nome} <span class="text-muted fw-normal">(${c.funcao || 'Convidado'})</span>
            </span>`
          ).join('');
          boxJaVinculados.style.display = 'block';
        } else {
          boxJaVinculados.style.display = 'none';
          listaJaVinculados.innerHTML = '';
        }

        // Reset de seleção e formulário
        document.getElementById('selected_rapido_convidado_id').value = '';
        if (btnSubmitVincularRapido) btnSubmitVincularRapido.disabled = true;

        // Reset do Painel de Pré-visualização
        document.getElementById('previewEmptyState').style.display = 'block';
        document.getElementById('previewFilledState').style.display = 'none';

        // Reset filtros
        if (searchRapidoInput) searchRapidoInput.value = '';
        if (btnClearSearchRapido) btnClearSearchRapido.style.display = 'none';
        if (filterCargoSelect) filterCargoSelect.value = '';

        document.querySelectorAll('#chipsFuncoesContainer .chip-funcao').forEach(chip => {
          chip.classList.toggle('active', chip.getAttribute('data-cargo-id') === '');
        });

        document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').forEach(el => {
          el.classList.remove('selected');
          const rad = el.querySelector('input[type="radio"]');
          if (rad) rad.checked = false;
        });

        filterConvidadosRapidos();
        modalVincularConvidadoRapidoEl.show();
      });
    });

    // Função de Filtragem Dinâmica da Lista
    function filterConvidadosRapidos() {
      const q = (searchRapidoInput ? searchRapidoInput.value : '').toLowerCase().trim();
      const cargoId = (filterCargoSelect ? filterCargoSelect.value : '').toString();

      // Botão Limpar
      if (btnClearSearchRapido) {
        btnClearSearchRapido.style.display = q ? 'inline-block' : 'none';
      }

      let visiveis = 0;
      const total = document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').length;

      document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').forEach(item => {
        const nome = item.getAttribute('data-nome') || '';
        const cargo = (item.getAttribute('data-id-funcao') || '').toString();
        const cargoTxt = item.getAttribute('data-cargo') || '';
        const nick = item.getAttribute('data-nick') || '';
        const tel = item.getAttribute('data-telefone') || '';

        const matchSearch = !q || nome.includes(q) || cargoTxt.includes(q) || nick.includes(q) || tel.includes(q);
        const matchCargo = !cargoId || cargo === cargoId;

        if (matchSearch && matchCargo) {
          item.classList.remove('d-none');
          visiveis++;
        } else {
          item.classList.add('d-none');
        }
      });

      if (lblContadorRapido) {
        lblContadorRapido.innerText = `Exibindo ${visiveis} de ${total} convidados`;
      }
    }

    if (searchRapidoInput) {
      searchRapidoInput.addEventListener('input', filterConvidadosRapidos);
    }

    if (filterCargoSelect) {
      filterCargoSelect.addEventListener('change', function() {
        // Atualiza chip ativo correspondente
        const val = this.value;
        document.querySelectorAll('#chipsFuncoesContainer .chip-funcao').forEach(chip => {
          chip.classList.toggle('active', chip.getAttribute('data-cargo-id') === val);
        });
        filterConvidadosRapidos();
      });
    }

    if (btnClearSearchRapido) {
      btnClearSearchRapido.addEventListener('click', function() {
        if (searchRapidoInput) {
          searchRapidoInput.value = '';
          searchRapidoInput.focus();
        }
        filterConvidadosRapidos();
      });
    }

    // Clique nos Chips de Cargo
    document.querySelectorAll('#chipsFuncoesContainer .chip-funcao').forEach(chip => {
      chip.addEventListener('click', function() {
        const cargoId = this.getAttribute('data-cargo-id') || '';
        if (filterCargoSelect) filterCargoSelect.value = cargoId;

        document.querySelectorAll('#chipsFuncoesContainer .chip-funcao').forEach(c => c.classList.remove('active'));
        this.classList.add('active');

        filterConvidadosRapidos();
      });
    });

    // Função que aplica a seleção e atualiza o Live Preview
    function selecionarConvidadoRapido(itemEl) {
      const convidadoId = itemEl.getAttribute('data-id');
      const nomeExibicao = itemEl.getAttribute('data-nome-exibicao') || 'Convidado';
      const cargoExibicao = itemEl.getAttribute('data-cargo-exibicao') || 'Sem Função';
      const avatarUrl = itemEl.getAttribute('data-avatar') || '';
      const nick = itemEl.getAttribute('data-nick') || '';
      const telefone = itemEl.getAttribute('data-telefone') || '';
      const linkFotos = itemEl.getAttribute('data-link-fotos') || '';
      const totalCultos = itemEl.getAttribute('data-total-cultos') || '0';

      // Atualiza input hidden e radio
      document.getElementById('selected_rapido_convidado_id').value = convidadoId;
      const rad = itemEl.querySelector('input[type="radio"]');
      if (rad) rad.checked = true;

      // Atualiza visual nos cards
      document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').forEach(el => el.classList.remove('selected'));
      itemEl.classList.add('selected');

      // Habilita botão de submissão
      if (btnSubmitVincularRapido) btnSubmitVincularRapido.disabled = false;

      // Popula o Card de Pré-visualização na Direita
      document.getElementById('previewEmptyState').style.display = 'none';
      document.getElementById('previewFilledState').style.display = 'block';

      document.getElementById('previewAvatar').src = avatarUrl;
      document.getElementById('previewNome').innerText = nomeExibicao;
      document.getElementById('previewCargo').innerText = cargoExibicao;

      // Telefone / WhatsApp
      const boxTel = document.getElementById('previewTelefoneBox');
      if (telefone) {
        const digitos = telefone.replace(/\D/g, '');
        boxTel.innerHTML = `<a href="https://wa.me/55${digitos}" target="_blank" class="text-success text-decoration-none fw-bold d-inline-flex align-items-center gap-1" title="Conversar no WhatsApp">
          <i class="bi bi-whatsapp"></i> ${telefone}
        </a>`;
      } else {
        boxTel.innerHTML = `<span class="text-muted fst-italic">Não informado</span>`;
      }

      // Instagram
      const boxInsta = document.getElementById('previewInstagramBox');
      if (nick) {
        boxInsta.innerHTML = `<a href="https://instagram.com/${nick}" target="_blank" class="text-danger text-decoration-none fw-bold d-inline-flex align-items-center gap-1" title="Abrir perfil no Instagram">
          <i class="bi bi-instagram"></i> @${nick}
        </a>`;
      } else {
        boxInsta.innerHTML = `<span class="text-muted fst-italic">Não informado</span>`;
      }

      // Fotos no Drive
      const boxFotos = document.getElementById('previewFotosBox');
      if (linkFotos) {
        boxFotos.innerHTML = `<a href="${linkFotos}" target="_blank" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-0.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs" title="Abrir pasta de fotos">
          <i class="bi bi-box-arrow-up-right"></i> Abrir Drive
        </a>`;
      } else {
        boxFotos.innerHTML = `<span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5">Pendente</span>`;
      }

      // Total de cultos
      document.getElementById('previewCultosCount').innerText = `${totalCultos} culto(s) agendado(s)`;
    }

    // Clique nos cards de convidados (Seleção)
    document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').forEach(item => {
      item.addEventListener('click', function(e) {
        selecionarConvidadoRapido(this);
      });

      // Clique duplo vincula na hora (agilidade máxima)
      item.addEventListener('dblclick', function(e) {
        selecionarConvidadoRapido(this);
        const form = document.getElementById('formVincularConvidadoRapido');
        if (form) form.requestSubmit();
      });
    });

    // Submissão do Formulário de Vinculação Rápida
    document.getElementById('formVincularConvidadoRapido').addEventListener('submit', function(e) {
      e.preventDefault();
      const convidadoId = document.getElementById('selected_rapido_convidado_id').value;

      if (!convidadoId) {
        notify('warning', 'Seleção Obrigatória', 'Selecione um convidado na lista antes de vincular.');
        return;
      }

      const btn = document.getElementById('btnSubmitVincularRapido');
      const lblBtn = document.getElementById('lblBtnVincularRapido');
      if (btn) btn.disabled = true;
      if (lblBtn) lblBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Vinculando...`;

      const formData = new FormData(this);

      fetch('<?= base_url('agenda/vincularConvidados') ?>', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            modalVincularConvidadoRapidoEl.hide();
            notify('success', 'Convidado Vinculado', data.message);
            setTimeout(() => location.reload(), 700);
          } else {
            notify('error', 'Vinculação Rápida', data.message);
            if (btn) btn.disabled = false;
            if (lblBtn) lblBtn.innerText = 'Vincular Convidado';
          }
        })
        .catch(err => {
          notify('error', 'Erro de Conexão', 'Não foi possível vincular o convidado.');
          if (btn) btn.disabled = false;
          if (lblBtn) lblBtn.innerText = 'Vincular Convidado';
        });
    });
  });
</script>