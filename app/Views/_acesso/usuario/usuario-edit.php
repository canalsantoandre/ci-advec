<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Editar informações do usuário <?= esc($tb_usuario->nome); ?></p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('usuario') ?>"><?= esc($sys_module->nome_modulo) ?></a></li>
          <li class="breadcrumb-item active" aria-current="page">Editar</li>
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

        <div class="card card-outline card-warning shadow-sm border-0">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-warning">
              <i class="bi bi-pencil-square me-2"></i>Editar Dados do Usuário
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" id="frm_usuario" name="frm_usuario" action="<?= ($sys_action->update ? base_url('/usuario/atualizar') : '') ?>">
              <input type="hidden" name="id_usuario" id="id_usuario" value="<?= esc($tb_usuario->id_usuario); ?>">
              <input type="hidden" name="hash_user" id="hash_user" value="<?= esc($tb_usuario->hash_user); ?>">

              <div class="mb-3">
                <label for="txtUsuarioNome" class="form-label fw-semibold">Nome Completo</label>
                <input type="text" name="txtUsuarioNome" id="txtUsuarioNome" value="<?= esc($tb_usuario->nome); ?>" class="form-control" maxlength="100" required>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="txtUsuario" class="form-label fw-semibold">Nome de Usuário (Login)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="txtUsuario" id="txtUsuario" value="<?= esc($tb_usuario->usuario); ?>" class="form-control" maxlength="30" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Perfil de Acesso</label>
                  <?= $comboPerfil ?>
                </div>
              </div>

              <div class="mb-4">
                <label for="cboUsuarioStatus" class="form-label fw-semibold">Status da Conta</label>
                <select class="form-select" name="cboUsuarioStatus" id="cboUsuarioStatus">
                  <option value="1" <?= ($tb_usuario->status_usuario == 1 ? "selected" : ""); ?>>Ativo</option>
                  <option value="0" <?= ($tb_usuario->status_usuario == 0 ? "selected" : ""); ?>>Inativo</option>
                </select>
              </div>

              <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 border-top">
                <div>
                  <?php if ($sys_action->reset_password) { ?>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-3" data-baseurl="<?= base_url('/usuario/getModalResetSenha/'); ?>" data-bs-toggle="modal" data-bs-target="#disabledAnimation" data-hash-user="<?= esc($tb_usuario->hash_user) ?>" id="btnReiniciarSenha">
                      <i class="bi bi-arrow-repeat me-1"></i> Reiniciar Senha
                    </button>
                  <?php } ?>
                </div>

                <div class="d-flex gap-2">
                  <a href="<?= base_url('usuario/') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                  </a>
                  <?php if ($sys_action->update) { ?>
                    <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold">
                      <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                    </button>
                  <?php } ?>
                </div>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="disabledAnimation" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="divModal"></div>
</div>

<script type="text/javascript" src="<?= base_url('templates/js') ?>/usuario/validacoes.js"></script>