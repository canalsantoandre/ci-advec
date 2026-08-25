<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? $title : 'ADVEC - Gestão' ?></title>
  <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('templates/AdminLTE') ?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('templates/AdminLTE') ?>/dist/css/adminlte.min.css">

  <!------------------------------------------------------------------------------->
  <!--ALERTS MESSAGENS -->
  <!------------------------------------------------------------------------------->
  <link href="<?= base_url('framework') ?>/us/message-alert/css/message-alert.css" rel="stylesheet" />
  <!------------------------------------------------------------------------------->
</head>

<body class="hold-transition sidebar-mini">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="<?= base_url('dashboard') ?>" class="nav-link">Inicio</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
          </a>
          <div class="navbar-search-block">
            <form class="form-inline">
              <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                  <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                  <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </li>

        <!-- Messages Dropdown Menu -->

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-user"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

            <div class="dropdown-divider"></div>
            <a href="<?= base_url('usuario/senha/' . $usuario->hash_user) ?>" class="dropdown-item">
              <i class="fas fa-key"></i> Alterar Senha
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?= base_url('dshlogout') ?>" class="dropdown-item">
              <i class="fas fa-sign-out-alt"></i> SAIR
            </a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown1" href="<?= base_url('dshlogout') ?>" title="Sair">
            <i class="fas fa-sign-out-alt"></i>
            <span class="badge badge-warning navbar-badge"></span>
          </a>

        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="<?= base_url('/'); ?>" class="brand-link">
        <img src="<?= base_url('templates/AdminLTE') ?>/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">CRM UNIC</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="<?= base_url('templates/AdminLTE') ?>/dist/img/user.png" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block"><?= $usuario->nome; ?></a>
          </div>
        </div>


        <!-- ======= Sidebar ======= -->
        <nav class="mt-2">

          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <?php
            $nome_categoria_modulo = "";

            foreach ($usuario->perfil_acesso as $p) {

              $possuiSubItem = (count($p->subitem) > 0);
              if ($nome_categoria_modulo != $p->nome_categoria_modulo && $p->exibir_titulo == 1) { ?>
                <li class="nav-header"><?= $p->nome_categoria_modulo; ?></li>
              <?php }
              $nome_categoria_modulo = $p->nome_categoria_modulo;
              ?>

              <li class="nav-item">
                <a href="<?= ($possuiSubItem ? '#' :  base_url($p->uri_modulo)) ?>" class="nav-link <?= ($sys_module->uri_modulo == $p->uri_modulo ? 'active' : '') ?>">
                  <i class="nav-icon <?= $p->icon_class_modulo; ?>"></i>
                  <p>
                    <?= $p->nome_modulo; ?>
                    <?= ($possuiSubItem ?  '<i class="fas fa-angle-left right"></i>' : '') ?>
                    <!-- <span class="right badge badge-danger">New</span> -->
                  </p>
                </a>

                <?php
                echo ($possuiSubItem ? '<ul class="nav nav-treeview" style="display: none;">' : '');
                foreach ($p->subitem as $s) {

                  echo '<li class="nav-item">
                          <a href="' . base_url($s->uri_modulo) . '" class="nav-link ' . ($sys_module->uri_modulo == $s->uri_modulo ? 'active' : '') . '">
                              <i class="nav-icon ' . $p->icon_class_modulo . '"></i><span>' . $s->nome_modulo . '</span>
                          </a>
                      </li>';
                }
                echo ($possuiSubItem ? '</ul>' : '');
                ?>
              </li>
            <?php } ?>
          </ul>
        </nav>
        <!-- End Sidebar-->

      </div>
      <!-- /.sidebar -->
    </aside>


    <!-- Content Wrapper. Contains page content -->
    <?= $content_view; ?>
    <!-- /.content-wrapper -->


    <footer class="main-footer">
      <div class="float-right d-none d-sm-block">
        <b><a href="https://www.eabc.com.br" target="_blank">EABC Tecnologia</a></b> 1.0 | CI-<?= CodeIgniter\CodeIgniter::CI_VERSION ?> | <?= $_SERVER['SERVER_NAME']; ?>
      </div>
      <strong>Copyright &copy; <?= date('Y'); ?> <a href="https://www.eabc.com.br">EABC Tecnologia</a></strong>
      Todos direitos Reservados.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url('templates/AdminLTE') ?>/dist/js/adminlte.min.js"></script>

  <!-- InputMask -->
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/moment/moment.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/inputmask/jquery.inputmask.min.js"></script>

  <!-- DataTables  & Plugins -->
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/jszip/jszip.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/pdfmake/pdfmake.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/pdfmake/vfs_fonts.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="<?= base_url('templates/AdminLTE') ?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

  <!------------------------------------------------------------------------------->
  <!--ALERTS MESSAGENS -->
  <!------------------------------------------------------------------------------->
  <script src="<?= base_url('framework/') ?>/us/framework.js"></script>
  <script src="<?= base_url('framework/') ?>/us/message-alert/message-alert.js"></script>
  <!--
    <script type="text/javascript" src="<?= base_url('framework/') ?>/us/jquery.mask/jquery.mask.min.js"></script>
  -->
  <div id="divMensagemGlobal" style="margin-bottom: 7px; z-index: 1000000001" class="mensagens">
  </div>
  <!------------------------------------------------------------------------------->
</body>

</html>