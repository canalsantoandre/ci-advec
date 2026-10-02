<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

  <!-- PWA & Mobile Fullscreen Settings (Oculta barra do navegador no mobile) -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ADVEC Voluntário">
  <meta name="theme-color" content="#0f172a">
  <meta name="msapplication-navbutton-color" content="#0f172a">
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

  <title><?= esc($title ?? 'Meu Perfil & Métricas - ADVEC') ?></title>

  <!-- Google Font: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- jQuery & Mask Plugin -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

  <style>
    :root {
      --gold-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      --silver-gradient: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
      --bronze-gradient: linear-gradient(135deg, #d97706 0%, #92400e 100%);
    }

    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
      background-color: #f1f5f9;
      min-height: 100dvh;
    }

    [data-bs-theme="dark"] body {
      background-color: #0b1329;
      color: #e2e8f0;
    }

    /* Hero Card Premium (Degradê da Tela de Ranking) */
    .hero-profile-card {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1e3a8a 100%);
      color: #ffffff;
      border-radius: 1.5rem;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.35);
    }

    .hero-profile-card::before {
      content: '';
      position: absolute;
      top: -40%;
      right: -10%;
      width: 320px;
      height: 320px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(37, 99, 235, 0) 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .hero-profile-card::after {
      content: '';
      position: absolute;
      bottom: -30%;
      left: 10%;
      width: 250px;
      height: 250px;
      background: radial-gradient(circle, rgba(234, 179, 8, 0.15) 0%, rgba(234, 179, 8, 0) 70%);
      border-radius: 50%;
      pointer-events: none;
    }

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
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4), 0 0 15px rgba(59, 130, 246, 0.4);
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
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.35);
      transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .avatar-edit-badge:hover {
      transform: scale(1.15);
      background: #1d4ed8;
    }

    /* Níveis de Conhecimento */
    .badge-level-aprendiz {
      background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
      color: #ffffff !important;
      border: 1px solid rgba(255, 255, 255, 0.25);
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .badge-level-junior {
      background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
      color: #ffffff !important;
      border: 1px solid rgba(255, 255, 255, 0.25);
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .badge-level-pleno {
      background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%) !important;
      color: #ffffff !important;
      border: 1px solid rgba(255, 255, 255, 0.25);
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .badge-level-senior {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
      color: #ffffff !important;
      border: 1px solid rgba(255, 255, 255, 0.3);
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    /* Glassmorphic Info Panels */
    .glass-info-pill {
      background: rgba(15, 23, 42, 0.55);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1.25rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    }

    /* Cards de Seção Minimalistas */
    .profile-section-card {
      border-radius: 1.25rem;
      border: 1px solid rgba(226, 232, 240, 0.8);
      background: #ffffff;
      transition: box-shadow 0.2s ease;
    }

    [data-bs-theme="dark"] .profile-section-card {
      background-color: #1e293b;
      border-color: #334155;
    }

    /* Collapse Chevrons */
    .collapse-chevron {
      transition: transform 0.25s ease-in-out;
    }

    .collapse-header-btn[aria-expanded="true"] .collapse-chevron {
      transform: rotate(180deg);
    }

    /* Form Floating Customizations */
    .form-floating>label {
      padding-left: 1rem;
      color: #64748b;
      font-weight: 500;
      font-size: 0.88rem;
    }

    .form-floating>.form-control {
      border-radius: 0.85rem;
      font-size: 0.95rem;
      border-color: #cbd5e1;
    }

    .form-floating>.form-control:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
    }

    [data-bs-theme="dark"] .form-floating>.form-control {
      background-color: #0f172a;
      border-color: #334155;
      color: #f8fafc;
    }

    [data-bs-theme="dark"] .form-floating>label {
      color: #94a3b8;
    }

    /* Pulsação no Ícone de Informação (Disponibilidade) */
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
      border: 1px solid rgba(255, 255, 255, 0.9);
      flex-shrink: 0;
    }

    /* Floating Save Bar */
    .floating-save-bar {
      position: sticky;
      bottom: 1.25rem;
      z-index: 1040;
      animation: slideUpFade 0.25s ease-out;
    }

    @keyframes slideUpFade {
      from {
        opacity: 0;
        transform: translateY(18px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Strength Bar */
    .strength-meter-bar {
      height: 6px;
      border-radius: 3px;
      transition: all 0.3s ease;
      background-color: #e2e8f0;
    }

    [data-bs-theme="dark"] .strength-meter-bar {
      background-color: #334155;
    }

    /* Efeito Dock do Mac para as Redes Sociais */
    .social-dock-container {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.65rem;
      padding: 0.35rem 0;
    }

    .social-dock-item {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.4rem 0.85rem;
      border-radius: 9999px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
      transition: all 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
      user-select: none;
    }

    [data-bs-theme="dark"] .social-dock-item {
      background: #1e293b;
      border-color: #334155;
    }

    .social-dock-item:hover {
      transform: translateY(-7px) scale(1.08);
      box-shadow: 0 10px 22px -4px rgba(0, 0, 0, 0.18), 0 0 10px rgba(59, 130, 246, 0.25);
      border-color: #3b82f6;
      z-index: 10;
    }

    .social-dock-link {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      text-decoration: none;
      color: inherit;
      font-weight: 600;
      font-size: 0.82rem;
    }

    .social-dock-icon {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      font-size: 0.82rem;
      flex-shrink: 0;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }

    .social-dock-remove {
      background: transparent;
      border: none;
      color: #94a3b8;
      padding: 0;
      margin-left: 0.25rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      transition: all 0.15s ease;
    }

    .social-dock-remove:hover {
      background: rgba(239, 68, 68, 0.15);
      color: #ef4444;
      transform: scale(1.15);
    }

    /* Micro-form de Adicionar Rede */
    .social-add-card {
      background: rgba(248, 250, 252, 0.95);
      border: 1px dashed #3b82f6;
      border-radius: 1rem;
      animation: slideDownFade 0.22s ease-out;
    }

    [data-bs-theme="dark"] .social-add-card {
      background: rgba(15, 23, 42, 0.7);
      border-color: #2563eb;
    }
  </style>
</head>

<body>

  <!-- Navigation -->
  <?= view('portal_voluntario/_nav', ['voluntario' => $voluntario, 'menuAtivo' => 'perfil']) ?>

  <?php
  if (!function_exists('getSocialInfo')) {
    function getSocialInfo($plataforma, $valor)
    {
      $p = strtolower(trim((string)$plataforma));
      $v = trim((string)$valor);
      $cleanNick = ltrim($v, '@');

      $config = [
        'instagram' => ['icon' => 'bi-instagram', 'color' => '#E1306C', 'base' => 'https://instagram.com/'],
        'facebook'  => ['icon' => 'bi-facebook',  'color' => '#1877F2', 'base' => 'https://facebook.com/'],
        'youtube'   => ['icon' => 'bi-youtube',   'color' => '#FF0000', 'base' => 'https://youtube.com/@'],
        'tiktok'    => ['icon' => 'bi-tiktok',    'color' => '#000000', 'base' => 'https://tiktok.com/@'],
        'linkedin'  => ['icon' => 'bi-linkedin',  'color' => '#0A66C2', 'base' => 'https://linkedin.com/in/'],
        'twitter'   => ['icon' => 'bi-twitter-x', 'color' => '#000000', 'base' => 'https://x.com/'],
        'x'         => ['icon' => 'bi-twitter-x', 'color' => '#000000', 'base' => 'https://x.com/'],
        'threads'   => ['icon' => 'bi-threads',   'color' => '#000000', 'base' => 'https://threads.net/@'],
        'github'    => ['icon' => 'bi-github',    'color' => '#24292e', 'base' => 'https://github.com/'],
        'behance'   => ['icon' => 'bi-behance',   'color' => '#1769ff', 'base' => 'https://behance.net/'],
        'spotify'   => ['icon' => 'bi-spotify',   'color' => '#1DB954', 'base' => 'https://open.spotify.com/'],
        'pinterest' => ['icon' => 'bi-pinterest', 'color' => '#BD081C', 'base' => 'https://pinterest.com/'],
        'outro'     => ['icon' => 'bi-globe',     'color' => '#64748b', 'base' => 'https://']
      ];

      $info = $config[$p] ?? ['icon' => 'bi-link-45deg', 'color' => '#64748b', 'base' => 'https://'];

      if (empty($v)) {
        $finalUrl = '#';
      } elseif (preg_match('#^https?://#i', $v)) {
        $finalUrl = $v;
      } else {
        $finalUrl = $info['base'] . $cleanNick;
      }

      return [
        'icon'  => $info['icon'],
        'color' => $info['color'],
        'base'  => $info['base'],
        'url'   => $finalUrl
      ];
    }
  }

  $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($voluntario->nome) . '&background=2563eb&color=fff&size=150&bold=true';
  $fotoSrc = !empty($voluntario->foto_url) ? $voluntario->foto_url : $defaultAvatar;
  $nv = !empty($voluntario->nivel_conhecimento) ? strtoupper($voluntario->nivel_conhecimento) : 'JUNIOR';
  ?>

  <div class="container max-w-portal py-3 px-3">

    <!-- ======================================================== -->
    <!-- 1. HERO HEADER: PERFIL COM DEGRADÊ PREMIUM DA TELA RANKING-->
    <!-- ======================================================== -->
    <div class="hero-profile-card p-4 p-md-5 mb-4 text-center position-relative">

      <!-- Avatar & Upload Automático -->
      <div class="avatar-wrapper mb-3 position-relative">
        <img src="<?= esc($fotoSrc) ?>" id="avatarPreview" class="avatar-img" alt="Foto do Voluntário" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
        <label for="foto_file" class="avatar-edit-badge" title="Alterar Foto de Perfil" id="labelFotoFile">
          <i class="bi bi-camera-fill fs-6" id="avatarCameraIcon"></i>
          <div class="spinner-border spinner-border-sm text-white d-none" id="avatarSpinner" role="status" style="width: 1rem; height: 1rem;"></div>
        </label>
        <input type="file" id="foto_file" name="foto_file" class="d-none" accept="image/*" onchange="uploadFotoAutomatico(this)">
      </div>

      <!-- Nome & Nickname Centralizados -->
      <h3 class="fw-bold mb-1 text-white" style="letter-spacing: -0.3px; font-size: 1.55rem; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);"><?= esc($voluntario->nome) ?></h3>

      <?php $nick = trim((string)($voluntario->nickname ?? '')); ?>
      <?php if (!empty($nick)) { ?>
        <div class="mb-2">
          <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background: rgba(255, 255, 255, 0.12); color: #f8fafc; border: 1px solid rgba(255, 255, 255, 0.2); font-size: 0.82rem; backdrop-filter: blur(6px);">
            <i class="bi bi-tag-fill me-1 text-warning"></i>&ldquo;<?= esc($nick) ?>&rdquo;
          </span>
        </div>
      <?php } ?>

      <!-- Redes Sociais no Perfil (Links Diretos Clicáveis em Nova Aba) -->
      <?php if (!empty($redesSociais)) { ?>
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-3 mt-1">
          <?php foreach ($redesSociais as $r) {
            $pName = $r['plataforma'] ?? ($r['rede'] ?? 'Outro');
            $rVal  = $r['url'] ?? ($r['link'] ?? '');
            if (empty(trim((string)$rVal))) continue;
            $sInfo = getSocialInfo($pName, $rVal);
          ?>
            <a href="<?= esc($sInfo['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center text-white shadow-sm" style="width: 34px; height: 34px; background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px); transition: all 0.2s ease;" title="<?= esc($pName) ?>: <?= esc($rVal) ?> (Abrir em nova aba)" onmouseover="this.style.transform='translateY(-2px)'; this.style.background='rgba(255,255,255,0.25)';" onmouseout="this.style.transform='none'; this.style.background='rgba(255,255,255,0.12)';">
              <i class="bi <?= $sInfo['icon'] ?>" style="font-size: 0.95rem;"></i>
            </a>
          <?php } ?>
        </div>
      <?php } else { ?>
        <div class="mb-2"></div>
      <?php } ?>

      <!-- Bloco de Informações Glassmorphic: Departamentos, Nível & Status -->
      <div class="glass-info-pill p-3 p-md-4 mt-2 text-start">

        <!-- Header de Departamentos -->
        <div class="d-flex align-items-center gap-2 pb-2.5 border-bottom border-white border-opacity-10">
          <i class="bi bi-diagram-3-fill text-info"></i>
          <span class="text-white-50 small fw-bold text-uppercase" style="letter-spacing: 0.6px; font-size: 0.72rem;">
            Departamentos & Áreas de Atuação
          </span>
        </div>

        <?php
        // Agrupamento de sub-áreas por departamento
        $departamentosAgrupados = [];
        if (!empty($voluntario->areas)) {
          foreach ($voluntario->areas as $va) {
            $depNome = !empty($va->nome_departamento) ? $va->nome_departamento : 'Geral';
            $corDep  = !empty($va->cor_identificacao) ? $va->cor_identificacao : '#3b82f6';
            if (!isset($departamentosAgrupados[$depNome])) {
              $departamentosAgrupados[$depNome] = [
                'cor'   => $corDep,
                'areas' => []
              ];
            }
            $departamentosAgrupados[$depNome]['areas'][] = $va->nome_area;
          }
        }
        ?>

        <!-- Departamentos Agrupados com suas Sub-áreas (Espaçamento simétrico py-3) -->
        <div class="d-flex flex-column gap-2.5 py-3">
          <?php if (!empty($departamentosAgrupados)) { ?>
            <?php foreach ($departamentosAgrupados as $depNome => $depInfo) {
              $cDep = $depInfo['cor'];
            ?>
              <div class="p-2 px-3 rounded-3 d-flex flex-wrap align-items-center gap-2" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">

                <!-- Nome do Departamento com Ponto de Cor -->
                <div class="d-inline-flex align-items-center gap-1.5 me-1 text-nowrap">
                  <span class="rounded-circle flex-shrink-0" style="width: 8px; height: 8px; background-color: <?= esc($cDep) ?>; box-shadow: 0 0 8px <?= esc($cDep) ?>;"></span>
                  <span class="fw-bold" style="color: #94a3b8; font-size: 0.8rem;">&nbsp;&nbsp;<?= esc($depNome) ?>:</span>
                </div>

                <!-- Pílulas das Sub-áreas -->
                <div class="d-flex flex-wrap gap-1.5 align-items-center">
                  <?php foreach ($depInfo['areas'] as $areaNome) { ?>
                    <span class="badge rounded-pill fw-bold text-white px-2.5 py-1" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2); font-size: 0.78rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                      <?= esc($areaNome) ?>
                    </span>
                  <?php } ?>
                </div>

              </div>
            <?php } ?>
          <?php } else { ?>
            <span class="badge bg-white bg-opacity-10 text-white-50 rounded-pill px-3 py-1.5 align-self-start">Sem áreas vinculadas</span>
          <?php } ?>
        </div>

        <!-- Grade com 3 Métricas Rápidas (Nível, Limite e Status) -->
        <div class="row g-2 pt-3 border-top border-white border-opacity-10 text-center">

          <!-- Nível -->
          <div class="col-4">
            <div class="h-100 d-flex flex-column justify-content-between align-items-center" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; padding: 8px 4px 10px 4px; min-height: 64px;">
              <div class="text-white-50 fw-bold" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Nível</div>
              <div class="d-flex align-items-center justify-content-center w-100 mt-1">
                <span class="badge badge-level-<?= strtolower($nv) ?> rounded-pill px-2 py-1 text-uppercase fw-bold d-inline-flex align-items-center justify-content-center" style="font-size: 0.7rem; letter-spacing: 0.3px; max-width: 100%;">
                  <i class="bi bi-mortarboard-fill me-1" style="font-size: 0.72rem;"></i><?= $nv ?>
                </span>
              </div>
            </div>
          </div>

          <!-- Limite de Escalas -->
          <div class="col-4">
            <div class="h-100 d-flex flex-column justify-content-between align-items-center" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; padding: 8px 4px 10px 4px; min-height: 64px;">
              <div class="text-white-50 fw-bold" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Limite</div>
              <div class="d-flex align-items-center justify-content-center w-100 mt-1">
                <span class="text-white fw-bold d-inline-flex align-items-center justify-content-center text-truncate" style="font-size: 0.78rem;">
                  <i class="bi bi-calendar2-check text-info me-1" style="font-size: 0.78rem;"></i><?= ($voluntario->max_escalas_mes > 0) ? "{$voluntario->max_escalas_mes}/mês" : "Ilimitado" ?>
                </span>
              </div>
            </div>
          </div>

          <!-- Status -->
          <div class="col-4">
            <div class="h-100 d-flex flex-column justify-content-between align-items-center" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; padding: 8px 4px 10px 4px; min-height: 64px;">
              <div class="text-white-50 fw-bold" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
              <div class="d-flex align-items-center justify-content-center w-100 mt-1">
                <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.25); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.5); font-size: 0.7rem;">
                  <i class="bi bi-check-circle-fill me-1" style="font-size: 0.72rem;"></i>Ativo
                </span>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- ======================================================== -->
    <!-- FORMULÁRIO PRINCIPAL COM MULTI-COLLAPSE E FLOATING LABELS-->
    <!-- ======================================================== -->
    <form id="formSalvarPerfil" onsubmit="salvarPerfilAjax(event)">

      <!-- ======================================================== -->
      <!-- PAINEL 1: MEUS DADOS (COLLAPSIBLE)                      -->
      <!-- ======================================================== -->
      <div class="profile-section-card shadow-sm mb-3">

        <!-- Header do Collapse 1 -->
        <button type="button" class="btn w-100 p-3 p-md-3 text-start d-flex justify-content-between align-items-center collapse-header-btn border-0 rounded-4" data-bs-toggle="collapse" data-bs-target="#collapseDadosPessoais" aria-expanded="false" aria-controls="collapseDadosPessoais">
          <div class="d-flex align-items-center gap-2.5">
            <i class="bi bi-person-lines-fill text-primary fs-5"></i>&nbsp;&nbsp;
            <span class="fw-medium fw-md-bold fs-6 text-body">Meus Dados</span>
          </div>
          <i class="bi bi-chevron-down fs-6 text-muted collapse-chevron"></i>
        </button>

        <!-- Conteúdo do Collapse 1 -->
        <div class="collapse" id="collapseDadosPessoais">
          <div class="p-3 p-md-4 pt-1 pb-4">
            <div class="row g-3">

              <!-- Nome Completo (Read-Only) com Form-Floating -->
              <div class="col-12 col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control bg-body-secondary" id="floatingNome" value="<?= esc($voluntario->nome) ?>" placeholder="Nome Completo" readonly>
                  <label for="floatingNome"><i class="bi bi-lock-fill text-muted me-1"></i>Nome Completo (Oficial)</label>
                </div>
                <small class="text-muted ps-2" style="font-size: 0.7rem;">Para alterar seu nome oficial, consulte seu líder.</small>
              </div>

              <!-- Nickname / Apelido com Form-Floating -->
              <div class="col-12 col-md-6">
                <div class="form-floating">
                  <input type="text" id="nickname" name="nickname" class="form-control input-monitor-change" placeholder="Como quer ser chamado" value="<?= esc($voluntario->nickname ?? '') ?>" autocomplete="off">
                  <label for="nickname"><i class="bi bi-tag-fill text-primary me-1"></i>Nickname / Apelido (Aparece na Grade)</label>
                </div>
              </div>

              <!-- Telefone WhatsApp com Form-Floating -->
              <div class="col-12 col-md-6">
                <?php
                $foneExibicao = (string)($voluntario->telefone_whatsapp ?? '');
                $foneDigits = preg_replace('/\D/', '', $foneExibicao);
                if (strpos($foneDigits, '55') === 0 && strlen($foneDigits) >= 12) {
                  $foneExibicao = substr($foneDigits, 2);
                }
                ?>
                <div class="form-floating">
                  <input type="tel" id="telefone_whatsapp" name="telefone_whatsapp" class="form-control input-monitor-change" placeholder="(11) 99999-9999" value="<?= esc($foneExibicao) ?>" required autocomplete="tel">
                  <label for="telefone_whatsapp"><i class="bi bi-whatsapp text-success me-1"></i>WhatsApp / Login do Portal *</label>
                </div>
                <small class="text-muted ps-2" style="font-size: 0.7rem;">Usado como seu identificador de acesso.</small>
              </div>

              <!-- Data de Nascimento com Form-Floating -->
              <div class="col-12 col-md-6">
                <div class="form-floating">
                  <input type="date" id="data_nascimento" name="data_nascimento" class="form-control input-monitor-change" placeholder="Data de Nascimento" value="<?= esc($voluntario->data_nascimento) ?>">
                  <label for="data_nascimento"><i class="bi bi-cake2 text-warning me-1"></i>Data de Nascimento</label>
                </div>
              </div>

              <!-- Redes Sociais Dinâmicas com Efeito Dock e Micro-Form -->
              <div class="col-12 mt-3 pt-2 border-top">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div>
                    <label class="form-label fw-semibold small mb-0 text-body">
                      <i class="bi bi-share text-primary me-1"></i> Minhas Redes Sociais
                    </label>

                  </div>
                  <button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-1 px-3 small" onclick="toggleFormAdicionarRede()" title="Adicionar Rede Social" id="btnToggleAddRede">
                    <i class="bi bi-plus-lg"></i>
                  </button>
                </div>

                <!-- Micro-Form de Adicionar Nova Rede (Oculto por padrão) -->
                <div id="cardAdicionarRede" class="social-add-card p-3 mb-3 d-none">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-primary"><i class="bi bi-plus-circle me-1"></i> Nova Rede Social</span>
                    <button type="button" class="btn-close btn-sm" onclick="toggleFormAdicionarRede(false)" aria-label="Fechar"></button>
                  </div>
                  <div class="row g-2 align-items-end">
                    <!-- 1. Escolher a Rede -->
                    <div class="col-12 col-sm-5">
                      <label class="form-label small text-muted mb-1" style="font-size: 0.75rem;">1. Escolha a Rede</label>
                      <select id="novoRedePlataforma" class="form-select form-select-sm rounded-3" onchange="onNovaRedePlataformaChange(this)">
                        <option value="Instagram" selected>Instagram</option>
                        <option value="TikTok">TikTok</option>
                        <option value="YouTube">YouTube</option>
                        <option value="LinkedIn">LinkedIn</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Twitter">X / Twitter</option>
                        <option value="Threads">Threads</option>
                        <option value="GitHub">GitHub</option>
                        <option value="Spotify">Spotify</option>
                        <option value="Behance">Behance</option>
                        <option value="Pinterest">Pinterest</option>
                        <option value="Outro">Outro Link</option>
                      </select>
                    </div>

                    <!-- 2. Digitar o Nick -->
                    <div class="col-9 col-sm-6">
                      <label class="form-label small text-muted mb-1" style="font-size: 0.75rem;">2. Digite seu @nickname</label>
                      <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary" id="novoRedeIconPreview">
                          <i class="bi bi-instagram" style="color: #E1306C;"></i>
                        </span>
                        <input type="text" id="novoRedeUrl" class="form-control" placeholder="@seu_nick no Instagram" onkeypress="if(event.key === 'Enter'){ event.preventDefault(); confirmarAdicionarRede(); }">
                      </div>
                    </div>

                    <!-- 3. Botão com Tick Verde -->
                    <div class="col-3 col-sm-1 text-end">
                      <button type="button" class="btn btn-success btn-sm rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="confirmarAdicionarRede()" title="Confirmar e Salvar Rede">
                        <i class="bi bi-check-lg fs-6"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Dock / Lista Visual de Redes Sociais Cadastradas -->
                <div id="containerRedesSociais" class="social-dock-container">
                  <?php if (!empty($redesSociais)) { ?>
                    <?php foreach ($redesSociais as $index => $rede) {
                      $plat = $rede['plataforma'] ?? ($rede['rede'] ?? 'Instagram');
                      $uVal = $rede['url'] ?? ($rede['link'] ?? '');
                      if (empty(trim((string)$uVal))) continue;
                      $sInfo = getSocialInfo($plat, $uVal);
                    ?>
                      <div class="social-dock-item" data-index="<?= $index ?>">
                        <input type="hidden" name="rede_plataforma[]" value="<?= esc($plat) ?>">
                        <input type="hidden" name="rede_url[]" value="<?= esc($uVal) ?>">

                        <a href="<?= esc($sInfo['url']) ?>" target="_blank" rel="noopener noreferrer" class="social-dock-link" title="Abrir perfil no <?= esc($plat) ?> em nova aba">
                          <span class="social-dock-icon" style="background-color: <?= esc($sInfo['color']) ?>;">
                            <i class="bi <?= $sInfo['icon'] ?>"></i>
                          </span>
                          <span><?= esc($plat) ?></span>
                          <span class="text-muted fw-normal small" style="font-size: 0.78rem;"><?= esc($uVal) ?></span>
                          <i class="bi bi-box-arrow-up-right text-muted ms-0.5" style="font-size: 0.7rem;"></i>
                        </a>
                        <button type="button" class="social-dock-remove" onclick="removerRedeSocial(this)" title="Remover <?= esc($plat) ?>">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </div>
                    <?php } ?>
                  <?php } ?>
                </div>


              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- PAINEL 2: DISPONIBILIDADE (COLLAPSIBLE)                  -->
      <!-- ======================================================== -->
      <div class="profile-section-card shadow-sm mb-4">

        <!-- Header do Collapse 2 -->
        <button type="button" class="btn w-100 p-3 p-md-3 text-start d-flex justify-content-between align-items-center collapse-header-btn border-0 rounded-4" data-bs-toggle="collapse" data-bs-target="#collapseDisponibilidade" aria-expanded="false" aria-controls="collapseDisponibilidade">
          <div class="d-flex align-items-center gap-2.5">
            <i class="bi bi-calendar2-week-fill text-warning fs-5"></i>&nbsp;&nbsp;
            <span class="fw-medium fw-md-bold fs-6 text-body">Disponibilidade</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-bold" id="badgeTotalCultosPortal">
              <i class="bi bi-check2-all me-1"></i> <?= count($cultosSelecionadosIds ?? []) ?> selecionado(s)
            </span>
            <i class="bi bi-chevron-down fs-6 text-muted collapse-chevron"></i>
          </div>
        </button>

        <!-- Conteúdo do Collapse 2 -->
        <div class="collapse" id="collapseDisponibilidade">
          <div class="p-3 p-md-4 pt-1 pb-4">



            <!-- Banner Interativo: Header com (i) Pulsando que Expande e Mostra o Conteúdo + [X] -->
            <div class="alert alert-primary-subtle border border-primary-subtle rounded-4 p-3 mb-3 shadow-xs">
              <div class="d-flex align-items-center justify-content-between" role="button" data-bs-toggle="collapse" data-bs-target="#bodyRegrasDisponibilidade" aria-expanded="false" aria-controls="bodyRegrasDisponibilidade" style="cursor: pointer;">
                <div class="d-flex align-items-center gap-2">
                  <span class="pulse-info-btn">
                    <i class="bi bi-info-lg fw-bold" style="font-size: 0.8rem;"></i>
                  </span>
                  <strong class="text-body small fw-bold">Regras de Disponibilidade</strong>
                </div>
              </div>

              <div class="collapse" id="bodyRegrasDisponibilidade">
                <div class="small text-secondary pt-2 mt-2 border-top border-primary-subtle">
                  <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="flex-grow-1">
                      <p class="mb-1">&bull; <strong>Disponibilidade Total:</strong> Se você deixar <strong>todos os cultos desmarcados</strong>, entenderemos que você está disponível para servir em <strong>qualquer dia e horário</strong>.</p>
                      <p class="mb-0">&bull; <strong>Disponibilidade Específica:</strong> Se marcar <strong>um ou mais cultos</strong>, a liderança só poderá escalá-lo estritamente nos cultos selecionados.</p>
                    </div>
                    <button type="button" class="btn-close btn-sm flex-shrink-0 mt-0.5" data-bs-toggle="collapse" data-bs-target="#bodyRegrasDisponibilidade" aria-label="Fechar"></button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Ações Rápidas de Marcar Todos / Limpar -->
            <div class="d-flex justify-content-end align-items-center mb-3 gap-1.5">
              <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 py-1 small" onclick="marcarTodosCultosPortal(true)">
                <i class="bi bi-check-all me-1"></i> Todos
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 py-1 small" onclick="marcarTodosCultosPortal(false)">
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
                  $isChecked = in_array((int)$cp->id_culto_padrao, $cultosSelecionadosIds ?? []);
                  $nomeDia = $diasNomes[(int)$cp->dia_semana] ?? 'Culto';
                  $horaInicio = substr($cp->horario_inicio, 0, 5);
                  $horaTermino = substr($cp->horario_termino, 0, 5);
                  $corEvento = !empty($cp->cor_evento) ? $cp->cor_evento : '#2563eb';
                  $labelFormatado = "{$nomeDia} - {$horaInicio} - {$cp->nome_culto}";
                ?>
                  <div class="col-12 col-md-6">
                    <label class="d-flex align-items-center gap-3 p-3 rounded-3 border user-select-none h-100 culto-portal-card <?= $isChecked ? 'bg-primary-subtle border-primary' : 'bg-body-tertiary' ?>" for="portal_culto_check_<?= $cp->id_culto_padrao ?>" style="cursor: pointer; transition: all 0.15s ease; border-left: 4px solid <?= esc($corEvento) ?> !important;">
                      <input class="form-check-input mt-0 flex-shrink-0 culto-portal-checkbox input-monitor-change" type="checkbox" name="cultos[]" value="<?= $cp->id_culto_padrao ?>" id="portal_culto_check_<?= $cp->id_culto_padrao ?>" <?= $isChecked ? 'checked' : '' ?> style="cursor: pointer; width: 1.25rem; height: 1.25rem;">
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
              <div class="alert alert-warning border-0 rounded-3 small">
                Nenhum culto padrão disponível no momento.
              </div>
            <?php } ?>

          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- BARRA FLUTUANTE DE SALVAR (SÓ APARECE SE HOUVER ALTERAÇÃO) -->
      <!-- ======================================================== -->
      <div id="saveBar" class="floating-save-bar d-none">
        <div class="card border-0 shadow-lg rounded-pill bg-dark text-white p-2 px-3 mx-auto" style="max-width: 480px; backdrop-filter: blur(12px); background: rgba(15, 23, 42, 0.92) !important;">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 ps-2">
              <span class="spinner-grow spinner-grow-sm text-warning" style="width: 0.7rem; height: 0.7rem;"></span>
              <span class="small fw-semibold text-white">Alterações não salvas</span>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" id="btnSalvarPerfilSubmit">
              <i class="bi bi-check2-circle me-1"></i> Salvar
            </button>
          </div>
        </div>
      </div>

    </form>

    <!-- ======================================================== -->
    <!-- PAINEL 3: ALTERAR SENHA (COLLAPSED BY DEFAULT COM VALIDADOR)-->
    <!-- ======================================================== -->
    <div class="profile-section-card shadow-sm mb-4">

      <!-- Header do Collapse 3 -->
      <button type="button" class="btn w-100 p-3 p-md-3 text-start d-flex justify-content-between align-items-center collapse-header-btn border-0 rounded-4" data-bs-toggle="collapse" data-bs-target="#collapseAlterarSenha" aria-expanded="false" aria-controls="collapseAlterarSenha">
        <div class="d-flex align-items-center gap-2.5">
          <i class="bi bi-shield-lock-fill text-danger fs-5"></i>&nbsp;&nbsp;
          <span class="fw-medium fw-md-bold fs-6 text-body">Alterar Senha</span>
        </div>
        <div class="d-flex align-items-center gap-2">

          <i class="bi bi-chevron-down fs-6 text-muted collapse-chevron"></i>
        </div>
      </button>

      <!-- Conteúdo do Collapse 3 (Oculto por padrão) -->
      <div class="collapse" id="collapseAlterarSenha">
        <div class="p-3 p-md-4 pt-1 pb-4">

          <form id="formAlterarSenha" onsubmit="alterarSenhaAjax(event)">
            <div class="row g-3">

              <!-- Senha Atual com Form-Floating e Toggle Visual -->
              <div class="col-12 col-md-4">
                <div class="form-floating position-relative">
                  <input type="password" id="senha_atual" name="senha_atual" class="form-control" placeholder="Senha Atual" required autocomplete="current-password">
                  <label for="senha_atual"><i class="bi bi-key-fill text-muted me-1"></i>Senha Atual *</label>
                  <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none me-2" onclick="toggleVisibilidadeSenha('senha_atual', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <!-- Nova Senha com Form-Floating e Força de Senha -->
              <div class="col-12 col-md-4">
                <div class="form-floating position-relative">
                  <input type="password" id="nova_senha" name="nova_senha" class="form-control" placeholder="Nova Senha" minlength="6" required autocomplete="new-password" oninput="avaliarForcaSenha(this.value)">
                  <label for="nova_senha"><i class="bi bi-lock-fill text-primary me-1"></i>Nova Senha *</label>
                  <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none me-2" onclick="toggleVisibilidadeSenha('nova_senha', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>

                <!-- Barra e Indicador de Força da Senha -->
                <div class="mt-2 px-1">
                  <div class="progress strength-meter-bar mb-1" style="height: 5px;">
                    <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center small" style="font-size: 0.72rem;">
                    <span class="text-muted">Nível de segurança:</span>
                    <span id="passwordStrengthText" class="fw-bold text-muted">-</span>
                  </div>
                </div>
              </div>

              <!-- Confirmar Nova Senha com Form-Floating -->
              <div class="col-12 col-md-4">
                <div class="form-floating position-relative">
                  <input type="password" id="confirma_nova_senha" name="confirma_nova_senha" class="form-control" placeholder="Confirmar Senha" minlength="6" required autocomplete="new-password">
                  <label for="confirma_nova_senha"><i class="bi bi-shield-check text-success me-1"></i>Confirmar Nova Senha *</label>
                  <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none me-2" onclick="toggleVisibilidadeSenha('confirma_nova_senha', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

            </div>

            <!-- Requisitos de Senha -->
            <div class="mt-3 p-3 rounded-3 bg-body-tertiary border d-flex flex-wrap gap-3 small text-muted" style="font-size: 0.75rem;">
              <span id="req-len"><i class="bi bi-circle me-1"></i>Mínimo 6 caracteres</span>
              <span id="req-num"><i class="bi bi-circle me-1"></i>Contém números</span>
              <span id="req-upper"><i class="bi bi-circle me-1"></i>Letras maiúsculas e minúsculas</span>
            </div>

            <!-- Botão Atualizar Minha Senha Centralizado e Azul -->
            <div class="mt-4 mb-2 text-center">
              <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm" id="btnAlterarSenhaSubmit">
                <i class="bi bi-key-fill me-1.5"></i> Atualizar Minha Senha
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>

  </div>

  <!-- Toast Notification Container -->
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="portalToast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body fw-semibold" id="portalToastBody"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let initialFormSerialized = '';

    $(document).ready(function() {
      // 1. Máscara dinâmica de telefone SP/BR
      const maskBehavior = function(val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
      };
      const options = {
        onKeyPress: function(val, e, field, options) {
          field.mask(maskBehavior.apply({}, arguments), options);
        }
      };
      $('#telefone_whatsapp').mask(maskBehavior, options);

      // 2. Atualiza estado inicial de cultos
      atualizarContadorCultosPortal();

      // 3. Registra estado inicial do formulário para detecção de alterações
      salvarEstadoInicial();

      // 4. Monitora alterações em tempo real no formulário
      $(document).on('input change keyup', '#formSalvarPerfil input, #formSalvarPerfil select', function() {
        verificarAlteracoesFormulario();
      });

      // 5. Event listener nos checkboxes de cultos do portal
      document.querySelectorAll('.culto-portal-checkbox').forEach(chk => {
        chk.addEventListener('change', function() {
          atualizarContadorCultosPortal();
          verificarAlteracoesFormulario();
        });
      });
    });

    function salvarEstadoInicial() {
      initialFormSerialized = $('#formSalvarPerfil').serialize();
      verificarAlteracoesFormulario();
    }

    function verificarAlteracoesFormulario() {
      const currentSerialized = $('#formSalvarPerfil').serialize();
      const isDirty = (currentSerialized !== initialFormSerialized);
      const saveBar = document.getElementById('saveBar');

      if (saveBar) {
        if (isDirty) {
          saveBar.classList.remove('d-none');
        } else {
          saveBar.classList.add('d-none');
        }
      }
    }

    function atualizarContadorCultosPortal() {
      const checkboxes = document.querySelectorAll('.culto-portal-checkbox');
      let totalChecked = 0;
      checkboxes.forEach(chk => {
        const parent = chk.closest('.culto-portal-card');
        if (chk.checked) {
          totalChecked++;
          if (parent) {
            parent.classList.add('bg-primary-subtle', 'border-primary');
            parent.classList.remove('bg-body-tertiary');
          }
        } else {
          if (parent) {
            parent.classList.remove('bg-primary-subtle', 'border-primary');
            parent.classList.add('bg-body-tertiary');
          }
        }
      });

      const badge = document.getElementById('badgeTotalCultosPortal');

      const htmlBadge = (totalChecked === 0) ?
        `<i class="bi bi-asterisk me-1"></i> Todos (Disponibilidade Total)` :
        `<i class="bi bi-check2-all me-1"></i> ${totalChecked} selecionado(s)`;

      const classBadge = (totalChecked === 0) ?
        'badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1 small fw-bold' :
        'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-bold';

      if (badge) {
        badge.className = classBadge;
        badge.innerHTML = htmlBadge;
      }
    }

    function marcarTodosCultosPortal(marcar) {
      document.querySelectorAll('.culto-portal-checkbox').forEach(chk => {
        chk.checked = marcar;
      });
      atualizarContadorCultosPortal();
      verificarAlteracoesFormulario();
    }

    function showToast(tipo, mensagem) {
      const toastEl = document.getElementById('portalToast');
      const toastBody = document.getElementById('portalToastBody');
      toastEl.className = 'toast align-items-center text-white border-0 shadow-lg rounded-3 ' + (tipo === 'success' ? 'bg-success' : 'bg-danger');
      toastBody.innerHTML = (tipo === 'success' ? '<i class="bi bi-check-circle-fill me-2"></i>' : '<i class="bi bi-exclamation-octagon-fill me-2"></i>') + mensagem;
      const bsToast = new bootstrap.Toast(toastEl, {
        delay: 3500
      });
      bsToast.show();
    }

    function uploadFotoAutomatico(input) {
      if (!input.files || !input.files[0]) return;

      const file = input.files[0];
      const avatarImg = document.getElementById('avatarPreview');
      const cameraIcon = document.getElementById('avatarCameraIcon');
      const spinner = document.getElementById('avatarSpinner');

      const reader = new FileReader();
      reader.onload = function(e) {
        if (avatarImg) avatarImg.src = e.target.result;
      };
      reader.readAsDataURL(file);

      if (cameraIcon) cameraIcon.classList.add('d-none');
      if (spinner) spinner.classList.remove('d-none');

      const formData = new FormData();
      formData.append('foto_file', file);

      fetch('<?= base_url('portal/uploadFoto') ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          if (cameraIcon) cameraIcon.classList.remove('d-none');
          if (spinner) spinner.classList.add('d-none');

          if (data.status === 'success') {
            showToast('success', data.message || 'Foto atualizada e salva com sucesso!');
            if (data.foto_url) {
              if (avatarImg) avatarImg.src = data.foto_url;
              document.querySelectorAll('.avatar-nav-img').forEach(img => {
                img.src = data.foto_url;
              });
            }
          } else {
            showToast('error', data.message || 'Erro ao salvar a nova foto.');
          }
        })
        .catch(err => {
          if (cameraIcon) cameraIcon.classList.remove('d-none');
          if (spinner) spinner.classList.add('d-none');
          showToast('error', 'Falha na conexão ao salvar a foto.');
        });
    }

    const SOCIAL_CONFIG = {
      'Instagram': {
        icon: 'bi-instagram',
        color: '#E1306C',
        base: 'https://instagram.com/'
      },
      'Facebook': {
        icon: 'bi-facebook',
        color: '#1877F2',
        base: 'https://facebook.com/'
      },
      'YouTube': {
        icon: 'bi-youtube',
        color: '#FF0000',
        base: 'https://youtube.com/@'
      },
      'TikTok': {
        icon: 'bi-tiktok',
        color: '#000000',
        base: 'https://tiktok.com/@'
      },
      'LinkedIn': {
        icon: 'bi-linkedin',
        color: '#0A66C2',
        base: 'https://linkedin.com/in/'
      },
      'Twitter': {
        icon: 'bi-twitter-x',
        color: '#000000',
        base: 'https://x.com/'
      },
      'Threads': {
        icon: 'bi-threads',
        color: '#000000',
        base: 'https://threads.net/@'
      },
      'GitHub': {
        icon: 'bi-github',
        color: '#24292e',
        base: 'https://github.com/'
      },
      'Behance': {
        icon: 'bi-behance',
        color: '#1769ff',
        base: 'https://behance.net/'
      },
      'Spotify': {
        icon: 'bi-spotify',
        color: '#1DB954',
        base: 'https://open.spotify.com/'
      },
      'Pinterest': {
        icon: 'bi-pinterest',
        color: '#BD081C',
        base: 'https://pinterest.com/'
      },
      'Outro': {
        icon: 'bi-globe',
        color: '#64748b',
        base: 'https://'
      }
    };

    function resolveSocialUrl(platform, val) {
      val = (val || '').trim();
      if (!val) return '';
      if (/^https?:\/\//i.test(val)) return val;
      const cfg = SOCIAL_CONFIG[platform] || SOCIAL_CONFIG['Outro'];
      const nick = val.replace(/^@/, '');
      return cfg.base + nick;
    }

    function toggleFormAdicionarRede(force) {
      const card = document.getElementById('cardAdicionarRede');
      const btn = document.getElementById('btnToggleAddRede');
      if (!card) return;
      const isHidden = card.classList.contains('d-none');
      const show = typeof force === 'boolean' ? force : isHidden;

      if (show) {
        card.classList.remove('d-none');
        if (btn) {
          btn.innerHTML = '<i class="bi bi-dash-lg"></i>';
          btn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
        }
        setTimeout(() => {
          const inp = document.getElementById('novoRedeUrl');
          if (inp) inp.focus();
        }, 50);
      } else {
        card.classList.add('d-none');
        if (btn) {
          btn.innerHTML = '<i class="bi bi-plus-lg"></i>';
          btn.classList.replace('btn-outline-secondary', 'btn-outline-primary');
        }
        const inp = document.getElementById('novoRedeUrl');
        if (inp) inp.value = '';
      }
    }

    function onNovaRedePlataformaChange(select) {
      const plat = select.value;
      const cfg = SOCIAL_CONFIG[plat] || SOCIAL_CONFIG['Outro'];
      const preview = document.getElementById('novoRedeIconPreview');
      const input = document.getElementById('novoRedeUrl');

      if (preview) {
        preview.innerHTML = `<i class="bi ${cfg.icon}" style="color: ${cfg.color};"></i>`;
      }
      if (input) {
        input.placeholder = `@seu_nick no ${plat}`;
      }
    }

    function confirmarAdicionarRede() {
      const platSelect = document.getElementById('novoRedePlataforma');
      const urlInput = document.getElementById('novoRedeUrl');
      if (!platSelect || !urlInput) return;

      const plat = platSelect.value;
      const val = urlInput.value.trim();

      if (!val) {
        showToast('error', 'Digite seu @nickname ou link para adicionar.');
        urlInput.focus();
        return;
      }

      const container = document.getElementById('containerRedesSociais');
      const emptyMsg = document.getElementById('emptyRedesSociaisMsg');
      if (emptyMsg) emptyMsg.classList.add('d-none');

      const resolvedUrl = resolveSocialUrl(plat, val);
      const cfg = SOCIAL_CONFIG[plat] || SOCIAL_CONFIG['Outro'];

      const item = document.createElement('div');
      item.className = 'social-dock-item';
      item.innerHTML = `
        <input type="hidden" name="rede_plataforma[]" value="${plat}">
        <input type="hidden" name="rede_url[]" value="${val}">
        
        <a href="${resolvedUrl}" target="_blank" rel="noopener noreferrer" class="social-dock-link" title="Abrir perfil no ${plat} em nova aba">
          <span class="social-dock-icon" style="background-color: ${cfg.color};">
            <i class="bi ${cfg.icon}"></i>
          </span>
          <span>${plat}</span>
          <span class="text-muted fw-normal small" style="font-size: 0.78rem;">${val}</span>
          <i class="bi bi-box-arrow-up-right text-muted ms-0.5" style="font-size: 0.7rem;"></i>
        </a>
        <button type="button" class="social-dock-remove" onclick="removerRedeSocial(this)" title="Remover ${plat}">
          <i class="bi bi-x-lg"></i>
        </button>
      `;

      container.appendChild(item);

      // Fecha o micro-form e limpa
      toggleFormAdicionarRede(false);

      // Atualiza monitoramento do formulário para habilitar o botão Salvar
      verificarAlteracoesFormulario();
      showToast('success', `${plat} adicionado com sucesso!`);
    }

    function removerRedeSocial(btn) {
      const item = btn.closest('.social-dock-item');
      if (item) {
        item.style.transform = 'scale(0.8)';
        item.style.opacity = '0';
        setTimeout(() => {
          item.remove();
          const container = document.getElementById('containerRedesSociais');
          const emptyMsg = document.getElementById('emptyRedesSociaisMsg');
          if (container && container.querySelectorAll('.social-dock-item').length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
          }
          verificarAlteracoesFormulario();
        }, 200);
      }
    }

    function salvarPerfilAjax(e) {
      e.preventDefault();
      const form = document.getElementById('formSalvarPerfil');
      const formData = new FormData(form);
      const btn = document.getElementById('btnSalvarPerfilSubmit');

      const fotoFileInput = document.getElementById('foto_file');
      if (fotoFileInput.files && fotoFileInput.files[0]) {
        formData.append('foto_file', fotoFileInput.files[0]);
      }

      btn.disabled = true;
      btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Salvando...';

      fetch('<?= base_url('portal/salvarPerfil') ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Salvar';

          if (data.status === 'success') {
            showToast('success', data.message);
            if (data.foto_url) {
              document.getElementById('avatarPreview').src = data.foto_url;
            }
            salvarEstadoInicial();
          } else {
            showToast('error', data.message || 'Erro ao salvar perfil.');
          }
        })
        .catch(err => {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Salvar';
          showToast('error', 'Falha na conexão com o servidor.');
        });
    }

    // Toggle Mostrar/Ocultar Senha
    function toggleVisibilidadeSenha(inputId, btn) {
      const input = document.getElementById(inputId);
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }

    // Avaliador de Força de Senha
    function avaliarForcaSenha(senha) {
      const bar = document.getElementById('passwordStrengthBar');
      const text = document.getElementById('passwordStrengthText');
      const reqLen = document.getElementById('req-len');
      const reqNum = document.getElementById('req-num');
      const reqUpper = document.getElementById('req-upper');

      if (!senha) {
        bar.style.width = '0%';
        bar.className = 'progress-bar';
        text.textContent = '-';
        text.className = 'fw-bold text-muted';
        reqLen.className = 'text-muted';
        reqNum.className = 'text-muted';
        reqUpper.className = 'text-muted';
        return;
      }

      let score = 0;
      const hasLen = senha.length >= 6;
      const hasNum = /[0-9]/.test(senha);
      const hasUpper = /[A-Z]/.test(senha) && /[a-z]/.test(senha);
      const hasSpecial = /[^A-Za-z0-9]/.test(senha);

      if (hasLen) score += 1;
      if (hasNum) score += 1;
      if (hasUpper) score += 1;
      if (hasSpecial) score += 1;
      if (senha.length >= 10) score += 1;

      // Update checklists
      reqLen.innerHTML = (hasLen ? '<i class="bi bi-check-circle-fill text-success me-1"></i>' : '<i class="bi bi-circle me-1"></i>') + 'Mínimo 6 caracteres';
      reqNum.innerHTML = (hasNum ? '<i class="bi bi-check-circle-fill text-success me-1"></i>' : '<i class="bi bi-circle me-1"></i>') + 'Contém números';
      reqUpper.innerHTML = (hasUpper ? '<i class="bi bi-check-circle-fill text-success me-1"></i>' : '<i class="bi bi-circle me-1"></i>') + 'Letras maiúsculas e minúsculas';

      if (score <= 2) {
        bar.style.width = '33%';
        bar.className = 'progress-bar bg-danger';
        text.textContent = 'Fraca';
        text.className = 'fw-bold text-danger';
      } else if (score <= 4) {
        bar.style.width = '66%';
        bar.className = 'progress-bar bg-warning';
        text.textContent = 'Média';
        text.className = 'fw-bold text-warning';
      } else {
        bar.style.width = '100%';
        bar.className = 'progress-bar bg-success';
        text.textContent = 'Forte';
        text.className = 'fw-bold text-success';
      }
    }

    function alterarSenhaAjax(e) {
      e.preventDefault();
      const form = document.getElementById('formAlterarSenha');
      const formData = new FormData(form);
      const btn = document.getElementById('btnAlterarSenhaSubmit');

      const novaSenha = document.getElementById('nova_senha').value;
      const confirma = document.getElementById('confirma_nova_senha').value;

      if (novaSenha !== confirma) {
        showToast('error', 'A confirmação de senha não coincide com a nova senha.');
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Atualizando...';

      fetch('<?= base_url('portal/alterarSenha') ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-key-fill me-1"></i> Atualizar Minha Senha';

          if (data.status === 'success') {
            showToast('success', data.message);
            form.reset();
            avaliarForcaSenha('');
            const collapseEl = document.getElementById('collapseAlterarSenha');
            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
            if (bsCollapse) bsCollapse.hide();
          } else {
            showToast('error', data.message || 'Erro ao alterar senha.');
          }
        })
        .catch(err => {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-key-fill me-1"></i> Atualizar Minha Senha';
          showToast('error', 'Falha na conexão com o servidor.');
        });
    }
  </script>
</body>

</html>