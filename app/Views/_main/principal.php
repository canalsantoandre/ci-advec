<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard
        </h3>
        <p class="text-secondary small mb-0">Bem-vindo ao sistema ADVEC Gestão, <?= esc($usuario->nome); ?></p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Início</a></li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Stat Widgets Row -->
    <div class="row g-3 mb-4">
      <div class="col-lg-3 col-sm-6">
        <div class="small-box text-bg-primary">
          <div class="inner">
            <h3>Empreendedores</h3>
            <p>Gestão de Inscritos</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-briefcase-fill"></i>
          </div>
          <a href="<?= base_url('empreendedor/lista') ?>" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-75-hover">
            Acessar módulo <i class="bi bi-arrow-right-circle-fill ms-1"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="small-box text-bg-success">
          <div class="inner">
            <h3>Convidados</h3>
            <p>Lista de Participantes</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-people-fill"></i>
          </div>
          <a href="<?= base_url('convidado/lista') ?>" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-75-hover">
            Acessar módulo <i class="bi bi-arrow-right-circle-fill ms-1"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="small-box text-bg-warning">
          <div class="inner">
            <h3>Contatos</h3>
            <p>Formulários & Contatos</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-telephone-fill"></i>
          </div>
          <a href="<?= base_url('contato/lista') ?>" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-75-hover">
            Acessar módulo <i class="bi bi-arrow-right-circle-fill ms-1"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-sm-6">
        <div class="small-box text-bg-info">
          <div class="inner">
            <h3>Usuários</h3>
            <p>Controle de Acesso</p>
          </div>
          <div class="small-box-icon">
            <i class="bi bi-person-gear"></i>
          </div>
          <a href="<?= base_url('usuario/lista') ?>" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-75-hover">
            Acessar módulo <i class="bi bi-arrow-right-circle-fill ms-1"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Main Section Row -->
    <div class="row">
      <div class="col-lg-8">
        <div class="card card-primary card-outline mb-4">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0">
              <i class="bi bi-shield-check text-primary me-2"></i>Perfil Atual: <?= esc($usuario->nome_perfil); ?>
            </h5>
            <div class="card-tools">
              <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3">Ativo</span>
            </div>
          </div>
          <div class="card-body">
            <p class="card-text">
              Utilize o menu lateral ou os atalhos rápidos acima para acessar as opções disponíveis para o seu perfil de usuário.
            </p>
            <hr>
            <div class="d-flex flex-wrap gap-2">
              <a href="<?= base_url('empreendedor/lista') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-person-lines-fill me-1"></i> Ver Empreendedores
              </a>
              <a href="<?= base_url('convidado/lista') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-person-plus-fill me-1"></i> Ver Convidados
              </a>
              <a href="<?= base_url('sorteio') ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3">
                <i class="bi bi-gift-fill me-1"></i> Realizar Sorteio
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card card-info card-outline mb-4">
          <div class="card-header">
            <h5 class="card-title fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Informações do Sistema</h5>
          </div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-secondary"><i class="bi bi-person-badge me-2"></i>Usuário</span>
                <span class="fw-semibold"><?= esc($usuario->nome); ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-secondary"><i class="bi bi-sliders me-2"></i>Perfil</span>
                <span class="badge bg-primary rounded-pill"><?= esc($usuario->nome_perfil); ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-secondary"><i class="bi bi-code-slash me-2"></i>Framework</span>
                <span class="fw-semibold">CodeIgniter <?= CodeIgniter\CodeIgniter::CI_VERSION ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-secondary"><i class="bi bi-layout-sidebar me-2"></i>Interface</span>
                <span class="badge bg-secondary rounded-pill">AdminLTE v4.8.5</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>