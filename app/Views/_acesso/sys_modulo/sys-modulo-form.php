<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-menu-button-wide-fill text-primary me-2"></i><?= $modulo ? 'Editar Módulo' : 'Novo Módulo / Menu' ?>
        </h3>
        <p class="text-secondary small mb-0">Configuração de rotas, ícones, categoria e permissões de ação</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('sysmodulo') ?>">Módulos</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= $modulo ? 'Editar' : 'Novo' ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-lg-8">

        <div class="card card-outline card-primary shadow-sm border-0">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-sliders me-2"></i>Parâmetros do Módulo
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" action="<?= base_url('sysmodulo/salvar') ?>">
              <input type="hidden" name="id_modulo" value="<?= $modulo ? esc($modulo->id_modulo) : '' ?>">

              <div class="row g-3 mb-3">
                <div class="col-md-8">
                  <label for="nome_modulo" class="form-label fw-semibold">Nome do Módulo (Menu)</label>
                  <input type="text" class="form-control" name="nome_modulo" id="nome_modulo" value="<?= $modulo ? esc($modulo->nome_modulo) : '' ?>" placeholder="Ex: Gestão de Membros" required>
                </div>
                <div class="col-md-4">
                  <label for="uri_modulo" class="form-label fw-semibold">URI / Rota do Módulo</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input type="text" class="form-control font-monospace" name="uri_modulo" id="uri_modulo" value="<?= $modulo ? esc($modulo->uri_modulo) : '' ?>" placeholder="membro/" required>
                  </div>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="id_categoria_modulo" class="form-label fw-semibold">Categoria no Menu</label>
                  <select class="form-select" name="id_categoria_modulo" id="id_categoria_modulo" required>
                    <option value="">Selecione uma Categoria</option>
                    <?php foreach ($categorias as $cat) { ?>
                      <option value="<?= $cat->id_categoria_modulo ?>" <?= ($modulo && $modulo->id_categoria_modulo == $cat->id_categoria_modulo) ? 'selected' : '' ?>>
                        <?= esc($cat->nome_categoria_modulo) ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="id_modulo_pai" class="form-label fw-semibold">Módulo Pai (Menu Principal)</label>
                  <select class="form-select" name="id_modulo_pai" id="id_modulo_pai">
                    <option value="">Nenhum (É um Menu Principal)</option>
                    <?php foreach ($modulosPai as $pai) { ?>
                      <option value="<?= $pai->id_modulo ?>" <?= ($modulo && $modulo->id_modulo_pai == $pai->id_modulo) ? 'selected' : '' ?>>
                        <?= esc($pai->nome_modulo) ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="icon_class_modulo" class="form-label fw-semibold">Classe do Ícone (Bootstrap Icons / FontAwesome)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-app-indicator"></i></span>
                    <input type="text" class="form-control font-monospace" name="icon_class_modulo" id="icon_class_modulo" value="<?= $modulo ? esc($modulo->icon_class_modulo) : 'bi bi-app-indicator' ?>" placeholder="bi bi-people-fill">
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="status_modulo" class="form-label fw-semibold">Status do Módulo</label>
                  <select class="form-select" name="status_modulo" id="status_modulo">
                    <option value="1" <?= (!$modulo || $modulo->status_modulo == 1) ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= ($modulo && $modulo->status_modulo == 0) ? 'selected' : '' ?>>Inativo</option>
                  </select>
                </div>
              </div>

              <!-- Ações Suportadas pelo Módulo (Tags Dinâmicas) -->
              <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <label class="form-label fw-semibold mb-0">
                    <i class="bi bi-tags-fill text-primary me-1"></i> Ações Suportadas pelo Módulo
                  </label>
                  <small class="text-muted">Ação base <code class="text-primary fw-bold">read</code> é mandatória</small>
                </div>
                <p class="text-secondary small mb-3">
                  Defina as permissões específicas que este módulo suporta. Digite o nome da ação (ex: <code>update_past</code>, <code>export</code>, <code>print</code>) e pressione <kbd>Enter</kbd> ou clique em Adicionar.
                </p>

                <!-- Input para Nova Ação -->
                <div class="input-group mb-3">
                  <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-plus-circle-fill text-primary"></i></span>
                  <input type="text" id="inputNovaAcao" class="form-control font-monospace border-start-0" placeholder="Digite uma nova ação (ex: update_past, reset_password)..." autocomplete="off">
                  <button type="button" class="btn btn-primary px-3 fw-semibold d-inline-flex align-items-center gap-1" id="btnAdicionarAcao">
                    <i class="bi bi-plus-lg"></i> Adicionar Ação
                  </button>
                </div>

                <!-- Container Visual das Tags Cadastradas -->
                <div class="p-3 bg-body-tertiary rounded-4 border" style="min-height: 80px;">
                  <div class="d-flex flex-wrap gap-2 align-items-center" id="containerTagsAcoes">
                    <?php 
                    $acoesExistentes = !empty($acoesModulo) ? $acoesModulo : ['read', 'create', 'update', 'delete'];
                    if (!in_array('read', $acoesExistentes)) {
                      array_unshift($acoesExistentes, 'read');
                    }
                    $acoesExistentes = array_values(array_unique($acoesExistentes));

                    foreach ($acoesExistentes as $acTag) { 
                      $isRead = ($acTag === 'read');
                    ?>
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill py-2 px-3 fw-bold font-monospace d-inline-flex align-items-center gap-2 shadow-xs tag-item" data-acao="<?= esc($acTag) ?>" style="font-size: 0.85rem;">
                        <i class="bi bi-key-fill"></i>
                        <span><?= esc($acTag) ?></span>
                        <?php if (!$isRead) { ?>
                          <button type="button" class="btn-close btn-close-sm p-0 ms-1 btn-remover-tag" style="font-size: 0.65rem;" title="Remover ação <?= esc($acTag) ?>" aria-label="Remover"></button>
                        <?php } else { ?>
                          <span class="badge bg-primary text-white rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 14px; height: 14px; font-size: 0.60rem;" title="Ação de leitura padrão">*</span>
                        <?php } ?>
                      </span>
                    <?php } ?>
                  </div>

                  <!-- Container para inputs hidden submetidos via form -->
                  <div id="hiddenInputsContainer">
                    <?php foreach ($acoesExistentes as $acTag) { ?>
                      <input type="hidden" name="modulo_acoes[]" value="<?= esc($acTag) ?>" id="hidden_acao_<?= esc($acTag) ?>">
                    <?php } ?>
                  </div>
                </div>

                <!-- Atalhos / Sugestões de Ações Frequentes -->
                <div class="mt-2 pt-1 d-flex align-items-center gap-2 flex-wrap">
                  <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Sugestões rápidas:</small>
                  <?php 
                  $sugestoes = ['create', 'update', 'delete', 'update_past', 'reset_password', 'export', 'print', 'linkfotos_visualizar', 'linkfotos_inserir', 'linkfotos_excluir'];
                  foreach ($sugestoes as $sug) {
                  ?>
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2 font-monospace small btn-sugestao-acao" data-sugestao="<?= $sug ?>" style="font-size: 0.72rem;">
                      + <?= $sug ?>
                    </button>
                  <?php } ?>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('sysmodulo') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                  <i class="bi bi-arrow-left me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                  <i class="bi bi-save me-1"></i> Salvar Módulo
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
document.addEventListener('DOMContentLoaded', function() {
  const inputNovaAcao        = document.getElementById('inputNovaAcao');
  const btnAdicionarAcao     = document.getElementById('btnAdicionarAcao');
  const containerTags        = document.getElementById('containerTagsAcoes');
  const hiddenContainer      = document.getElementById('hiddenInputsContainer');

  function normalizarAcao(texto) {
    if (!texto) return '';
    return texto.toLowerCase().trim()
      .replace(/[\s\-]+/g, '_')
      .replace(/[^a-z0-9_]/g, '');
  }

  function adicionarAcao(nomeAcao) {
    const acaoSlug = normalizarAcao(nomeAcao);
    if (!acaoSlug) return;

    // Evita duplicatas
    if (document.getElementById(`hidden_acao_${acaoSlug}`)) {
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show('warning', 'Ação já existe', `A ação "${acaoSlug}" já está na lista.`);
      }
      inputNovaAcao.value = '';
      return;
    }

    // Cria Tag Visual
    const tagSpan = document.createElement('span');
    tagSpan.className = 'badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill py-2 px-3 fw-bold font-monospace d-inline-flex align-items-center gap-2 shadow-xs tag-item';
    tagSpan.setAttribute('data-acao', acaoSlug);
    tagSpan.style.fontSize = '0.85rem';
    tagSpan.innerHTML = `
      <i class="bi bi-key-fill"></i>
      <span>${escapeHtml(acaoSlug)}</span>
      <button type="button" class="btn-close btn-close-sm p-0 ms-1 btn-remover-tag" style="font-size: 0.65rem;" title="Remover ação ${escapeHtml(acaoSlug)}" aria-label="Remover"></button>
    `;

    // Cria Input Hidden
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'modulo_acoes[]';
    hiddenInput.value = acaoSlug;
    hiddenInput.id = `hidden_acao_${acaoSlug}`;

    containerTags.appendChild(tagSpan);
    hiddenContainer.appendChild(hiddenInput);

    inputNovaAcao.value = '';
    inputNovaAcao.focus();
  }

  function removerAcao(acaoSlug) {
    if (acaoSlug === 'read') return; // Read é protegido

    const tag = containerTags.querySelector(`.tag-item[data-acao="${acaoSlug}"]`);
    if (tag) tag.remove();

    const hiddenInput = document.getElementById(`hidden_acao_${acaoSlug}`);
    if (hiddenInput) hiddenInput.remove();
  }

  // Event listener no botão de Adicionar
  btnAdicionarAcao?.addEventListener('click', function(e) {
    e.preventDefault();
    adicionarAcao(inputNovaAcao.value);
  });

  // Event listener para Enter, vírgula e espaço no input
  inputNovaAcao?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      adicionarAcao(this.value);
    } else if (e.key === ',' || e.key === ' ') {
      e.preventDefault();
      adicionarAcao(this.value);
    }
  });

  // Event delegation para remoção de tags
  containerTags?.addEventListener('click', function(e) {
    const btnRemover = e.target.closest('.btn-remover-tag');
    if (btnRemover) {
      e.preventDefault();
      const tagItem = btnRemover.closest('.tag-item');
      if (tagItem) {
        const acao = tagItem.getAttribute('data-acao');
        removerAcao(acao);
      }
    }
  });

  // Sugestões de ações rápidas
  document.querySelectorAll('.btn-sugestao-acao').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const sug = this.getAttribute('data-sugestao');
      adicionarAcao(sug);
    });
  });

  function escapeHtml(text) {
    if (!text) return '';
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});
</script>
