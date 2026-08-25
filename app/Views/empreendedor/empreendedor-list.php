<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Gestão e acompanhamento de empreendedores inscritos</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= esc($sys_module->nome_modulo) ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    
    <!-- Stats Row -->
    <div class="row g-3 mb-4">
      <div class="col-lg-4 col-sm-6">
        <div class="small-box text-bg-warning shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $totalGeral - $totalConvidados ?></h3>
            <p class="mb-0">Inscritos Diretos</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-person-fill-add"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-6">
        <div class="small-box text-bg-primary shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $totalConvidados ?></h3>
            <p class="mb-0">Total de Convidados</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-people-fill"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-12">
        <div class="small-box text-bg-success shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $totalGeral ?></h3>
            <p class="mb-0">Total Geral de Participantes</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-bar-chart-line-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions & Data Card -->
    <div class="row">
      <div class="col-12">
        <?php if ($sys_action->create) { ?>
          <div class="mb-3 d-flex justify-content-end">
            <a href="<?= base_url('usuario/novo') ?>" class="btn btn-primary shadow-sm rounded-pill px-4">
              <i class="bi bi-person-plus-fill me-2"></i> Incluir Novo Usuário
            </a>
          </div>
        <?php } ?>

        <div class="card card-outline card-primary shadow-sm border-0">
          <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-table me-2"></i>Lista de Inscritos
            </h5>
            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3"><?= count($empreendedor) ?> Registros</span>
          </div>

          <div class="card-body p-3">
            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle" id="tblInscritos">
                <thead class="table-light">
                  <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Telefone</th>
                    <th scope="col" class="text-center">Convidados</th>
                    <th scope="col">Instagram</th>
                    <th scope="col">Ramo de Atividade</th>
                    <th scope="col">Data Inscrição</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($empreendedor as $row) { ?>
                    <tr>
                      <td class="fw-semibold text-body"><?= esc($row->nome); ?></td>
                      <td>
                        <a href="mailto:<?= esc($row->email); ?>" class="text-decoration-none">
                          <i class="bi bi-envelope me-1 text-secondary"></i><?= esc($row->email); ?>
                        </a>
                      </td>
                      <td>
                        <a href="tel:<?= esc($row->telefone); ?>" class="text-decoration-none text-body">
                          <i class="bi bi-telephone me-1 text-secondary"></i><?= esc($row->telefone); ?>
                        </a>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 fw-bold">
                          <?= esc($row->qtde_convidado) ?>
                        </span>
                      </td>
                      <td>
                        <?php if (!empty($row->instagram)) { ?>
                          <span class="badge bg-info-subtle text-info-emphasis">
                            <i class="bi bi-instagram me-1"></i><?= esc($row->instagram); ?>
                          </span>
                        <?php } else { ?>
                          <span class="text-muted small">-</span>
                        <?php } ?>
                      </td>
                      <td><span class="badge bg-light text-dark border"><?= esc($row->ramo_atividade); ?></span></td>
                      <td class="small text-secondary"><?= date('d/m/Y H:i', strtotime($row->date_insert)); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr class="table-group-divider fw-bold">
                    <td colspan="3" class="text-end">Resumo:</td>
                    <td class="text-center"><span class="badge bg-primary rounded-pill"><?= $totalConvidados; ?></span></td>
                    <td></td>
                    <td class="text-end">TOTAL GERAL:</td>
                    <td><span class="badge bg-success rounded-pill px-3"><?= $totalGeral ?></span></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<div class="modal fade" id="divModalConfirmaDelete" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="divModalDelete"></div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
      $('#tblInscritos').DataTable({
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json',
        },
        pageLength: 25,
        order: [[0, 'asc']]
      });
    }
  });
</script>