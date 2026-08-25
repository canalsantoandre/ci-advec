<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Gestão de contatos recebidos pelos formulários do site</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Contatos</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Stats Widget -->
    <div class="row g-3 mb-4">
      <div class="col-lg-4 col-sm-6">
        <div class="small-box text-bg-success shadow-sm">
          <div class="inner p-3">
            <h3 class="fw-bold"><?= $totalGeral ?></h3>
            <p class="mb-0">Total de Contatos Cadastrados</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-telephone-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table Card -->
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-success shadow-sm border-0">
          <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-success">
              <i class="bi bi-list-stars me-2"></i>Lista de Contatos
            </h5>
            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3"><?= count($contato) ?> Registros</span>
          </div>

          <div class="card-body p-3">
            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle" id="tblInscritos">
                <thead class="table-light">
                  <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Telefone</th>
                    <th scope="col">Instagram</th>
                    <th scope="col">Data de Cadastro</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($contato as $row) { ?>
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
                      <td>
                        <?php if (!empty($row->instagram)) { ?>
                          <span class="badge bg-info-subtle text-info-emphasis">
                            <i class="bi bi-instagram me-1"></i><?= esc($row->instagram); ?>
                          </span>
                        <?php } else { ?>
                          <span class="text-muted small">-</span>
                        <?php } ?>
                      </td>
                      <td class="small text-secondary"><?= date('d/m/Y H:i', strtotime($row->date_insert)); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr class="table-group-divider fw-bold">
                    <td colspan="3"></td>
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