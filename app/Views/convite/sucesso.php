<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Cadastro Enviado com Sucesso - ADVEC</title>

  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
      background: #f1f5f9;
      min-height: 100dvh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    [data-bs-theme="dark"] body {
      background-color: #0b1329;
      color: #e2e8f0;
    }

    .success-card {
      max-width: 480px;
      width: 100%;
      border-radius: 1.5rem;
      border: 1px solid rgba(226, 232, 240, 0.8);
      background: #ffffff;
      box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.08);
    }

    [data-bs-theme="dark"] .success-card {
      background-color: #1e293b;
      border-color: #334155;
    }

    .icon-success-circle {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(16, 185, 129, 0.15);
      color: #10b981;
      font-size: 2.5rem;
      margin-bottom: 1.25rem;
      box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
    }
  </style>
</head>

<body class="p-3">
  <div class="success-card p-4 p-md-5 text-center">
    <div class="icon-success-circle">
      <i class="bi bi-check-lg"></i>
    </div>

    <h4 class="fw-bold mb-2 text-body">Cadastro Enviado com Sucesso! 🎉</h4>
    <p class="text-secondary small mb-4">
      Seus dados e foto foram enviados para a liderança do departamento.
    </p>

    <div class="p-3 bg-body-tertiary rounded-4 border text-start small mb-4">
      <div class="d-flex align-items-start gap-2 mb-2">
        <i class="bi bi-whatsapp text-success fs-5 flex-shrink-0"></i>
        <div>
          <strong class="text-body d-block">Notificação no WhatsApp</strong>
          <span class="text-muted">Assim que o líder aprovar seu cadastro, você receberá uma mensagem no WhatsApp com o link oficial de acesso ao portal e instruções.</span>
        </div>
      </div>
      <div class="d-flex align-items-start gap-2 pt-2 border-top">
        <i class="bi bi-shield-lock-fill text-primary fs-5 flex-shrink-0"></i>
        <div>
          <strong class="text-body d-block">Primeiro Acesso Seguro</strong>
          <span class="text-muted">No primeiro login você definirá sua senha definitiva através de um código de verificação.</span>
        </div>
      </div>
    </div>

    <a href="<?= base_url('portal/login') ?>" class="btn btn-outline-primary rounded-pill px-4 py-2.5 fw-bold w-100">
      <i class="bi bi-house-door-fill me-1"></i> Ir para a Página Inicial
    </a>
  </div>
</body>

</html>
