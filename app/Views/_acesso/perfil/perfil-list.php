<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Gestão dos perfis e níveis de permissão</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Perfis</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="d-flex justify-content-end mb-3">
      <?php if (!empty($sys_action->create)) { ?>
        <a href="<?= base_url('perfil/novo') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
          <i class="bi bi-plus-lg me-1"></i> Criar Novo Perfil de Acesso
        </a>
      <?php } ?>
    </div>

    <div class="row">
      <div class="col-12">
        
        <div class="card card-outline card-primary shadow-sm border-0">
          <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-shield-lock-fill me-2"></i>Lista de Perfis de Acesso
            </h5>
            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3"><?= count($perfil) ?> Perfis</span>
          </div>

          <div class="card-body p-3">
            <div class="table-responsive">
              <table id="tblPerfil" name="tblPerfil" class="table table-hover align-middle datatable">
                <thead class="table-light">
                  <tr>
                    <th scope="col">Nome do Perfil</th>
                    <th scope="col">Content View Padrão</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end" style="width: 100px;">Opções</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($perfil as $row) { ?>
                    <tr>
                      <td class="fw-bold text-body">
                        <i class="bi bi-shield-check text-primary me-2"></i><?= esc($row->nome_perfil); ?>
                      </td>
                      <td><code><?= esc($row->content_view_default); ?></code></td>
                      <td>
                        <span class="badge rounded-pill bg-<?= ($row->status_perfil == 0 ? "danger-subtle text-danger" : "success-subtle text-success"); ?>">
                          <?= ($row->status_perfil == 0 ? "Inativo" : "Ativo"); ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <a href="<?= base_url('perfil/editar/' . $row->id_perfil) ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3" title="Configurar Permissões">
                          <i class="bi bi-gear-fill me-1"></i> Configurar
                        </a>
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
  </div>
</div>