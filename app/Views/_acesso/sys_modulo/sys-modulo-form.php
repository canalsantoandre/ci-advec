<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-menu-button-wide-fill text-primary me-2"></i><?= $modulo ? 'Editar Módulo' : 'Novo Módulo / Menu' ?>
        </h3>
        <p class="text-secondary small mb-0">Configuração de rotas, ícones, categoria e permissões de ação</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('sysmodulo') ?>">Módulos</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= $modulo ? 'Editar' : 'Novo' ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-lg-8">

        <div class="card card-outline card-primary shadow-sm border-0">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-sliders me-2"></i>Parâmetros do Módulo
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" action="<?= base_url('sysmodulo/salvar') ?>">
              <input type="hidden" name="id_modulo" value="<?= $modulo ? esc($modulo->id_modulo) : '' ?>">

              <div class="row g-3 mb-3">
                <div class="col-md-8">
                  <label for="nome_modulo" class="form-label fw-semibold">Nome do Módulo (Menu)</label>
                  <input type="text" class="form-control" name="nome_modulo" id="nome_modulo" value="<?= $modulo ? esc($modulo->nome_modulo) : '' ?>" placeholder="Ex: Gestão de Membros" required>
                </div>
                <div class="col-md-4">
                  <label for="uri_modulo" class="form-label fw-semibold">URI / Rota do Módulo</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input type="text" class="form-control font-monospace" name="uri_modulo" id="uri_modulo" value="<?= $modulo ? esc($modulo->uri_modulo) : '' ?>" placeholder="membro/" required>
                  </div>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="id_categoria_modulo" class="form-label fw-semibold">Categoria no Menu</label>
                  <select class="form-select" name="id_categoria_modulo" id="id_categoria_modulo" required>
                    <option value="">Selecione uma Categoria</option>
                    <?php foreach ($categorias as $cat) { ?>
                      <option value="<?= $cat->id_categoria_modulo ?>" <?= ($modulo && $modulo->id_categoria_modulo == $cat->id_categoria_modulo) ? 'selected' : '' ?>>
                        <?= esc($cat->nome_categoria_modulo) ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="id_modulo_pai" class="form-label fw-semibold">Módulo Pai (Menu Principal)</label>
                  <select class="form-select" name="id_modulo_pai" id="id_modulo_pai">
                    <option value="">Nenhum (É um Menu Principal)</option>
                    <?php foreach ($modulosPai as $pai) { ?>
                      <option value="<?= $pai->id_modulo ?>" <?= ($modulo && $modulo->id_modulo_pai == $pai->id_modulo) ? 'selected' : '' ?>>
                        <?= esc($pai->nome_modulo) ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="icon_class_modulo" class="form-label fw-semibold">Classe do Ícone (Bootstrap Icons / FontAwesome)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-app-indicator"></i></span>
                    <input type="text" class="form-control font-monospace" name="icon_class_modulo" id="icon_class_modulo" value="<?= $modulo ? esc($modulo->icon_class_modulo) : 'bi bi-app-indicator' ?>" placeholder="bi bi-people-fill">
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="status_modulo" class="form-label fw-semibold">Status do Módulo</label>
                  <select class="form-select" name="status_modulo" id="status_modulo">
                    <option value="1" <?= (!$modulo || $modulo->status_modulo == 1) ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= ($modulo && $modulo->status_modulo == 0) ? 'selected' : '' ?>>Inativo</option>
                  </select>
                </div>
              </div>

              <!-- Ações Suportadas -->
              <div class="mb-4">
                <label class="form-label fw-semibold">Ações Suportadas pelo Módulo</label>
                <p class="text-secondary small mb-2">Marque as permissões específicas que este módulo suportará para os perfis de acesso:</p>
                <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded-3 border">
                  <?php 
                  $todasAcoes = ['read' => 'Visualizar (read)', 'create' => 'Incluir (create)', 'update' => 'Editar (update)', 'delete' => 'Excluir (delete)', 'reset_password' => 'Resetar Senha', 'linkfotos_visualizar' => 'Ver Fotos'];
                  foreach ($todasAcoes as $key => $label) { 
                    $checked = in_array($key, $acoesModulo) || $key === 'read';
                    $disabled = ($key === 'read');
                  ?>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="modulo_acoes[]" value="<?= $key ?>" id="chkAcao_<?= $key ?>" <?= $checked ? 'checked' : '' ?> <?= $disabled ? 'onclick="return false;"' : '' ?>>
                      <label class="form-check-label small fw-semibold" for="chkAcao_<?= $key ?>">
                        <?= $label ?>
                      </label>
                    </div>
                  <?php } ?>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('sysmodulo') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                  <i class="bi bi-arrow-left me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                  <i class="bi bi-save me-1"></i> Salvar Módulo
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
