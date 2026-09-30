<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-graph-up-arrow text-primary me-2"></i>Dashboard do Voluntário
        </h3>
        <p class="text-secondary small mb-0">Estatísticas de participação, assiduidade e histórico de serviços prestados</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('voluntario') ?>">Voluntários</a></li>
          <li class="breadcrumb-item active" aria-current="page">Assiduidade</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    
    <!-- Profile Card Header -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
      <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
          
          <div class="d-flex align-items-center gap-3">
            <?php 
              $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($stats->voluntario->nome) . '&background=2563eb&color=fff&size=120&bold=true';
              $avatarSrc = !empty($stats->voluntario->foto_url) ? $stats->voluntario->foto_url : $defaultAvatar;
            ?>
            <img src="<?= esc($avatarSrc) ?>" class="rounded-circle border shadow-sm" style="width: 76px; height: 76px; object-fit: cover;" alt="avatar" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="fw-bold mb-0 text-body"><?= esc($stats->voluntario->nome) ?></h4>
                <?php if ($stats->voluntario->status == 1) { ?>
                  <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 small">Ativo</span>
                <?php } else { ?>
                  <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2 py-1 small">Inativo</span>
                <?php } ?>
              </div>

              <div class="d-flex flex-wrap align-items-center gap-2 text-secondary small mb-2">
                <span><i class="bi bi-envelope me-1"></i><?= esc($stats->voluntario->email) ?></span>
                &bull;
                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $stats->voluntario->telefone_whatsapp) ?>" target="_blank" class="text-success text-decoration-none fw-semibold">
                  <i class="bi bi-whatsapp me-1"></i><?= esc($stats->voluntario->telefone_whatsapp) ?>
                </a>
              </div>

              <!-- Áreas vinculadas -->
              <?php if (!empty($stats->voluntario->areas)) { ?>
                <div class="d-flex flex-wrap gap-1">
                  <?php foreach ($stats->voluntario->areas as $va) { 
                    $corDep = !empty($va->cor_identificacao) ? $va->cor_identificacao : '#2563eb';
                  ?>
                    <span class="badge rounded-pill text-white px-2 py-1 small" style="background-color: <?= esc($corDep) ?>;">
                      <?= esc($va->nome_departamento) ?>: <strong><?= esc($va->nome_area) ?></strong>
                    </span>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
          </div>

          <div class="d-flex gap-2">
            <a href="<?= base_url('voluntario/desempenho') ?>" class="btn btn-outline-info rounded-pill px-3 fw-semibold">
              <i class="bi bi-person-lines-fill me-1"></i> Relatório Geral
            </a>
            <a href="<?= base_url('escala/grade') ?>" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
              <i class="bi bi-calendar-check me-1"></i> Grade de Escalas
            </a>
            <a href="<?= base_url('voluntario/editar/' . ($stats->voluntario->hash_voluntario ?: $stats->voluntario->id_voluntario)) ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
              <i class="bi bi-pencil-square me-1"></i> Editar Cadastro
            </a>
          </div>

        </div>
      </div>
    </div>

    <!-- Seletor de Período -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <h5 class="fw-bold text-body mb-0">
        <i class="bi bi-calendar-range text-primary me-2"></i>Período: <span class="text-primary"><?= esc($stats->labelPeriodo) ?></span>
      </h5>

      <div class="btn-group shadow-sm" role="group">
        <a href="<?= base_url("voluntario/dashVoluntario/{$stats->voluntario->id_voluntario}?periodo=mes_atual") ?>" class="btn btn-sm <?= ($periodo === 'mes_atual') ? 'btn-primary' : 'btn-outline-secondary bg-body' ?>">Mês Atual</a>
        <a href="<?= base_url("voluntario/dashVoluntario/{$stats->voluntario->id_voluntario}?periodo=ultimos_3_meses") ?>" class="btn btn-sm <?= ($periodo === 'ultimos_3_meses') ? 'btn-primary' : 'btn-outline-secondary bg-body' ?>">Últimos 3 Meses</a>
        <a href="<?= base_url("voluntario/dashVoluntario/{$stats->voluntario->id_voluntario}?periodo=ano_atual") ?>" class="btn btn-sm <?= ($periodo === 'ano_atual') ? 'btn-primary' : 'btn-outline-secondary bg-body' ?>">Ano Atual</a>
        <a href="<?= base_url("voluntario/dashVoluntario/{$stats->voluntario->id_voluntario}?periodo=tudo") ?>" class="btn btn-sm <?= ($periodo === 'tudo') ? 'btn-primary' : 'btn-outline-secondary bg-body' ?>">Todo Período</a>
      </div>
    </div>

    <!-- Stat Widgets Row (5 Colunas) -->
    <div class="row g-3 mb-4">
      <div class="col-lg col-sm-6">
        <div class="small-box text-bg-primary shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $stats->totalEscalas ?></h3>
            <p class="mb-0">Escalações</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-calendar-event"></i>
          </div>
        </div>
      </div>

      <div class="col-lg col-sm-6">
        <div class="small-box text-bg-success shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $stats->totalConfirmados ?></h3>
            <p class="mb-0">Confirmadas</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-check-circle-fill"></i>
          </div>
        </div>
      </div>

      <div class="col-lg col-sm-6">
        <div class="small-box text-bg-danger shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $stats->totalCancelamentos ?></h3>
            <p class="mb-0">Cancelamentos / Recusas</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-x-circle-fill"></i>
          </div>
        </div>
      </div>

      <div class="col-lg col-sm-6">
        <div class="small-box text-bg-warning shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $stats->totalPendentes ?></h3>
            <p class="mb-0">Aguardando Resposta</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-hourglass-split"></i>
          </div>
        </div>
      </div>

      <div class="col-lg col-sm-6">
        <div class="small-box text-bg-info shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $stats->taxaAssiduidade ?>%</h3>
            <p class="mb-0">Assiduidade</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-pie-chart-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Seção de Justificativas de Cancelamento (Caso existam) -->
    <?php if (!empty($stats->listaCancelamentos)) { ?>
      <div class="card card-outline card-danger shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-danger-subtle py-3">
          <h5 class="card-title fw-bold mb-0 text-danger-emphasis">
            <i class="bi bi-chat-square-quote-fill me-2"></i> Motivos de Cancelamento Informados pelo Voluntário (<?= count($stats->listaCancelamentos) ?>)
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">
            <?php foreach ($stats->listaCancelamentos as $c) { ?>
              <div class="col-md-6">
                <div class="p-3 border border-danger-subtle rounded-3 bg-body shadow-sm">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-primary font-monospace">
                      <i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y', strtotime($c->data_culto)) ?>
                    </span>
                    <span class="badge rounded-pill text-white px-2 py-1 small" style="background-color: <?= esc($c->cor_departamento ?: '#2563eb') ?>;">
                      <?= esc($c->nome_departamento) ?>: <?= esc($c->nome_area) ?>
                    </span>
                  </div>
                  <div class="fw-semibold text-body mb-2"><?= esc($c->titulo_culto) ?></div>
                  <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle text-danger-emphasis small fst-italic mb-2">
                    <i class="bi bi-chat-quote-fill me-1 text-danger"></i> "<?= nl2br(esc($c->justificativa_recusa ?: 'Nenhuma justificativa preenchida.')) ?>"
                  </div>
                  <?php if (!empty($c->data_resposta)) { ?>
                    <div class="text-end text-muted small" style="font-size: 0.72rem;">
                      <i class="bi bi-clock-history me-1"></i> Cancelado em: <?= date('d/m/Y H:i', strtotime($c->data_resposta)) ?>
                    </div>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>

    <!-- Distribuição por Sub-áreas -->
    <?php if (!empty($stats->distribuicaoAreas)) { ?>
      <div class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-body py-3">
          <h5 class="card-title fw-bold mb-0 text-primary">
            <i class="bi bi-pie-chart-fill me-2"></i> Participação por Departamento & Sub-área
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">
            <?php foreach ($stats->distribuicaoAreas as $dArea) { 
              $percent = $stats->totalEscalas > 0 ? round(($dArea['total'] / $stats->totalEscalas) * 100) : 0;
            ?>
              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-body-tertiary">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-body d-flex align-items-center gap-2">
                      <span class="badge rounded-circle" style="background-color: <?= esc($dArea['cor']) ?>; width: 12px; height: 12px;"></span>
                      <?= esc($dArea['departamento']) ?>: <?= esc($dArea['area']) ?>
                    </strong>
                    <span class="badge bg-primary rounded-pill px-3"><?= $dArea['total'] ?> escalas</span>
                  </div>
                  <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background-color: <?= esc($dArea['cor']) ?>;"></div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mt-1 text-muted small">
                    <span>Presenças: <?= $dArea['presencas'] ?> &bull; Recusas: <?= $dArea['cancelamentos'] ?></span>
                    <span><?= $percent ?>% do total servido</span>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>

    <!-- Tabela Histórico de Escalas -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-body py-3">
        <h5 class="card-title fw-bold mb-0 text-primary">
          <i class="bi bi-clock-history me-2"></i> Histórico Completo de Escalas no Período (<?= count($stats->historicoEscalas) ?>)
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4">Data do Culto</th>
                <th>Nome do Culto / Horário</th>
                <th>Departamento & Sub-área</th>
                <th class="text-center">Confirmação</th>
                <th class="text-center">Presença</th>
                <th class="pe-4">Justificativa / Observações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($stats->historicoEscalas)) { ?>
                <?php foreach ($stats->historicoEscalas as $h) { ?>
                  <tr class="<?= ($h->status_confirmacao === 'RECUSADO') ? 'table-danger-subtle' : '' ?>">
                    <td class="ps-4 fw-bold font-monospace">
                      <?= date('d/m/Y', strtotime($h->data_culto)) ?>
                    </td>
                    <td>
                      <div class="fw-semibold text-body"><?= esc($h->titulo_culto) ?></div>
                      <small class="text-muted"><i class="bi bi-clock me-1"></i><?= substr($h->horario_inicio, 0, 5) ?> às <?= substr($h->horario_termino, 0, 5) ?></small>
                    </td>
                    <td>
                      <span class="badge rounded-pill text-white px-3 py-2 fw-semibold" style="background-color: <?= esc($h->cor_departamento ?: '#2563eb') ?>;">
                        <?= esc($h->nome_departamento) ?>: <?= esc($h->nome_area) ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <?php if ($h->status_confirmacao === 'CONFIRMADO') { ?>
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                          <i class="bi bi-check-circle-fill me-1"></i> Confirmado
                        </span>
                      <?php } elseif ($h->status_confirmacao === 'RECUSADO') { ?>
                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                          <i class="bi bi-x-circle-fill me-1"></i> Recusado
                        </span>
                      <?php } else { ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">
                          <i class="bi bi-hourglass-split me-1"></i> Pendente
                        </span>
                      <?php } ?>
                    </td>
                    <td class="text-center">
                      <?php if ($h->status_presenca == 1) { ?>
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 small">
                          Presente
                        </span>
                      <?php } else { ?>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2 py-1 small">
                          Não registrado
                        </span>
                      <?php } ?>
                    </td>
                    <td class="pe-4 small">
                      <?php if (!empty($h->justificativa_recusa)) { ?>
                        <div class="text-danger fst-italic">
                          <i class="bi bi-chat-quote me-1"></i> "<?= esc($h->justificativa_recusa) ?>"
                        </div>
                      <?php } elseif (!empty($h->obs_escala)) { ?>
                        <span class="text-secondary"><?= esc($h->obs_escala) ?></span>
                      <?php } else { ?>
                        <span class="text-muted">-</span>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                    Nenhuma escala registrada para este voluntário no período selecionado.
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
