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
  <meta name="description" content="Sistema de Gestão ADVEC" />
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

    .user-avatar-circle {
      width: 36px;
      height: 36px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, 0.2);
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

          <!-- User Menu Dropdown -->
          <?php
            $nomeUserLayout = isset($usuario->nome) ? trim($usuario->nome) : 'Usuário';
            $partesNomeUser = preg_split('/\s+/', $nomeUserLayout);
            $iniciaisUser = '';
            if (count($partesNomeUser) >= 2) {
                $iniciaisUser = mb_strtoupper(mb_substr($partesNomeUser[0], 0, 1) . mb_substr(end($partesNomeUser), 0, 1));
            } elseif (!empty($nomeUserLayout)) {
                $iniciaisUser = mb_strtoupper(mb_substr($nomeUserLayout, 0, 2));
            } else {
                $iniciaisUser = 'US';
            }
            $fotoUserLayout = !empty($usuario->foto_url) ? $usuario->foto_url : (!empty($usuario->foto) ? $usuario->foto : null);
          ?>
          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
              <?php if ($fotoUserLayout) { ?>
                <img src="<?= esc($fotoUserLayout) ?>" class="user-avatar-circle shadow-sm" alt="Foto do Usuário" />
              <?php } else { ?>
                <span class="user-avatar-circle shadow-sm d-inline-flex align-items-center justify-content-center fw-bold text-white text-center" style="background: linear-gradient(135deg, #0284c7 0%, #1d4ed8 100%); font-size: 0.85rem; letter-spacing: 0.5px;">
                  <?= esc($iniciaisUser) ?>
                </span>
              <?php } ?>
              <span class="d-none d-md-inline fw-semibold"><?= esc($nomeUserLayout); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow-lg rounded-3 border-0">
              <!--begin::User Header-->
              <li class="user-header text-bg-primary rounded-top p-4 text-center">
                <?php if ($fotoUserLayout) { ?>
                  <img src="<?= esc($fotoUserLayout) ?>" class="rounded-circle shadow mb-2" style="width: 70px; height: 70px; object-fit: cover;" alt="Foto do Usuário" />
                <?php } else { ?>
                  <div class="rounded-circle shadow mb-2 d-inline-flex align-items-center justify-content-center fw-bold text-white mx-auto border border-3 border-white-50" style="width: 70px; height: 70px; font-size: 1.6rem; letter-spacing: 1px; background: linear-gradient(135deg, #0284c7 0%, #1e40af 100%);">
                    <?= esc($iniciaisUser) ?>
                  </div>
                <?php } ?>
                <p class="mb-0 fw-bold fs-6">
                  <?= esc($nomeUserLayout); ?>
                </p>
                <small class="text-white-50"><?= isset($usuario->nome_perfil) ? esc($usuario->nome_perfil) : 'Perfil Acesso'; ?></small>
              </li>
              <!--end::User Header-->
              
              <!--begin::User Actions-->
              <li class="user-footer p-3 bg-body">
                <div class="d-flex flex-column gap-2">
                  <a href="<?= base_url('usuario/senha/' . (isset($usuario->hash_user) ? $usuario->hash_user : '')) ?>" class="btn btn-outline-secondary btn-sm text-start d-flex align-items-center gap-2">
                    <i class="bi bi-key-fill text-warning"></i> Alterar Senha
                  </a>
                  <a href="<?= base_url('dshlogout') ?>" class="btn btn-danger btn-sm text-start d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Sair do Sistema
                  </a>
                </div>
              </li>
              <!--end::User Actions-->
            </ul>
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
  </script>
  <!--end::Script-->
</body>
<!--end::Body-->

</html>