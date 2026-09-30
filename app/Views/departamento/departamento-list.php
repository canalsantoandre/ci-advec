<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-diagram-3-fill text-primary me-2"></i>Departamentos & Sub-áreas
        </h3>
        <p class="text-secondary small mb-0">Gestão dos departamentos, equipes e suas respectivas sub-áreas de atuação</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Departamentos</li>
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
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold">
          <i class="bi bi-building me-1"></i> Total: <?= count($departamentos) ?> Departamentos
        </span>
      </div>

      <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('escala/grade') ?>" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
          <i class="bi bi-calendar-check me-1"></i> Ver Grade de Escalas
        </a>

        <?php if (!empty($sys_action->create)) { ?>
          <a href="<?= base_url('departamento/novo') ?>" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Novo Departamento
          </a>
        <?php } ?>
      </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')) { ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
        <div><?= session()->getFlashdata('success') ?></div>
      </div>
    <?php } ?>
    <?php if (session()->getFlashdata('error')) { ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <div><?= session()->getFlashdata('error') ?></div>
      </div>
    <?php } ?>

    <!-- Tabela Principal -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col" class="ps-4">Departamento</th>
                <th scope="col">Responsável & Contato</th>
                <th scope="col" class="text-center">Sub-áreas</th>
                <th scope="col" class="text-center">Voluntários</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-end pe-4" style="width: 150px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($departamentos)) { ?>
                <?php foreach ($departamentos as $d) { 
                  $cor = !empty($d->cor_identificacao) ? $d->cor_identificacao : '#2563eb';
                  $defaultLogo = 'https://ui-avatars.com/api/?name=' . urlencode($d->nome) . '&background=' . str_replace('#', '', $cor) . '&color=fff&size=80&bold=true';
                  $logoSrc = !empty($d->logo_url) ? $d->logo_url : $defaultLogo;
                ?>
                  <tr>
                    <td class="ps-4">
                      <div class="d-flex align-items-center gap-3">
                        <img src="<?= esc($logoSrc) ?>" alt="<?= esc($d->nome) ?>" class="rounded-3 shadow-sm border" style="width: 46px; height: 46px; object-fit: cover;" onerror="this.onerror=null;this.src='<?= $defaultLogo ?>';">
                        <div>
                          <div class="fw-bold text-body fs-6 d-flex align-items-center gap-2">
                            <span class="badge rounded-circle p-1" style="background-color: <?= esc($cor) ?>; width: 10px; height: 10px; display: inline-block;"></span>
                            <?= esc($d->nome) ?>
                          </div>
                          <?php if (!empty($d->descricao)) { ?>
                            <small class="text-secondary text-truncate d-inline-block" style="max-width: 320px;" title="<?= esc($d->descricao) ?>">
                              <?= esc($d->descricao) ?>
                            </small>
                          <?php } ?>
                        </div>
                      </div>
                    </td>

                    <td>
                      <div class="fw-semibold text-body"><?= esc($d->responsavel_nome) ?></div>
                      <small class="text-muted">
                        <a href="https://wa.me/55<?= preg_replace('/\D/', '', $d->responsavel_telefone) ?>" target="_blank" class="text-decoration-none text-success">
                          <i class="bi bi-whatsapp me-1"></i><?= esc($d->responsavel_telefone) ?>
                        </a>
                      </small>
                    </td>

                    <td class="text-center">
                      <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-2 fw-bold">
                        <i class="bi bi-grid-fill me-1"></i><?= (int)$d->total_areas ?> áreas
                      </span>
                    </td>

                    <td class="text-center">
                      <a href="<?= base_url('voluntario?id_departamento=' . $d->id_departamento) ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold text-decoration-none" title="Ver voluntários deste departamento">
                        <i class="bi bi-people-fill me-1"></i><?= (int)$d->total_voluntarios ?> voluntários
                      </a>
                    </td>

                    <td class="text-center">
                      <?php if ($d->status == 1) { ?>
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 fw-semibold">
                          <i class="bi bi-check-circle-fill me-1"></i> Ativo
                        </span>
                      <?php } else { ?>
                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1 fw-semibold">
                          <i class="bi bi-x-circle-fill me-1"></i> Inativo
                        </span>
                      <?php } ?>
                    </td>

                    <td class="text-end pe-4">
                      <div class="d-inline-flex gap-1">
                        <a href="<?= base_url('escala/grade/' . $d->id_departamento) ?>" class="btn btn-outline-info btn-action" title="Grade de Escalas">
                          <i class="bi bi-calendar-check"></i>
                        </a>

                        <?php if (!empty($sys_action->update)) { ?>
                          <a href="<?= base_url('departamento/editar/' . $d->id_departamento) ?>" class="btn btn-outline-primary btn-action" title="Editar Departamento & Áreas">
                            <i class="bi bi-pencil-fill"></i>
                          </a>
                        <?php } ?>

                        <?php if (!empty($sys_action->delete)) { ?>
                          <button type="button" class="btn btn-outline-danger btn-action" title="Excluir Departamento" onclick="confirmarExclusao(<?= $d->id_departamento ?>, '<?= esc($d->nome) ?>')">
                            <i class="bi bi-trash3-fill"></i>
                          </button>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-building-slash fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-2 fw-semibold">Nenhum departamento cadastrado.</p>
                    <?php if (!empty($sys_action->create)) { ?>
                      <a href="<?= base_url('departamento/novo') ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="bi bi-plus-lg me-1"></i> Cadastrar Primeiro Departamento
                      </a>
                    <?php } ?>
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

<!-- Modal Confirmação de Exclusão -->
<div class="modal fade" id="modalConfirmarExclusao" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Exclusão
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body py-3">
        <p class="mb-0">Deseja realmente excluir o departamento <strong id="modalNomeDep"></strong>?</p>
        <small class="text-danger d-block mt-2">
          <i class="bi bi-info-circle me-1"></i> Esta ação removerá também as sub-áreas vinculadas se não houverem escalas ativas.
        </small>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnConfirmarDeleteDep" class="btn btn-danger rounded-pill px-4 fw-bold">
          <i class="bi bi-trash3-fill me-1"></i> Excluir Departamento
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  function confirmarExclusao(id, nome) {
    document.getElementById('modalNomeDep').textContent = nome;
    document.getElementById('btnConfirmarDeleteDep').href = '<?= base_url('departamento/apagar/') ?>/' + id;
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarExclusao'));
    modal.show();
  }
</script>
