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

  <title><?= esc($title ?? 'Meu Perfil & Métricas - ADVEC') ?></title>

  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- jQuery & Mask Plugin -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

  <style>
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
      background-color: #f1f5f9;
      min-height: 100dvh;
    }
    [data-bs-theme="dark"] body {
      background-color: #0b1120;
    }

    .profile-card {
      border: 1px solid rgba(0, 0, 0, 0.08);
      border-radius: 1.25rem;
      background: #fff;
    }
    [data-bs-theme="dark"] .profile-card {
      border-color: rgba(255, 255, 255, 0.08);
      background: #1e293b;
    }

    .avatar-wrapper {
      position: relative;
      width: 100px;
      height: 100px;
      margin: 0 auto;
    }
    .avatar-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #2563eb;
      box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
    }
    .avatar-edit-badge {
      position: absolute;
      bottom: 0;
      right: 0;
      background: #2563eb;
      color: #fff;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border: 2px solid #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      transition: transform 0.15s ease;
    }
    .avatar-edit-badge:hover {
      transform: scale(1.1);
    }

    .metric-kpi-card {
      border-radius: 1rem;
      padding: 1rem;
      text-align: center;
      transition: transform 0.15s ease;
    }
    .metric-kpi-card:hover {
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <?= view('portal_voluntario/_nav', ['voluntario' => $voluntario, 'menuAtivo' => 'perfil']) ?>

  <?php
    $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($voluntario->nome) . '&background=2563eb&color=fff&size=150&bold=true';
    $fotoSrc = !empty($voluntario->foto_url) ? $voluntario->foto_url : $defaultAvatar;
  ?>

  <div class="container max-w-portal py-3 px-3">

    <!-- Card Principal: Perfil Header -->
    <div class="profile-card shadow-sm p-4 mb-3 text-center position-relative">
      
      <!-- Avatar & Upload -->
      <div class="avatar-wrapper mb-3">
        <img src="<?= esc($fotoSrc) ?>" id="avatarPreview" class="avatar-img" alt="Foto do Voluntário" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
        <label for="foto_file" class="avatar-edit-badge" title="Alterar Foto">
          <i class="bi bi-camera-fill fs-6"></i>
        </label>
        <input type="file" id="foto_file" name="foto_file" class="d-none" accept="image/*" onchange="previewFoto(this)">
      </div>

      <h4 class="fw-bold mb-0 text-body"><?= esc($voluntario->nome) ?></h4>
      <?php if (!empty($voluntario->nickname)) { ?>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold mt-1" style="font-size: 0.8rem;">
          <i class="bi bi-tag-fill me-1"></i><?= esc($voluntario->nickname) ?>
        </span>
      <?php } ?>

      <!-- Seção Read-Only: Departamentos & Áreas de Servir -->
      <div class="mt-3 pt-3 border-top text-start">
        <label class="form-label text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
          <i class="bi bi-lock-fill me-1"></i> Departamentos & Sub-áreas Cadastradas (Gerenciado pela Liderança)
        </label>

        <div class="d-flex flex-wrap gap-1 mb-2">
          <?php if (!empty($voluntario->areas)) { ?>
            <?php foreach ($voluntario->areas as $va) { 
              $cDep = !empty($va->cor_identificacao) ? $va->cor_identificacao : '#2563eb';
            ?>
              <span class="badge rounded-pill px-3 py-2 text-white small shadow-xs" style="background-color: <?= esc($cDep) ?>;">
                <?= esc($va->nome_departamento) ?>: <strong><?= esc($va->nome_area) ?></strong>
              </span>
            <?php } ?>
          <?php } else { ?>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">Sem áreas vinculadas</span>
          <?php } ?>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center small text-muted">
          <?php
            $badgeNivel = [
              'APRENDIZ' => 'bg-info-subtle text-info border border-info-subtle',
              'JUNIOR'   => 'bg-primary-subtle text-primary border border-primary-subtle',
              'PLENO'    => 'bg-purple-subtle text-purple border border-purple-subtle',
              'SENIOR'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold'
            ];
            $nv = !empty($voluntario->nivel_conhecimento) ? strtoupper($voluntario->nivel_conhecimento) : 'JUNIOR';
          ?>
          <span>Nível: <strong class="badge <?= $badgeNivel[$nv] ?? 'bg-light text-dark' ?> rounded-pill px-2"><?= $nv ?></strong></span>
          <span>&bull;</span>
          <span>Limite de Escalas: <strong class="text-body"><?= ($voluntario->max_escalas_mes > 0) ? "{$voluntario->max_escalas_mes} por mês" : "Sem limite (Ilimitado)" ?></strong></span>
          <span>&bull;</span>
          <span>Status: <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Ativo</span></span>
        </div>
      </div>

      <div class="mt-3 pt-3 border-top text-center d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Para ver suas estatísticas e ranking, acesse a aba de métricas.</span>
        <a href="<?= base_url('portal/metricas') ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold">
          <i class="bi bi-trophy-fill me-1"></i> Ver Métricas & Ranking
        </a>
      </div>

    </div>

    <!-- Formulário de Edição de Dados Pessoais -->
    <div class="profile-card shadow-sm p-4 mb-3 bg-body">
      <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-pencil-square text-primary fs-5"></i>
        <h5 class="fw-bold mb-0 text-body">Meus Dados Pessoais</h5>
      </div>

      <form id="formSalvarPerfil" onsubmit="salvarPerfilAjax(event)">
        
        <div class="row g-3">
          
          <!-- Nome Completo (Apenas Leitura) -->
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold small text-secondary">Nome Completo</label>
            <input type="text" class="form-control bg-body-tertiary" value="<?= esc($voluntario->nome) ?>" readonly>
            <small class="text-muted" style="font-size: 0.7rem;">Para alterar seu nome oficial, consulte o líder do departamento.</small>
          </div>

          <!-- Nickname / Apelido (Editável) -->
          <div class="col-12 col-md-6">
            <label for="nickname" class="form-label fw-semibold small">
              Nickname / Como quer ser chamado
              <span class="badge bg-primary-subtle text-primary ms-1" style="font-size: 0.68rem;">Aparece na Grade</span>
            </label>
            <input type="text" id="nickname" name="nickname" class="form-control" placeholder="Ex: Taty, Gabs, Periclão..." value="<?= esc($voluntario->nickname ?? '') ?>">
          </div>

          <!-- Telefone WhatsApp (Editável) -->
          <div class="col-12 col-md-6">
            <label for="telefone_whatsapp" class="form-label fw-semibold small">
              <i class="bi bi-whatsapp text-success me-1"></i> Telefone WhatsApp <span class="text-danger">*</span>
            </label>
            <input type="tel" id="telefone_whatsapp" name="telefone_whatsapp" class="form-control" placeholder="(11) 99999-9999" value="<?= esc($voluntario->telefone_whatsapp) ?>" required>
            <small class="text-muted" style="font-size: 0.7rem;">Este número é usado como seu usuário de login no portal.</small>
          </div>

          <!-- Data de Nascimento (Editável) -->
          <div class="col-12 col-md-6">
            <label for="data_nascimento" class="form-label fw-semibold small">
              <i class="bi bi-cake2 text-warning me-1"></i> Data de Nascimento / Aniversário
            </label>
            <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" value="<?= esc($voluntario->data_nascimento) ?>">
          </div>

          <!-- Redes Sociais Dinâmicas -->
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label fw-semibold small mb-0">
                <i class="bi bi-share text-primary me-1"></i> Minhas Redes Sociais
              </label>
              <button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-0 px-2 small" onclick="adicionarRedeSocial()">
                <i class="bi bi-plus-lg me-1"></i> Adicionar Rede
              </button>
            </div>

            <div id="containerRedesSociais" class="d-flex flex-column gap-2">
              <?php if (!empty($redesSociais)) { ?>
                <?php foreach ($redesSociais as $rede) { ?>
                  <div class="input-group input-group-sm rede-item">
                    <select name="rede_plataforma[]" class="form-select" style="max-width: 140px;">
                      <option value="Instagram" <?= ($rede['plataforma'] === 'Instagram') ? 'selected' : '' ?>>Instagram</option>
                      <option value="Facebook" <?= ($rede['plataforma'] === 'Facebook') ? 'selected' : '' ?>>Facebook</option>
                      <option value="YouTube" <?= ($rede['plataforma'] === 'YouTube') ? 'selected' : '' ?>>YouTube</option>
                      <option value="TikTok" <?= ($rede['plataforma'] === 'TikTok') ? 'selected' : '' ?>>TikTok</option>
                      <option value="LinkedIn" <?= ($rede['plataforma'] === 'LinkedIn') ? 'selected' : '' ?>>LinkedIn</option>
                      <option value="Outro" <?= ($rede['plataforma'] === 'Outro') ? 'selected' : '' ?>>Outro</option>
                    </select>
                    <input type="text" name="rede_url[]" class="form-control" placeholder="@usuario ou link do perfil" value="<?= esc($rede['url']) ?>">
                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.rede-item').remove()">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                <?php } ?>
              <?php } else { ?>
                <div class="input-group input-group-sm rede-item">
                  <select name="rede_plataforma[]" class="form-select" style="max-width: 140px;">
                    <option value="Instagram" selected>Instagram</option>
                    <option value="Facebook">Facebook</option>
                    <option value="YouTube">YouTube</option>
                    <option value="TikTok">TikTok</option>
                    <option value="LinkedIn">LinkedIn</option>
                    <option value="Outro">Outro</option>
                  </select>
                  <input type="text" name="rede_url[]" class="form-control" placeholder="@seu_perfil ou link">
                  <button type="button" class="btn btn-outline-danger" onclick="this.closest('.rede-item').remove()">
                    <i class="bi bi-trash3"></i>
                  </button>
                </div>
              <?php } ?>
            </div>
          </div>

        </div>

        <div class="mt-4 pt-3 border-top text-end">
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="btnSalvarPerfilSubmit">
            <i class="bi bi-check2-circle me-1"></i> Salvar Alterações
          </button>
        </div>

      </form>
    </div>

    <!-- Card de Alteração de Senha -->
    <div class="profile-card shadow-sm p-4 mb-4 bg-body">
      <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
        <h5 class="fw-bold mb-0 text-body">Alterar Senha de Acesso</h5>
      </div>

      <form id="formAlterarSenha" onsubmit="alterarSenhaAjax(event)">
        <div class="row g-3">
          
          <div class="col-12 col-md-4">
            <label for="senha_atual" class="form-label fw-semibold small">Senha Atual <span class="text-danger">*</span></label>
            <input type="password" id="senha_atual" name="senha_atual" class="form-control" placeholder="Digite sua senha atual" required>
          </div>

          <div class="col-12 col-md-4">
            <label for="nova_senha" class="form-label fw-semibold small">Nova Senha <span class="text-danger">*</span></label>
            <input type="password" id="nova_senha" name="nova_senha" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6" required>
          </div>

          <div class="col-12 col-md-4">
            <label for="confirma_nova_senha" class="form-label fw-semibold small">Confirmar Nova Senha <span class="text-danger">*</span></label>
            <input type="password" id="confirma_nova_senha" name="confirma_nova_senha" class="form-control" placeholder="Repita a nova senha" minlength="6" required>
          </div>

        </div>

        <div class="mt-3 text-end">
          <button type="submit" class="btn btn-outline-warning rounded-pill px-4 fw-bold" id="btnAlterarSenhaSubmit">
            <i class="bi bi-key-fill me-1"></i> Atualizar Minha Senha
          </button>
        </div>
      </form>
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
    $(document).ready(function() {
      // Máscara dinâmica de telefone SP/BR
      const maskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
      };
      const options = {
        onKeyPress: function(val, e, field, options) {
          field.mask(maskBehavior.apply({}, arguments), options);
        }
      };
      $('#telefone_whatsapp').mask(maskBehavior, options);
    });

    function showToast(tipo, mensagem) {
      const toastEl = document.getElementById('portalToast');
      const toastBody = document.getElementById('portalToastBody');
      toastEl.className = 'toast align-items-center text-white border-0 shadow-lg rounded-3 ' + (tipo === 'success' ? 'bg-success' : 'bg-danger');
      toastBody.innerHTML = (tipo === 'success' ? '<i class="bi bi-check-circle-fill me-2"></i>' : '<i class="bi bi-exclamation-octagon-fill me-2"></i>') + mensagem;
      const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
      bsToast.show();
    }

    function previewFoto(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function adicionarRedeSocial() {
      const container = document.getElementById('containerRedesSociais');
      const item = document.createElement('div');
      item.className = 'input-group input-group-sm rede-item';
      item.innerHTML = `
        <select name="rede_plataforma[]" class="form-select" style="max-width: 140px;">
          <option value="Instagram">Instagram</option>
          <option value="Facebook">Facebook</option>
          <option value="YouTube">YouTube</option>
          <option value="TikTok">TikTok</option>
          <option value="LinkedIn">LinkedIn</option>
          <option value="Outro">Outro</option>
        </select>
        <input type="text" name="rede_url[]" class="form-control" placeholder="@seu_perfil ou link">
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.rede-item').remove()">
          <i class="bi bi-trash3"></i>
        </button>
      `;
      container.appendChild(item);
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
        btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Salvar Alterações';

        if (data.status === 'success') {
          showToast('success', data.message);
          if (data.foto_url) {
            document.getElementById('avatarPreview').src = data.foto_url;
          }
        } else {
          showToast('error', data.message || 'Erro ao salvar perfil.');
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Salvar Alterações';
        showToast('error', 'Falha na conexão com o servidor.');
      });
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
