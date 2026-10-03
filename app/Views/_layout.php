<!doctype html>
<html lang="pt-br" data-bs-theme="light">
<!--begin::Head-->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?= isset($title) ? $title : 'ADVEC - Sistema de Gestão' ?></title>
  <!--begin::Primary Meta Tags-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="title" content="ADVEC - Sistema de Gestão" />
  <meta name="author" content="ADVEC" />
  <meta name="base-url" content="<?= base_url() ?>" />
  <script>
    window.baseUrl = '<?= rtrim(base_url(), '/') ?>';
  </script>
  <!--end::Primary Meta Tags-->

  <!--begin::Fonts-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!--end::Fonts-->

  <!--begin::Third Party Plugins-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
  <!--jQuery (Requerido no head para plugins do Framework US e scripts das views)-->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <!--end::Third Party Plugins-->

  <!--begin::Required Plugin(AdminLTE)-->
  <link rel="stylesheet" href="<?= base_url('templates/AdminLTE') ?>/dist/css/adminlte.min.css" />
  <!--end::Required Plugin(AdminLTE)-->

  <!--begin::Framework US CSS (Toast Banner & Message Alert)-->
  <link rel="stylesheet" href="<?= base_url('framework/us/toast-banner/toast-banner.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('framework/us/message-alert/css/message-alert.css') ?>" />
  <!--end::Framework US CSS-->

  <style>
    :root {
      --advec-font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      --advec-primary: #0f172a;
      --advec-accent: #2563eb;
    }

    body {
      font-family: var(--advec-font-family) !important;
    }

    .app-header {
      backdrop-filter: blur(8px);
      border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }
    
    [data-bs-theme="dark"] .app-header {
      border-bottom-color: rgba(255, 255, 255, 0.08);
    }

    .sidebar-brand {
      padding: 0.85rem 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .sidebar-brand .brand-link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      color: #fff;
    }

    .sidebar-brand .brand-image {
      width: 38px !important;
      height: 38px !important;
      max-height: 38px !important;
      object-fit: cover !important;
      border-radius: 50% !important;
      border: 2px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    }

    .sidebar-brand .brand-text {
      font-weight: 700;
      font-size: 1.1rem;
      letter-spacing: 0.5px;
      color: #f8fafc;
    }

    .app-sidebar .nav-link {
      border-radius: 0.5rem;
      margin: 0.15rem 0.5rem;
      padding: 0.6rem 0.85rem;
      transition: all 0.2s ease-in-out;
    }

    .app-sidebar .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.08);
      transform: translateX(2px);
    }

    .app-sidebar .nav-link.active {
      background-color: var(--advec-accent) !important;
      color: #ffffff !important;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .card {
      border-radius: 0.75rem;
      border: 1px solid rgba(0, 0, 0, 0.06);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
      transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    [data-bs-theme="dark"] .card {
      border-color: rgba(255, 255, 255, 0.08);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .small-box {
      border-radius: 0.85rem;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .small-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .small-box .inner {
      padding: 1.25rem;
    }

    .small-box .inner h3 {
      font-weight: 700;
      font-size: 2.2rem;
      margin-bottom: 0.25rem;
    }

    .table th {
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      opacity: 0.8;
    }

    .btn-action {
      width: 32px;
      height: 32px;
      padding: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 0.5rem;
      transition: all 0.15s ease;
    }

    .btn-action:hover {
      transform: scale(1.08);
    }

    /* Premium User Menu Dropdown */
    .user-menu .dropdown-toggle {
      border-radius: 9999px;
      padding: 0.35rem 0.75rem 0.35rem 0.45rem;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid transparent;
    }

    .user-menu .dropdown-toggle:hover {
      background: rgba(15, 23, 42, 0.05);
      border-color: rgba(15, 23, 42, 0.08);
    }

    [data-bs-theme="dark"] .user-menu .dropdown-toggle:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.12);
    }

    .user-avatar-circle {
      width: 36px;
      height: 36px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, 0.5);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
      flex-shrink: 0;
    }

    .user-menu-dropdown {
      width: 290px;
      border-radius: 1.25rem !important;
      border: 1px solid rgba(226, 232, 240, 0.8) !important;
      box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.3), 0 0 1px 1px rgba(0, 0, 0, 0.05) !important;
      overflow: hidden;
      padding: 0;
      margin-top: 0.65rem !important;
    }

    [data-bs-theme="dark"] .user-menu-dropdown {
      background-color: #1e293b !important;
      border-color: rgba(255, 255, 255, 0.1) !important;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
    }

    .user-dropdown-hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #1e3a8a 100%);
      color: #ffffff;
      padding: 1.75rem 1.25rem 1.25rem 1.25rem;
      text-align: center;
      position: relative;
    }

    .user-dropdown-hero::after {
      content: '';
      position: absolute;
      top: -20px;
      right: -20px;
      width: 120px;
      height: 120px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, rgba(37, 99, 235, 0) 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .avatar-upload-wrapper {
      position: relative;
      width: 80px;
      height: 80px;
      margin: 0 auto 0.75rem auto;
    }

    .avatar-dropdown-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid rgba(255, 255, 255, 0.9);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35);
      background-color: #1e293b;
    }

    .avatar-dropdown-badge {
      position: absolute;
      bottom: 0px;
      right: 0px;
      background: #2563eb;
      color: #ffffff;
      border-radius: 50%;
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border: 2px solid #ffffff;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
      transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease;
    }

    .avatar-dropdown-badge:hover {
      transform: scale(1.15);
      background: #1d4ed8;
    }

    .user-dropdown-menu-item {
      padding: 0.65rem 0.85rem;
      border-radius: 0.75rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-weight: 500;
      font-size: 0.88rem;
      text-decoration: none;
      transition: all 0.15s ease;
    }

    .user-dropdown-menu-item:hover {
      background: rgba(15, 23, 42, 0.05);
      transform: translateX(2px);
    }

    [data-bs-theme="dark"] .user-dropdown-menu-item:hover {
      background: rgba(255, 255, 255, 0.06);
    }

    .user-dropdown-menu-item.item-danger {
      color: #ef4444;
    }

    .user-dropdown-menu-item.item-danger:hover {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
    }

    /* Regras Globais: Ícone do WhatsApp em botões/pills com fundo azul (contraste #FFFFFF) */
    .btn-primary .bi-whatsapp,
    .btn-info .bi-whatsapp,
    .nav-pills .nav-link.active .bi-whatsapp,
    .badge.bg-primary .bi-whatsapp {
      color: #ffffff !important;
    }

    /* Regras Globais: Inputs no Dark Mode com fonte digitada PRETA e fundo claro/branco para máxima legibilidade */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="reset"]),
    [data-bs-theme="dark"] select,
    [data-bs-theme="dark"] textarea {
      background-color: #ffffff !important;
      border-color: #cbd5e1 !important;
      color: #000000 !important;
      font-weight: 500;
    }

    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus,
    [data-bs-theme="dark"] input:focus,
    [data-bs-theme="dark"] select:focus,
    [data-bs-theme="dark"] textarea:focus {
      background-color: #ffffff !important;
      color: #000000 !important;
      border-color: #3b82f6 !important;
      box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
    }

    [data-bs-theme="dark"] .form-floating > label {
      color: #475569 !important;
    }

    [data-bs-theme="dark"] .form-control::placeholder,
    [data-bs-theme="dark"] input::placeholder,
    [data-bs-theme="dark"] textarea::placeholder {
      color: #64748b !important;
      opacity: 0.85 !important;
    }
  </style>
</head>
<!--end::Head-->

<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <!--begin::App Wrapper-->
  <div class="app-wrapper">
    <!--begin::Header-->
    <nav class="app-header navbar navbar-expand bg-body">
      <!--begin::Container-->
      <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" title="Alternar Menu">
              <i class="bi bi-list fs-5"></i>
            </a>
          </li>
          <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url('dashboard') ?>" class="nav-link fw-semibold">
              <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
          </li>
        </ul>
        <!--end::Start Navbar Links-->

        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto align-items-center">
          
          <!-- Theme Switcher -->
          <li class="nav-item dropdown me-2">
            <button class="btn btn-link nav-link px-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" title="Alternar Tema">
              <i class="bi bi-sun-fill fs-5 theme-icon-active" id="theme-icon-main"></i>
              <span class="d-none" id="bd-theme-text">Alternar Tema</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                  <i class="bi bi-sun-fill me-2 text-warning"></i> Claro
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                  <i class="bi bi-moon-stars-fill me-2 text-info"></i> Escuro
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto">
                  <i class="bi bi-circle-half me-2 text-secondary"></i> Automático
                </button>
              </li>
            </ul>
          </li>

          <!-- Fullscreen Toggle -->
          <li class="nav-item me-2">
            <a class="nav-link" href="#" data-lte-toggle="fullscreen" title="Tela Cheia">
              <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen fs-6"></i>
              <i data-lte-icon="minimize" class="bi bi-fullscreen-exit fs-6" style="display: none"></i>
            </a>
          </li>

          <!-- User Menu Dropdown (Premium High-End Redesign) -->
          <?php
            $nomeUserLayout = isset($usuario->nome) ? trim($usuario->nome) : 'Usuário';
            $loginUserLayout = isset($usuario->usuario) ? trim($usuario->usuario) : '';
            $perfilUserLayout = isset($usuario->nome_perfil) ? trim($usuario->nome_perfil) : 'Perfil Acesso';
            $defaultAvatarUser = 'https://ui-avatars.com/api/?name=' . urlencode($nomeUserLayout) . '&background=2563eb&color=fff&size=160&bold=true';
            $fotoUserLayout = !empty($usuario->foto_url) ? $usuario->foto_url : (!empty($usuario->foto) ? $usuario->foto : null);
            $fotoSrcUser = $fotoUserLayout ?: $defaultAvatarUser;
          ?>
          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?= esc($fotoSrcUser) ?>" class="user-avatar-circle shadow-sm" id="userNavbarAvatarMain" alt="Foto do Usuário" onerror="this.onerror=null;this.src='<?= $defaultAvatarUser ?>';" />
              <div class="d-none d-md-flex flex-column text-start" style="line-height: 1.15;">
                <span class="fw-bold text-body" style="font-size: 0.88rem;"><?= esc($nomeUserLayout); ?></span>
                <span class="text-secondary small" style="font-size: 0.72rem;"><?= esc($perfilUserLayout); ?></span>
              </div>
              <i class="bi bi-chevron-down fs-7 text-muted ms-0.5"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-end user-menu-dropdown shadow-lg border-0">
              <!-- Header com Avatar, Upload Instantâneo e Badges -->
              <div class="user-dropdown-hero">
                <div class="avatar-upload-wrapper">
                  <img src="<?= esc($fotoSrcUser) ?>" id="userNavbarAvatarDropdown" class="avatar-dropdown-img" alt="Foto do Usuário" onerror="this.onerror=null;this.src='<?= $defaultAvatarUser ?>';">
                  <label for="inputUploadFotoNavbar" class="avatar-dropdown-badge" title="Alterar Foto de Perfil" id="labelUploadFotoNavbar">
                    <i class="bi bi-camera-fill" id="iconCameraNavbar" style="font-size: 0.75rem;"></i>
                    <div class="spinner-border spinner-border-sm text-white d-none" id="spinnerCameraNavbar" role="status" style="width: 0.8rem; height: 0.8rem;"></div>
                  </label>
                  <input type="file" id="inputUploadFotoNavbar" class="d-none" accept="image/*" onchange="uploadFotoNavbarAutomatico(this)">
                </div>

                <h6 class="fw-bold mb-0 text-white" style="letter-spacing: -0.2px; font-size: 1.05rem;">
                  <?= esc($nomeUserLayout); ?>
                </h6>
                <?php if (!empty($loginUserLayout)) { ?>
                  <small class="text-white-50 d-block mb-2" style="font-size: 0.78rem;">@<?= esc($loginUserLayout); ?></small>
                <?php } ?>

                <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-0.5 rounded-pill mt-1" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(4px);">
                  <i class="bi bi-shield-lock-fill text-warning" style="font-size: 0.72rem;"></i>
                  <span class="fw-bold text-white text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                    <?= esc($perfilUserLayout); ?>
                  </span>
                </div>
              </div>

              <!-- Menu Actions -->
              <div class="p-2 bg-body">
                <div class="d-flex flex-column gap-1">
                  <!-- Alterar Senha -->
                  <a href="<?= base_url('usuario/senha/' . (isset($usuario->hash_user) ? $usuario->hash_user : '')) ?>" class="user-dropdown-menu-item text-body">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-warning-subtle text-warning-emphasis flex-shrink-0" style="width: 34px; height: 34px;">
                      <i class="bi bi-key-fill fs-6"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="small fw-bold">Alterar Senha</div>
                      <div class="text-muted" style="font-size: 0.7rem;">Atualize suas credenciais</div>
                    </div>
                    <i class="bi bi-chevron-right text-muted fs-7"></i>
                  </a>

                  <div class="border-top my-1 opacity-50"></div>

                  <!-- Sair do Sistema -->
                  <a href="<?= base_url('dshlogout') ?>" class="user-dropdown-menu-item item-danger">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-danger-subtle text-danger flex-shrink-0" style="width: 34px; height: 34px;">
                      <i class="bi bi-box-arrow-right fs-6"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="small fw-bold">Sair do Sistema</div>
                      <div class="text-muted" style="font-size: 0.7rem;">Encerrar sessão ativa</div>
                    </div>
                    <i class="bi bi-box-arrow-up-right fs-7 opacity-75"></i>
                  </a>
                </div>
              </div>
            </div>
          </li>
        </ul>
        <!--end::End Navbar Links-->
      </div>
      <!--end::Container-->
    </nav>
    <!--end::Header-->

    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
      <!--begin::Sidebar Brand-->
      <div class="sidebar-brand">
        <a href="<?= base_url('dashboard'); ?>" class="brand-link">
          <img src="<?= base_url('logo-advec.png') ?>" alt="ADVEC Logo" class="brand-image rounded-circle shadow me-2" onerror="this.src='<?= base_url('templates/AdminLTE') ?>/dist/img/AdminLTELogo.png'">
          <span class="brand-text">ADVEC <span class="badge bg-primary fs-7">GESTÃO</span></span>
        </a>
      </div>
      <!--end::Sidebar Brand-->

      <!--begin::Sidebar Wrapper-->
      <div class="sidebar-wrapper">
        <nav class="mt-2">
          <!--begin::Sidebar Menu-->
          <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
            <?php
            if (isset($usuario->perfil_acesso) && is_array($usuario->perfil_acesso)) {
              $nome_categoria_modulo = "";

              foreach ($usuario->perfil_acesso as $p) {
                $possuiSubItem = (isset($p->subitem) && is_array($p->subitem) && count($p->subitem) > 0);
                
                if ($nome_categoria_modulo != $p->nome_categoria_modulo && isset($p->exibir_titulo) && $p->exibir_titulo == 1) { ?>
                  <li class="nav-header text-uppercase text-muted fw-bold px-3 pt-3 pb-1" style="font-size: 0.75rem; letter-spacing: 1px;">
                    <?= esc($p->nome_categoria_modulo); ?>
                  </li>
                <?php }
                $nome_categoria_modulo = $p->nome_categoria_modulo;
                
                $isCurrentActive = (isset($sys_module->uri_modulo) && $sys_module->uri_modulo == $p->uri_modulo);
                ?>

                <li class="nav-item <?= $possuiSubItem ? 'has-treeview' : '' ?>">
                  <a href="<?= ($possuiSubItem ? '#' : base_url($p->uri_modulo)) ?>" class="nav-link <?= ($isCurrentActive ? 'active' : '') ?>">
                    <i class="nav-icon <?= esc($p->icon_class_modulo); ?>"></i>
                    <p class="mb-0">
                      <?= esc($p->nome_modulo); ?>
                      <?= ($possuiSubItem ? '<i class="nav-arrow bi bi-chevron-right float-end ms-auto"></i>' : '') ?>
                    </p>
                  </a>

                  <?php if ($possuiSubItem) { ?>
                    <ul class="nav nav-treeview ps-3" style="display: none;">
                      <?php foreach ($p->subitem as $s) {
                        $isSubActive = (isset($sys_module->uri_modulo) && $sys_module->uri_modulo == $s->uri_modulo);
                      ?>
                        <li class="nav-item">
                          <a href="<?= base_url($s->uri_modulo) ?>" class="nav-link <?= ($isSubActive ? 'active' : '') ?>">
                            <i class="nav-icon bi bi-circle fs-8 me-2"></i>
                            <p class="mb-0"><?= esc($s->nome_modulo) ?></p>
                          </a>
                        </li>
                      <?php } ?>
                    </ul>
                  <?php } ?>
                </li>
            <?php 
              }
            } 
            ?>
          </ul>
          <!--end::Sidebar Menu-->
        </nav>
      </div>
      <!--end::Sidebar Wrapper-->
    </aside>
    <!--end::Sidebar-->

    <!--begin::App Main-->
    <main class="app-main">
      <?= $content_view; ?>
    </main>
    <!--end::App Main-->

    <!--begin::Footer-->
    <footer class="app-footer bg-body border-top py-3 px-4">
      <div class="float-end d-none d-sm-inline text-muted">
        <small>ADVEC Gestão v2.0</small>
      </div>
      <strong>
        Copyright &copy; <?= date('Y') ?> <a href="#" class="text-decoration-none text-primary">ADVEC</a>.
      </strong>
      Todos os direitos reservados.
    </footer>
    <!--end::Footer-->
    <!-- Container Global de Modais de Confirmação / Exclusão -->
    <div id="divModalConfirmaDelete" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" id="divModalDelete"></div>
    </div>

    <!-- Modal Global de Confirmação Padrão (System Design) -->
    <div class="modal fade" id="usGlobalConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold" id="usGlobalConfirmTitle">
              <i id="usGlobalConfirmIcon" class="bi bi-exclamation-triangle-fill text-danger me-2"></i> <span>Confirmação</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body py-3">
            <div id="usGlobalConfirmMessage" class="fs-6 text-body"></div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" id="usGlobalConfirmBtnCancel">Cancelar</button>
            <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="usGlobalConfirmBtnOk">
              <i class="bi bi-check-lg me-1"></i> Confirmar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--end::App Wrapper-->

  <!--begin::Script-->
  <!--jQuery (Requerido para plugins do Framework US)-->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <!--OverlayScrollbars-->
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
  <!--Bootstrap 5 Bundle-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <!--AdminLTE 4 JS-->
  <script src="<?= base_url('templates/AdminLTE') ?>/dist/js/adminlte.min.js"></script>

  <!--Framework US JS Plugins-->
  <script src="<?= base_url('framework/us/toast-banner/toast-banner.js') ?>"></script>
  <script src="<?= base_url('framework/us/message-alert/message-alert.js') ?>"></script>
  <script src="<?= base_url('framework/us/framework.js') ?>"></script>

  <!-- Global Confirm Engine (System Design) -->
  <script>
    window.usConfirm = function(options, onConfirm) {
      if (typeof options === 'string') {
        options = { message: options };
      }
      options = options || {};
      const title = options.title || 'Confirmação';
      const message = options.message || 'Deseja realmente prosseguir com esta ação?';
      const confirmText = options.confirmText || 'Confirmar';
      const cancelText = options.cancelText || 'Cancelar';
      const type = (options.type || 'danger').toLowerCase();
      
      let iconClass = 'bi-exclamation-triangle-fill text-danger';
      let btnClass = 'btn-danger';

      if (type === 'warning') {
        iconClass = 'bi-exclamation-circle-fill text-warning';
        btnClass = 'btn-warning text-dark';
      } else if (type === 'primary') {
        iconClass = 'bi-question-circle-fill text-primary';
        btnClass = 'btn-primary';
      } else if (type === 'success') {
        iconClass = 'bi-check-circle-fill text-success';
        btnClass = 'btn-success';
      } else if (type === 'info') {
        iconClass = 'bi-info-circle-fill text-info';
        btnClass = 'btn-info text-white';
      }

      if (options.icon) iconClass = options.icon;
      if (options.btnClass) btnClass = options.btnClass;

      const modalEl = document.getElementById('usGlobalConfirmModal');
      if (!modalEl) {
        if (window.confirm(message)) {
          if (typeof onConfirm === 'function') onConfirm();
        }
        return;
      }

      $('#usGlobalConfirmTitle span').text(title);
      $('#usGlobalConfirmIcon').attr('class', 'bi ' + iconClass + ' me-2');
      $('#usGlobalConfirmMessage').html(message);
      $('#usGlobalConfirmBtnCancel').text(cancelText);
      
      const $btnOk = $('#usGlobalConfirmBtnOk');
      $btnOk.attr('class', 'btn rounded-pill px-4 fw-bold ' + btnClass);
      $btnOk.html('<i class="bi bi-check-lg me-1"></i> ' + confirmText);

      const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);

      $btnOk.off('click').on('click', function() {
        modalInstance.hide();
        if (typeof onConfirm === 'function') {
          onConfirm();
        }
      });

      modalInstance.show();
    };
  </script>

  <!--Theme Toggler Script & Sidebar Initializer-->
  <script>
    // Theme Switcher Logic
    (() => {
      'use strict'
      const getStoredTheme = () => localStorage.getItem('theme')
      const setStoredTheme = theme => localStorage.setItem('theme', theme)

      const getPreferredTheme = () => {
        const storedTheme = getStoredTheme()
        if (storedTheme) {
          return storedTheme
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
      }

      const setTheme = theme => {
        if (theme === 'auto') {
          document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'))
        } else {
          document.documentElement.setAttribute('data-bs-theme', theme)
        }
      }

      setTheme(getPreferredTheme())

      const showActiveTheme = (theme, focus = false) => {
        const themeIconMain = document.querySelector('#theme-icon-main')
        if (!themeIconMain) return

        if (theme === 'dark') {
          themeIconMain.className = 'bi bi-moon-stars-fill fs-5 text-info theme-icon-active'
        } else if (theme === 'light') {
          themeIconMain.className = 'bi bi-sun-fill fs-5 text-warning theme-icon-active'
        } else {
          themeIconMain.className = 'bi bi-circle-half fs-5 text-secondary theme-icon-active'
        }
      }

      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const storedTheme = getStoredTheme()
        if (storedTheme !== 'light' && storedTheme !== 'dark') {
          setTheme(getPreferredTheme())
        }
      })

      window.addEventListener('DOMContentLoaded', () => {
        showActiveTheme(getPreferredTheme())

        document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
          toggle.addEventListener('click', () => {
            const theme = toggle.getAttribute('data-bs-theme-value')
            setStoredTheme(theme)
            setTheme(theme)
            showActiveTheme(theme, true)
          })
        })
      })
    })()

    // OverlayScrollbars & Sidebar Setup
    document.addEventListener('DOMContentLoaded', function () {
      const sidebarWrapper = document.querySelector('.sidebar-wrapper');
      if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: 'os-theme-light',
            autoHide: 'leave',
            clickScroll: true,
          },
        });
      }
    });

    // Upload Instantâneo de Foto do Usuário (Admin Navbar)
    function uploadFotoNavbarAutomatico(input) {
      if (!input.files || !input.files[0]) return;

      const file = input.files[0];
      if (!file.type.match('image.*')) {
        if (typeof usShowToast === 'function') {
          usShowToast('warning', 'Arquivo Inválido', 'Por favor, selecione um arquivo de imagem válido (JPG, PNG ou WEBP).');
        } else {
          alert('Por favor, selecione um arquivo de imagem válido (JPG, PNG ou WEBP).');
        }
        return;
      }

      const iconCam = document.getElementById('iconCameraNavbar');
      const spinnerCam = document.getElementById('spinnerCameraNavbar');
      if (iconCam) iconCam.classList.add('d-none');
      if (spinnerCam) spinnerCam.classList.remove('d-none');

      const formData = new FormData();
      formData.append('foto_file', file);

      $.ajax({
        url: '<?= base_url('usuario/uploadFotoPerfil') ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
          if (iconCam) iconCam.classList.remove('d-none');
          if (spinnerCam) spinnerCam.classList.add('d-none');

          if (res.status === 'success' && res.foto_url) {
            const newUrl = res.foto_url + '?t=' + new Date().getTime();
            const elMain = document.getElementById('userNavbarAvatarMain');
            const elDropdown = document.getElementById('userNavbarAvatarDropdown');
            if (elMain) elMain.src = newUrl;
            if (elDropdown) elDropdown.src = newUrl;
            if (typeof usShowToast === 'function') {
              usShowToast('success', 'Foto Atualizada', 'Sua foto de perfil foi salva com sucesso!');
            }
          } else {
            if (typeof usShowToast === 'function') {
              usShowToast('error', 'Erro', res.message || 'Erro ao atualizar foto de perfil.');
            }
          }
        },
        error: function(xhr) {
          if (iconCam) iconCam.classList.remove('d-none');
          if (spinnerCam) spinnerCam.classList.add('d-none');
          const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao processar upload da foto.';
          if (typeof usShowToast === 'function') {
            usShowToast('error', 'Erro', msg);
          }
        }
      });
    }
  </script>
  <!--end::Script-->
</body>
<!--end::Body-->

</html>