<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Configuração do perfil <?= esc($perfil->nome_perfil); ?></p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('perfil') ?>"><?= esc($sys_module->nome_modulo) ?></a></li>
          <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row g-4">
      
      <!-- Profile Info Column -->
      <div class="col-lg-5">
        <div class="card card-outline card-info shadow-sm border-0">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-info">
              <i class="bi bi-info-circle-fill me-2"></i>Informações Principais
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" id="frm_perfil" name="frm_perfil" action="<?= base_url('/perfil/atualizar') ?>">
              <input type="hidden" name="id_perfil" id="id_perfil" value="<?= esc($perfil->id_perfil); ?>">

              <div class="mb-3">
                <label for="txtPerfilNome" class="form-label fw-semibold">Nome do Perfil</label>
                <input type="text" name="txtPerfilNome" id="txtPerfilNome" value="<?= esc($perfil->nome_perfil); ?>" class="form-control" maxlength="100" required>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Content View Padrão</label>
                <div class="form-control bg-light text-muted font-monospace">
                  <?= esc($perfil->content_view_default); ?>
                </div>
              </div>

              <div class="mb-4">
                <label for="cboPerfilStatus" class="form-label fw-semibold">Status do Perfil</label>
                <select class="form-select" name="cboPerfilStatus" id="cboPerfilStatus">
                  <option value="1" <?= ($perfil->status_perfil == 1 ? "selected" : ""); ?>>Ativo</option>
                  <option value="0" <?= ($perfil->status_perfil == 0 ? "selected" : ""); ?>>Inativo</option>
                </select>
              </div>

              <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('perfil/') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                  <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
                <button type="submit" class="btn btn-info text-white rounded-pill px-4 shadow-sm fw-bold">
                  <i class="bi bi-check-lg me-1"></i> Salvar Perfil
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modules & Permissions Column -->
      <div class="col-lg-7">
        <div class="card card-outline card-primary shadow-sm border-0">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-key-fill me-2"></i>Módulos e Permissões de Acesso
            </h5>
          </div>

          <div class="card-body p-3">
            <div class="table-responsive">
              <table id="tblPerfils" name="tblPerfils" class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th scope="col">Módulo</th>
                    <th scope="col" class="text-end" style="width: 120px;">Acesso</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $nome_categoria_modulo = "";
                  foreach ($perfil_modulo as $row) {
                    if ($row->nome_categoria_modulo != $nome_categoria_modulo) {
                  ?>
                      <tr class="table-secondary">
                        <td colspan="2" class="fw-bold text-uppercase small tracking-wide px-3 py-2">
                          <i class="bi bi-folder2-open me-2"></i><?= esc($row->nome_categoria_modulo) ?>
                        </td>
                      </tr>
                    <?php
                    }
                    $nome_categoria_modulo = $row->nome_categoria_modulo;
                    ?>
                    <tr>
                      <td class="fw-semibold">
                        <i class="<?= esc($row->icon_class_modulo); ?> me-2 text-primary"></i> <?= esc($row->nome_modulo); ?>
                      </td>
                      <td class="text-end">
                        <div class="form-check form-switch d-inline-block m-0">
                          <input class="form-check-input" type="checkbox" data-url="<?= base_url('perfil/atualizarModulo'); ?>" data-baseurl="<?= base_url('perfil/atualizarModulo'); ?>" data-id-perfil="<?= esc($perfil->id_perfil); ?>" data-id-modulo="<?= esc($row->id_modulo); ?>" data-nome-modulo="<?= esc($row->nome_categoria_modulo . "->" . $row->nome_modulo) ?>" id="chkModulo_<?= $row->id_modulo; ?>" name="chkModulo_<?= $row->id_modulo; ?>" <?= ($row->id_modulo_perfil == 0 ? "" : "checked"); ?>>
                        </div>
                      </td>
                    </tr>

                    <?php if (count($row->perfil_acoes) > 0) { ?>
                      <tr id="trPerfil<?= $row->id_modulo; ?>" class="bg-body-tertiary" style="display: <?= ($row->id_modulo_perfil == 0 ? "none" : ""); ?>;">
                        <td colspan="2" class="ps-4 py-2">
                          <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($row->perfil_acoes as $rowD) { ?>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" data-url="<?= base_url('perfil/atualizarModuloAcao'); ?>" data-baseurl="<?= base_url('perfil/atualizarModuloAcao'); ?>" data-id-perfil="<?= esc($perfil->id_perfil); ?>" data-id-modulo="<?= esc($row->id_modulo); ?>" data-modulo-acao="<?= esc($rowD->modulo_acao); ?>" id="chkModuloAcao_<?= $row->id_modulo . '_' . $rowD->modulo_acao; ?>" name="chkModuloAcao_<?= $row->id_modulo . '_' . $rowD->modulo_acao; ?>" <?= ($rowD->perfil_acao == 0 ? "" : "checked"); ?>>
                                <label class="form-check-label small fw-semibold" for="chkModuloAcao_<?= $row->id_modulo . '_' . $rowD->modulo_acao; ?>">
                                  <?= esc($rowD->modulo_acao); ?>
                                </label>
                              </div>
                            <?php } ?>
                          </div>
                        </td>
                      </tr>
                    <?php } ?>

                    <?php foreach ($row->subitem as $s) { ?>
                      <tr class="ps-4">
                        <td class="ps-4 text-secondary small">
                          <i class="bi bi-arrow-return-right me-2"></i><?= esc($s->nome_modulo); ?>
                        </td>
                        <td class="text-end">
                          <div class="form-check form-switch d-inline-block m-0">
                            <input class="form-check-input" type="checkbox" data-url="<?= base_url('perfil/atualizarModulo'); ?>" data-baseurl="<?= base_url('perfil/atualizarModulo'); ?>" data-id-perfil="<?= esc($perfil->id_perfil); ?>" data-id-modulo="<?= esc($s->id_modulo); ?>" data-nome-modulo="<?= esc($s->nome_categoria_modulo . "->" . $s->nome_modulo) ?>" id="chkModulo_<?= $s->id_modulo; ?>" name="chkModulo_<?= $s->id_modulo; ?>" <?= ($s->id_modulo_perfil == 0 ? "" : "checked"); ?>>
                          </div>
                        </td>
                      </tr>
                    <?php } ?>

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

<script type="text/javascript" src="<?= base_url('templates/js') ?>/perfil/validacoes.js"></script>