<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-menu-button-wide-fill text-primary me-2"></i>Módulos e Menus do Sistema
        </h3>
        <p class="text-secondary small mb-0">Gerenciamento de categorias, menus e ações do sistema</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Módulos</li>
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
      <button type="button" class="btn btn-outline-secondary rounded-pill px-3 btn-sm" id="btnNovaCategoria">
        <i class="bi bi-folder-plus me-1"></i> Nova Categoria
      </button>

      <?php if (!empty($sys_action->create)) { ?>
        <a href="<?= base_url('sysmodulo/novo') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
          <i class="bi bi-plus-lg me-1"></i> Criar Novo Módulo / Menu
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

    <!-- Categories & Modules Accordion -->
    <div class="row">
      <div class="col-12">
        <div class="accordion" id="accordionCategorias">
          <?php foreach ($modulosPorCategoria as $catId => $dataCat) { 
            $cat = $dataCat['categoria'];
            $menus = $dataCat['menus'];
          ?>
            <div class="card card-outline card-primary shadow-sm border-0 mb-3 rounded-3 overflow-hidden">
              <div class="card-header bg-body py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <i class="<?= !empty($cat->icon_class_categoria) ? esc($cat->icon_class_categoria) : 'bi bi-folder-fill' ?> text-primary fs-5"></i>
                  <h5 class="fw-bold mb-0 text-primary"><?= esc($cat->nome_categoria_modulo) ?></h5>
                  <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3"><?= count($menus) ?> Menus</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                  <button type="button" class="btn btn-outline-warning btn-sm rounded-pill btnEditarCategoria" data-id="<?= $cat->id_categoria_modulo ?>" data-nome="<?= esc($cat->nome_categoria_modulo) ?>" title="Editar Categoria">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <?php if (count($menus) == 0) { ?>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" data-titulo="Confirmar exclusão" data-url="<?= base_url('geral/getModalDelete') ?>" data-link="<?= base_url('sysmodulo/apagarCategoria/' . $cat->id_categoria_modulo) ?>" data-mensagem="Deseja excluir a categoria <strong><?= esc($cat->nome_categoria_modulo) ?></strong>?" title="Excluir Categoria" data-bs-toggle="modal" data-bs-target="#divModalConfirmaDelete" id="btnConfirmarDelete">
                      <i class="bi bi-trash"></i>
                    </button>
                  <?php } ?>
                </div>
              </div>

              <div class="card-body p-0">
                <?php if (count($menus) > 0) { ?>
                  <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                      <thead class="table-light">
                        <tr>
                          <th scope="col" style="width: 40px;">#</th>
                          <th scope="col">Nome do Módulo / URI</th>
                          <th scope="col">Ações Suportadas</th>
                          <th scope="col" class="text-center">Status</th>
                          <th scope="col" class="text-end" style="width: 120px;">Opções</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($menus as $m) { ?>
                          <tr>
                            <td class="text-secondary"><i class="bi bi-grip-vertical"></i></td>
                            <td>
                              <div class="d-flex align-items-center gap-2">
                                <i class="<?= !empty($m->icon_class_modulo) ? esc($m->icon_class_modulo) : 'bi bi-app-indicator' ?> text-primary"></i>
                                <div>
                                  <div class="fw-bold text-body"><?= esc($m->nome_modulo) ?></div>
                                  <small class="text-muted font-monospace"><?= esc($m->uri_modulo) ?></small>
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($m->acoes as $act) { ?>
                                  <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1 small">
                                    <?= esc($act) ?>
                                  </span>
                                <?php } ?>
                              </div>
                            </td>
                            <td class="text-center">
                              <span class="badge rounded-pill bg-<?= ($m->status_modulo == 1 ? 'success-subtle text-success' : 'danger-subtle text-danger') ?>">
                                <?= ($m->status_modulo == 1 ? 'Ativo' : 'Inativo') ?>
                              </span>
                            </td>
                            <td class="text-end">
                              <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('sysmodulo/editar/' . $m->id_modulo) ?>" class="btn btn-outline-warning" title="Editar">
                                  <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" data-titulo="Confirmar exclusão" data-url="<?= base_url('geral/getModalDelete') ?>" data-link="<?= base_url('sysmodulo/apagar/' . $m->id_modulo) ?>" data-mensagem="Deseja realmente excluir o módulo <strong><?= esc($m->nome_modulo) ?></strong>?" title="Excluir" data-bs-toggle="modal" data-bs-target="#divModalConfirmaDelete" id="btnConfirmarDelete">
                                  <i class="bi bi-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>

                          <!-- Subitens (Submenus) -->
                          <?php foreach ($m->subitens as $sub) { ?>
                            <tr class="table-light">
                              <td></td>
                              <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                  <i class="bi bi-arrow-return-right text-secondary"></i>
                                  <i class="<?= !empty($sub->icon_class_modulo) ? esc($sub->icon_class_modulo) : 'bi bi-circle' ?> text-secondary"></i>
                                  <div>
                                    <div class="fw-semibold text-body"><?= esc($sub->nome_modulo) ?></div>
                                    <small class="text-muted font-monospace"><?= esc($sub->uri_modulo) ?></small>
                                  </div>
                                </div>
                              </td>
                              <td>
                                <div class="d-flex flex-wrap gap-1">
                                  <?php foreach ($sub->acoes as $actSub) { ?>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1 small">
                                      <?= esc($actSub) ?>
                                    </span>
                                  <?php } ?>
                                </div>
                              </td>
                              <td class="text-center">
                                <span class="badge rounded-pill bg-<?= ($sub->status_modulo == 1 ? 'success-subtle text-success' : 'danger-subtle text-danger') ?>">
                                  <?= ($sub->status_modulo == 1 ? 'Ativo' : 'Inativo') ?>
                                </span>
                              </td>
                              <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                  <a href="<?= base_url('sysmodulo/editar/' . $sub->id_modulo) ?>" class="btn btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                  </a>
                                  <button type="button" class="btn btn-outline-danger" data-titulo="Confirmar exclusão" data-url="<?= base_url('geral/getModalDelete') ?>" data-link="<?= base_url('sysmodulo/apagar/' . $sub->id_modulo) ?>" data-mensagem="Deseja realmente excluir o submenu <strong><?= esc($sub->nome_modulo) ?></strong>?" title="Excluir" data-bs-toggle="modal" data-bs-target="#divModalConfirmaDelete" id="btnConfirmarDelete">
                                    <i class="bi bi-trash"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          <?php } ?>

                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } else { ?>
                  <div class="text-center py-4 text-muted">
                    <small>Nenhum menu cadastrado nesta categoria.</small>
                  </div>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal Categoria -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-primary text-white rounded-top-4 py-3">
        <h5 class="modal-title fw-bold" id="modalCatTitle">
          <i class="bi bi-folder-plus me-2"></i>Nova Categoria
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formCategoria">
        <div class="modal-body p-4">
          <input type="hidden" name="id_categoria_modulo" id="cat_id" value="">
          <div class="mb-3">
            <label for="cat_nome" class="form-label fw-semibold">Nome da Categoria</label>
            <input type="text" class="form-control" name="nome_categoria_modulo" id="cat_nome" placeholder="Ex: RELATÓRIOS" required>
          </div>
        </div>
        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-save me-1"></i> Salvar Categoria
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalCatEl = new bootstrap.Modal(document.getElementById('modalCategoria'));
    const btnNovaCat = document.getElementById('btnNovaCategoria');

    if (btnNovaCat) {
      btnNovaCat.addEventListener('click', function() {
        document.getElementById('cat_id').value = '';
        document.getElementById('cat_nome').value = '';
        document.getElementById('modalCatTitle').innerHTML = '<i class="bi bi-folder-plus me-2"></i>Nova Categoria';
        modalCatEl.show();
      });
    }

    document.querySelectorAll('.btnEditarCategoria').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const nome = this.getAttribute('data-nome');
        document.getElementById('cat_id').value = id;
        document.getElementById('cat_nome').value = nome;
        document.getElementById('modalCatTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Categoria';
        modalCatEl.show();
      });
    });

    document.getElementById('formCategoria').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('<?= base_url('sysmodulo/salvarCategoria') ?>', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.erro === 0) {
          location.reload();
        } else {
          if (typeof USToast !== 'undefined') {
            USToast.show('error', 'Gerenciador de Módulos', data.mensagem);
          } else if (typeof $.messageAlert === 'function') {
            $.messageAlert({ tipoMensagem: 2, mensagemDestaque: 'Aviso', mensagem: data.mensagem });
          }
        }
      });
    });
  });
</script>
