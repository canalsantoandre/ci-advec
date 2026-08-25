<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold text-danger">
          <i class="bi bi-shield-lock-fill me-2"></i>Acesso Negado
        </h3>
        <p class="text-secondary small mb-0">Você não possui permissão para acessar este recurso</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Acesso Negado</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center py-4">
      <div class="col-md-8 col-lg-6 text-center">
        <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-body">
          
          <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle p-4 mb-3" style="width: 90px; height: 90px;">
              <i class="bi bi-shield-x display-4"></i>
            </div>
            <h2 class="fw-bold text-body mb-2">403 &bull; Permissão Insuficiente</h2>
            <p class="text-secondary fs-6">
              Você não possui permissão de acesso para visualizar ou executar ações nesta funcionalidade do sistema.
            </p>
          </div>

          <div class="alert alert-warning border-0 rounded-3 text-start small mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
            Caso necessite deste acesso para suas atividades, por favor solicite a atribuição de permissões ao administrador do sistema através do seu perfil.
          </div>

          <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4">
              <i class="bi bi-arrow-left me-1"></i> Voltar à Página Anterior
            </a>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
              <i class="bi bi-house-door-fill me-1"></i> Ir para o Dashboard
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
