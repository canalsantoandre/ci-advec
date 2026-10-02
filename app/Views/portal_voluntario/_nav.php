<?php
  $nomeExibicao = !empty($voluntario->nickname) ? "{$voluntario->nome} ({$voluntario->nickname})" : $voluntario->nome;
  $primeiroNome = explode(' ', trim($voluntario->nome))[0] ?? 'Voluntário';
  $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($voluntario->nome) . '&background=2563eb&color=fff&size=90&bold=true';
  $fotoSrc = !empty($voluntario->foto_url) ? $voluntario->foto_url : $defaultAvatar;
  $menuAtivo = $menuAtivo ?? 'agenda';
?>

<!-- Navbar Superior do Portal -->
<header class="navbar navbar-expand bg-body-tertiary border-bottom sticky-top py-2 px-3 shadow-xs">
  <div class="container-fluid max-w-portal px-0 d-flex justify-content-between align-items-center">
    
    <!-- Brand / Voluntário -->
    <a href="<?= base_url('portal/agenda') ?>" class="navbar-brand d-flex align-items-center gap-2 text-decoration-none me-0">
      <img src="<?= base_url('logo-advec.png') ?>" alt="ADVEC" class="rounded-circle border" style="width: 34px; height: 34px; object-fit: cover;" onerror="this.src='<?= base_url('templates/AdminLTE/dist/img/AdminLTELogo.png') ?>'">
      <div class="d-flex flex-column">
        <span class="fw-bold text-body" style="font-size: 0.95rem; line-height: 1.2;">Portal do Voluntário</span>
        <a href="<?= base_url('portal/perfil') ?>" class="text-muted text-decoration-none text-truncate hover-primary" style="font-size: 0.72rem; max-width: 170px;" title="Editar Perfil">
          Olá, <strong class="text-primary"><?= esc($primeiroNome) ?></strong> <i class="bi bi-pencil-fill" style="font-size: 0.65rem;"></i>
        </a>
      </div>
    </a>

    <!-- Desktop Nav Links -->
    <nav class="d-none d-md-flex align-items-center gap-2">
      <a href="<?= base_url('portal/agenda') ?>" class="btn btn-sm <?= ($menuAtivo === 'agenda') ? 'btn-primary text-white fw-bold' : 'btn-light text-body' ?> rounded-pill px-3">
        <i class="bi bi-calendar-check me-1"></i> Minhas Escalas
      </a>
      <a href="<?= base_url('portal/metricas') ?>" class="btn btn-sm <?= ($menuAtivo === 'metricas') ? 'btn-primary text-white fw-bold' : 'btn-light text-body' ?> rounded-pill px-3">
        <i class="bi bi-trophy-fill text-warning me-1"></i> Métricas & Ranking
      </a>
      <a href="<?= base_url('portal/perfil') ?>" class="btn btn-sm <?= ($menuAtivo === 'perfil') ? 'btn-primary text-white fw-bold' : 'btn-light text-body' ?> rounded-pill px-3">
        <i class="bi bi-person-gear me-1"></i> Meu Perfil
      </a>
    </nav>

    <!-- Right Profile, Theme Switcher & Logout -->
    <div class="d-flex align-items-center gap-1.5 gap-sm-2">

      <!-- Theme Switcher (Claro / Escuro / Automático) -->
      <div class="dropdown me-1">
        <button class="btn btn-sm btn-link text-body p-1.5 d-flex align-items-center justify-content-center rounded-circle border-0 text-decoration-none" id="bd-theme-portal" type="button" aria-expanded="false" data-bs-toggle="dropdown" title="Alternar Tema (Claro / Escuro / Automático)">
          <i class="bi bi-circle-half fs-5 theme-icon-active" id="theme-icon-portal"></i>
          <span class="visually-hidden" id="bd-theme-portal-text">Alternar Tema</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border rounded-4 py-2 mt-2" aria-labelledby="bd-theme-portal-text" style="min-width: 160px;">
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fw-medium" data-bs-theme-value="light">
              <i class="bi bi-sun-fill text-warning fs-6"></i> <span>Claro</span>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fw-medium" data-bs-theme-value="dark">
              <i class="bi bi-moon-stars-fill text-info fs-6"></i> <span>Escuro</span>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fw-medium" data-bs-theme-value="auto">
              <i class="bi bi-circle-half text-secondary fs-6"></i> <span>Automático</span>
            </button>
          </li>
        </ul>
      </div>

      <!-- Avatar com link para Perfil -->
      <a href="<?= base_url('portal/perfil') ?>" class="d-flex align-items-center text-decoration-none" title="Editar Meu Perfil">
        <img src="<?= esc($fotoSrc) ?>" class="rounded-circle border shadow-xs avatar-nav-img" style="width: 36px; height: 36px; object-fit: cover;" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
      </a>
      <a href="<?= base_url('portal/logout') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1 ms-1" title="Sair do Portal">
        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline ms-1">Sair</span>
      </a>
    </div>

  </div>
</header>

<!-- Barra Inferior Flutuante (Bottom Navigation para Mobile) -->
<nav class="portal-mobile-bottom-nav d-md-none bg-body border-top shadow-lg">
  <div class="d-flex justify-content-around align-items-center py-2">
    
    <a href="<?= base_url('portal/agenda') ?>" class="bottom-nav-item <?= ($menuAtivo === 'agenda') ? 'active text-primary' : 'text-muted' ?>">
      <i class="bi bi-calendar-check fs-5 d-block"></i>
      <span class="small fw-semibold">Escalas</span>
    </a>

    <a href="<?= base_url('portal/metricas') ?>" class="bottom-nav-item <?= ($menuAtivo === 'metricas') ? 'active text-primary' : 'text-muted' ?>">
      <i class="bi bi-trophy fs-5 d-block"></i>
      <span class="small fw-semibold">Ranking</span>
    </a>

    <a href="<?= base_url('portal/perfil') ?>" class="bottom-nav-item <?= ($menuAtivo === 'perfil') ? 'active text-primary' : 'text-muted' ?>">
      <i class="bi bi-person-gear fs-5 d-block"></i>
      <span class="small fw-semibold">Perfil</span>
    </a>

    <a href="<?= base_url('portal/logout') ?>" class="bottom-nav-item text-danger">
      <i class="bi bi-box-arrow-right fs-5 d-block"></i>
      <span class="small fw-semibold">Sair</span>
    </a>

  </div>
</nav>

<style>
  .max-w-portal {
    max-width: 960px;
  }
  .portal-mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1040;
    backdrop-filter: blur(12px);
    background: rgba(255, 255, 255, 0.95);
    padding-bottom: env(safe-area-inset-bottom, 0px);
  }
  [data-bs-theme="dark"] .portal-mobile-bottom-nav {
    background: rgba(15, 23, 42, 0.95);
  }
  .bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    padding: 4px 16px;
    transition: transform 0.15s ease;
  }
  .bottom-nav-item:active {
    transform: scale(0.92);
  }
  .bottom-nav-item.active {
    font-weight: 700;
  }
  body {
    padding-bottom: calc(75px + env(safe-area-inset-bottom, 0px));
  }
  @media (min-width: 768px) {
    body {
      padding-bottom: 20px;
    }
  }
</style>

<!-- Script para Ocultar Barra de Endereço no Mobile e Suporte PWA -->
<script>
  (function() {
    // 1. Ocultar barra de endereços do navegador em navegadores móveis (Safari / Chrome)
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

    // 2. Registrar Service Worker para habilitar modo PWA / Standalone Fullscreen
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
        .then(function(reg) {
          // Service worker registrado
        }).catch(function(err) {
          console.warn('SW registration failed:', err);
        });
    }

    // 3. Gerenciamento de Tema (Claro / Escuro / Automático)
    const getStoredTheme = () => localStorage.getItem('portal_theme') || localStorage.getItem('theme');
    const setStoredTheme = theme => {
      localStorage.setItem('portal_theme', theme);
      localStorage.setItem('theme', theme);
    };

    const getPreferredTheme = () => {
      const storedTheme = getStoredTheme();
      if (storedTheme) {
        return storedTheme;
      }
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };

    const setTheme = theme => {
      let resolvedTheme = theme;
      if (theme === 'auto') {
        resolvedTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      }
      document.documentElement.setAttribute('data-bs-theme', resolvedTheme);
      
      const metaThemeColor = document.querySelector('meta[name="theme-color"]');
      if (metaThemeColor) {
        metaThemeColor.setAttribute('content', resolvedTheme === 'dark' ? '#0f172a' : '#ffffff');
      }
    };

    setTheme(getPreferredTheme());

    const showActiveTheme = (theme) => {
      const themeIconPortal = document.querySelector('#theme-icon-portal');
      if (!themeIconPortal) return;

      if (theme === 'dark') {
        themeIconPortal.className = 'bi bi-moon-stars-fill fs-5 text-info theme-icon-active';
      } else if (theme === 'light') {
        themeIconPortal.className = 'bi bi-sun-fill fs-5 text-warning theme-icon-active';
      } else {
        themeIconPortal.className = 'bi bi-circle-half fs-5 text-secondary theme-icon-active';
      }

      document.querySelectorAll('[data-bs-theme-value]').forEach(btn => {
        const val = btn.getAttribute('data-bs-theme-value');
        if (val === theme) {
          btn.classList.add('active', 'fw-bold');
        } else {
          btn.classList.remove('active', 'fw-bold');
        }
      });
    };

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      const storedTheme = getStoredTheme();
      if (storedTheme !== 'light' && storedTheme !== 'dark') {
        setTheme(getPreferredTheme());
        showActiveTheme('auto');
      }
    });

    document.addEventListener('DOMContentLoaded', () => {
      showActiveTheme(getStoredTheme() || 'auto');

      document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
          e.preventDefault();
          const theme = toggle.getAttribute('data-bs-theme-value');
          setStoredTheme(theme);
          setTheme(theme);
          showActiveTheme(theme);
        });
      });
    });
  })();
</script>

