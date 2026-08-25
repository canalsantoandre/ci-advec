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
          <a href="<?= base_url('agenda') ?>" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-calendar-month me-1"></i> Calendário Visual
          </a>
          <a href="<?= base_url("agenda/imprimir/{$ano}/{$mes}") ?>" target="_blank" class="btn btn-outline-dark rounded-pill px-3 fw-semibold">
            <i class="bi bi-printer-fill me-1"></i> Imprimir Agenda (PDF)
          </a>
          <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" id="btnGerarGradeMes">
            <i class="bi bi-lightning-charge-fill me-1"></i> Gerar Grade do Mês
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

          <!-- Filtros Rápidos de Exibição da Tabela -->
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="small fw-bold text-secondary me-1"><i class="bi bi-funnel-fill me-1"></i> Filtrar Grade:</span>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btnFilterGradeRow active" data-filter="all">
              Todos (<?= $totalDias ?>d)
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 btnFilterGradeRow" data-filter="sem_convidado">
              <i class="bi bi-exclamation-triangle-fill me-1"></i> Sem Convidado (<?= $cultosSemConvidado ?>)
            </button>
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 btnFilterGradeRow" data-filter="com_convidado">
              <i class="bi bi-check-circle-fill me-1"></i> Com Convidado (<?= $cultosComConvidado ?>)
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- Month Navigation Tabs (Estilo Abas da Planilha Excel) -->
    <?php
      $mesesNomes = [
        1 => 'JAN', 2 => 'FEV', 3 => 'MAR', 4 => 'ABR', 5 => 'MAI', 6 => 'JUN',
        7 => 'JUL', 8 => 'AGO', 9 => 'SET', 10 => 'OUT', 11 => 'NOV', 12 => 'DEZ'
      ];
    ?>
    <div class="card bg-body-tertiary border-0 rounded-4 shadow-sm mb-4">
      <div class="card-body p-2 overflow-auto">
        <div class="d-flex align-items-center justify-content-between flex-nowrap gap-2">
          
          <!-- Abas dos Meses -->
          <div class="d-flex align-items-center gap-1 flex-nowrap">
            <?php for ($m = 1; $m <= 12; $m++) { 
              $isActive = ($m == $mes);
            ?>
              <a href="<?= base_url("agenda/grade/{$ano}/{$m}") ?>" class="btn btn-sm rounded-pill px-3 fw-bold text-nowrap <?= $isActive ? 'btn-primary shadow-sm' : 'btn-outline-secondary border-0' ?>">
                <?= $mesesNomes[$m] ?>-<?= $ano ?>
              </a>
            <?php } ?>
          </div>

          <!-- Seletor de Ano -->
          <div class="d-flex align-items-center gap-2 ms-3 flex-nowrap">
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
                <th scope="col" class="text-center py-3" style="width: 120px;">Dia</th>
                <th scope="col" class="py-3" style="width: 140px;">Dia Semana</th>
                <th scope="col" class="py-3">Nome do Culto / Evento</th>
                <th scope="col" class="py-3">Convidado(s) Vinculado(s)</th>
                <th scope="col" class="text-end py-3 pe-4" style="width: 120px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($diasGrade as $dia) { 
                $isFimSemana = $dia['is_fim_semana'];
                $bgClass = $isFimSemana ? 'table-warning-custom' : '';

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
              ?>
                <tr class="grade-row <?= $bgClass ?>" data-status-convidado="<?= $statusFiltro ?>">
                  
                  <!-- Coluna: Dia (DD/MM/YYYY) -->
                  <td class="text-center fw-bold font-monospace fs-6">
                    <?= $dia['data_formatada'] ?>
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

                                    <span class="fw-bold text-primary-hover"><?= esc($conv->nome_convidado) ?></span>
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
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill btnQuickVincularConvidado" data-id-culto="<?= $c->id_culto ?>" data-titulo-culto="<?= esc($c->titulo_culto) ?>" data-data-formatada="<?= $dia['data_formatada'] ?>" title="Vincular Convidado">
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
<!-- MODAL: VINCULAÇÃO RÁPIDA DE CONVIDADO -->
<!-- ========================================== -->
<div class="modal fade" id="modalVincularConvidadoRapido" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-success text-white rounded-top-4 py-3">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-person-plus-fill me-2"></i>Vincular Convidado ao Culto
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formVincularConvidadoRapido">
        <div class="modal-body p-4">
          <input type="hidden" name="id_culto" id="vincular_rapido_id_culto" value="0">
          <input type="hidden" name="ids_convidados[]" id="selected_rapido_convidado_id" value="">

          <div class="mb-3">
            <label class="form-label fw-semibold">Culto Selecionado</label>
            <div id="vincular_rapido_culto_display" class="fw-bold text-success fs-6"></div>
          </div>

          <!-- Filtro por Cargo (Função Eclesiástica) e Autocomplete -->
          <div class="mb-3">
            <label class="form-label fw-semibold mb-1">Buscar Convidado por Nome ou Cargo</label>
            
            <div class="row g-2 mb-2">
              <!-- Filtro por Cargo -->
              <div class="col-md-6">
                <select class="form-select form-select-sm" id="filterRapidoCargoSelect">
                  <option value="">Todas as Funções / Cargos</option>
                  <?php if (!empty($funcoesEclesiasticas)) { ?>
                    <?php foreach ($funcoesEclesiasticas as $func) { ?>
                      <option value="<?= $func->id_funcao_eclesiastica ?>"><?= esc($func->nm_funcao_eclesiastica) ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>

              <!-- Campo de Pesquisa Autocomplete -->
              <div class="col-md-6">
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                  <input type="text" class="form-control form-control-sm border-start-0" id="searchRapidoConvidadoInput" placeholder="Digite o nome...">
                </div>
              </div>
            </div>

            <!-- Lista de Seleção de Convidados com Foto/Iniciais e Badges de Cargo -->
            <div class="list-group border rounded-3 overflow-auto bg-white" style="max-height: 220px;" id="listRapidaConvidados">
              <?php if (!empty($convidados)) { ?>
                <?php foreach ($convidados as $c) { 
                  $nomePartes = explode(' ', trim($c->nome_convidado));
                  $iniciais   = strtoupper(substr($nomePartes[0], 0, 1) . (isset($nomePartes[1]) && !empty($nomePartes[1]) ? substr($nomePartes[1], 0, 1) : (strlen($nomePartes[0]) > 1 ? substr($nomePartes[0], 1, 1) : '')));
                  $uiAvatar   = 'https://ui-avatars.com/api/?name=' . urlencode($c->nome_convidado) . '&background=2563eb&color=fff&size=70';
                  $avatarUrl  = !empty($c->url_foto_instagram) ? esc($c->url_foto_instagram) : $uiAvatar;
                ?>
                  <label class="list-group-item d-flex align-items-center gap-2 item-convidado-rapido py-2 cursor-pointer border-bottom-0" data-id="<?= $c->id_convidado ?>" data-id-funcao="<?= $c->id_funcao_eclesiastica ?>" data-nome="<?= esc(mb_strtolower($c->nome_convidado)) ?>" data-cargo="<?= esc(mb_strtolower($c->nm_funcao_eclesiastica ?? '')) ?>">
                    <input class="form-check-input me-2 radConvidadoRapido" type="radio" name="radConvidado" value="<?= $c->id_convidado ?>">
                    
                    <?php if (!empty($c->url_foto_instagram)) { ?>
                      <img src="<?= $avatarUrl ?>" class="rounded-circle shadow-sm" style="width: 32px; height: 32px; object-fit: cover;" alt="Avatar" onError="this.onerror=null;this.src='<?= $uiAvatar ?>';">
                    <?php } else { ?>
                      <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <?= esc($iniciais) ?>
                      </span>
                    <?php } ?>

                    <div class="flex-grow-1">
                      <div class="fw-bold text-body small mb-0"><?= esc($c->nome_convidado) ?></div>
                      <small class="text-secondary" style="font-size: 0.75rem;">
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-0 me-1"><?= esc($c->nm_funcao_eclesiastica ?? 'Sem Função') ?></span>
                        <?= !empty($c->nick_instagram) ? '&bull; @' . esc($c->nick_instagram) : '' ?>
                      </small>
                    </div>

                    <a href="<?= base_url('convidado/editar/' . esc($c->hash_convidado ?: $c->id_convidado)) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle p-1 border-0" title="Ver cadastro completo em nova aba" onclick="event.stopPropagation();">
                      <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                  </label>
                <?php } ?>
              <?php } else { ?>
                <div class="p-3 text-center text-muted small">Nenhum convidado cadastrado.</div>
              <?php } ?>
            </div>
          </div>
        </div>

        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-check-lg me-1"></i> Vincular Convidado
          </button>
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
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modalAgendamentoGradeEl = new bootstrap.Modal(document.getElementById('modalAgendamentoGrade'));
    const modalVincularConvidadoRapidoEl = new bootstrap.Modal(document.getElementById('modalVincularConvidadoRapido'));
    const modalConfirmExcluirCultoGradeEl = new bootstrap.Modal(document.getElementById('modalConfirmExcluirCultoGrade'));
    const modalConfirmDesvincularConvidadoGradeEl = new bootstrap.Modal(document.getElementById('modalConfirmDesvincularConvidadoGrade'));

    function notify(type, title, message) {
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show(type, title, message);
      } else if (typeof $.messageAlert === 'function') {
        var tipoMap = { 'success': 1, 'error': 2, 'warning': 3, 'info': 4 };
        $.messageAlert({
          tipoMensagem: tipoMap[type] || 4,
          mensagemDestaque: title,
          mensagem: message,
          temporizador: 5000
        });
      }
    }

    // Filtro Rápido da Tabela da Grade por Status do Convidado
    document.querySelectorAll('.btnFilterGradeRow').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.btnFilterGradeRow').forEach(b => {
          b.classList.remove('btn-primary', 'active');
          b.classList.add('btn-outline-secondary');
        });
        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-primary', 'active');

        const filter = this.getAttribute('data-filter');

        document.querySelectorAll('#tblGradeMensal tbody tr.grade-row').forEach(row => {
          const status = row.getAttribute('data-status-convidado');
          if (filter === 'all') {
            row.style.display = '';
          } else if (filter === 'sem_convidado') {
            row.style.display = (status === 'sem_convidado') ? '' : 'none';
          } else if (filter === 'com_convidado') {
            row.style.display = (status === 'com_convidado') ? '' : 'none';
          }
        });
      });
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
        const titulo  = this.getAttribute('data-titulo');
        const inicio  = this.getAttribute('data-inicio');
        const termino = this.getAttribute('data-termino');
        const cor     = this.getAttribute('data-cor');

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
        const dataIso       = this.getAttribute('data-data');
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
        const id      = this.getAttribute('data-id');
        const dataIso = this.getAttribute('data-data');
        const titulo  = this.getAttribute('data-titulo');
        const inicio  = this.getAttribute('data-inicio');
        const termino = this.getAttribute('data-termino');
        const cor     = this.getAttribute('data-cor');

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
        const idCulto     = this.getAttribute('data-id-culto');
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

    // Clique em + Convidado (Vinculação Rápida)
    document.querySelectorAll('.btnQuickVincularConvidado').forEach(btn => {
      btn.addEventListener('click', function() {
        const idCulto = this.getAttribute('data-id-culto');
        const titulo  = this.getAttribute('data-titulo-culto');
        const dataFmt = this.getAttribute('data-data-formatada');

        document.getElementById('vincular_rapido_id_culto').value = idCulto;
        document.getElementById('vincular_rapido_culto_display').innerText = `${titulo} - ${dataFmt}`;
        document.getElementById('selected_rapido_convidado_id').value = '';

        // Reset filtros e seleções
        if (searchRapido) searchRapido.value = '';
        if (filterCargo) filterCargo.value = '';
        filterConvidadosRapidos();
        document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').forEach(el => el.classList.remove('bg-success-subtle', 'border-success'));
        document.querySelectorAll('#listRapidaConvidados input[type="radio"]').forEach(rad => rad.checked = false);

        modalVincularConvidadoRapidoEl.show();
      });
    });

    // Autocomplete & Filtro por Cargo no Modal de Vinculação Rápida
    const searchRapido = document.getElementById('searchRapidoConvidadoInput');
    const filterCargo = document.getElementById('filterRapidoCargoSelect');

    function filterConvidadosRapidos() {
      const q = (searchRapido.value || '').toLowerCase().trim();
      const cargoId = (filterCargo.value || '').toString();

      document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').forEach(item => {
        const nome = item.getAttribute('data-nome') || '';
        const cargo = (item.getAttribute('data-id-funcao') || '').toString();

        const matchName = !q || nome.includes(q);
        const matchCargo = !cargoId || cargo === cargoId;

        if (matchName && matchCargo) {
          item.classList.remove('d-none');
          item.classList.add('d-flex');
        } else {
          item.classList.remove('d-flex');
          item.classList.add('d-none');
        }
      });
    }

    if (searchRapido) searchRapido.addEventListener('input', filterConvidadosRapidos);
    if (filterCargo) filterCargo.addEventListener('change', filterConvidadosRapidos);

    // Quando clica em um item da lista rápida, seleciona o radio correspondente
    document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').forEach(item => {
      item.addEventListener('click', function() {
        const rad = this.querySelector('input[type="radio"]');
        if (rad) {
          rad.checked = true;
          document.getElementById('selected_rapido_convidado_id').value = rad.value;
          
          document.querySelectorAll('#listRapidaConvidados .item-convidado-rapido').forEach(el => el.classList.remove('bg-success-subtle', 'border-success'));
          this.classList.add('bg-success-subtle');
        }
      });
    });

    // Submit Vinculação Rápida de Convidado
    document.getElementById('formVincularConvidadoRapido').addEventListener('submit', function(e) {
      e.preventDefault();
      const convidadoId = document.getElementById('selected_rapido_convidado_id').value;

      if (!convidadoId) {
        notify('warning', 'Seleção Obrigatória', 'Selecione um convidado na lista.');
        return;
      }

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
          setTimeout(() => location.reload(), 800);
        } else {
          notify('error', 'Vinculação Rápida', data.message);
        }
      });
    });
  });
</script>
