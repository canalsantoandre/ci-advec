<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

  <!-- PWA & Mobile Fullscreen Settings -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ADVEC Voluntários">
  <meta name="theme-color" content="#0f172a">
  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <link rel="apple-touch-icon" href="<?= base_url('logo-advec.png') ?>">

  <!-- Anti-flicker Theme Init -->
  <script>
    (function() {
      const t = localStorage.getItem('portal_theme') || localStorage.getItem('theme');
      let resolved = t;
      if (!t || t === 'auto') {
        resolved = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      }
      document.documentElement.setAttribute('data-bs-theme', resolved);
    })();
  </script>

  <title>Seja Bem-vindo à Equipe - ADVEC</title>

  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- jQuery & Mask -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

  <!-- Framework US Toast Banner -->
  <link rel="stylesheet" href="<?= base_url('framework/us/toast-banner/toast-banner.css') ?>">
  <script src="<?= base_url('framework/us/toast-banner/toast-banner.js') ?>"></script>

  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
      background: #f1f5f9;
      min-height: 100dvh;
      display: flex;
      flex-direction: column;
    }

    [data-bs-theme="dark"] body {
      background-color: #0b1329;
      color: #e2e8f0;
    }

    .max-w-onboarding {
      max-width: 540px;
      margin: 0 auto;
      width: 100%;
    }

    /* Hero Header Premium */
    .hero-invite-card {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1e3a8a 100%);
      color: #ffffff;
      border-radius: 1.5rem;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.35);
    }

    .hero-invite-card::before {
      content: '';
      position: absolute;
      top: -30%;
      right: -10%;
      width: 250px;
      height: 250px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(37, 99, 235, 0) 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .step-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: #2563eb;
      color: #fff;
      font-weight: 700;
      font-size: 0.85rem;
    }

    /* Avatar Upload */
    .avatar-wrapper {
      position: relative;
      width: 110px;
      height: 110px;
      margin: 0 auto;
    }

    .avatar-img {
      width: 110px;
      height: 110px;
      object-fit: cover;
      border-radius: 50%;
      border: 3.5px solid #3b82f6;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 15px rgba(59, 130, 246, 0.25);
      background-color: #1e293b;
    }

    .avatar-edit-badge {
      position: absolute;
      bottom: 2px;
      right: 2px;
      background: #2563eb;
      color: #fff;
      border-radius: 50%;
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border: 2px solid #ffffff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .avatar-edit-badge:hover {
      transform: scale(1.15);
      background: #1d4ed8;
    }

    /* Card Section */
    .onboarding-card {
      border-radius: 1.25rem;
      border: 1px solid rgba(226, 232, 240, 0.8);
      background: #ffffff;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] .onboarding-card {
      background-color: #1e293b;
      border-color: #334155;
    }

    /* Form Floating */
    .form-floating>label {
      padding-left: 1rem;
      color: #64748b;
      font-weight: 500;
      font-size: 0.88rem;
    }

    .form-floating>.form-control,
    .form-floating>.form-select {
      border-radius: 0.85rem;
      font-size: 0.95rem;
      border-color: #cbd5e1;
    }

    .form-floating>.form-control:focus,
    .form-floating>.form-select:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
    }

    /* Inputs no Dark Mode com Fonte Preta Obrigatória */
    [data-bs-theme="dark"] .form-floating>.form-control,
    [data-bs-theme="dark"] .form-floating>.form-select,
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] input,
    [data-bs-theme="dark"] select,
    [data-bs-theme="dark"] textarea {
      background-color: #ffffff !important;
      border-color: #cbd5e1 !important;
      color: #000000 !important;
      font-weight: 500;
    }

    [data-bs-theme="dark"] .form-floating>label {
      color: #475569 !important;
    }

    [data-bs-theme="dark"] .form-control::placeholder,
    [data-bs-theme="dark"] input::placeholder {
      color: #94a3b8 !important;
      opacity: 0.8 !important;
    }

    /* OTP Inputs */
    .otp-input-code {
      font-size: 1.75rem;
      letter-spacing: 0.5rem;
      text-align: center;
      font-weight: 800;
      font-family: monospace;
      border-radius: 1rem;
    }

    /* Social Dock */
    .social-dock-container {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.55rem;
    }

    .social-dock-item {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.35rem 0.75rem;
      border-radius: 9999px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      font-size: 0.8rem;
      font-weight: 600;
    }

    [data-bs-theme="dark"] .social-dock-item {
      background: #0f172a;
      border-color: #334155;
    }

    .social-dock-remove {
      background: transparent;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      padding: 0;
    }

    @keyframes pulseInfo {
      0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
      }

      70% {
        transform: scale(1.08);
        box-shadow: 0 0 0 6px rgba(59, 130, 246, 0);
      }

      100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
      }
    }

    .pulse-info-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #2563eb;
      color: #ffffff;
      animation: pulseInfo 2s infinite;
    }

    /* Sub-áreas Multi-select Cards */
    .subarea-onboarding-card {
      border-radius: 0.95rem;
      border: 1.5px solid rgba(203, 213, 225, 0.8);
      background: #f8fafc;
      padding: 0.75rem 1rem;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      user-select: none;
    }

    .subarea-onboarding-card:hover {
      transform: translateY(-2px);
      border-color: #93c5fd;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }

    .subarea-onboarding-card.subarea-selected {
      background: rgba(37, 99, 235, 0.08) !important;
      border-color: #2563eb !important;
      box-shadow: 0 4px 14px rgba(37, 99, 235, 0.15) !important;
    }

    [data-bs-theme="dark"] .subarea-onboarding-card {
      background: #1e293b;
      border-color: #334155;
    }

    [data-bs-theme="dark"] .subarea-onboarding-card.subarea-selected {
      background: rgba(37, 99, 235, 0.2) !important;
      border-color: #3b82f6 !important;
    }

    /* Collapse Chevrons */
    .collapse-chevron {
      transition: transform 0.25s ease-in-out;
    }

    .collapse-header-btn[aria-expanded="true"] .collapse-chevron {
      transform: rotate(180deg);
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

  <div class="container max-w-onboarding py-4 px-3">

    <!-- Header / Brand -->
    <div class="text-center mb-3">
      <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-2" style="background: rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.2);">
        <i class="bi bi-cross text-primary"></i>
        <span class="fw-bold text-primary small" style="letter-spacing: 0.5px;">ADVEC &bull; PORTAL DO VOLUNTÁRIO</span>
      </div>
    </div>

    <!-- Hero Card do Convite -->
    <div class="hero-invite-card p-4 mb-4 text-center">
      <span class="badge rounded-pill px-3 py-1 mb-2 fw-bold text-uppercase" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); font-size: 0.72rem; letter-spacing: 0.5px;">
        <i class="bi bi-envelope-open-heart-fill me-1 text-warning"></i> Convite de Voluntário
      </span>

      <h3 class="fw-bold text-white mb-1" style="font-size: 1.45rem;">
        <?= esc($convite->nome_departamento) ?>
      </h3>
      <p class="text-white-50 small mb-0">
        Você foi convidado para servir e fazer a diferença na equipe.
      </p>
    </div>

    <!-- Container do Formulário / Wizard Step-by-Step -->
    <div class="onboarding-card p-4">

      <!-- ========================================== -->
      <!-- ETAPA 1: VALIDAÇÃO DE WHATSAPP VIA OTP    -->
      <!-- ========================================== -->
      <div id="etapaOtp" class="<?= (!empty($challenge['validado'])) ? 'd-none' : '' ?>">
        <div class="d-flex align-items-center gap-2 mb-3">
          <span class="step-badge">1</span>
          <h5 class="fw-bold mb-0 text-body">Validação do WhatsApp</h5>
        </div>
        <p class="text-secondary small mb-3">
          Para garantir sua segurança e vincular seu acesso oficial ao portal, confirme seu número de WhatsApp.
        </p>

        <!-- Form Solicitar OTP -->
        <div id="blocoSolicitarOtp">
          <div class="form-floating mb-3">
            <?php
            $isDireto = ($convite->tipo === 'DIRETO' && !empty($convite->telefone));
            $fonePrefill = $isDireto ? $convite->telefone : ($challenge['telefone'] ?? '');
            ?>
            <input type="tel" id="inputTelefoneOtp" class="form-control" placeholder="(11) 99999-9999" value="<?= esc($fonePrefill) ?>" <?= $isDireto ? 'readonly' : '' ?> autocomplete="tel">
            <label for="inputTelefoneOtp">
              <i class="bi bi-whatsapp text-success me-1"></i> Número de WhatsApp *
            </label>
            <?php if ($isDireto) { ?>
              <small class="text-muted ps-2" style="font-size: 0.72rem;">
                <i class="bi bi-lock-fill me-1"></i> Convite direto vinculado ao número acima.
              </small>
            <?php } ?>
          </div>

          <button type="button" id="btnEnviarOtp" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-sm" onclick="solicitarCodigoOtp()">
            <i class="bi bi-send-fill me-1"></i> Enviar Código por WhatsApp
          </button>
        </div>

        <!-- Form Confirmar OTP (Oculto até disparar) -->
        <div id="blocoConfirmarOtp" class="d-none mt-3 pt-3 border-top">
          <div class="alert alert-info border-0 rounded-3 p-3 small mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-chat-left-dots-fill fs-5 text-info"></i>
            <div>
              Enviamos um código de 6 dígitos para <strong id="txtTelefoneDestino"></strong> via WhatsApp.
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-center w-100 mb-2">Digite o código de 6 dígitos:</label>
            <input type="text" id="inputCodigoOtp" class="form-control otp-input-code" maxlength="6" placeholder="000000" autocomplete="one-time-code" onkeyup="if(this.value.length === 6) validarCodigoOtp();">
          </div>

          <button type="button" id="btnValidarOtp" class="btn btn-success w-100 rounded-pill py-2.5 fw-bold shadow-sm mb-2" onclick="validarCodigoOtp()">
            <i class="bi bi-check-circle-fill me-1"></i> Confirmar Código
          </button>

          <div class="text-center mt-2">
            <button type="button" id="btnReenviarOtp" class="btn btn-link btn-sm text-decoration-none text-muted" onclick="solicitarCodigoOtp()" disabled>
              Reenviar código em <span id="timerReenvio">60</span>s
            </button>
          </div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- ETAPA 2: FORMULÁRIO DE CADASTRO COMPLETO   -->
      <!-- ========================================== -->
      <div id="etapaCadastro" class="<?= (empty($challenge['validado'])) ? 'd-none' : '' ?>">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
          <div class="d-flex align-items-center gap-2">
            <span class="step-badge bg-success"><i class="bi bi-check-lg"></i></span>
            <h5 class="fw-bold mb-0 text-body">Seus Dados</h5>
          </div>
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
            <i class="bi bi-shield-check me-1"></i> WhatsApp Verificado
          </span>
        </div>

        <form id="formCadastroVoluntario" onsubmit="submeterCadastro(event)" enctype="multipart/form-data">
          <input type="hidden" name="token" value="<?= esc($token) ?>">

          <!-- Foto de Perfil com Preview -->
          <div class="text-center mb-3">
            <div class="avatar-wrapper mb-2 position-relative">
              <img src="https://ui-avatars.com/api/?name=Novo+Voluntario&background=2563eb&color=fff&size=150" id="avatarPreviewOnboarding" class="avatar-img" alt="Foto">
              <label for="foto_file_input" class="avatar-edit-badge" title="Tirar foto ou escolher da galeria">
                <i class="bi bi-camera-fill fs-6"></i>
              </label>
              <input type="file" id="foto_file_input" name="foto_file" class="d-none" accept="image/*" onchange="previewFotoLocal(this)">
            </div>
            <small class="text-muted d-block" style="font-size: 0.72rem;">Toque no ícone da câmera para enviar sua foto</small>
          </div>

          <!-- Nome Completo -->
          <div class="form-floating mb-3">
            <input type="text" id="nome" name="nome" class="form-control" placeholder="Seu nome completo" required autocomplete="name" oninput="atualizarAvatarPreview(this.value)">
            <label for="nome"><i class="bi bi-person-fill text-primary me-1"></i> Nome Completo *</label>
          </div>

          <!-- Apelido / Como quer ser chamado -->
          <div class="form-floating mb-3">
            <input type="text" id="nickname" name="nickname" class="form-control" placeholder="Como quer ser chamado">
            <label for="nickname"><i class="bi bi-tag-fill text-warning me-1"></i> Apelido / Como quer ser chamado</label>
          </div>

          <!-- Data de Nascimento -->
          <div class="form-floating mb-3">
            <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" placeholder="Data de Nascimento" required>
            <label for="data_nascimento"><i class="bi bi-cake2 text-info me-1"></i> Data de Nascimento *</label>
          </div>

          <!-- Sub-áreas de Atuação (Múltipla Seleção) -->
          <div class="card border border-body-secondary rounded-4 shadow-sm mb-3 overflow-hidden" id="containerSubareasOnboarding">
            <div class="p-3 bg-body-tertiary border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-diagram-3-fill text-primary fs-5"></i>
                  <strong class="text-body fs-6">Sub-áreas de Atuação *</strong>
                </div>
                <small class="text-muted d-block mt-0.5" style="font-size: 0.76rem;">
                  Selecione uma ou mais áreas em que você deseja servir:
                </small>
              </div>
              <div class="d-flex align-items-center gap-1.5">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold" id="badgeTotalSubareas">
                  <i class="bi bi-check2-all me-1"></i> 0 selecionada(s)
                </span>
              </div>
            </div>

            <div class="p-3">
              <?php if (!empty($subareas)) { ?>
                <div class="row g-2">
                  <?php foreach ($subareas as $area) { ?>
                    <div class="col-12 col-md-6">
                      <label class="subarea-onboarding-card d-flex align-items-start gap-2.5 m-0 w-100 h-100" for="subarea_<?= $area->id_area ?>">
                        <input type="checkbox"
                          name="id_area[]"
                          id="subarea_<?= $area->id_area ?>"
                          value="<?= $area->id_area ?>"
                          class="form-check-input subarea-checkbox mt-1 flex-shrink-0"
                          onchange="aoAlternarSubarea(this)">
                        <div class="flex-grow-1">
                          <strong class="d-block text-body font-semibold" style="font-size: 0.92rem;">&nbsp;&nbsp;
                            <?= esc($area->nome_area) ?>
                          </strong>
                          <?php if (!empty($area->descricao)) { ?>
                            <small class="text-muted d-block mt-0.5" style="font-size: 0.74rem; line-height: 1.25;">
                              <?= esc($area->descricao) ?>
                            </small>
                          <?php } ?>
                        </div>
                      </label>
                    </div>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="text-center py-3 text-muted small">
                  <i class="bi bi-info-circle me-1"></i> Nenhuma sub-área cadastrada para este departamento.
                </div>
              <?php } ?>
            </div>
          </div>

          <!-- E-mail Opcional -->
          <div class="form-floating mb-3">
            <input type="email" id="email" name="email" class="form-control" placeholder="nome@exemplo.com" autocomplete="email">
            <label for="email"><i class="bi bi-envelope text-muted me-1"></i> E-mail (Opcional)</label>
          </div>

          <!-- Disponibilidade de Cultos (Collapsible) -->
          <div class="card border border-body-secondary rounded-4 shadow-sm mb-3 overflow-hidden" id="cardDisponibilidadeOnboarding">
            <button type="button" class="btn w-100 p-3 text-start d-flex justify-content-between align-items-center collapse-header-btn border-0 bg-body-tertiary" data-bs-toggle="collapse" data-bs-target="#collapseDisponibilidadeOnboarding" aria-expanded="true" aria-controls="collapseDisponibilidadeOnboarding">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar2-week-fill text-warning fs-5"></i>
                <span class="fw-bold fs-6 text-body">Disponibilidade de Cultos *</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-bold" id="badgeTotalCultosOnboarding">
                  <i class="bi bi-check2-all me-1"></i> 0 selecionado(s)
                </span>
                <i class="bi bi-chevron-down fs-6 text-muted collapse-chevron"></i>
              </div>
            </button>

            <div class="collapse show" id="collapseDisponibilidadeOnboarding">
              <div class="p-3 pt-2">
                <!-- Banner Regras de Disponibilidade -->
                <div class="alert alert-primary-subtle border border-primary-subtle rounded-4 p-3 mb-3 shadow-xs">
                  <div class="d-flex align-items-center justify-content-between" role="button" data-bs-toggle="collapse" data-bs-target="#bodyRegrasDisponibilidade" aria-expanded="false" aria-controls="bodyRegrasDisponibilidade" style="cursor: pointer;">
                    <div class="d-flex align-items-center gap-2">
                      <span class="pulse-info-btn">
                        <i class="bi bi-info-lg fw-bold" style="font-size: 0.8rem;"></i>
                      </span>
                      <strong class="text-body small fw-bold">Selecione os cultos em que você pode servir</strong>
                    </div>
                  </div>

                  <div class="collapse show" id="bodyRegrasDisponibilidade">
                    <div class="small text-secondary pt-2 mt-2 border-top border-primary-subtle">
                      <p class="mb-0">&bull; Marque <strong>ao menos um dia/culto</strong> de sua preferência para que a liderança possa montar sua escala.</p>
                    </div>
                  </div>
                </div>

                <!-- Ações Rápidas de Marcar Todos / Limpar -->
                <div class="d-flex justify-content-end align-items-center mb-3 gap-1.5">
                  <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 py-1 small" onclick="marcarTodosCultosOnboarding(true)">
                    <i class="bi bi-check-all me-1"></i> Todos
                  </button>
                  <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 py-1 small" onclick="marcarTodosCultosOnboarding(false)">
                    <i class="bi bi-x-circle me-1"></i> Limpar
                  </button>
                </div>

                <!-- Lista de Cultos Padrão em Grid Responsivo -->
                <?php if (!empty($cultosPadrao)) { ?>
                  <div class="row g-2">
                    <?php
                    $diasNomes = [
                      0 => 'Domingo',
                      1 => 'Segunda-feira',
                      2 => 'Terça-feira',
                      3 => 'Quarta-feira',
                      4 => 'Quinta-feira',
                      5 => 'Sexta-feira',
                      6 => 'Sábado'
                    ];
                    foreach ($cultosPadrao as $cp) {
                      $nomeDia = $diasNomes[(int)$cp->dia_semana] ?? 'Culto';
                      $horaInicio = substr($cp->horario_inicio, 0, 5);
                      $horaTermino = substr($cp->horario_termino, 0, 5);
                      $corEvento = !empty($cp->cor_evento) ? $cp->cor_evento : '#2563eb';
                      $labelFormatado = "{$nomeDia} - {$horaInicio} - {$cp->nome_culto}";
                    ?>
                      <div class="col-12">
                        <label class="d-flex align-items-center gap-3 p-3 rounded-3 border user-select-none h-100 culto-onboarding-card bg-body-tertiary" for="onboarding_culto_check_<?= $cp->id_culto_padrao ?>" style="cursor: pointer; transition: all 0.15s ease; border-left: 4px solid <?= esc($corEvento) ?> !important;">
                          <input class="form-check-input mt-0 flex-shrink-0 culto-onboarding-checkbox" type="checkbox" name="cultos[]" value="<?= $cp->id_culto_padrao ?>" id="onboarding_culto_check_<?= $cp->id_culto_padrao ?>" style="cursor: pointer; width: 1.25rem; height: 1.25rem;" onchange="aoAlternarCulto(this)">
                          <div class="flex-grow-1">
                            <div class="fw-bold text-body small"><?= esc($labelFormatado) ?></div>
                            <div class="text-muted small d-flex align-items-center gap-2 mt-1" style="font-size: 0.72rem;">
                              <span><i class="bi bi-clock me-1"></i><?= $horaInicio ?> às <?= $horaTermino ?></span>
                              <?php if (!empty($cp->descricao)) { ?>
                                <span>&bull;</span>
                                <span class="text-truncate" style="max-width: 150px;"><?= esc($cp->descricao) ?></span>
                              <?php } ?>
                            </div>
                          </div>
                        </label>
                      </div>
                    <?php } ?>
                  </div>
                <?php } else { ?>
                  <div class="text-center text-muted small py-3">
                    Nenhum culto padrão cadastrado.
                  </div>
                <?php } ?>

              </div>
            </div>
          </div>

          <!-- Redes Sociais Opcionais -->
          <div class="p-3 bg-body-tertiary rounded-4 border mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label small fw-bold mb-0 text-body">
                <i class="bi bi-instagram text-danger me-1"></i> Redes Sociais (Opcional)
              </label>
              <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-0.5" onclick="adicionarRedeSocialModal()" style="font-size: 0.75rem;">
                <i class="bi bi-plus-lg me-1"></i> Adicionar
              </button>
            </div>
            <div id="containerRedesOnboarding" class="social-dock-container">
              <span class="text-muted small" id="msgSemRedes" style="font-size: 0.75rem;">Nenhuma rede cadastrada.</span>
            </div>
          </div>

          <!-- Botão Submeter -->
          <button type="submit" id="btnFinalizarCadastro" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg fs-6">
            <i class="bi bi-send-check-fill me-2"></i> Concluir e Enviar para Aprovação
          </button>
        </form>
      </div>

    </div>

    <!-- Rodapé -->
    <div class="text-center text-muted small mt-4">
      &copy; <?= date('Y') ?> ADVEC. Todos os direitos reservados.
    </div>

  </div>

  <!-- ======================================================== -->
  <!-- MODAL ADICIONAR REDE SOCIAL (SYSTEM DESIGN)              -->
  <!-- ======================================================== -->
  <div class="modal fade" id="modalAdicionarRedeSocial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 shadow-lg border-0">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold text-body">
            <i class="bi bi-share-fill text-primary me-2"></i> Adicionar Rede Social
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body py-3">
          <div class="mb-3">
            <label class="form-label small fw-bold text-body" for="modalRedePlataforma">Plataforma / Rede *</label>
            <select id="modalRedePlataforma" class="form-select rounded-3">
              <option value="Instagram" selected>Instagram</option>
              <option value="TikTok">TikTok</option>
              <option value="LinkedIn">LinkedIn</option>
              <option value="YouTube">YouTube</option>
              <option value="Facebook">Facebook</option>
              <option value="Threads">Threads</option>
              <option value="X (Twitter)">X (Twitter)</option>
              <option value="Outra">Outra</option>
            </select>
          </div>
          <div class="form-floating mb-1">
            <input type="text" id="modalRedeNickname" class="form-control" placeholder="@seunome ou link">
            <label for="modalRedeNickname">@nickname ou link do perfil *</label>
          </div>
          <small class="text-muted" style="font-size: 0.72rem;">Ex: @pastorjoao ou https://instagram.com/seunome</small>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="confirmarAdicionarRedeSocial()">
            <i class="bi bi-plus-lg me-1"></i> Adicionar
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const tokenConvite = '<?= esc($token) ?>';
    let timerInterval = null;

    $(document).ready(function() {
      $('#inputTelefoneOtp').mask('(00) 00000-0000');
    });

    function solicitarCodigoOtp() {
      const fone = $('#inputTelefoneOtp').val().trim();
      if (!fone) {
        usShowToast('warning', 'Atenção', 'Por favor, informe seu número de WhatsApp.');
        return;
      }

      const btn = document.getElementById('btnEnviarOtp');
      btn.disabled = true;
      btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Enviando código...';

      $.post('<?= base_url('convite/solicitar-otp') ?>', {
        token: tokenConvite,
        telefone: fone
      }, function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Enviar Código por WhatsApp';

        if (res.status === 'success') {
          $('#txtTelefoneDestino').text(fone);
          $('#blocoConfirmarOtp').removeClass('d-none');
          $('#inputCodigoOtp').focus();
          iniciarTimerReenvio();
          usShowToast('success', 'Código Enviado', 'Código de verificação enviado para seu WhatsApp.');
        } else {
          usShowToast('error', 'Atenção', res.message || 'Erro ao enviar código.');
        }
      }).fail(function(xhr) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Enviar Código por WhatsApp';
        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro na comunicação com o servidor.';
        usShowToast('error', 'Erro', msg);
      });
    }

    function iniciarTimerReenvio() {
      let segundos = 60;
      $('#btnReenviarOtp').prop('disabled', true);
      $('#timerReenvio').text(segundos);

      if (timerInterval) clearInterval(timerInterval);

      timerInterval = setInterval(function() {
        segundos--;
        $('#timerReenvio').text(segundos);
        if (segundos <= 0) {
          clearInterval(timerInterval);
          $('#btnReenviarOtp').prop('disabled', false).text('Reenviar código via WhatsApp');
        }
      }, 1000);
    }

    function validarCodigoOtp() {
      const otp = $('#inputCodigoOtp').val().trim();
      if (otp.length < 6) {
        return;
      }

      const btn = document.getElementById('btnValidarOtp');
      btn.disabled = true;
      btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Validando...';

      $.post('<?= base_url('convite/validar-otp') ?>', {
        token: tokenConvite,
        otp: otp
      }, function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Confirmar Código';

        if (res.status === 'success') {
          $('#etapaOtp').addClass('d-none');
          $('#etapaCadastro').removeClass('d-none');
          $('#nome').focus();
          usShowToast('success', 'Autenticado', 'Telefone confirmado com sucesso!');
        } else {
          usShowToast('error', 'Código Inválido', res.message || 'Código inválido.');
        }
      }).fail(function(xhr) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Confirmar Código';
        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Código incorreto ou expirado.';
        usShowToast('error', 'Erro', msg);
      });
    }

    function previewFotoLocal(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#avatarPreviewOnboarding').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function atualizarAvatarPreview(nome) {
      const fileInput = document.getElementById('foto_file_input');
      if (!fileInput.files || fileInput.files.length === 0) {
        const cleanName = encodeURIComponent(nome.trim() || 'Novo Voluntario');
        $('#avatarPreviewOnboarding').attr('src', 'https://ui-avatars.com/api/?name=' + cleanName + '&background=2563eb&color=fff&size=150');
      }
    }

    let modalRedeSocialInstance = null;

    function adicionarRedeSocialModal() {
      $('#modalRedeNickname').val('');
      if (!modalRedeSocialInstance) {
        modalRedeSocialInstance = new bootstrap.Modal(document.getElementById('modalAdicionarRedeSocial'));
      }
      modalRedeSocialInstance.show();
      setTimeout(function() {
        $('#modalRedeNickname').focus();
      }, 350);
    }

    function getIconeRedeSocial(plat) {
      const p = (plat || '').toString().toLowerCase();
      if (p.includes('instagram')) return 'bi bi-instagram text-danger';
      if (p.includes('tiktok')) return 'bi bi-tiktok text-body';
      if (p.includes('linkedin')) return 'bi bi-linkedin text-primary';
      if (p.includes('youtube')) return 'bi bi-youtube text-danger';
      if (p.includes('facebook')) return 'bi bi-facebook text-primary';
      if (p.includes('threads')) return 'bi bi-threads text-body';
      if (p.includes('twitter') || p.includes('x')) return 'bi bi-twitter-x text-body';
      return 'bi bi-link-45deg text-secondary';
    }

    function confirmarAdicionarRedeSocial() {
      const plat = $('#modalRedePlataforma').val();
      let nick = $('#modalRedeNickname').val().trim();

      if (!nick) {
        usShowToast('warning', 'Atenção', 'Por favor, informe seu @nickname ou link do perfil.');
        $('#modalRedeNickname').focus();
        return;
      }

      if (!nick.startsWith('@') && !nick.startsWith('http://') && !nick.startsWith('https://')) {
        nick = '@' + nick;
      }

      const iconeClass = getIconeRedeSocial(plat);

      $('#msgSemRedes').remove();
      const html = `
        <div class="social-dock-item d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill border bg-body-tertiary">
          <input type="hidden" name="rede_plataforma[]" value="${plat}">
          <input type="hidden" name="rede_url[]" value="${nick}">
          <i class="${iconeClass} fs-6"></i>
          <span class="fw-semibold text-body ms-1" style="font-size: 0.85rem;">${nick}</span>
          <button type="button" class="social-dock-remove ms-1 text-muted" onclick="$(this).parent().remove(); if($('#containerRedesOnboarding .social-dock-item').length === 0) $('#containerRedesOnboarding').html('<span class=\\'text-muted small\\' id=\\'msgSemRedes\\' style=\\'font-size: 0.75rem;\\'>Nenhuma rede cadastrada.</span>');" title="Remover">
            <i class="bi bi-x fs-6"></i>
          </button>
        </div>
      `;
      $('#containerRedesOnboarding').append(html);

      if (modalRedeSocialInstance) {
        modalRedeSocialInstance.hide();
      }

      usShowToast('success', 'Rede Adicionada', `${plat} adicionado com sucesso!`);
    }

    function aoAlternarSubarea(checkbox) {
      const card = $(checkbox).closest('.subarea-onboarding-card');
      if (checkbox.checked) {
        card.addClass('subarea-selected');
      } else {
        card.removeClass('subarea-selected');
      }
      atualizarContadorSubareas();
    }

    function atualizarContadorSubareas() {
      const total = $('.subarea-checkbox:checked').length;
      $('#badgeTotalSubareas').html('<i class="bi bi-check2-all me-1"></i> ' + total + ' selecionada(s)');
      if (total > 0) {
        $('#containerSubareasOnboarding').removeClass('border-danger');
      }
    }

    function submeterCadastro(e) {
      e.preventDefault();

      const emailVal = $('#email').val().trim();
      if (emailVal !== '') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailVal)) {
          $('#email').focus();
          usShowToast('warning', 'E-mail Inválido', 'Por favor, informe um endereço de e-mail válido ou deixe o campo em branco.');
          return;
        }
      }

      const totalSubareas = $('.subarea-checkbox:checked').length;
      if (totalSubareas === 0) {
        $('#containerSubareasOnboarding').addClass('border-danger');
        document.getElementById('containerSubareasOnboarding').scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
        usShowToast('warning', 'Sub-área Obrigatória', 'Por favor, selecione pelo menos uma sub-área de atuação.');
        return;
      }

      const totalCultos = $('.culto-onboarding-checkbox:checked').length;
      if (totalCultos === 0) {
        $('#collapseDisponibilidadeOnboarding').collapse('show');
        $('#cardDisponibilidadeOnboarding').addClass('border-danger');
        document.getElementById('cardDisponibilidadeOnboarding').scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
        usShowToast('warning', 'Disponibilidade Obrigatória', 'Por favor, selecione ao menos um dia/culto de disponibilidade.');
        return;
      }

      const btn = document.getElementById('btnFinalizarCadastro');
      btn.disabled = true;
      btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div> Enviando cadastro...';

      const formData = new FormData(document.getElementById('formCadastroVoluntario'));

      $.ajax({
        url: '<?= base_url('convite/concluir-cadastro') ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
          if (res.status === 'success') {
            window.location.href = res.redirect || '<?= base_url('convite/sucesso') ?>';
          } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-check-fill me-2"></i> Concluir e Enviar para Aprovação';
            usShowToast('error', 'Falha no Cadastro', res.message || 'Falha ao concluir cadastro.');
          }
        },
        error: function(xhr) {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-send-check-fill me-2"></i> Concluir e Enviar para Aprovação';
          const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao processar cadastro.';
          usShowToast('error', 'Erro', msg);
        }
      });
    }

    function aoAlternarCulto(checkbox) {
      const card = $(checkbox).closest('.culto-onboarding-card');
      if (checkbox.checked) {
        card.addClass('bg-primary-subtle border-primary').removeClass('bg-body-tertiary');
      } else {
        card.removeClass('bg-primary-subtle border-primary').addClass('bg-body-tertiary');
      }
      atualizarContadorCultosOnboarding();
    }

    function marcarTodosCultosOnboarding(marcar) {
      $('.culto-onboarding-checkbox').each(function() {
        this.checked = marcar;
        aoAlternarCulto(this);
      });
      atualizarContadorCultosOnboarding();
    }

    function atualizarContadorCultosOnboarding() {
      const total = $('.culto-onboarding-checkbox:checked').length;
      $('#badgeTotalCultosOnboarding').html('<i class="bi bi-check2-all me-1"></i> ' + total + ' selecionado(s)');
      if (total > 0) {
        $('#cardDisponibilidadeOnboarding').removeClass('border-danger');
      }
    }
  </script>
</body>

</html>