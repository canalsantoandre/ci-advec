<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>403 - Acesso Negado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5 text-center">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle p-4 mb-4 mx-auto" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-lock display-5"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">403 &bull; Acesso Negado</h2>
                    <p class="text-secondary mb-4">
                        <?= !empty($message) ? esc($message) : 'Você não possui permissão para acessar esta funcionalidade.' ?>
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Voltar
                        </a>
                        <a href="/dashboard" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-house-door-fill me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
