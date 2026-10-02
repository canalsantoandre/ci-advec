<?php
$isEdit = !empty($recurso);
$actionUrl = base_url('resource/salvar');
?>

<!-- Content Header -->
<div class="app-content-header py-3">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold text-dark-emphasis">
          <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-plus-circle-fill' ?> text-primary me-2"></i>
          <?= $isEdit ? 'Editar Material de Apoio' : 'Novo Material de Apoio' ?>
        </h3>
        <p class="text-secondary small mb-0">Cadastro e auto-parsing de vídeos, músicas, cifras e documentos</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end mb-0 bg-transparent p-0 small">
          <li class="breadcrumb-item"><a href="<?= base_url('resource') ?>" class="text-decoration-none">Biblioteca</a></li>
          <li class="breadcrumb-item active"><?= $isEdit ? 'Editar' : 'Novo' ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main Form Content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-xl-9 col-lg-10">

        <form id="formResource" onsubmit="salvarResourceAjax(event)">
          <input type="hidden" name="id" value="<?= $isEdit ? $recurso->id : '' ?>">

          <div class="card border rounded-4 shadow-sm mb-4 bg-body">
            <div class="card-body p-4">

              <!-- Linha 1: Tipo e Departamento -->
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="resource_type_id" class="form-label fw-semibold">Tipo de Mídia <span class="text-danger">*</span></label>
                  <select name="resource_type_id" id="resource_type_id" class="form-select" required onchange="aoMudarTipoMidia(this.value)">
                    <option value="">Selecione o tipo de mídia...</option>
                    <?php foreach ($tipos as $tipo) { 
                      $selected = ($isEdit && $recurso->resource_type_id == $tipo->id) ? 'selected' : (!$isEdit && $tipo->code === 'video' ? 'selected' : '');
                    ?>
                      <option value="<?= $tipo->id ?>" data-code="<?= esc($tipo->code) ?>" <?= $selected ?>>
                        <?= esc($tipo->name) ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="department_id" class="form-label fw-semibold">Departamento / Equipe</label>
                  <select name="department_id" id="department_id" class="form-select">
                    <option value="">🌐 Disponível para Todos os Departamentos (Global)</option>
                    <?php foreach ($departamentos as $dep) { 
                      $selected = ($isEdit && $recurso->department_id == $dep->id_departamento) ? 'selected' : '';
                    ?>
                      <option value="<?= $dep->id_departamento ?>" <?= $selected ?>>
                        <?= esc($dep->nome) ?>
                      </option>
                    <?php } ?>
                  </select>
                  <div class="form-text small">Selecione se este material for específico de um departamento (ex: Louvor, Mídia, Recepção).</div>
                </div>
              </div>

              <!-- Linha 2: Título do Material -->
              <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Título do Material / Música / Documento <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control form-control-lg" placeholder="Ex: Bondade de Deus (Tom G), Escala Especial de Páscoa..." value="<?= $isEdit ? htmlspecialchars_decode(esc($recurso->title)) : '' ?>" required autocomplete="off">
              </div>

              <!-- Linha 3: URL com Auto-Parser Inteligente -->
              <div class="mb-3" id="group_url_input">
                <label for="url" class="form-label fw-semibold d-flex align-items-center justify-content-between">
                  <span>URL / Link Externo (YouTube, Spotify, Drive, PDF)</span>
                  <span class="badge bg-primary-subtle text-primary fw-normal small" id="badge_auto_parser_status">
                    <i class="bi bi-magic me-1"></i> Auto-Parser Ativo
                  </span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-body-tertiary"><i class="bi bi-link-45deg"></i></span>
                  <input type="url" name="url" id="url" class="form-control" placeholder="https://www.youtube.com/watch?v=... ou https://open.spotify.com/track/..." value="<?= $isEdit ? esc($recurso->url) : '' ?>" oninput="debounceAutoParse()" onpaste="setTimeout(debounceAutoParse, 50)" autocomplete="off">
                  <button class="btn btn-outline-secondary" type="button" onclick="executarAutoParse(true)">
                    <i class="bi bi-arrow-repeat me-1"></i> Analisar
                  </button>
                </div>
                <div class="form-text small">Cole o link completo com <code>http://</code> ou <code>https://</code>. O sistema detectará a capa e reprodutor automaticamente.</div>
              </div>

              <!-- Box de Pré-visualização do Auto-Parser em Tempo Real -->
              <div id="box_auto_parser_preview" class="border rounded-4 p-3 mb-4 bg-body-tertiary <?= ($isEdit && !empty($recurso->thumbnail_url)) ? '' : 'd-none' ?>">
                <div class="d-flex align-items-start gap-3">
                  <div class="preview-thumb-box rounded-3 overflow-hidden bg-dark position-relative flex-shrink-0" style="width: 140px; height: 90px;">
                    <img id="preview_thumb_img" src="<?= $isEdit ? esc($recurso->thumbnail_url) : '' ?>" alt="Preview" class="w-100 h-100 object-fit-cover" onerror="this.style.display='none'">
                    <span class="badge bg-primary position-absolute bottom-0 start-0 m-1" id="preview_provider_badge" style="font-size: 0.65rem;">
                      <?= $isEdit ? esc($recurso->provider) : 'YouTube' ?>
                    </span>
                  </div>
                  <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <span class="badge bg-success-subtle text-success-emphasis rounded-pill small">
                        <i class="bi bi-check-circle-fill me-1"></i> Link Identificado
                      </span>
                    </div>
                    <h6 class="fw-bold text-dark-emphasis mb-1 text-truncate" id="preview_parsed_title">Visualização Pronta</h6>
                    <p class="text-secondary small mb-0" id="preview_parsed_details">A thumbnail e o player interativo já foram configurados para consumo na agenda do voluntário.</p>
                  </div>
                </div>
              </div>

              <!-- Linha 4: Cifra / Texto Rico (Aparece se for tipo Texto ou Cifra) -->
              <div class="mb-3 <?= ($isEdit && $recurso->resource_type_id == 5) ? '' : 'd-none' ?>" id="group_content_text">
                <label for="content_text" class="form-label fw-semibold">Cifra / Letra da Música / Conteúdo em Texto</label>
                <textarea name="content_text" id="content_text" class="form-control font-monospace" rows="8" placeholder="[Intro] G  D/F#  Em  C ..."><?= $isEdit ? htmlspecialchars_decode(esc($recurso->content_text)) : '' ?></textarea>
                <div class="form-text small">Utilize fonte monoespaçada para manter o alinhamento das cifras sobre a letra.</div>
              </div>

              <!-- Linha 5: Observações / Descrição -->
              <div class="mb-4">
                <label for="description" class="form-label fw-semibold">Observações / Orientações para a Escala</label>
                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Ex: Mudar arranjo no segundo refrão; Levar partitura impressa; Tom original em G..."><?= $isEdit ? htmlspecialchars_decode(esc($recurso->description)) : '' ?></textarea>
              </div>

              <!-- Linha 6: Status -->
              <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-body-tertiary border">
                <div>
                  <h6 class="fw-bold mb-0 text-dark-emphasis">Disponibilidade do Material</h6>
                  <span class="small text-secondary">Materiais inativos não aparecem na busca rápida para novas escalas</span>
                </div>
                <div class="form-check form-switch fs-5 mb-0">
                  <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" <?= (!$isEdit || $recurso->status == 1) ? 'checked' : '' ?>>
                </div>
              </div>

            </div>

            <!-- Card Footer com Ações -->
            <div class="card-footer bg-transparent border-top p-3 d-flex align-items-center justify-content-between">
              <a href="<?= base_url('resource') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Lista
              </a>
              <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm" id="btnSalvarRecurso">
                <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Salvar Alterações' : 'Cadastrar Material' ?>
              </button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<script>
let autoParseTimer = null;

function aoMudarTipoMidia(tipoId) {
  const select = document.getElementById('resource_type_id');
  const selectedOption = select.options[select.selectedIndex];
  const code = selectedOption ? selectedOption.getAttribute('data-code') : '';

  const groupText = document.getElementById('group_content_text');
  const groupUrl = document.getElementById('group_url_input');

  if (code === 'text' || String(tipoId) === '5') {
    groupText.classList.remove('d-none');
  } else {
    groupText.classList.add('d-none');
  }
}

function debounceAutoParse() {
  clearTimeout(autoParseTimer);
  autoParseTimer = setTimeout(() => executarAutoParse(false), 400);
}

function executarAutoParse(forceAlert) {
  const urlInput = document.getElementById('url');
  const url = urlInput.value.trim();
  const typeId = document.getElementById('resource_type_id').value;

  if (!url) {
    document.getElementById('box_auto_parser_preview').classList.add('d-none');
    return;
  }

  const badgeStatus = document.getElementById('badge_auto_parser_status');
  if (badgeStatus) {
    badgeStatus.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width: 0.75rem; height: 0.75rem;"></span> Identificando...';
  }

  fetch(`<?= base_url('resource/autoParseUrl') ?>?url=${encodeURIComponent(url)}&type_id=${typeId}`)
    .then(r => r.json())
    .then(res => {
      if (badgeStatus) {
        badgeStatus.innerHTML = '<i class="bi bi-magic me-1"></i> Auto-Parser Ativo';
      }

      if (res.valid && res.data) {
        const d = res.data;
        const titleInput = document.getElementById('title');
        const contentTextInput = document.getElementById('content_text');
        const typeSelect = document.getElementById('resource_type_id');
        let notificacoes = [];

        // 1. Preenche Título Automaticamente (se estiver vazio, se clicou em Analisar, ou se o usuário acabou de colar a URL)
        if (d.title && (!titleInput.value.trim() || forceAlert || document.activeElement === urlInput)) {
          titleInput.value = d.title;
          titleInput.classList.add('is-valid');
          setTimeout(() => titleInput.classList.remove('is-valid'), 2000);
          notificacoes.push('Título');
        }

        // 2. Ajusta Tipo de Mídia se sugerido
        if (d.suggested_type_id) {
          if (!typeSelect.value || typeSelect.value == '1' || forceAlert || d.suggested_type_id === 5) {
            typeSelect.value = d.suggested_type_id;
            aoMudarTipoMidia(d.suggested_type_id);
            notificacoes.push('Tipo de Mídia');
          }
        }

        // 3. Preenche Conteúdo / Cifra / Letra se veio do Cifra Club ou texto
        if (d.content_text) {
          contentTextInput.value = d.content_text;
          const groupText = document.getElementById('group_content_text');
          if (groupText) groupText.classList.remove('d-none');
          contentTextInput.classList.add('is-valid');
          setTimeout(() => contentTextInput.classList.remove('is-valid'), 2000);
          notificacoes.push('Cifra e Letra Completa');
        }

        // 4. Atualiza Box de Pré-visualização
        document.getElementById('box_auto_parser_preview').classList.remove('d-none');
        document.getElementById('preview_thumb_img').src = d.thumbnail_url || '';
        document.getElementById('preview_thumb_img').style.display = d.thumbnail_url ? 'block' : 'none';
        
        let providerNome = (d.provider || 'Link').toUpperCase();
        if (d.provider === 'cifraclub') providerNome = 'Cifra Club';
        document.getElementById('preview_provider_badge').textContent = providerNome;

        let providerMsg = 'Link Externo identificado';
        if (d.provider === 'youtube') providerMsg = 'Vídeo do YouTube detectado';
        else if (d.provider === 'spotify') providerMsg = 'Áudio do Spotify detectado';
        else if (d.provider === 'cifraclub') providerMsg = 'Cifra e Letra do Cifra Club capturadas';
        else if (d.provider === 'drive') providerMsg = 'Documento do Google Drive detectado';
        else if (d.provider === 'pdf') providerMsg = 'Arquivo PDF detectado';

        document.getElementById('preview_parsed_title').textContent = d.title || providerMsg;
        document.getElementById('preview_parsed_details').textContent = `${providerMsg}. Dados alimentados automaticamente no formulário.`;
        
        const feedbackMsg = notificacoes.length > 0 
          ? `Identificado com sucesso! Campos preenchidos: ${notificacoes.join(', ')}.` 
          : 'Metadados e reprodução identificados!';

        if (forceAlert && typeof USToast !== 'undefined') {
          USToast.show('success', 'Auto-Parser', feedbackMsg);
        } else if (forceAlert && typeof usShowToast === 'function') {
          usShowToast('success', 'Auto-Parser', feedbackMsg);
        }
      } else {
        if (forceAlert && typeof USToast !== 'undefined') {
          USToast.show('warning', 'Aviso', res.message || 'URL genérica.');
        } else if (forceAlert && typeof usShowToast === 'function') {
          usShowToast('warning', 'Aviso', res.message || 'URL genérica.');
        }
      }
    })
    .catch(() => {
      if (badgeStatus) {
        badgeStatus.innerHTML = '<i class="bi bi-magic me-1"></i> Auto-Parser Ativo';
      }
    });
}

function salvarResourceAjax(e) {
  e.preventDefault();
  const form = document.getElementById('formResource');
  const formData = new FormData(form);
  const btn = document.getElementById('btnSalvarRecurso');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

  fetch('<?= base_url('resource/salvar') ?>', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Salvar Material';

    if (data.status === 'success') {
      if (typeof USToast !== 'undefined') {
        USToast.show('success', 'Sucesso', data.message);
      } else if (typeof usShowToast === 'function') {
        usShowToast('success', 'Sucesso', data.message);
      }
      setTimeout(() => window.location.href = '<?= base_url('resource') ?>', 700);
    } else {
      if (typeof USToast !== 'undefined') {
        USToast.show('error', 'Erro', data.message || 'Erro ao salvar.');
      } else if (typeof usShowToast === 'function') {
        usShowToast('error', 'Erro', data.message || 'Erro ao salvar.');
      }
    }
  })
  .catch(err => {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Salvar Material';
    if (typeof USToast !== 'undefined') {
      USToast.show('error', 'Erro de Conexão', 'Falha ao salvar material.');
    }
  });
}
</script>
