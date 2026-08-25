<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Sistema ADVEC Gestão</title>

  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">

  <!-- AdminLTE 4 CSS -->
  <link rel="stylesheet" href="<?= base_url('templates/AdminLTE') ?>/dist/css/adminlte.min.css">

  <style>
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
    }

    .login-card-container {
      width: 100%;
      max-width: 420px;
    }

    .login-card {
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(16px);
      background: rgba(30, 41, 59, 0.85);
      border-radius: 1.25rem;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
      color: #f8fafc;
    }

    .login-brand-logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
      background: rgba(255, 255, 255, 0.1);
      padding: 10px;
      border-radius: 50%;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .form-control {
      border-radius: 0.75rem;
      padding: 0.75rem 1rem;
      background: rgba(15, 23, 42, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #fff;
    }

    .form-control:focus {
      background: rgba(15, 23, 42, 0.8);
      border-color: #3b82f6;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
      color: #fff;
    }

    .form-control::placeholder {
      color: #94a3b8;
    }

    .input-group-text {
      border-radius: 0.75rem;
      background: rgba(15, 23, 42, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #94a3b8;
    }

    .btn-login {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      border: none;
      border-radius: 0.75rem;
      padding: 0.8rem 1.5rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
      transition: all 0.2s ease;
    }

    .btn-login:hover {
      background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
    }
  </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">
  <div class="login-card-container">
    <div class="login-card p-4 p-md-5 text-center">
      
      <!-- Brand Logo / Header -->
      <div class="mb-4">
        <img src="<?= base_url('logo-advec.png') ?>" alt="ADVEC Logo" class="login-brand-logo mb-3" onerror="this.src='<?= base_url('templates/AdminLTE') ?>/dist/img/AdminLTELogo.png'">
        <h4 class="fw-bold mb-1 text-white">ADVEC GESTÃO</h4>
        <p class="text-secondary small mb-0">Informe suas credenciais para continuar</p>
      </div>

      <!-- Login Form -->
      <form method="post" action="<?= base_url('dshlogin') ?>">
        
        <div class="mb-3 text-start">
          <label for="txtUsuario" class="form-label text-secondary small fw-medium">Usuário</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
            <input type="text" id="txtUsuario" name="txtUsuario" class="form-control" placeholder="Digite seu usuário" value="<?= isset($usuario) ? esc($usuario) : '' ?>" required autofocus>
          </div>
        </div>

        <div class="mb-4 text-start">
          <label for="txtSenhaAtual" class="form-label text-secondary small fw-medium">Senha</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" id="txtSenhaAtual" name="txtSenhaAtual" placeholder="Digite sua senha" value="<?= isset($senha) ? esc($senha) : '' ?>" required>
            <button class="btn btn-outline-secondary input-group-text" type="button" id="togglePassword">
              <i class="bi bi-eye-fill" id="toggleIcon"></i>
            </button>
          </div>
        </div>

        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-primary btn-login text-white">
            <i class="bi bi-box-arrow-in-right me-2"></i> ACESSAR SISTEMA
          </button>
        </div>

        <!-- Validation Alerts -->
        <?php if (isset($validation)) : ?>
          <div class="alert alert-danger border-0 shadow-sm rounded-3 text-start small mb-3" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
              <i class="bi bi-exclamation-triangle-fill"></i> ATENÇÃO
            </div>
            <?= $validation->listErrors() ?>
          </div>
        <?php endif; ?>

        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25 text-center text-secondary small">
          ADVEC Gestão &bull; Versão 1.0 (CI v<?= CodeIgniter\CodeIgniter::CI_VERSION ?>)
        </div>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
    // Password toggle
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#txtSenhaAtual');
    const toggleIcon = document.querySelector('#toggleIcon');

    if (togglePassword && passwordInput) {
      togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        toggleIcon.classList.toggle('bi-eye-fill');
        toggleIcon.classList.toggle('bi-eye-slash-fill');
      });
    }
  </script>
</body>

</html>