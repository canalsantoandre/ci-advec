<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Gerenciamento de usuários e perfis de acesso</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Usuários</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
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
              <i class="bi bi-person-gear me-2"></i>Lista de Usuários
            </h5>
            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3"><?= count($usuarios) ?> Usuários</span>
          </div>

          <div class="card-body p-3">
            <div class="table-responsive">
              <table class="table table-hover align-middle datatable">
                <thead class="table-light">
                  <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Usuário (Login)</th>
                    <th scope="col">Perfil</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end" style="width: 140px;">Opções</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($usuarios as $row) { ?>
                    <tr>
                      <td class="fw-semibold text-body">
                        <div class="d-flex align-items-center gap-2">
                          <img src="<?= base_url('templates/AdminLTE/dist/img/user1-128x128.jpg') ?>" class="rounded-circle border shadow-sm" style="width: 34px; height: 34px; object-fit: cover;" alt="avatar">
                          <span><?= esc($row->nome); ?></span>
                        </div>
                      </td>
                      <td><code><?= esc($row->usuario); ?></code></td>
                      <td><span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3"><?= esc($row->nome_perfil); ?></span></td>
                      <td>
                        <span class="badge rounded-pill bg-<?= ($row->status_usuario == 0 ? "danger-subtle text-danger" : "success-subtle text-success"); ?>">
                          <?= ($row->status_usuario == 0 ? "Inativo" : "Ativo"); ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <div class="btn-group btn-group-sm" role="group">
                          <?php if ($sys_action->read) { ?>
                            <a href="<?= base_url('usuario/editar/' . $row->hash_user) ?>" class="btn btn-outline-warning" title="Editar">
                              <i class="bi bi-pencil-square"></i>
                            </a>
                          <?php } ?>
                          <?php if ($sys_action->delete) { ?>
                            <button type="button" class="btn btn-outline-danger btnConfirmarDelete" data-titulo="Confirmar exclusão" data-url="<?= base_url('geral/getModalDelete') ?>" data-link="<?= base_url('usuario/apagar/' . $row->hash_user) ?>" data-mensagem="Deseja confirmar a exclusão deste usuário <strong><?= esc($row->nome) ?></strong>?" title="Apagar" data-bs-toggle="modal" data-bs-target="#divModalConfirmaDelete">
                              <i class="bi bi-trash"></i>
                            </button>
                          <?php } ?>
                        </div>
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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery !== 'undefined') {
      $(document).on("click", ".btnConfirmarDelete", function (e) {
        e.preventDefault();
        if (typeof FrameworkModal !== 'undefined') {
          FrameworkModal.getModalDelete($(this));
        }
      });
    }
  });
</script>