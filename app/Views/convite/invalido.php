<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Convite Indisponível - ADVEC</title>

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

    .error-card {
      max-width: 460px;
      width: 100%;
      border-radius: 1.5rem;
      border: 1px solid rgba(226, 232, 240, 0.8);
      background: #ffffff;
      box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.08);
    }

    [data-bs-theme="dark"] .error-card {
      background-color: #1e293b;
      border-color: #334155;
    }

    .icon-circle {
      width: 76px;
      height: 76px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
      font-size: 2.2rem;
      margin-bottom: 1.25rem;
    }
  </style>
</head>

<body class="p-3">
  <div class="error-card p-4 p-md-5 text-center">
    <div class="icon-circle">
      <i class="bi bi-link-45deg"></i>
    </div>

    <h4 class="fw-bold mb-2 text-body">Link de Convite Indisponível</h4>
    <p class="text-secondary small mb-4">
      <?= esc($motivo ?? 'Este link de convite não está mais ativo, expirou a data limite ou já atingiu o limite máximo de utilizações.') ?>
    </p>

    <div class="p-3 bg-body-tertiary rounded-3 border small text-muted mb-4">
      Se você acredita que isto é um engano, por favor entre em contato diretamente com o líder do seu departamento para solicitar um novo link de convite.
    </div>

    <a href="<?= base_url('portal/login') ?>" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm w-100">
      <i class="bi bi-box-arrow-in-right me-1"></i> Ir para o Portal do Voluntário
    </a>
  </div>
</body>

</html>
