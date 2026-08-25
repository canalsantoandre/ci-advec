<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-graph-up-arrow text-primary me-2"></i>Dashboard do Convidado
        </h3>
        <p class="text-secondary small mb-0">Estatísticas de presença e participação nos cultos da ADVEC</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('convidado') ?>">Convidados</a></li>
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
    <div class="card card-outline card-primary shadow-sm border-0 mb-4">
      <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-3">
            <img src="<?= !empty($stats->convidado->url_foto_instagram) ? esc($stats->convidado->url_foto_instagram) : base_url('templates/AdminLTE/dist/img/user1-128x128.jpg') ?>" class="rounded-circle border shadow-sm" style="width: 70px; height: 70px; object-fit: cover;" alt="avatar">
            <div>
              <h4 class="fw-bold mb-1 text-body"><?= esc($stats->convidado->nome_convidado) ?></h4>
              <div class="d-flex flex-wrap align-items-center gap-2 text-secondary small">
                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-1 fw-bold">
                  <i class="bi bi-person-badge me-1"></i><?= esc($stats->convidado->nm_funcao_eclesiastica ?? 'Sem Função') ?>
                </span>
                <?php if (!empty($stats->convidado->nick_instagram)) { ?>
                  &bull;
                  <a href="https://www.instagram.com/<?= str_replace('@', '', $stats->convidado->nick_instagram); ?>" class="text-decoration-none text-info fw-semibold" target="_blank">
                    <i class="bi bi-instagram me-1"></i>@<?= str_replace('@', '', $stats->convidado->nick_instagram) ?>
                  </a>
                <?php } ?>
                <?php if (!empty($stats->convidado->telefone)) { ?>
                  &bull;
                  <span><i class="bi bi-telephone me-1"></i><?= esc($stats->convidado->telefone) ?></span>
                <?php } ?>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <a href="<?= base_url('agenda') ?>" class="btn btn-outline-primary rounded-pill px-4">
              <i class="bi bi-calendar-event me-1"></i> Voltar à Agenda
            </a>
            <a href="<?= base_url('convidado/editar/' . $stats->convidado->hash_convidado) ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
              <i class="bi bi-pencil-square me-1"></i> Editar Cadastro
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Stat Widgets Row -->
    <div class="row g-3 mb-4">
      <div class="col-lg-4 col-sm-6">
        <div class="small-box text-bg-success shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $stats->totalPresencas ?></h3>
            <p class="mb-0">Vezes que esteve na ADVEC</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-check-circle-fill"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-6">
        <div class="small-box text-bg-primary shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $stats->totalCultosRealizados ?></h3>
            <p class="mb-0">Total de Cultos Realizados</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-building"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-12">
        <div class="small-box text-bg-info shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $stats->percentualAssiduidade ?>%</h3>
            <p class="mb-0">Taxa de Assiduidade</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-pie-chart-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Cultos Participados vs Ausentes Row -->
    <div class="row g-4 mb-4">
      
      <!-- Cultos Participados -->
      <div class="col-lg-6">
        <div class="card card-outline card-success shadow-sm border-0 h-100">
          <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-success">
              <i class="bi bi-calendar-check-fill me-2"></i>Cultos em que Participou (<?= count($stats->cultosPresente) ?>)
            </h5>
            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3">Presença Confirmada</span>
          </div>

          <div class="card-body p-3">
            <?php if (count($stats->cultosPresente) > 0) { ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th scope="col">Data</th>
                      <th scope="col">Horário</th>
                      <th scope="col">Culto / Evento</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($stats->cultosPresente as $culto) { ?>
                      <tr>
                        <td class="fw-bold text-success">
                          <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($culto->data_culto)) ?>
                        </td>
                        <td class="small text-secondary">
                          <?= substr($culto->horario_inicio, 0, 5) ?> - <?= substr($culto->horario_termino, 0, 5) ?>
                        </td>
                        <td class="fw-semibold text-body">
                          <span class="badge rounded-pill me-1" style="background-color: <?= esc($culto->cor_evento ?? '#2563eb') ?>;">&nbsp;</span>
                          <?= esc($culto->titulo_culto) ?>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                <p class="mb-0">Nenhuma presença em cultos registrada até o momento.</p>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <!-- Cultos Ausente -->
      <div class="col-lg-6">
        <div class="card card-outline card-danger shadow-sm border-0 h-100">
          <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-danger">
              <i class="bi bi-calendar-x-fill me-2"></i>Cultos em que NÃO Participou (<?= count($stats->cultosAusente) ?>)
            </h5>
            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3">Ausente</span>
          </div>

          <div class="card-body p-3">
            <?php if (count($stats->cultosAusente) > 0) { ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th scope="col">Data</th>
                      <th scope="col">Horário</th>
                      <th scope="col">Culto / Evento</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($stats->cultosAusente as $culto) { ?>
                      <tr>
                        <td class="fw-bold text-danger">
                          <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($culto->data_culto)) ?>
                        </td>
                        <td class="small text-secondary">
                          <?= substr($culto->horario_inicio, 0, 5) ?> - <?= substr($culto->horario_termino, 0, 5) ?>
                        </td>
                        <td class="fw-semibold text-body">
                          <span class="badge rounded-pill me-1" style="background-color: <?= esc($culto->cor_evento ?? '#2563eb') ?>;">&nbsp;</span>
                          <?= esc($culto->titulo_culto) ?>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="text-center py-5 text-muted">
                <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                <p class="mb-0">Sem registros de faltas nos cultos agendados.</p>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>
