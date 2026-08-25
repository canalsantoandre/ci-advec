<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Tipos de Culto Padrão
        </h3>
        <p class="text-secondary small mb-0">Cadastro prévio de cultos por dia da semana, horário e regras de recorrência para a agenda</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('agenda') ?>">Agenda</a></li>
          <li class="breadcrumb-item active" aria-current="page">Tipos de Culto</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
        <i class="bi bi-list-stars me-1"></i> Total: <?= count($cultosPadrao) ?> Modelos de Culto
      </span>

      <?php if (!empty($sys_action->create)) { ?>
        <a href="<?= base_url('cultopadrao/novo') ?>" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
          <i class="bi bi-plus-lg me-1"></i> Cadastrar Novo Tipo de Culto
        </a>
      <?php } ?>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')) { ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
      </div>
    <?php } ?>
    <?php if (session()->getFlashdata('error')) { ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
      </div>
    <?php } ?>

    <!-- Tabela Principal -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col" class="ps-4">Nome do Culto / Modelo</th>
                <th scope="col">Dia da Semana</th>
                <th scope="col">Horário</th>
                <th scope="col">Recorrência no Mês</th>
                <th scope="col">Cor no Calendário</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-end pe-4" style="width: 130px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($cultosPadrao)) { ?>
                <?php foreach ($cultosPadrao as $c) { 
                  $nomeDia = \App\Models\CultoPadraoModel::getNomeDiaSemana($c->dia_semana);
                  $cor = !empty($c->cor_evento) ? $c->cor_evento : '#2563eb';

                  $tRec = $c->tipo_recorrencia ?? 'todas';
                  $pos  = $c->posicao_semana ?? '';
                  $lblRec = 'Todas as semanas';
                  $badgeClass = 'bg-secondary-subtle text-secondary-emphasis';

                  if ($tRec === 'apenas_posicao') {
                    $lblRec = "Apenas na {$pos}ª {$nomeDia}";
                    $badgeClass = 'bg-info-subtle text-info-emphasis';
                  } elseif ($tRec === 'exceto_posicao') {
                    $lblRec = "Exceto na {$pos}ª {$nomeDia}";
                    $badgeClass = 'bg-warning-subtle text-warning-emphasis';
                  } elseif ($tRec === 'santa_ceia_domingo') {
                    $lblRec = "Santa Ceia Domingo (1º/2º Dom)";
                    $badgeClass = 'bg-danger-subtle text-danger-emphasis';
                  } elseif ($tRec === 'exceto_santa_ceia_domingo') {
                    $lblRec = "Todos Dom. (Exceto Sta. Ceia)";
                    $badgeClass = 'bg-primary-subtle text-primary-emphasis';
                  }
                ?>
                  <tr>
                    <td class="ps-4">
                      <div class="fw-bold text-body fs-6"><?= esc($c->nome_culto) ?></div>
                      <?php if (!empty($c->descricao)) { ?>
                        <small class="text-secondary d-block"><?= esc($c->descricao) ?></small>
                      <?php } ?>
                    </td>

                    <td>
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold">
                        <i class="bi bi-calendar-day me-1"></i><?= esc($nomeDia) ?>
                      </span>
                    </td>

                    <td>
                      <span class="font-monospace fw-semibold text-body">
                        <i class="bi bi-clock me-1 text-secondary"></i>
                        <?= substr($c->horario_inicio, 0, 5) ?> às <?= substr($c->horario_termino, 0, 5) ?>
                      </span>
                    </td>

                    <td>
                      <span class="badge rounded-pill px-3 py-2 fw-bold <?= $badgeClass ?>">
                        <i class="bi bi-repeat me-1"></i><?= esc($lblRec) ?>
                      </span>
                    </td>

                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-inline-block border" style="width: 20px; height: 20px; background-color: <?= esc($cor) ?>;"></span>
                        <span class="small font-monospace text-secondary"><?= esc($cor) ?></span>
                      </div>
                    </td>

                    <td class="text-center">
                      <span class="badge rounded-pill bg-<?= ($c->status_culto == 1 ? 'success-subtle text-success' : 'danger-subtle text-danger') ?> px-3 py-1">
                        <?= ($c->status_culto == 1 ? 'Ativo' : 'Inativo') ?>
                      </span>
                    </td>

                    <td class="text-end pe-4">
                      <div class="btn-group btn-group-sm">
                        <?php if (!empty($sys_action->update)) { ?>
                          <a href="<?= base_url('cultopadrao/editar/' . $c->id_culto_padrao) ?>" class="btn btn-outline-warning" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                          </a>
                        <?php } ?>
                        <?php if (!empty($sys_action->delete)) { ?>
                          <button type="button" class="btn btn-outline-danger" data-titulo="Confirmar exclusão" data-url="<?= base_url('geral/getModalDelete') ?>" data-link="<?= base_url('cultopadrao/apagar/' . $c->id_culto_padrao) ?>" data-mensagem="Deseja confirmar a exclusão do culto padrão <strong><?= esc($c->nome_culto) ?></strong>?" title="Apagar" data-bs-toggle="modal" data-bs-target="#divModalConfirmaDelete" id="btnConfirmarDelete">
                            <i class="bi bi-trash"></i>
                          </button>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                    Nenhum tipo de culto padrão cadastrado ainda.
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
