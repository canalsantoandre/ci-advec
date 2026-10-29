<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-cpu-fill text-primary me-2"></i>Integrações & APIs
        </h3>
        <p class="text-secondary small mb-0">Gerencie credenciais seguras de autenticação OAuth2 / JWT, controle limites de taxa (Rate Limit) e monitore automações externas</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Aplicações API</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')) { ?>
      <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
        <div><?= session()->getFlashdata('success') ?></div>
      </div>
    <?php } ?>
    <?php if (session()->getFlashdata('error')) { ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <div><?= session()->getFlashdata('error') ?></div>
      </div>
    <?php } ?>

    <style>
      /* Design System Premium para Documentação e Endpoints */
      .api-doc-card {
        border-radius: 18px !important;
        border: 1px solid var(--bs-border-color-translucent) !important;
        background: var(--bs-body-bg);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      }

      .swagger-accordion .accordion-item {
        border: 1px solid var(--bs-border-color-translucent) !important;
        border-radius: 14px !important;
        margin-bottom: 1.25rem;
        overflow: hidden;
        background: transparent;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      }

      .swagger-accordion .accordion-item:hover {
        border-color: rgba(37, 99, 235, 0.35) !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
      }

      .swagger-accordion .accordion-button {
        padding: 1.15rem 1.4rem;
        font-size: 0.95rem;
        background: rgba(var(--bs-tertiary-bg-rgb), 0.4);
        border: 0;
        transition: all 0.2s ease;
      }

      .swagger-accordion .accordion-button:not(.collapsed) {
        background: rgba(37, 99, 235, 0.06);
        box-shadow: none;
      }

      .swagger-accordion .accordion-body {
        padding: 2rem 2.2rem;
        background: var(--bs-body-bg);
      }

      /* Banner Informativo */
      .api-info-banner {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        border-radius: 12px;
        background: rgba(37, 99, 235, 0.05);
        border: 1px solid rgba(37, 99, 235, 0.15);
        margin-bottom: 1.75rem;
        color: var(--bs-body-color);
        font-size: 0.92rem;
        line-height: 1.65;
      }

      .api-info-banner i {
        font-size: 1.35rem;
        color: #3b82f6;
        flex-shrink: 0;
        margin-top: 1px;
      }

      /* Barra de URL Estilo Ferramenta de API */
      .api-url-bar {
        display: flex;
        align-items: stretch;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-tertiary-bg);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1.75rem;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
      }

      .api-url-bar .method-badge {
        padding: 10px 18px;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.86rem;
        display: flex;
        align-items: center;
        letter-spacing: 0.5px;
      }

      .api-url-bar .url-text {
        flex: 1;
        border: 0;
        background: transparent;
        padding: 10px 16px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.88rem;
        color: var(--bs-body-color);
        outline: none;
      }

      .api-url-bar .btn-copy {
        border: 0;
        background: transparent;
        padding: 0 18px;
        font-size: 0.84rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
        transition: all 0.2s ease;
        border-left: 1px solid var(--bs-border-color);
        display: inline-flex;
        align-items: center;
        gap: 6px;
      }

      .api-url-bar .btn-copy:hover {
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
      }

      /* Segmented Control / Nav Pills */
      .api-segmented-pills {
        display: flex;
        gap: 8px;
        background: var(--bs-tertiary-bg);
        padding: 6px;
        border-radius: 12px;
        border: 1px solid var(--bs-border-color-translucent);
        margin-bottom: 1.75rem;
      }

      .api-segmented-pills .nav-link {
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
        border: 0;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 7px;
      }

      .api-segmented-pills .nav-link:hover {
        color: var(--bs-body-color);
        background: rgba(255, 255, 255, 0.05);
      }

      .api-segmented-pills .nav-link.active {
        background: #2563eb;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
      }

      /* Tabela de Parâmetros e Headers (Theme Adaptive) */
      .api-table-wrapper {
        border: 1px solid var(--bs-border-color-translucent);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1.5rem;
      }

      .api-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
      }

      .api-table th {
        background: var(--bs-tertiary-bg);
        color: var(--bs-secondary-color);
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 12px 18px;
        border-bottom: 1px solid var(--bs-border-color-translucent);
      }

      .api-table td {
        padding: 14px 18px;
        font-size: 0.88rem;
        color: var(--bs-body-color);
        border-bottom: 1px solid var(--bs-border-color-translucent);
        vertical-align: middle;
        line-height: 1.5;
      }

      .api-table tr:last-child td {
        border-bottom: 0;
      }

      .api-field-pill {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        font-weight: 700;
        color: #2563eb;
        background: rgba(37, 99, 235, 0.08);
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid rgba(37, 99, 235, 0.18);
        display: inline-block;
      }

      [data-bs-theme="dark"] .api-field-pill {
        color: #60a5fa;
        background: rgba(96, 165, 250, 0.12);
        border-color: rgba(96, 165, 250, 0.25);
      }

      /* Janela de Código Estilo Terminal Mac */
      .api-code-window {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: #090d16;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
      }

      .api-code-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        background: #111726;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
      }

      .api-code-dots {
        display: flex;
        gap: 7px;
        align-items: center;
      }

      .api-code-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
      }

      .dot-red { background: #ff5f56; }
      .dot-yellow { background: #ffbd2e; }
      .dot-green { background: #27c93f; }

      .api-code-title {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.76rem;
        color: #8b949e;
        letter-spacing: 0.5px;
      }

      .api-code-copy-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        color: #e6edf3;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 7px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
      }

      .api-code-copy-btn:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
      }

      .api-code-body {
        padding: 18px 20px;
        margin: 0;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        line-height: 1.65;
        color: #e6edf3;
        overflow-x: auto;
        background: transparent;
      }

      .section-heading-sm {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--bs-secondary-color);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 6px;
      }
    </style>

    <!-- Documentação Interativa dos Endpoints da API (Estilo Swagger Premium) -->
    <div class="card api-doc-card shadow-sm mb-4">
      <div class="card-header bg-transparent py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom">
        <div>
          <h5 class="fw-bold mb-1 text-body d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-code-fill text-primary fs-4"></i> Documentação da API REST (Endpoints)
          </h5>
          <p class="text-secondary small mb-0">Consulte schemas de envio, parâmetros obrigatórios e exemplos reais de resposta.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="<?= base_url('api/v1/docs') ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" title="Abrir interface completa do Swagger UI">
            <i class="bi bi-box-arrow-up-right"></i> Abrir Swagger UI Oficial
          </a>
          <a href="<?= base_url('api/v1/openapi.json') ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-2" title="Baixar especificação OpenAPI 3.0 em JSON">
            <i class="bi bi-filetype-json"></i> OpenAPI Spec
          </a>
        </div>
      </div>

      <div class="card-body p-4">
                <!-- Acordeão Swagger -->
        <div class="accordion swagger-accordion" id="accordionSwaggerEndpoints">

          <!-- ENDPOINT 1: POST /api/v1/auth/token -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingAuthToken">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAuthToken" aria-expanded="false" aria-controls="collapseAuthToken">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100 justify-content-between pe-3">
                  <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success px-3 py-2 fw-bold font-monospace shadow-sm" style="font-size: 0.82rem; letter-spacing: 0.5px;">POST</span>
                    <span class="font-monospace fw-bold text-body" style="font-size: 0.96rem;">/api/v1/auth/token</span>
                    <span class="text-secondary small d-none d-lg-inline">— Obtenção e Renovação de Token JWT</span>
                  </div>
                  <div>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border rounded-pill px-3 py-1.5 small">
                      <i class="bi bi-shield-check me-1 text-success"></i>Público • Anti Brute-Force
                    </span>
                  </div>
                </div>
              </button>
            </h2>
            <div id="collapseAuthToken" class="accordion-collapse collapse" aria-labelledby="headingAuthToken" data-bs-parent="#accordionSwaggerEndpoints">
              <div class="accordion-body">
                
                <!-- Banner Descritivo -->
                <div class="api-info-banner">
                  <i class="bi bi-info-circle-fill"></i>
                  <div>
                    Autentica sua automação externa (n8n, webhook, bot) utilizando o <strong>Client ID</strong> e <strong>Client Secret</strong> cadastrados na tabela abaixo. Emite um <strong>Bearer JWT</strong> válido por 1 hora para autenticar todas as demais consultas.
                  </div>
                </div>

                <!-- Barra de URL Estilizada -->
                <div class="api-url-bar">
                  <span class="method-badge bg-success text-white">POST</span>
                  <input type="text" class="url-text" readonly value="<?= base_url('api/v1/auth/token') ?>" id="epAuthUrl">
                  <button class="btn-copy" type="button" onclick="copiarTexto('epAuthUrl')" title="Copiar URL">
                    <i class="bi bi-clipboard"></i> Copiar URL
                  </button>
                </div>

                <!-- Nav Pills Segmented -->
                <ul class="nav api-segmented-pills" id="tabsAuth" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-auth-req-btn" data-bs-toggle="pill" data-bs-target="#tab-auth-req" type="button" role="tab">
                      <i class="bi bi-arrow-up-right-circle"></i> Dados de Envio (Request)
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-auth-res-btn" data-bs-toggle="pill" data-bs-target="#tab-auth-res" type="button" role="tab">
                      <i class="bi bi-check-circle-fill text-success"></i> Resposta (200 OK)
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-auth-curl-btn" data-bs-toggle="pill" data-bs-target="#tab-auth-curl" type="button" role="tab">
                      <i class="bi bi-terminal"></i> Exemplo cURL / n8n
                    </button>
                  </li>
                </ul>

                <div class="tab-content" id="tabsAuthContent">
                  <!-- Tab Envio -->
                  <div class="tab-pane fade show active" id="tab-auth-req" role="tabpanel">
                    <div class="row g-4">
                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-card-heading"></i> Headers Obrigatórios</div>
                        <div class="api-table-wrapper">
                          <table class="api-table">
                            <thead><tr><th style="width: 40%;">Header</th><th>Valor</th></tr></thead>
                            <tbody>
                              <tr>
                                <td><span class="api-field-pill">Content-Type</span></td>
                                <td class="font-monospace text-secondary">application/json</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>

                        <div class="section-heading-sm mt-3"><i class="bi bi-code-square"></i> Parâmetros do Body (JSON)</div>
                        <div class="api-table-wrapper">
                          <table class="api-table">
                            <thead>
                              <tr>
                                <th style="width: 32%;">Campo</th>
                                <th style="width: 22%;">Tipo</th>
                                <th style="width: 18%;">Obrigatório</th>
                                <th>Descrição</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td><span class="api-field-pill">client_id</span></td>
                                <td class="text-secondary small">string</td>
                                <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">Sim</span></td>
                                <td class="small">Identificador público da sua aplicação.</td>
                              </tr>
                              <tr>
                                <td><span class="api-field-pill">client_secret</span></td>
                                <td class="text-secondary small">string</td>
                                <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">Sim</span></td>
                                <td class="small">Chave secreta privada da aplicação.</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-file-earmark-code"></i> Exemplo de Payload (JSON)</div>
                        <div class="api-code-window">
                          <div class="api-code-header">
                            <div class="api-code-dots">
                              <span class="api-code-dot dot-red"></span>
                              <span class="api-code-dot dot-yellow"></span>
                              <span class="api-code-dot dot-green"></span>
                            </div>
                            <span class="api-code-title">request-body.json</span>
                            <button type="button" class="api-code-copy-btn" onclick="copiarValor(document.getElementById('codeReqAuth').innerText)">
                              <i class="bi bi-clipboard"></i> Copiar
                            </button>
                          </div>
                          <pre class="api-code-body" id="codeReqAuth">{
  "client_id": "advec_n8n_escalas",
  "client_secret": "sec_77220678f07452fb20b5d7f771f647bac2368f227232ded6"
}</pre>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tab Retorno -->
                  <div class="tab-pane fade" id="tab-auth-res" role="tabpanel">
                    <div class="row g-4">
                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-file-earmark-check"></i> Campos do Retorno</div>
                        <div class="api-table-wrapper">
                          <table class="api-table">
                            <thead><tr><th style="width: 35%;">Campo</th><th style="width: 20%;">Tipo</th><th>Significado</th></tr></thead>
                            <tbody>
                              <tr><td><span class="api-field-pill">access_token</span></td><td class="text-secondary small">string</td><td class="small">Bearer JWT para autenticar as próximas chamadas.</td></tr>
                              <tr><td><span class="api-field-pill">token_type</span></td><td class="text-secondary small">string</td><td class="small">Tipo do token (sempre <code class="text-body">Bearer</code>).</td></tr>
                              <tr><td><span class="api-field-pill">expires_in</span></td><td class="text-secondary small">integer</td><td class="small">Tempo de vida do token em segundos (3600 = 1 hora).</td></tr>
                              <tr><td><span class="api-field-pill">client_id</span></td><td class="text-secondary small">string</td><td class="small">ID da aplicação proprietária.</td></tr>
                              <tr><td><span class="api-field-pill">app_name</span></td><td class="text-secondary small">string</td><td class="small">Nome amigável da aplicação cadastrada.</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-check2-circle text-success"></i> Resposta da API (Status 200 OK)</div>
                        <div class="api-code-window">
                          <div class="api-code-header">
                            <div class="api-code-dots">
                              <span class="api-code-dot dot-red"></span>
                              <span class="api-code-dot dot-yellow"></span>
                              <span class="api-code-dot dot-green"></span>
                            </div>
                            <span class="api-code-title">response-200.json</span>
                            <button type="button" class="api-code-copy-btn" onclick="copiarValor(document.getElementById('codeResAuth').innerText)">
                              <i class="bi bi-clipboard"></i> Copiar
                            </button>
                          </div>
                          <pre class="api-code-body" id="codeResAuth">{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhZHZlY19uOG5fZXNjYWxhcy...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "client_id": "advec_n8n_escalas",
  "app_name": "n8n - Automação Cobrança de Escalas"
}</pre>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tab cURL -->
                  <div class="tab-pane fade" id="tab-auth-curl" role="tabpanel">
                    <div class="section-heading-sm"><i class="bi bi-terminal"></i> Execução rápida via Terminal</div>
                    <div class="api-code-window">
                      <div class="api-code-header">
                        <div class="api-code-dots">
                          <span class="api-code-dot dot-red"></span>
                          <span class="api-code-dot dot-yellow"></span>
                          <span class="api-code-dot dot-green"></span>
                        </div>
                        <span class="api-code-title">curl-command.sh</span>
                        <button type="button" class="api-code-copy-btn" onclick="copiarValor(document.getElementById('codeCurlAuth').innerText)">
                          <i class="bi bi-clipboard"></i> Copiar cURL
                        </button>
                      </div>
                      <pre class="api-code-body" id="codeCurlAuth">curl -X POST <?= base_url('api/v1/auth/token') ?> \
  -H "Content-Type: application/json" \
  -d '{"client_id":"SEU_CLIENT_ID","client_secret":"SEU_CLIENT_SECRET"}'</pre>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- ENDPOINT 2: GET /api/v1/escalas/lembretes-pendentes -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingLembretes">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLembretes" aria-expanded="false" aria-controls="collapseLembretes">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100 justify-content-between pe-3">
                  <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary px-3 py-2 fw-bold font-monospace shadow-sm" style="font-size: 0.82rem; letter-spacing: 0.5px;">GET</span>
                    <span class="font-monospace fw-bold text-body" style="font-size: 0.96rem;">/api/v1/escalas/lembretes-pendentes</span>
                    <span class="text-secondary small d-none d-lg-inline">— Consulta de Voluntários com Escalas Pendentes</span>
                  </div>
                  <div>
                    <span class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1.5 small">
                      <i class="bi bi-key-fill me-1"></i>Requer Bearer JWT
                    </span>
                  </div>
                </div>
              </button>
            </h2>
            <div id="collapseLembretes" class="accordion-collapse collapse" aria-labelledby="headingLembretes" data-bs-parent="#accordionSwaggerEndpoints">
              <div class="accordion-body">
                
                <!-- Banner Descritivo -->
                <div class="api-info-banner">
                  <i class="bi bi-bell-fill"></i>
                  <div>
                    Retorna voluntários <strong>ativos</strong> escalados em cultos futuros com status <strong>PENDENTE</strong> de confirmação. A API agrupa automaticamente múltiplos cultos da mesma pessoa em um só bloco e entrega a <strong>mensagem_pronta</strong> com emojis e link para disparo direto no WhatsApp.
                  </div>
                </div>

                <!-- Barra de URL Estilizada -->
                <div class="api-url-bar">
                  <span class="method-badge bg-primary text-white">GET</span>
                  <input type="text" class="url-text" readonly value="<?= base_url('api/v1/escalas/lembretes-pendentes') ?>" id="epLembretesUrl">
                  <button class="btn-copy" type="button" onclick="copiarTexto('epLembretesUrl')" title="Copiar URL">
                    <i class="bi bi-clipboard"></i> Copiar URL
                  </button>
                </div>

                <!-- Nav Pills Segmented -->
                <ul class="nav api-segmented-pills" id="tabsLembretes" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-lemb-req-btn" data-bs-toggle="pill" data-bs-target="#tab-lemb-req" type="button" role="tab">
                      <i class="bi bi-arrow-up-right-circle"></i> Parâmetros & Headers
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-lemb-res-btn" data-bs-toggle="pill" data-bs-target="#tab-lemb-res" type="button" role="tab">
                      <i class="bi bi-check-circle-fill text-success"></i> Resposta Completa (200 OK)
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-lemb-curl-btn" data-bs-toggle="pill" data-bs-target="#tab-lemb-curl" type="button" role="tab">
                      <i class="bi bi-terminal"></i> Exemplo cURL / n8n
                    </button>
                  </li>
                </ul>

                <div class="tab-content" id="tabsLembretesContent">
                  <!-- Tab Envio -->
                  <div class="tab-pane fade show active" id="tab-lemb-req" role="tabpanel">
                    <div class="row g-4">
                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-card-heading"></i> Headers Obrigatórios</div>
                        <div class="api-table-wrapper">
                          <table class="api-table">
                            <thead><tr><th style="width: 40%;">Header</th><th>Valor / Formato</th></tr></thead>
                            <tbody>
                              <tr>
                                <td><span class="api-field-pill">Authorization</span></td>
                                <td class="font-monospace text-secondary">Bearer &lt;TOKEN_JWT&gt;</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>

                        <div class="section-heading-sm mt-3"><i class="bi bi-sliders"></i> Filtros de Query (Opcionais)</div>
                        <div class="api-table-wrapper">
                          <table class="api-table">
                            <thead>
                              <tr>
                                <th style="width: 32%;">Parâmetro</th>
                                <th style="width: 22%;">Tipo</th>
                                <th style="width: 20%;">Padrão</th>
                                <th>Descrição</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td><span class="api-field-pill">data_inicio</span></td>
                                <td class="text-secondary small">date (YYYY-MM-DD)</td>
                                <td class="small">Hoje</td>
                                <td class="small">Escalas a partir desta data</td>
                              </tr>
                              <tr>
                                <td><span class="api-field-pill">data_fim</span></td>
                                <td class="text-secondary small">date (YYYY-MM-DD)</td>
                                <td class="small">Hoje + 30 dias</td>
                                <td class="small">Escalas até esta data</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-shield-lock"></i> Regras de Negócio e Anti-Spam</div>
                        <div class="p-3.5 rounded-3 bg-body-tertiary border" style="line-height: 1.7;">
                          <ul class="mb-0 ps-3 small text-secondary">
                            <li>Voluntário deve ter cadastro <strong>Ativo</strong> e aprovação <strong>APROVADO</strong>.</li>
                            <li>Apenas escalas futuras com status <strong>PENDENTE</strong>.</li>
                            <li>Cultos do dia com horário de término já expirado são descartados.</li>
                            <li><strong class="text-body">Proteção Anti-Spam:</strong> Se o voluntário já recebeu lembrete na data de hoje (<code class="text-body font-monospace">data_ultimo_lembrete = hoje</code>), a API o ignora automaticamente para evitar duplicidade.</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tab Retorno -->
                  <div class="tab-pane fade" id="tab-lemb-res" role="tabpanel">
                    <div class="row g-4">
                      <div class="col-lg-5">
                        <div class="section-heading-sm"><i class="bi bi-file-earmark-check"></i> Campos do Array "voluntarios"</div>
                        <div class="api-table-wrapper" style="max-height: 380px; overflow-y: auto;">
                          <table class="api-table">
                            <thead><tr><th style="width: 45%;">Campo</th><th>Uso no WhatsApp / n8n</th></tr></thead>
                            <tbody>
                              <tr><td><span class="api-field-pill">telefone_whatsapp</span></td><td class="small">Dígitos puros (ex: 11950207384), pronto para o nó de WhatsApp.</td></tr>
                              <tr><td><span class="api-field-pill">mensagem_pronta</span></td><td class="small"><strong>Texto formatado com emojis</strong>, cultos e link individual.</td></tr>
                              <tr><td><span class="api-field-pill">ids_escala</span></td><td class="small">Array com os IDs para baixa posterior no endpoint <code class="text-body font-monospace">/marcar-lembrete</code>.</td></tr>
                              <tr><td><span class="api-field-pill">primeiro_nome</span></td><td class="small">Primeiro nome do voluntário.</td></tr>
                              <tr><td><span class="api-field-pill">cultos</span></td><td class="small">Lista detalhada com datas, horários e áreas de serviço.</td></tr>
                              <tr><td><span class="api-field-pill">link_portal</span></td><td class="small">Link direto para confirmação de presença na agenda.</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-lg-7">
                        <div class="section-heading-sm"><i class="bi bi-check2-circle text-success"></i> Exemplo de Resposta (Status 200 OK)</div>
                        <div class="api-code-window">
                          <div class="api-code-header">
                            <div class="api-code-dots">
                              <span class="api-code-dot dot-red"></span>
                              <span class="api-code-dot dot-yellow"></span>
                              <span class="api-code-dot dot-green"></span>
                            </div>
                            <span class="api-code-title">voluntarios-pendentes.json</span>
                            <button type="button" class="api-code-copy-btn" onclick="copiarValor(document.getElementById('codeResLemb').innerText)">
                              <i class="bi bi-clipboard"></i> Copiar
                            </button>
                          </div>
                          <pre class="api-code-body" style="max-height: 380px;" id="codeResLemb">{
  "status": "success",
  "total_voluntarios_pendentes": 1,
  "voluntarios": [
    {
      "id_voluntario": 28,
      "nome_completo": "Mariana Procopio",
      "primeiro_nome": "Mariana",
      "telefone_whatsapp": "11950207384",
      "departamento": "Comunicação",
      "ids_escala": [141, 143],
      "total_escalas": 2,
      "link_portal": "<?= base_url('portal/agenda') ?>",
      "mensagem_pronta": "ADVEC: Olá, *Mariana*! 🙌 Graça e paz!

Passando para lembrar que você foi escalado(a) no departamento *Comunicação*:

• 📅 *01/11/2026 (09:00)* - Escola da Palavra (Edição)
• 📅 *01/11/2026 (10:20)* - Culto de Celebração (Edição)

Por favor, acesse o link abaixo para CONFIRMAR:..."
    }
  ]
}</pre>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tab cURL -->
                  <div class="tab-pane fade" id="tab-lemb-curl" role="tabpanel">
                    <div class="section-heading-sm"><i class="bi bi-terminal"></i> Execução rápida via Terminal</div>
                    <div class="api-code-window">
                      <div class="api-code-header">
                        <div class="api-code-dots">
                          <span class="api-code-dot dot-red"></span>
                          <span class="api-code-dot dot-yellow"></span>
                          <span class="api-code-dot dot-green"></span>
                        </div>
                        <span class="api-code-title">curl-lembretes.sh</span>
                        <button type="button" class="api-code-copy-btn" onclick="copiarValor(document.getElementById('codeCurlLemb').innerText)">
                          <i class="bi bi-clipboard"></i> Copiar cURL
                        </button>
                      </div>
                      <pre class="api-code-body" id="codeCurlLemb">curl -X GET "<?= base_url('api/v1/escalas/lembretes-pendentes') ?>" \
  -H "Authorization: Bearer &lt;SEU_TOKEN_JWT&gt;"</pre>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- ENDPOINT 3: POST /api/v1/escalas/marcar-lembrete -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingBaixa">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBaixa" aria-expanded="false" aria-controls="collapseBaixa">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100 justify-content-between pe-3">
                  <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success px-3 py-2 fw-bold font-monospace shadow-sm" style="font-size: 0.82rem; letter-spacing: 0.5px;">POST</span>
                    <span class="font-monospace fw-bold text-body" style="font-size: 0.96rem;">/api/v1/escalas/marcar-lembrete</span>
                    <span class="text-secondary small d-none d-lg-inline">— Marcar Lembrete como Enviado (Baixa)</span>
                  </div>
                  <div>
                    <span class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1.5 small">
                      <i class="bi bi-key-fill me-1"></i>Requer Bearer JWT
                    </span>
                  </div>
                </div>
              </button>
            </h2>
            <div id="collapseBaixa" class="accordion-collapse collapse" aria-labelledby="headingBaixa" data-bs-parent="#accordionSwaggerEndpoints">
              <div class="accordion-body">
                
                <!-- Banner Descritivo -->
                <div class="api-info-banner">
                  <i class="bi bi-check2-circle"></i>
                  <div>
                    Após o nó do WhatsApp disparar a mensagem com sucesso, o n8n deve chamar este endpoint passando os IDs das escalas notificados. A API grava <code class="text-body font-monospace">data_ultimo_lembrete = NOW()</code>, assegurando que o voluntário não receba lembretes repetidos no mesmo dia.
                  </div>
                </div>

                <!-- Barra de URL Estilizada -->
                <div class="api-url-bar">
                  <span class="method-badge bg-success text-white">POST</span>
                  <input type="text" class="url-text" readonly value="<?= base_url('api/v1/escalas/marcar-lembrete') ?>" id="epBaixaUrl">
                  <button class="btn-copy" type="button" onclick="copiarTexto('epBaixaUrl')" title="Copiar URL">
                    <i class="bi bi-clipboard"></i> Copiar URL
                  </button>
                </div>

                <!-- Nav Pills Segmented -->
                <ul class="nav api-segmented-pills" id="tabsBaixa" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-baixa-req-btn" data-bs-toggle="pill" data-bs-target="#tab-baixa-req" type="button" role="tab">
                      <i class="bi bi-arrow-up-right-circle"></i> Dados de Envio (Request)
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-baixa-res-btn" data-bs-toggle="pill" data-bs-target="#tab-baixa-res" type="button" role="tab">
                      <i class="bi bi-check-circle-fill text-success"></i> Resposta (200 OK)
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-baixa-curl-btn" data-bs-toggle="pill" data-bs-target="#tab-baixa-curl" type="button" role="tab">
                      <i class="bi bi-terminal"></i> Exemplo cURL / n8n
                    </button>
                  </li>
                </ul>

                <div class="tab-content" id="tabsBaixaContent">
                  <!-- Tab Envio -->
                  <div class="tab-pane fade show active" id="tab-baixa-req" role="tabpanel">
                    <div class="row g-4">
                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-card-heading"></i> Headers Obrigatórios</div>
                        <div class="api-table-wrapper">
                          <table class="api-table">
                            <thead><tr><th style="width: 40%;">Header</th><th>Valor</th></tr></thead>
                            <tbody>
                              <tr>
                                <td><span class="api-field-pill">Authorization</span></td>
                                <td class="font-monospace text-secondary">Bearer &lt;TOKEN_JWT&gt;</td>
                              </tr>
                              <tr>
                                <td><span class="api-field-pill">Content-Type</span></td>
                                <td class="font-monospace text-secondary">application/json</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>

                        <div class="section-heading-sm mt-3"><i class="bi bi-code-square"></i> Parâmetros do Body (JSON)</div>
                        <div class="api-table-wrapper">
                          <table class="api-table">
                            <thead>
                              <tr>
                                <th style="width: 32%;">Campo</th>
                                <th style="width: 22%;">Tipo</th>
                                <th style="width: 18%;">Obrigatório</th>
                                <th>Descrição</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td><span class="api-field-pill">ids_escala</span></td>
                                <td class="text-secondary small">array de inteiros</td>
                                <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">Sim</span></td>
                                <td class="small">IDs das escalas notificadas (direto de <code class="text-body font-monospace">$json.ids_escala</code>).</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-file-earmark-code"></i> Exemplo de Payload (JSON)</div>
                        <div class="api-code-window">
                          <div class="api-code-header">
                            <div class="api-code-dots">
                              <span class="api-code-dot dot-red"></span>
                              <span class="api-code-dot dot-yellow"></span>
                              <span class="api-code-dot dot-green"></span>
                            </div>
                            <span class="api-code-title">request-marcar.json</span>
                            <button type="button" class="api-code-copy-btn" onclick="copiarValor(document.getElementById('codeReqBaixa').innerText)">
                              <i class="bi bi-clipboard"></i> Copiar
                            </button>
                          </div>
                          <pre class="api-code-body" id="codeReqBaixa">{
  "ids_escala": [141, 143]
}</pre>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tab Retorno -->
                  <div class="tab-pane fade" id="tab-baixa-res" role="tabpanel">
                    <div class="row g-4">
                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-file-earmark-check"></i> Campos do Retorno</div>
                        <div class="api-table-wrapper">
                          <table class="api-table">
                            <thead><tr><th style="width: 40%;">Campo</th><th style="width: 22%;">Tipo</th><th>Significado</th></tr></thead>
                            <tbody>
                              <tr><td><span class="api-field-pill">status</span></td><td class="text-secondary small">string</td><td class="small">Retorna <code class="text-body">success</code> quando gravado.</td></tr>
                              <tr><td><span class="api-field-pill">escalas_atualizadas</span></td><td class="text-secondary small">boolean</td><td class="small"><code class="text-body">true</code> indicando que o banco foi atualizado.</td></tr>
                              <tr><td><span class="api-field-pill">message</span></td><td class="text-secondary small">string</td><td class="small">Mensagem descritiva da operação.</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="section-heading-sm"><i class="bi bi-check2-circle text-success"></i> Resposta da API (Status 200 OK)</div>
                        <div class="api-code-window">
                          <div class="api-code-header">
                            <div class="api-code-dots">
                              <span class="api-code-dot dot-red"></span>
                              <span class="api-code-dot dot-yellow"></span>
                              <span class="api-code-dot dot-green"></span>
                            </div>
                            <span class="api-code-title">response-baixa.json</span>
                            <button type="button" class="api-code-copy-btn" onclick="copiarValor(document.getElementById('codeResBaixa').innerText)">
                              <i class="bi bi-clipboard"></i> Copiar
                            </button>
                          </div>
                          <pre class="api-code-body" id="codeResBaixa">{
  "status": "success",
  "escalas_atualizadas": true,
  "message": "Lembrete marcado como enviado com sucesso para 2 escala(s)."
}</pre>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tab cURL -->
                  <div class="tab-pane fade" id="tab-baixa-curl" role="tabpanel">
                    <div class="section-heading-sm"><i class="bi bi-terminal"></i> Execução rápida via Terminal</div>
                    <div class="api-code-window">
                      <div class="api-code-header">
                        <div class="api-code-dots">
                          <span class="api-code-dot dot-red"></span>
                          <span class="api-code-dot dot-yellow"></span>
                          <span class="api-code-dot dot-green"></span>
                        </div>
                        <span class="api-code-title">curl-baixa.sh</span>
                        <button type="button" class="api-code-copy-btn" onclick="copiarValor(document.getElementById('codeCurlBaixa').innerText)">
                          <i class="bi bi-clipboard"></i> Copiar cURL
                        </button>
                      </div>
                      <pre class="api-code-body" id="codeCurlBaixa">curl -X POST "<?= base_url('api/v1/escalas/marcar-lembrete') ?>" \
  -H "Authorization: Bearer &lt;SEU_TOKEN_JWT&gt;" \
  -H "Content-Type: application/json" \
  -d '{"ids_escala":[141, 143]}'</pre>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Barra de Ações & Filtro -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
      <div class="card-body p-3">
        <div class="row g-2 align-items-center justify-content-between">
          <div class="col-12 col-md-6 col-lg-5">
            <form method="get" action="<?= base_url('api-client') ?>">
              <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0">
                  <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por aplicação, Client ID..." value="<?= esc($busca ?? '') ?>">
                <?php if (!empty($busca)) { ?>
                  <a href="<?= base_url('api-client') ?>" class="btn btn-outline-secondary" title="Limpar busca">
                    <i class="bi bi-x-lg"></i>
                  </a>
                <?php } ?>
              </div>
            </form>
          </div>
          <div class="col-12 col-md-6 text-md-end">
            <?php if (!empty($sys_action->create)) { ?>
              <a href="<?= base_url('api-client/novo') ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Aplicação
              </a>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabela de Aplicações Conectadas -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-body py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold mb-0">
          <i class="bi bi-shield-lock-fill text-primary me-2"></i> Aplicações Autorizadas (<?= count($clientes) ?>)
        </h5>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col" class="ps-4">Aplicação</th>
                <th scope="col">Client ID</th>
                <th scope="col">Client Secret</th>
                <th scope="col" class="text-center">Rate Limit</th>
                <th scope="col" class="text-center">Expiração JWT</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col">Uso & Último Acesso</th>
                <th scope="col" class="text-end pe-4" style="width: 140px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($clientes)) { ?>
                <?php foreach ($clientes as $c) { ?>
                  <tr id="row-client-<?= $c->id_api_client ?>">
                    <!-- Aplicação -->
                    <td class="ps-4">
                      <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 text-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #1e40af);">
                          <i class="bi bi-cpu fs-5"></i>
                        </div>
                        <div>
                          <span class="fw-bold text-body fs-6 d-block"><?= esc($c->nome_aplicacao) ?></span>
                          <?php if (!empty($c->descricao)) { ?>
                            <small class="text-muted d-block text-truncate" style="max-width: 240px;" title="<?= esc($c->descricao) ?>"><?= esc($c->descricao) ?></small>
                          <?php } ?>
                        </div>
                      </div>
                    </td>

                    <!-- Client ID -->
                    <td>
                      <div class="d-inline-flex align-items-center gap-1.5 p-1 px-2 rounded-3 bg-body-tertiary border font-monospace small">
                        <span id="cid-text-<?= $c->id_api_client ?>"><?= esc($c->client_id) ?></span>
                        <button type="button" class="btn btn-sm btn-link p-0 text-muted" onclick="copiarValor('<?= esc($c->client_id) ?>')" title="Copiar Client ID">
                          <i class="bi bi-clipboard"></i>
                        </button>
                      </div>
                    </td>

                    <!-- Client Secret -->
                    <td>
                      <div class="d-inline-flex align-items-center gap-1.5 p-1 px-2 rounded-3 bg-body-tertiary border font-monospace small">
                        <span id="secret-masked-<?= $c->id_api_client ?>" class="text-muted">••••••••••••••••••••</span>
                        <span id="secret-unmasked-<?= $c->id_api_client ?>" class="d-none text-body fw-bold"><?= esc($c->client_secret) ?></span>
                        <button type="button" class="btn btn-sm btn-link p-0 text-muted" onclick="toggleMascaraSecret(<?= $c->id_api_client ?>)" id="btn-eye-<?= $c->id_api_client ?>" title="Mostrar/Ocultar Segredo">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-link p-0 text-muted" onclick="copiarValor('<?= esc($c->client_secret) ?>')" title="Copiar Secret">
                          <i class="bi bi-clipboard"></i>
                        </button>
                      </div>
                    </td>

                    <!-- Rate Limit -->
                    <td class="text-center">
                      <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2.5 py-1 small">
                        <i class="bi bi-speedometer2 me-1"></i><?= $c->limite_requisicoes_minuto ?> req/min
                      </span>
                    </td>

                    <!-- Expiração JWT -->
                    <td class="text-center">
                      <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 small">
                        <i class="bi bi-clock-history me-1 text-primary"></i><?= round($c->tempo_expiracao / 60) ?> min
                      </span>
                    </td>

                    <!-- Status -->
                    <td class="text-center" id="cell-status-<?= $c->id_api_client ?>">
                      <?php if ((int)$c->status === 1) { ?>
                        <button type="button" class="btn badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold" title="Clique para desativar" onclick="alternarStatusCliente(<?= $c->id_api_client ?>)">
                          <i class="bi bi-check-circle-fill text-success me-1"></i> Ativo
                        </button>
                      <?php } else { ?>
                        <button type="button" class="btn badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2.5 py-1 small fw-semibold" title="Clique para ativar" onclick="alternarStatusCliente(<?= $c->id_api_client ?>)">
                          <i class="bi bi-slash-circle-fill text-danger me-1"></i> Inativo
                        </button>
                      <?php } ?>
                    </td>

                    <!-- Uso & Último Acesso -->
                    <td>
                      <small class="d-block text-body fw-semibold">
                        <i class="bi bi-arrow-repeat text-primary me-1"></i><?= number_format((int)$c->total_requisicoes, 0, ',', '.') ?> requisições
                      </small>
                      <small class="text-muted" style="font-size: 0.72rem;">
                        Último acesso: <?= !empty($c->ultimo_acesso) ? date('d/m/Y H:i', strtotime($c->ultimo_acesso)) : 'Nunca utilizado' ?>
                      </small>
                    </td>

                    <!-- Ações -->
                    <td class="text-end pe-4">
                      <div class="d-inline-flex gap-1">
                        <!-- Rotacionar Secret -->
                        <?php if (!empty($sys_action->update)) { ?>
                          <button type="button" class="btn btn-outline-warning btn-action" title="Rotacionar Chaves (Gerar Novo Secret)" onclick="confirmarRotacaoChaves(<?= $c->id_api_client ?>, '<?= esc(addslashes($c->nome_aplicacao)) ?>')">
                            <i class="bi bi-arrow-clockwise"></i>
                          </button>
                          <a href="<?= base_url('api-client/editar/' . $c->id_api_client) ?>" class="btn btn-outline-primary btn-action" title="Editar Configurações">
                            <i class="bi bi-pencil-fill"></i>
                          </a>
                        <?php } ?>

                        <?php if (!empty($sys_action->delete)) { ?>
                          <button type="button" class="btn btn-outline-danger btn-action" title="Excluir Aplicação" onclick="confirmarExclusaoCliente(<?= $c->id_api_client ?>, '<?= esc(addslashes($c->nome_aplicacao)) ?>')">
                            <i class="bi bi-trash3-fill"></i>
                          </button>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-cpu fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-2 fw-semibold">Nenhuma aplicação de API cadastrada.</p>
                    <a href="<?= base_url('api-client/novo') ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                      <i class="bi bi-plus-lg me-1"></i> Criar primeira aplicação
                    </a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal Rotacionar Chaves -->
<div class="modal fade" id="modalRotacionarChaves" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-warning-emphasis">
          <i class="bi bi-shield-exclamation text-warning me-2"></i> Rotacionar Credenciais
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body py-3">
        <input type="hidden" id="rotacionar_id_cliente">
        <p class="mb-2">Deseja realmente gerar um novo <strong>Client Secret</strong> para a aplicação <strong id="rotacionar_nome_cliente"></strong>?</p>
        <div class="alert alert-warning border-0 rounded-3 small p-3 mb-0">
          <i class="bi bi-exclamation-triangle-fill me-1"></i>
          O segredo atual deixará de funcionar imediatamente. Você precisará atualizar a credencial correspondente no seu <strong>n8n</strong>.
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold" id="btnConfirmarRotacao" onclick="executarRotacaoChaves()">
          <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerRotacao"></span>
          <i class="bi bi-arrow-clockwise me-1"></i> Gerar Novo Segredo
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Exclusão -->
<div class="modal fade" id="modalConfirmarExclusaoCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Exclusão
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body py-3">
        <p class="mb-0">Deseja realmente revogar e excluir a aplicação <strong id="modalNomeCliente"></strong>?</p>
        <small class="text-danger d-block mt-2">
          <i class="bi bi-info-circle me-1"></i> Qualquer automação vinculada a este Client ID deixará de ter acesso à API.
        </small>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnConfirmarDeleteCliente" class="btn btn-danger rounded-pill px-4 fw-bold">
          <i class="bi bi-trash3-fill me-1"></i> Excluir Definitivamente
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  let modalRotacaoInstance = null;
  let modalExclusaoInstance = null;

  function toggleMascaraSecret(id) {
    const masked = document.getElementById('secret-masked-' + id);
    const unmasked = document.getElementById('secret-unmasked-' + id);
    const btn = document.getElementById('btn-eye-' + id);

    if (masked.classList.contains('d-none')) {
      masked.classList.remove('d-none');
      unmasked.classList.add('d-none');
      btn.innerHTML = '<i class="bi bi-eye"></i>';
    } else {
      masked.classList.add('d-none');
      unmasked.classList.remove('d-none');
      btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
    }
  }

  function copiarTexto(elementId, btnElement = null) {
    const input = document.getElementById(elementId);
    if (!input) return;
    copiarValor(input.value, btnElement);
  }

  function copiarValor(texto, btnElement = null) {
    navigator.clipboard.writeText(texto).then(() => {
      if (btnElement && btnElement instanceof HTMLElement) {
        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="bi bi-check2 text-success"></i> Copiado!';
        setTimeout(() => { btnElement.innerHTML = originalHtml; }, 1800);
      }
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show('success', 'Copiado!', 'Valor copiado para a área de transferência.');
      }
    });
  }

  function alternarStatusCliente(id) {
    const formData = new FormData();
    formData.append('id_api_client', id);

    fetch('<?= base_url('api-client/toggleStatus') ?>', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          const cell = document.getElementById('cell-status-' + id);
          if (cell) {
            if (data.novo_status === 1) {
              cell.innerHTML = `
              <button type="button" class="btn badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold" title="Clique para desativar" onclick="alternarStatusCliente(${id})">
                <i class="bi bi-check-circle-fill text-success me-1"></i> Ativo
              </button>
            `;
            } else {
              cell.innerHTML = `
              <button type="button" class="btn badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2.5 py-1 small fw-semibold" title="Clique para ativar" onclick="alternarStatusCliente(${id})">
                <i class="bi bi-slash-circle-fill text-danger me-1"></i> Inativo
              </button>
            `;
            }
          }
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Status Alterado', data.message);
          }
        }
      });
  }

  function confirmarRotacaoChaves(id, nome) {
    document.getElementById('rotacionar_id_cliente').value = id;
    document.getElementById('rotacionar_nome_cliente').textContent = nome;
    if (!modalRotacaoInstance) {
      modalRotacaoInstance = new bootstrap.Modal(document.getElementById('modalRotacionarChaves'));
    }
    modalRotacaoInstance.show();
  }

  function executarRotacaoChaves() {
    const id = document.getElementById('rotacionar_id_cliente').value;
    const btn = document.getElementById('btnConfirmarRotacao');
    const spinner = document.getElementById('spinnerRotacao');

    btn.disabled = true;
    spinner.classList.remove('d-none');

    const formData = new FormData();
    formData.append('id_api_client', id);

    fetch('<?= base_url('api-client/rotacionarSecret') ?>', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        spinner.classList.add('d-none');

        if (data.status === 'success') {
          modalRotacaoInstance.hide();
          document.getElementById('secret-unmasked-' + id).textContent = data.client_secret;
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Chaves Rotacionadas', data.message);
          } else {
            alert(data.message);
          }
        }
      })
      .catch(err => {
        btn.disabled = false;
        spinner.classList.add('d-none');
      });
  }

  function confirmarExclusaoCliente(id, nome) {
    document.getElementById('modalNomeCliente').textContent = nome;
    document.getElementById('btnConfirmarDeleteCliente').href = '<?= base_url('api-client/apagar/') ?>/' + id;
    if (!modalExclusaoInstance) {
      modalExclusaoInstance = new bootstrap.Modal(document.getElementById('modalConfirmarExclusaoCliente'));
    }
    modalExclusaoInstance.show();
  }
</script>