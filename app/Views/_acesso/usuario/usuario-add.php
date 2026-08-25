<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Cadastrar um novo usuário no sistema</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('usuario') ?>"><?= esc($sys_module->nome_modulo) ?></a></li>
          <li class="breadcrumb-item active" aria-current="page">Adicionar</li>
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
              <i class="bi bi-person-plus-fill me-2"></i>Informações do Novo Usuário
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" id="frm_usuario" name="frm_usuario" action="<?= base_url('/usuario/inserir') ?>">
              
              <div class="mb-3">
                <label for="txtUsuarioNome" class="form-label fw-semibold">Nome Completo</label>
                <input type="text" name="txtUsuarioNome" id="txtUsuarioNome" class="form-control" maxlength="100" placeholder="Ex: Maria Souza" required>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="txtUsuario" class="form-label fw-semibold">Nome de Usuário (Login)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="txtUsuario" id="txtUsuario" class="form-control" maxlength="30" placeholder="maria.souza" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Perfil de Acesso</label>
                  <?= $comboPerfil ?>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('usuario/') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                  <i class="bi bi-arrow-left me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                  <i class="bi bi-check-lg me-1"></i> Salvar Usuário
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= base_url('templates/js') ?>/usuario/validacoes.js"></script>
