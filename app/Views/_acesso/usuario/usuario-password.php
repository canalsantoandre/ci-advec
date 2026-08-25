<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-key-fill text-warning me-2"></i>Alterar Senha
        </h3>
        <p class="text-secondary small mb-0">Atualização de senha pessoal para <?= esc($tb_usuario->nome); ?></p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Alterar Senha</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">

        <div class="card card-outline card-warning shadow-sm border-0">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-warning">
              <i class="bi bi-shield-lock me-2"></i>Segurança do Usuário: <?= esc($tb_usuario->nome); ?>
            </h5>
          </div>

          <div class="card-body p-4">
            <?php if (isset($validation)) : ?>
              <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> ATENÇÃO</div>
                <?= $validation->listErrors() ?>
              </div>
            <?php endif; ?>

            <form method="post" id="frm_usuario" name="frm_usuario" action="<?= base_url('/usuario/atualizarSenha') ?>">
              <input type="hidden" name="id_usuario" id="id_usuario" value="<?= esc($tb_usuario->id_usuario); ?>">
              <input type="hidden" name="txtUsuario" id="txtUsuario" value="<?= esc($tb_usuario->usuario); ?>">

              <div class="mb-3">
                <label for="txtSenhaAtual" class="form-label fw-semibold">Senha Atual</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" name="txtSenhaAtual" class="form-control" id="txtSenhaAtual" placeholder="Digite sua senha atual" required>
                </div>
              </div>

              <div class="mb-4">
                <label for="txtUsuarioSenhaNova" class="form-label fw-semibold">Nova Senha</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-key"></i></span>
                  <input type="password" name="txtUsuarioSenhaNova" class="form-control" id="txtUsuarioSenhaNova" placeholder="Digite a nova senha" required>
                </div>
              </div>

              <div class="d-grid">
                <button class="btn btn-warning text-dark fw-bold rounded-pill py-2 shadow-sm" type="submit">
                  <i class="bi bi-check-circle-fill me-1"></i> Atualizar Senha
                </button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>