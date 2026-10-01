<?php
$nomeUsuario = $tb_usuario->nome ?? 'Usuário';
$userLogin   = $tb_usuario->usuario ?? '';
$emailUser   = $tb_usuario->email ?? '';
$hashUser    = $tb_usuario->hash_user ?? '';

// Gerar iniciais
$partesNome = preg_split('/\s+/', trim($nomeUsuario));
$iniciais = 'US';
if (count($partesNome) >= 2) {
  $iniciais = strtoupper(mb_substr($partesNome[0], 0, 1) . mb_substr($partesNome[count($partesNome) - 1], 0, 1));
} elseif (!empty($partesNome[0])) {
  $iniciais = strtoupper(mb_substr($partesNome[0], 0, 2));
}
?>

<style>
  .card-premium-password {
    background: #ffffff;
    border-radius: 1.25rem !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.05), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  [data-bs-theme="dark"] .card-premium-password {
    background: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.35) !important;
  }

  .user-header-banner {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.12) 0%, rgba(217, 119, 6, 0.04) 100%);
    border-bottom: 1px solid rgba(245, 158, 11, 0.18);
    padding: 1.5rem 1.75rem;
  }

  [data-bs-theme="dark"] .user-header-banner {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.18) 0%, rgba(30, 41, 59, 0.4) 100%);
    border-bottom-color: rgba(245, 158, 11, 0.25);
  }

  .avatar-gold-badge {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    border: 2.5px solid #f59e0b;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25), 0 4px 10px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.15rem;
    letter-spacing: 0.5px;
    flex-shrink: 0;
  }

  .input-group-premium .input-group-text {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    color: #64748b;
    border-top-left-radius: 0.75rem !important;
    border-bottom-left-radius: 0.75rem !important;
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .input-group-premium .form-control {
    border-color: #cbd5e1;
    font-size: 0.92rem;
    padding: 0.7rem 0.95rem;
  }

  .input-group-premium .btn-toggle-password {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    color: #64748b;
    border-top-right-radius: 0.75rem !important;
    border-bottom-right-radius: 0.75rem !important;
    padding-left: 0.9rem;
    padding-right: 0.9rem;
    transition: color 0.15s ease, background-color 0.15s ease;
  }

  .input-group-premium .btn-toggle-password:hover {
    color: #0f172a;
    background-color: #f1f5f9;
  }

  [data-bs-theme="dark"] .input-group-premium .input-group-text,
  [data-bs-theme="dark"] .input-group-premium .btn-toggle-password {
    background-color: #0f172a !important;
    border-color: #334155 !important;
    color: #94a3b8 !important;
  }

  [data-bs-theme="dark"] .input-group-premium .form-control {
    background-color: #0f172a !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
  }

  .btn-submit-premium {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border-radius: 50rem !important;
    padding: 0.75rem 1.5rem !important;
    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35) !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
  }

  .btn-submit-premium:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.45) !important;
    color: #ffffff !important;
  }

  .btn-submit-premium:active {
    transform: translateY(0);
  }

  .strength-meter-bar {
    height: 4px;
    border-radius: 4px;
    transition: width 0.3s ease, background-color 0.3s ease;
    width: 0%;
  }
</style>

<!-- Content Header (Page header) -->
<div class="app-content-header pb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-1 fw-bold text-body d-flex align-items-center gap-2">
          <span class="p-2 rounded-3 bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-key-fill fs-5"></i>
          </span>
          Alterar Senha
        </h3>
        <p class="text-secondary small mb-0 ms-1">Atualização de credenciais de acesso do gestor</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end mb-0">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-decoration-none"><i class="bi bi-house-door me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Alterar Senha</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content pt-3">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-xl-5 col-lg-7 col-md-9">

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('error')) : ?>
          <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2.5 p-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger flex-shrink-0"></i>
            <div class="small fw-semibold"><?= session()->getFlashdata('error') ?></div>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')) : ?>
          <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2.5 p-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success flex-shrink-0"></i>
            <div class="small fw-semibold"><?= session()->getFlashdata('success') ?></div>
          </div>
        <?php endif; ?>

        <?php if (isset($validation)) : ?>
          <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3" role="alert">
            <div class="fw-bold small mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Atenção aos campos:</div>
            <div class="small"><?= $validation->listErrors() ?></div>
          </div>
        <?php endif; ?>

        <!-- Card Principal -->
        <div class="card card-premium-password">
          
          <!-- Banner de Usuário com Avatar Dourado -->
          <div class="user-header-banner d-flex align-items-center gap-3">
            <div class="avatar-gold-badge">
              <?= esc($iniciais) ?>
            </div>
            <div class="d-flex flex-column min-w-0">
              <span class="text-uppercase fw-bold text-warning-emphasis small" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                <i class="bi bi-shield-lock-fill me-1 text-warning"></i> Painel de Segurança
              </span>
              <h5 class="fw-bold text-body mb-0 text-truncate" style="font-size: 1.05rem;">
                <?= esc($nomeUsuario) ?>
              </h5>
              <div class="d-flex align-items-center gap-2 mt-0.5">
                <?php if (!empty($userLogin)): ?>
                  <span class="badge bg-body-tertiary text-body-secondary border px-2 py-0.5 small" style="font-size: 0.72rem;">
                    <i class="bi bi-person me-1"></i><?= esc($userLogin) ?>
                  </span>
                <?php endif; ?>
                <?php if (!empty($emailUser)): ?>
                  <span class="text-muted small text-truncate" style="font-size: 0.75rem;">
                    <?= esc($emailUser) ?>
                  </span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Formulário -->
          <div class="card-body p-4 pt-3.5">
            <form method="post" id="frm_usuario" name="frm_usuario" action="<?= base_url('/usuario/atualizarSenha') ?>" onsubmit="return validarFormSenha();">
              <input type="hidden" name="id_usuario" id="id_usuario" value="<?= esc($tb_usuario->id_usuario); ?>">
              <input type="hidden" name="txtUsuario" id="txtUsuario" value="<?= esc($userLogin); ?>">

              <!-- Senha Atual -->
              <div class="mb-3.5">
                <label for="txtSenhaAtual" class="form-label fw-semibold text-body small mb-1.5">
                  <i class="bi bi-shield-check text-warning me-1"></i> Senha Atual
                </label>
                <div class="input-group input-group-premium">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" name="txtSenhaAtual" class="form-control" id="txtSenhaAtual" placeholder="Digite sua senha atual" required autocomplete="current-password">
                  <button type="button" class="btn btn-toggle-password" onclick="toggleSenhaVisibilidade('txtSenhaAtual', this)" title="Exibir/Ocultar senha">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <!-- Nova Senha -->
              <div class="mb-3">
                <label for="txtUsuarioSenhaNova" class="form-label fw-semibold text-body small mb-1.5 d-flex justify-content-between">
                  <span><i class="bi bi-key text-warning me-1"></i> Nova Senha</span>
                  <span class="text-muted fw-normal" style="font-size: 0.72rem;">Mínimo 6 caracteres</span>
                </label>
                <div class="input-group input-group-premium">
                  <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                  <input type="password" name="txtUsuarioSenhaNova" class="form-control" id="txtUsuarioSenhaNova" placeholder="Digite a nova senha segura" required minlength="6" autocomplete="new-password" oninput="analisarForcaSenha(this.value)">
                  <button type="button" class="btn btn-toggle-password" onclick="toggleSenhaVisibilidade('txtUsuarioSenhaNova', this)" title="Exibir/Ocultar senha">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <!-- Barra de Força da Senha -->
                <div class="progress mt-1.5 bg-body-secondary" style="height: 4px;" id="progress_strength">
                  <div class="progress-bar strength-meter-bar" id="bar_strength" role="progressbar"></div>
                </div>
                <div class="d-flex justify-content-between mt-1 text-muted small" style="font-size: 0.7rem;">
                  <span id="label_strength">Força da senha</span>
                  <span id="hint_strength"></span>
                </div>
              </div>

              <!-- Confirmar Nova Senha -->
              <div class="mb-4">
                <label for="txtUsuarioSenhaConfirm" class="form-label fw-semibold text-body small mb-1.5">
                  <i class="bi bi-check2-circle text-warning me-1"></i> Confirmar Nova Senha
                </label>
                <div class="input-group input-group-premium">
                  <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                  <input type="password" name="txtUsuarioSenhaConfirm" class="form-control" id="txtUsuarioSenhaConfirm" placeholder="Repita a nova senha" required minlength="6" autocomplete="new-password" oninput="conferirSenhas()">
                  <button type="button" class="btn btn-toggle-password" onclick="toggleSenhaVisibilidade('txtUsuarioSenhaConfirm', this)" title="Exibir/Ocultar senha">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div id="match_feedback" class="mt-1 small" style="font-size: 0.72rem; display: none;"></div>
              </div>

              <!-- Botões de Ação -->
              <div class="d-flex align-items-center justify-content-between gap-3 pt-2">
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 small fw-semibold">
                  <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
                <button class="btn btn-submit-premium px-4 py-2 d-inline-flex align-items-center gap-2" type="submit" id="btnSubmitSenha">
                  <i class="bi bi-check-circle-fill"></i>
                  <span>Atualizar Senha</span>
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  function toggleSenhaVisibilidade(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      if (icon) {
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      }
    } else {
      input.type = 'password';
      if (icon) {
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }
  }

  function analisarForcaSenha(val) {
    const bar = document.getElementById('bar_strength');
    const label = document.getElementById('label_strength');
    if (!bar || !label) return;

    if (!val || val.length === 0) {
      bar.style.width = '0%';
      bar.className = 'progress-bar strength-meter-bar';
      label.textContent = 'Força da senha';
      label.className = 'text-muted';
      return;
    }

    let score = 0;
    if (val.length >= 6) score += 25;
    if (val.length >= 10) score += 25;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score += 25;
    if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) score += 25;

    bar.style.width = score + '%';

    if (score <= 25) {
      bar.className = 'progress-bar strength-meter-bar bg-danger';
      label.textContent = 'Fraca';
      label.className = 'text-danger fw-semibold';
    } else if (score <= 50) {
      bar.className = 'progress-bar strength-meter-bar bg-warning';
      label.textContent = 'Média';
      label.className = 'text-warning fw-semibold';
    } else if (score <= 75) {
      bar.className = 'progress-bar strength-meter-bar bg-info';
      label.textContent = 'Boa';
      label.className = 'text-info fw-semibold';
    } else {
      bar.className = 'progress-bar strength-meter-bar bg-success';
      label.textContent = 'Forte e Segura';
      label.className = 'text-success fw-bold';
    }

    conferirSenhas();
  }

  function conferirSenhas() {
    const nova = document.getElementById('txtUsuarioSenhaNova')?.value || '';
    const confirm = document.getElementById('txtUsuarioSenhaConfirm')?.value || '';
    const feedback = document.getElementById('match_feedback');

    if (!feedback) return;

    if (confirm.length === 0) {
      feedback.style.display = 'none';
      return;
    }

    feedback.style.display = 'block';
    if (nova === confirm) {
      feedback.className = 'mt-1 text-success fw-semibold small';
      feedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> As senhas coincidem perfeitamente.';
    } else {
      feedback.className = 'mt-1 text-danger fw-semibold small';
      feedback.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> As senhas não conferem.';
    }
  }

  function validarFormSenha() {
    const nova = document.getElementById('txtUsuarioSenhaNova')?.value || '';
    const confirm = document.getElementById('txtUsuarioSenhaConfirm')?.value || '';

    if (nova.length < 6) {
      alert('A nova senha deve ter no mínimo 6 caracteres.');
      return false;
    }

    if (nova !== confirm) {
      alert('A nova senha e a confirmação de senha não coincidem.');
      return false;
    }

    const btn = document.getElementById('btnSubmitSenha');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Atualizando...';
    }
    return true;
  }
</script>