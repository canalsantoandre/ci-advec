<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ADVEC Voluntário">
  <meta name="theme-color" content="#0f172a">
  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <link rel="apple-touch-icon" href="<?= base_url('logo-advec.png') ?>">

  <title><?= esc($title ?? 'Definir Nova Senha - ADVEC') ?></title>

  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      background: radial-gradient(circle at 50% 10%, #1e293b 0%, #0f172a 100%);
      min-height: 100dvh;
      color: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.25rem;
    }

    .otp-container {
      width: 100%;
      max-width: 460px;
    }

    .portal-card {
      background: rgba(30, 41, 59, 0.75);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1.5rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
      padding: 2.25rem 2rem;
    }

    .shield-icon-wrapper {
      width: 72px;
      height: 72px;
      margin: 0 auto 1.25rem;
      background: rgba(37, 99, 235, 0.15);
      border: 1px solid rgba(59, 130, 246, 0.3);
      border-radius: 1.25rem;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #3b82f6;
      box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2);
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

    .otp-input {
      font-size: 1.6rem !important;
      letter-spacing: 0.5rem;
      font-weight: 800;
      text-align: center;
      color: #38bdf8 !important;
      font-family: monospace;
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
  </style>
</head>
<body>

  <div class="otp-container">
    <div class="portal-card text-center">
      
      <!-- Icon Header -->
      <div class="shield-icon-wrapper">
        <i class="bi bi-shield-check fs-1"></i>
      </div>

      <h3 class="fw-bold text-white mb-1">Validação de Segurança</h3>
      <p class="text-white-50 small mb-3">Defina sua senha pessoal para continuar</p>

      <!-- Badge com Telefone Mascarado -->
      <div class="mb-4">
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
          <i class="bi bi-whatsapp me-1"></i> Código enviado para <?= esc($telefoneMascarado ?? 'seu WhatsApp') ?>
        </span>
      </div>

      <!-- Alertas de Erro ou Sucesso -->
      <?php if (!empty($erro)) { ?>
        <div class="alert alert-danger border-0 rounded-3 text-start small mb-4 p-3 shadow-sm" role="alert">
          <div class="d-flex align-items-center gap-2 fw-bold mb-1">
            <i class="bi bi-exclamation-octagon-fill"></i> Atenção
          </div>
          <?= esc($erro) ?>
        </div>
      <?php } ?>

      <?php if (session()->getFlashdata('info_otp')) { ?>
        <div class="alert alert-info border-0 rounded-3 text-start small mb-4 p-3 shadow-sm" role="alert">
          <i class="bi bi-info-circle-fill me-1"></i> <?= session()->getFlashdata('info_otp') ?>
        </div>
      <?php } ?>

      <!-- Formulário de Troca de Senha com OTP -->
      <form method="post" action="<?= base_url('portal/confirmar-troca-senha-otp') ?>" id="formTrocaSenhaOtp">
        
        <!-- Código OTP -->
        <div class="mb-3 text-start">
          <label for="txtOtp" class="form-label text-white-50 small fw-semibold">
            <i class="bi bi-key-fill text-warning me-1"></i> Código OTP (6 dígitos) <span class="text-danger">*</span>
          </label>
          <input type="text" id="txtOtp" name="txtOtp" class="form-control otp-input" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required autofocus autocomplete="one-time-code">
          <div class="d-flex justify-content-between align-items-center mt-1">
            <small class="text-white-50" style="font-size: 0.72rem;">Válido por 10 minutos</small>
            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-info fw-semibold" id="btnReenviarOtp" onclick="reenviarOtpAjax()" style="font-size: 0.75rem;">
              <i class="bi bi-arrow-repeat me-1"></i> Reenviar código
            </button>
          </div>
        </div>

        <!-- Nova Senha -->
        <div class="mb-3 text-start">
          <label for="txtNovaSenha" class="form-label text-white-50 small fw-semibold">
            <i class="bi bi-lock-fill text-primary me-1"></i> Nova Senha Pessoal <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" id="txtNovaSenha" name="txtNovaSenha" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6" required>
            <button class="btn btn-toggle-pwd" type="button" onclick="togglePasswordInput('txtNovaSenha', 'iconNovaSenha')">
              <i class="bi bi-eye-fill" id="iconNovaSenha"></i>
            </button>
          </div>
        </div>

        <!-- Confirmar Nova Senha -->
        <div class="mb-4 text-start">
          <label for="txtConfirmaSenha" class="form-label text-white-50 small fw-semibold">
            <i class="bi bi-check2-circle text-success me-1"></i> Confirmar Nova Senha <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" id="txtConfirmaSenha" name="txtConfirmaSenha" class="form-control" placeholder="Repita a nova senha" minlength="6" required>
            <button class="btn btn-toggle-pwd" type="button" onclick="togglePasswordInput('txtConfirmaSenha', 'iconConfirmaSenha')">
              <i class="bi bi-eye-fill" id="iconConfirmaSenha"></i>
            </button>
          </div>
        </div>

        <!-- Botão Confirmar -->
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-primary btn-portal-submit text-white rounded-3 fs-6" id="btnSubmitOtp">
            <i class="bi bi-check2-all me-2"></i> VALIDAR & ATIVAR SENHA
          </button>
        </div>

        <!-- Voltar ao login -->
        <div class="pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center small">
          <a href="<?= base_url('portal/logout') ?>" class="text-white-50 text-decoration-none hover-white">
            <i class="bi bi-arrow-left me-1"></i> Cancelar / Voltar ao Login
          </a>
          <span class="text-white-50">ADVEC</span>
        </div>

      </form>

    </div>
  </div>

  <!-- Toast Notification Container -->
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="otpToast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body fw-semibold" id="otpToastBody"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function togglePasswordInput(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash-fill';
      } else {
        input.type = 'password';
        icon.className = 'bi bi-eye-fill';
      }
    }

    function showToast(tipo, mensagem) {
      const toastEl = document.getElementById('otpToast');
      const toastBody = document.getElementById('otpToastBody');
      toastEl.className = 'toast align-items-center text-white border-0 shadow-lg rounded-3 ' + (tipo === 'success' ? 'bg-success' : 'bg-danger');
      toastBody.innerHTML = (tipo === 'success' ? '<i class="bi bi-check-circle-fill me-2"></i>' : '<i class="bi bi-exclamation-octagon-fill me-2"></i>') + mensagem;
      const bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
      bsToast.show();
    }

    let cooldownTimer = null;
    let secondsLeft = 0;

    function reenviarOtpAjax() {
      if (secondsLeft > 0) return;

      const btn = document.getElementById('btnReenviarOtp');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';

      fetch('<?= base_url('portal/reenviar-otp') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          showToast('success', data.message || 'Novo código OTP enviado para o seu WhatsApp!');
          startCooldown(60);
        } else {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Reenviar código';
          showToast('error', data.message || 'Erro ao reenviar código.');
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Reenviar código';
        showToast('error', 'Falha na comunicação com o servidor.');
      });
    }

    function startCooldown(sec) {
      secondsLeft = sec;
      const btn = document.getElementById('btnReenviarOtp');
      btn.disabled = true;

      clearInterval(cooldownTimer);
      cooldownTimer = setInterval(() => {
        secondsLeft--;
        if (secondsLeft <= 0) {
          clearInterval(cooldownTimer);
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Reenviar código';
        } else {
          btn.innerHTML = `<i class="bi bi-clock me-1"></i> Aguarde (${secondsLeft}s)`;
        }
      }, 1000);
    }

    // Auto-submissão ou formatação rápida de OTP
    document.getElementById('txtOtp').addEventListener('input', function(e) {
      this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
  </script>
</body>
</html>
