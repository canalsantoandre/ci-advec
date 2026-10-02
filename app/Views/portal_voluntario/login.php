<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  
  <!-- PWA & Mobile Fullscreen Settings (Oculta barra do navegador no mobile) -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ADVEC Voluntário">
  <meta name="theme-color" content="#0b1120">
  <meta name="msapplication-navbutton-color" content="#0b1120">
  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <link rel="apple-touch-icon" href="<?= base_url('logo-advec.png') ?>">

  <title><?= esc($title ?? 'Portal do Voluntário - ADVEC') ?></title>

  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- jQuery & Mask Plugin -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

  <style>
    html {
      height: 100%;
      background-color: #0b1120;
    }

    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      background: radial-gradient(circle at 50% 30%, #1e293b 0%, #0b1120 100%) no-repeat fixed;
      background-color: #0b1120;
      min-height: 100%;
      min-height: 100vh;
      min-height: 100dvh;
      margin: 0;
      color: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem 1rem;
      box-sizing: border-box;
    }

    .login-container {
      width: 100%;
      max-width: 440px;
    }

    .portal-card {
      background: rgba(30, 41, 59, 0.75);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1.5rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
      padding: 2.5rem 2rem;
    }

    .portal-logo-wrapper {
      width: 80px;
      height: 80px;
      margin: 0 auto 1.25rem;
      padding: 8px;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1.25rem;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    .portal-logo-wrapper img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 0.85rem;
    }

    .input-group .form-control,
    .input-group .input-group-text,
    .input-group .btn {
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
    }

    .input-group-text {
      background-color: rgba(15, 23, 42, 0.6);
      border-color: rgba(255, 255, 255, 0.15);
      color: #94a3b8;
    }

    .form-control {
      background-color: rgba(15, 23, 42, 0.6);
      border-color: rgba(255, 255, 255, 0.15);
      color: #f8fafc;
    }

    .form-control:focus {
      background-color: rgba(15, 23, 42, 0.85);
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
      color: #ffffff;
    }

    .form-control::placeholder {
      color: #64748b;
    }

    .btn-toggle-pwd {
      background-color: rgba(15, 23, 42, 0.6);
      border-color: rgba(255, 255, 255, 0.15);
      color: #94a3b8;
    }

    .btn-toggle-pwd:hover {
      background-color: rgba(30, 41, 59, 0.9);
      border-color: rgba(255, 255, 255, 0.25);
      color: #f8fafc;
    }

    .btn-portal-submit {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      border: none;
      padding: 0.85rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
      transition: all 0.2s ease;
    }

    .btn-portal-submit:hover {
      background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(37, 99, 235, 0.5);
    }

    .btn-portal-submit:active {
      transform: translateY(0);
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="portal-card text-center">
      
      <!-- Logo & Header -->
      <div class="portal-logo-wrapper">
        <img src="<?= base_url('logo-advec.png') ?>" alt="ADVEC" onerror="this.src='<?= base_url('templates/AdminLTE/dist/img/AdminLTELogo.png') ?>'">
      </div>

      <h3 class="fw-bold text-white mb-1">Portal do Voluntário</h3>
      <p class="text-white-50 small mb-4">Consulte suas escalas e confirme sua presença</p>

      <!-- Alerta de Erro -->
      <?php if (!empty($erro)) { ?>
        <div class="alert alert-danger border-0 rounded-3 text-start small mb-4 p-3 shadow-sm" role="alert">
          <div class="d-flex align-items-center gap-2 fw-bold mb-1">
            <i class="bi bi-exclamation-triangle-fill"></i> Erro de Autenticação
          </div>
          <?= esc($erro) ?>
        </div>
      <?php } ?>

      <!-- Formulário de Login -->
      <form method="post" action="<?= base_url('portal/login') ?>">
        
        <!-- WhatsApp / Telefone -->
        <div class="mb-3 text-start">
          <label for="txtTelefone" class="form-label text-white-50 small fw-semibold">
            <i class="bi bi-whatsapp text-success me-1"></i> Telefone WhatsApp
          </label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="bi bi-phone"></i>
            </span>
            <input type="tel" id="txtTelefone" name="txtTelefone" class="form-control" placeholder="(11) 99999-9999" value="<?= isset($telefone) ? esc($telefone) : '' ?>" required autofocus>
          </div>
          <small class="text-white-50 d-block mt-1" style="font-size: 0.75rem;">
            <i class="bi bi-info-circle me-1"></i> Digite o telefone cadastrado na igreja
          </small>
        </div>

        <!-- Senha de Acesso -->
        <div class="mb-4 text-start">
          <label for="txtSenha" class="form-label text-white-50 small fw-semibold">
            <i class="bi bi-key-fill text-warning me-1"></i> Senha de Acesso
          </label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="bi bi-lock-fill"></i>
            </span>
            <input type="password" id="txtSenha" name="txtSenha" class="form-control" placeholder="Digite sua senha" required>
            <button class="btn btn-toggle-pwd" type="button" id="btnTogglePassword" title="Mostrar/Ocultar Senha">
              <i class="bi bi-eye-fill" id="iconTogglePassword"></i>
            </button>
          </div>
          <small class="text-white-50 d-block mt-1" style="font-size: 0.75rem;">
            <i class="bi bi-shield-lock me-1"></i> Primeiro acesso: sua senha inicial é o seu telefone
          </small>
        </div>

        <!-- Botão Entrar -->
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-primary btn-portal-submit text-white rounded-3 fs-6">
            <i class="bi bi-box-arrow-in-right me-2"></i> ENTRAR NO PORTAL
          </button>
        </div>

        <!-- Rodapé do Card -->
        <div class="pt-3 border-top border-secondary border-opacity-25 text-white-50 small">
          ADVEC &bull; Gestão de Voluntários e Escalas
        </div>

      </form>

    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script de Máscara e Toggle de Senha -->
  <script>
    $(document).ready(function() {
      // Máscara dinâmica de telefone SP/BR (9 dígitos com DDD)
      const maskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
      };
      const options = {
        onKeyPress: function(val, e, field, options) {
          field.mask(maskBehavior.apply({}, arguments), options);
        }
      };
      $('#txtTelefone').mask(maskBehavior, options);

      // Alternar visualização de senha
      $('#btnTogglePassword').on('click', function() {
        const input = $('#txtSenha');
        const icon = $('#iconTogglePassword');
        if (input.attr('type') === 'password') {
          input.attr('type', 'text');
          icon.removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
        } else {
          input.attr('type', 'password');
          icon.removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
        }
      });
    });

    // Ocultar barra de endereços em navegadores móveis (Safari / Chrome)
    function ocultarBarraMobile() {
      if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        setTimeout(function() {
          window.scrollTo(0, 1);
        }, 100);
      }
    }
    window.addEventListener('load', ocultarBarraMobile);
    window.addEventListener('orientationchange', ocultarBarraMobile);
    document.addEventListener('touchstart', function() {
      if (window.scrollY === 0) {
        window.scrollTo(0, 1);
      }
    }, { passive: true, once: true });

    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('<?= base_url('sw.js') ?>').catch(function(err) {});
    }
  </script>
</body>
</html>
