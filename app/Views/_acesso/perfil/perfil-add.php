<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-shield-plus text-primary me-2"></i>Novo Perfil de Acesso
        </h3>
        <p class="text-secondary small mb-0">Cadastrar um novo grupo/nível de permissão no sistema</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('perfil') ?>">Perfis</a></li>
          <li class="breadcrumb-item active" aria-current="page">Novo</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-lg-6">

        <div class="card card-outline card-primary shadow-sm border-0">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-shield-lock me-2"></i>Informações do Perfil
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" action="<?= base_url('perfil/inserir') ?>">
              
              <div class="mb-3">
                <label for="txtPerfilNome" class="form-label fw-semibold">Nome do Perfil</label>
                <input type="text" class="form-control" name="txtPerfilNome" id="txtPerfilNome" placeholder="Ex: Liderança / Recepção" required autofocus>
              </div>

              <div class="mb-3">
                <label for="txtContentViewDefault" class="form-label fw-semibold">View Inicial / Redirecionamento Padrão</label>
                <input type="text" class="form-control font-monospace" name="txtContentViewDefault" id="txtContentViewDefault" value="_main/principal" placeholder="_main/principal">
                <small class="text-muted">Caminho da view de entrada após o login</small>
              </div>

              <div class="mb-4">
                <label for="cboPerfilStatus" class="form-label fw-semibold">Status Inicial</label>
                <select class="form-select" name="cboPerfilStatus" id="cboPerfilStatus">
                  <option value="1" selected>Ativo</option>
                  <option value="0">Inativo</option>
                </select>
              </div>

              <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('perfil') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                  <i class="bi bi-arrow-left me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                  <i class="bi bi-check-lg me-1"></i> Salvar e Configurar Permissões
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
