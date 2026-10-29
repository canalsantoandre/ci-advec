<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>ADVEC API - Documentação Interativa (Swagger UI)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css" />
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f8fafc;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    .custom-topbar {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #fff;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      position: sticky;
      top: 0;
      z-index: 999;
    }
    .brand-title {
      font-weight: 700;
      font-size: 1.15rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .brand-badge {
      background: #2563eb;
      color: #fff;
      font-size: 0.72rem;
      padding: 3px 8px;
      border-radius: 999px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    .back-btn {
      color: #cbd5e1;
      text-decoration: none;
      font-size: 0.88rem;
      font-weight: 600;
      padding: 8px 16px;
      border-radius: 8px;
      background: rgba(255,255,255,0.08);
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .back-btn:hover {
      background: rgba(255,255,255,0.18);
      color: #fff;
    }
    .swagger-ui {
      max-width: 1200px;
      margin: 24px auto;
      padding: 0 20px 40px;
    }
    .swagger-ui .topbar { display: none; }
    .swagger-ui .info { margin: 20px 0; }
    .swagger-ui .info .title { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; color: #0f172a; }
    .swagger-ui .opblock { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 16px; }
    .swagger-ui .opblock .opblock-summary { padding: 12px 16px; }
    .swagger-ui .opblock-tag { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 1.15rem; }
    code, pre { font-family: 'JetBrains Mono', monospace !important; }
  </style>
</head>
<body>

  <header class="custom-topbar">
    <div class="brand-title">
      <i class="bi bi-cpu-fill" style="color: #60a5fa;"></i>
      <span>ADVEC • Documentação Oficial da API</span>
      <span class="brand-badge">REST v1</span>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
      <a href="<?= base_url('api/v1/openapi.json') ?>" target="_blank" class="back-btn" title="Baixar JSON OpenAPI">
        <i class="bi bi-filetype-json"></i> OpenAPI Spec
      </a>
      <a href="<?= base_url('api-client') ?>" class="back-btn">
        <i class="bi bi-arrow-left"></i> Voltar ao Painel ADVEC
      </a>
    </div>
  </header>

  <div id="swagger-ui"></div>

  <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" crossorigin></script>
  <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-standalone-preset.js" crossorigin></script>
  <script>
    window.onload = function() {
      SwaggerUIBundle({
        url: "<?= $openapiUrl ?>",
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],
        plugins: [
          SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "BaseLayout",
        defaultModelsExpandDepth: -1,
        docExpansion: "list"
      });
    };
  </script>
</body>
</html>
