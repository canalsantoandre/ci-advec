<?php
$isEdit = !empty($colecao);
$recursosAtuais = ($isEdit && isset($colecao->resources)) ? $colecao->resources : [];
$totalItens = count($recursosAtuais);
?>

<style>
  /* ============================================================
     ESTILOS PREMIUM - REPERTÓRIOS & COLEÇÕES (SPOTIFY / APPLE MUSIC)
     ============================================================ */
  .playlist-hero-card {
    border-radius: 24px;
    border: 1px solid rgba(0, 0, 0, 0.07);
    background: var(--bs-body-bg);
    box-shadow: 0 12px 35px -10px rgba(0, 0, 0, 0.08);
    overflow: hidden;
  }
  [data-bs-theme="dark"] .playlist-hero-card {
    border-color: rgba(255, 255, 255, 0.08);
    background: #14171d;
    box-shadow: 0 16px 40px -10px rgba(0, 0, 0, 0.4);
  }

  .playlist-cover-box {
    width: 130px;
    height: 130px;
    min-width: 130px;
    border-radius: 18px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #1e1b4b 100%);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.35);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    position: relative;
    overflow: hidden;
  }
  .playlist-cover-box::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
    pointer-events: none;
  }

  .collection-track-item {
    border-radius: 14px;
    padding: 10px 14px;
    border: 1px solid rgba(0, 0, 0, 0.06);
    background: var(--bs-body-bg);
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    user-select: none;
  }
  .collection-track-item:hover {
    transform: translateY(-2px);
    border-color: rgba(var(--bs-primary-rgb), 0.35);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
    background: rgba(var(--bs-primary-rgb), 0.025);
  }
  .collection-track-item.dragging {
    opacity: 0.4;
    border: 2px dashed var(--bs-primary) !important;
    transform: scale(0.98);
  }
  [data-bs-theme="dark"] .collection-track-item {
    border-color: rgba(255, 255, 255, 0.08);
    background: #181b20;
  }
  [data-bs-theme="dark"] .collection-track-item:hover {
    border-color: rgba(99, 102, 241, 0.5);
    background: #1f232b;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
  }

  .music-cover-art {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }

  .cover-gradient-video {
    background: linear-gradient(135deg, #ff0033 0%, #b3001e 100%);
    color: #fff;
  }
  .cover-gradient-audio {
    background: linear-gradient(135deg, #1db954 0%, #107c34 100%);
    color: #fff;
  }
  .cover-gradient-spotify {
    background: linear-gradient(135deg, #1db954 0%, #191414 100%);
    color: #fff;
  }
  .cover-gradient-pdf {
    background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
    color: #fff;
  }
  .cover-gradient-link {
    background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
    color: #fff;
  }
  .cover-gradient-text {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    color: #fff;
  }

  .badge-global-origin {
    background: rgba(16, 185, 129, 0.12);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
    font-weight: 600;
  }
  [data-bs-theme="dark"] .badge-global-origin {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.35);
  }

  .badge-dep-origin {
    background: rgba(99, 102, 241, 0.1);
    color: #4f46e5;
    border: 1px solid rgba(99, 102, 241, 0.2);
    font-weight: 600;
  }
  [data-bs-theme="dark"] .badge-dep-origin {
    background: rgba(99, 102, 241, 0.2);
    color: #a5b4fc;
    border-color: rgba(99, 102, 241, 0.35);
  }

  .music-modal-content {
    border-radius: 20px !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3) !important;
    backdrop-filter: blur(16px);
  }

  .pill-filter-btn {
    border-radius: 50rem;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 5px 14px;
    border: 1px solid rgba(0, 0, 0, 0.08);
    background: var(--bs-body-bg);
    color: var(--bs-body-color);
    transition: all 0.2s ease;
  }
  .pill-filter-btn:hover, .pill-filter-btn.active {
    background: var(--bs-primary);
    color: #fff;
    border-color: var(--bs-primary);
    box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.3);
  }
  [data-bs-theme="dark"] .pill-filter-btn {
    border-color: rgba(255, 255, 255, 0.1);
    background: #181b20;
  }

  .btn-attach-music {
    border-radius: 50rem;
    padding: 5px 14px;
    font-size: 0.80rem;
    font-weight: 600;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .btn-attach-music:hover {
    transform: scale(1.05);
  }

  .track-index-badge {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    background: rgba(0,0,0,0.06);
    color: var(--bs-secondary-color);
  }
  [data-bs-theme="dark"] .track-index-badge {
    background: rgba(255,255,255,0.08);
  }
</style>

<!-- Content Header -->
<div class="app-content-header py-3">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-3 p-2 bg-primary text-white shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-folder-plus' ?> fs-5"></i>
          </div>
          <div>
            <h3 class="mb-0 fw-bold text-dark-emphasis" style="font-size: 1.3rem;">
              <?= $isEdit ? 'Editar Repertório / Coleção' : 'Novo Repertório / Coleção' ?>
            </h3>
            <p class="text-secondary small mb-0">Agrupe músicas, cifras e vídeos para anexar de uma só vez nas escalas</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end mb-0 bg-transparent p-0 small">
          <li class="breadcrumb-item"><a href="<?= base_url('resource') ?>" class="text-decoration-none">Biblioteca</a></li>
          <li class="breadcrumb-item active"><?= $isEdit ? 'Editar Repertório' : 'Novo Repertório' ?></li>
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

        <form id="formCollection" onsubmit="salvarColecaoAjax(event)">
          <input type="hidden" name="id" value="<?= $isEdit ? $colecao->id : '' ?>">
          <input type="hidden" name="resource_ids" id="collection_resource_ids" value="">

          <div class="playlist-hero-card mb-4 p-4">

            <!-- Hero da Coleção / Playlist Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-4 mb-4 pb-4 border-bottom">
              <div class="playlist-cover-box shadow">
                <i class="bi bi-music-note-list display-4 mb-1"></i>
                <span class="small fw-bold" style="font-size: 0.70rem; letter-spacing: 0.5px;" id="hero_tracks_counter"><?= $totalItens ?> FAIXA(S)</span>
              </div>

              <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.70rem; letter-spacing: 0.5px;">REPERTÓRIO / PLAYLIST</span>
                  <span class="text-secondary small">• Biblioteca de Apoio</span>
                </div>

                <div class="mb-3">
                  <label for="title" class="form-label fw-bold small text-secondary mb-1">Título do Repertório / Playlist <span class="text-danger">*</span></label>
                  <input type="text" name="title" id="title" class="form-control form-control-lg fw-bold rounded-3" placeholder="Ex: Repertório Domingo Manhã - Especial de Louvor" value="<?= $isEdit ? htmlspecialchars_decode(esc($colecao->title)) : '' ?>" required autocomplete="off" style="font-size: 1.15rem;">
                </div>

                <div class="row g-2">
                  <div class="col-md-5">
                    <label for="department_id" class="form-label fw-semibold small text-secondary mb-1">Departamento / Disponibilidade</label>
                    <select name="department_id" id="department_id" class="form-select form-select-sm rounded-pill px-3 fw-medium">
                      <option value="">🌐 Global (Todos os Departamentos)</option>
                      <?php foreach ($departamentos as $dep) { 
                        $selected = ($isEdit && $colecao->department_id == $dep->id_departamento) ? 'selected' : '';
                      ?>
                        <option value="<?= $dep->id_departamento ?>" <?= $selected ?>>
                          📍 <?= esc($dep->nome) ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="col-md-7">
                    <label for="description" class="form-label fw-semibold small text-secondary mb-1">Descrição / Observações</label>
                    <input type="text" name="description" id="description" class="form-control form-control-sm rounded-pill px-3" placeholder="Ex: Ordem das músicas para o culto matinal..." value="<?= $isEdit ? htmlspecialchars_decode(esc($colecao->description)) : '' ?>">
                  </div>
                </div>
              </div>
            </div>

            <!-- Seção de Faixas / Materiais Ordenáveis (Spotify Tracklist Style) -->
            <div class="mb-3">
              <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div>
                  <h5 class="fw-bold mb-0 text-dark-emphasis d-flex align-items-center gap-2">
                    <i class="bi bi-disc-fill text-primary"></i>
                    Faixas & Materiais Inclusos
                    <span class="badge bg-primary rounded-pill small ms-1" id="badge_total_tracks"><?= $totalItens ?></span>
                  </h5>
                  <span class="small text-secondary">Arraste os itens pelo ícone à esquerda para ordenar a sequência de execução</span>
                </div>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 shadow-sm fw-semibold" onclick="abrirModalAdicionarMateriais()">
                  <i class="bi bi-plus-circle-fill me-1.5"></i> Adicionar Materiais
                </button>
              </div>

              <!-- Container de Faixas com Drag and Drop -->
              <div id="sortable_collection_resources" class="d-flex flex-column gap-2 p-2 rounded-4 bg-body-tertiary border" style="min-height: 140px;">
                <?php if (empty($recursosAtuais)) { ?>
                  <div id="empty_collection_notice" class="text-center py-5 text-secondary small">
                    <div class="rounded-circle bg-body p-3 d-inline-flex mb-2 shadow-xs border">
                      <i class="bi bi-music-note-beamed display-5 text-muted"></i>
                    </div>
                    <h6 class="fw-bold text-dark-emphasis mb-1">Seu repertório ainda está vazio</h6>
                    <p class="text-secondary small mb-3">Adicione músicas, cifras, vídeos ou documentos da biblioteca para montar a playlist.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-semibold" onclick="abrirModalAdicionarMateriais()">
                      <i class="bi bi-plus-lg me-1"></i> Adicionar Músicas
                    </button>
                  </div>
                <?php } else { 
                  $idx = 1;
                  foreach ($recursosAtuais as $res) { 
                    $isGlobal = ($res->department_id === null || $res->department_id == 0);
                    $titleClean = htmlspecialchars_decode($res->title);
                ?>
                    <div class="collection-track-item d-flex align-items-center justify-content-between shadow-xs" data-id="<?= $res->id ?>" draggable="true">
                      <div class="d-flex align-items-center gap-2.5 min-w-0">
                        <span class="drag-handle text-secondary fs-5" style="cursor: grab;" title="Arrastar para reordenar">
                          <i class="bi bi-grip-vertical"></i>
                        </span>
                        <div class="track-index-badge"><?= $idx++ ?></div>
                        
                        <!-- Capa / Artwork -->
                        <?php 
                          $gradClass = 'cover-gradient-audio';
                          $iconClass = $res->type_icon ?? 'bi bi-music-note-beamed';
                          if (($res->provider ?? '') === 'youtube' || ($res->type_code ?? '') === 'video') { $gradClass = 'cover-gradient-video'; $iconClass = 'bi bi-play-circle-fill'; }
                          elseif (($res->provider ?? '') === 'spotify') { $gradClass = 'cover-gradient-spotify'; $iconClass = 'bi bi-spotify'; }
                          elseif (($res->type_code ?? '') === 'pdf') { $gradClass = 'cover-gradient-pdf'; $iconClass = 'bi bi-file-earmark-pdf-fill'; }
                          elseif (($res->type_code ?? '') === 'link') { $gradClass = 'cover-gradient-link'; $iconClass = 'bi bi-link-45deg'; }
                          elseif (($res->type_code ?? '') === 'text') { $gradClass = 'cover-gradient-text'; $iconClass = 'bi bi-file-text-fill'; }
                        ?>
                        <div class="music-cover-art <?= $gradClass ?> flex-shrink-0">
                          <?php if (!empty($res->thumbnail_url) && in_array($res->provider, ['youtube', 'spotify'])) { ?>
                            <img src="<?= esc($res->thumbnail_url) ?>" alt="Capa" style="width: 100%; height: 100%; object-fit: cover;">
                          <?php } else { ?>
                            <i class="<?= esc($iconClass) ?> fs-5"></i>
                          <?php } ?>
                        </div>

                        <div class="min-w-0">
                          <div class="d-flex align-items-center gap-1.5 flex-wrap mb-0.5">
                            <h6 class="fw-bold mb-0 text-dark-emphasis text-truncate" style="font-size: 0.90rem;"><?= esc($titleClean) ?></h6>
                            <?php if ($isGlobal) { ?>
                              <span class="badge badge-global-origin rounded-pill small"><i class="bi bi-globe2 me-1"></i>Global</span>
                            <?php } else { ?>
                              <span class="badge badge-dep-origin rounded-pill small"><i class="bi bi-building me-1"></i><?= esc($res->department_name ?? 'Departamento') ?></span>
                            <?php } ?>
                            <?php if (($res->provider ?? '') === 'youtube') { ?>
                              <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.65rem;">YouTube</span>
                            <?php } elseif (($res->provider ?? '') === 'spotify') { ?>
                              <span class="badge bg-success text-white rounded-pill" style="font-size: 0.65rem;">Spotify</span>
                            <?php } ?>
                          </div>
                          <div class="text-secondary small text-truncate" style="font-size: 0.78rem;">
                            <?= esc($res->description ?: ($res->url ?: 'Material incluso no repertório')) ?>
                          </div>
                        </div>
                      </div>

                      <div class="d-flex align-items-center gap-1 flex-shrink-0 ms-2">
                        <button type="button" class="btn btn-sm btn-light border-0 rounded-circle p-2 d-flex align-items-center justify-content-center shadow-xs text-secondary" onclick="moverItem(this, -1)" title="Mover para cima" style="width: 32px; height: 32px;">
                          <i class="bi bi-chevron-up"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border-0 rounded-circle p-2 d-flex align-items-center justify-content-center shadow-xs text-secondary" onclick="moverItem(this, 1)" title="Mover para baixo" style="width: 32px; height: 32px;">
                          <i class="bi bi-chevron-down"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2 d-flex align-items-center justify-content-center shadow-xs ms-1" onclick="removerItemColecao(this)" title="Remover da coleção" style="width: 32px; height: 32px;">
                          <i class="bi bi-trash3-fill"></i>
                        </button>
                      </div>
                    </div>
                <?php } 
                } ?>
              </div>
            </div>

            <!-- Footer com Ações -->
            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
              <a href="<?= base_url('resource') ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Cancelar / Voltar
              </a>
              <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-semibold" id="btnSalvarColecao">
                <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Salvar Alterações' : 'Criar Coleção' ?>
              </button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<!-- Modal de Seleção de Recursos para a Coleção (Estilo Spotify) -->
<div class="modal fade" id="modalPickerRecursos" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content music-modal-content shadow-lg border-0">
      <div class="modal-header border-0 pb-1 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-3 p-2 bg-primary text-white shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="bi bi-music-note-list fs-5"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-1.5 mb-0">
              <span class="badge bg-primary-subtle text-primary rounded-pill px-2" style="font-size: 0.70rem; font-weight: 700; letter-spacing: 0.5px;">BIBLIOTECA</span>
            </div>
            <h5 class="modal-title fw-bold text-dark-emphasis mb-0" style="font-size: 1.15rem;">
              Adicionar Materiais ao Repertório
            </h5>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body py-3">
        <!-- Barra de Busca Spotify Style -->
        <div class="position-relative mb-3">
          <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 rounded-start-pill ps-3 text-muted">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="filtro_picker_busca" class="form-control bg-body-tertiary border-start-0 rounded-end-pill pe-3" placeholder="Buscar por título, autor, cifra ou link..." oninput="filtrarPickerModal()">
          </div>
        </div>

        <!-- Filtros Rápidos por Tipo de Mídia -->
        <div class="d-flex align-items-center gap-1.5 overflow-x-auto pb-2 mb-2" id="picker_modal_filter_pills">
          <button type="button" class="btn pill-filter-btn active" data-type="" onclick="aplicarFiltroTipoPickerModal('')">
            <i class="bi bi-grid-fill me-1"></i> Todos
          </button>
          <button type="button" class="btn pill-filter-btn" data-type="audio" onclick="aplicarFiltroTipoPickerModal('audio')">
            <i class="bi bi-music-note-beamed text-success me-1"></i> Músicas & Áudios
          </button>
          <button type="button" class="btn pill-filter-btn" data-type="video" onclick="aplicarFiltroTipoPickerModal('video')">
            <i class="bi bi-play-circle-fill text-danger me-1"></i> Vídeos
          </button>
          <button type="button" class="btn pill-filter-btn" data-type="pdf" onclick="aplicarFiltroTipoPickerModal('pdf')">
            <i class="bi bi-file-earmark-pdf-fill text-warning me-1"></i> Cifras / PDFs
          </button>
          <button type="button" class="btn pill-filter-btn" data-type="text" onclick="aplicarFiltroTipoPickerModal('text')">
            <i class="bi bi-file-text-fill text-secondary me-1"></i> Letras / Textos
          </button>
        </div>

        <!-- Lista de Recursos Disponíveis -->
        <div id="picker_resources_list" class="d-flex flex-column gap-2 overflow-auto p-1" style="max-height: 380px;">
          <?php foreach ($recursosDisponiveis as $r) { 
            $isGlobal = ($r->department_id === null || $r->department_id == 0);
            $cleanTitle = htmlspecialchars_decode($r->title);
            $cleanDesc = htmlspecialchars_decode($r->description ?: ($r->url ?: 'Disponível na biblioteca'));

            $gradClass = 'cover-gradient-audio';
            $iconClass = $r->type_icon ?? 'bi bi-music-note-beamed';
            if (($r->provider ?? '') === 'youtube' || ($r->type_code ?? '') === 'video') { $gradClass = 'cover-gradient-video'; $iconClass = 'bi bi-play-circle-fill'; }
            elseif (($r->provider ?? '') === 'spotify') { $gradClass = 'cover-gradient-spotify'; $iconClass = 'bi bi-spotify'; }
            elseif (($r->type_code ?? '') === 'pdf') { $gradClass = 'cover-gradient-pdf'; $iconClass = 'bi bi-file-earmark-pdf-fill'; }
            elseif (($r->type_code ?? '') === 'link') { $gradClass = 'cover-gradient-link'; $iconClass = 'bi bi-link-45deg'; }
            elseif (($r->type_code ?? '') === 'text') { $gradClass = 'cover-gradient-text'; $iconClass = 'bi bi-file-text-fill'; }
          ?>
            <div class="collection-track-item d-flex align-items-center justify-content-between picker-item-row" data-id="<?= $r->id ?>" data-title="<?= esc(strtolower($cleanTitle)) ?>" data-type="<?= esc(strtolower($r->type_code ?? '')) ?>" data-provider="<?= esc(strtolower($r->provider ?? '')) ?>">
              <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="music-cover-art <?= $gradClass ?> flex-shrink-0">
                  <?php if (!empty($r->thumbnail_url) && in_array($r->provider, ['youtube', 'spotify'])) { ?>
                    <img src="<?= esc($r->thumbnail_url) ?>" alt="Capa" style="width: 100%; height: 100%; object-fit: cover;">
                  <?php } else { ?>
                    <i class="<?= esc($iconClass) ?> fs-5"></i>
                  <?php } ?>
                </div>

                <div class="min-w-0">
                  <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                    <h6 class="fw-bold mb-0 text-dark-emphasis text-truncate" style="font-size: 0.90rem;"><?= esc($cleanTitle) ?></h6>
                    <?php if ($isGlobal) { ?>
                      <span class="badge badge-global-origin rounded-pill small"><i class="bi bi-globe2 me-1"></i>Global</span>
                    <?php } else { ?>
                      <span class="badge badge-dep-origin rounded-pill small"><i class="bi bi-building me-1"></i><?= esc($r->department_name ?? 'Departamento') ?></span>
                    <?php } ?>
                    <?php if (($r->provider ?? '') === 'youtube') { ?>
                      <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.65rem;">YouTube</span>
                    <?php } elseif (($r->provider ?? '') === 'spotify') { ?>
                      <span class="badge bg-success text-white rounded-pill" style="font-size: 0.65rem;">Spotify</span>
                    <?php } ?>
                    <?php if (!empty($r->type_name)) { ?>
                      <span class="badge bg-body-secondary text-secondary rounded-pill" style="font-size: 0.65rem;"><?= esc($r->type_name) ?></span>
                    <?php } ?>
                  </div>
                  <div class="text-secondary small text-truncate" style="font-size: 0.78rem;">
                    <?= esc($cleanDesc) ?>
                  </div>
                </div>
              </div>

              <button type="button" class="btn btn-sm btn-primary btn-attach-music flex-shrink-0 ms-2" id="btn_picker_add_<?= $r->id ?>" onclick="adicionarRecursoNaColecao(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') ?>)">
                <i class="bi bi-plus-lg me-1"></i> Adicionar
              </button>
            </div>
          <?php } ?>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Concluir</button>
      </div>
    </div>
  </div>
</div>

<script>
let modalPickerInstance = null;
let filtroTipoPickerModalAtual = '';

document.addEventListener('DOMContentLoaded', function() {
  initDragAndDrop();
  atualizarInputHiddenIds();
});

function safeDecode(text) {
  if (!text) return '';
  const doc = new DOMParser().parseFromString(String(text), 'text/html');
  return doc.body.textContent || '';
}

function escapeHtml(text) {
  if (!text) return '';
  const clean = safeDecode(text);
  return clean
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function getMediaCoverHtml(r) {
  if (r.thumbnail_url && (r.provider === 'youtube' || r.provider === 'spotify')) {
    return `
      <div class="music-cover-art flex-shrink-0 position-relative">
        <img src="${escapeHtml(r.thumbnail_url)}" alt="Capa" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
    `;
  }
  let gradClass = 'cover-gradient-audio';
  let iconClass = r.type_icon || 'bi bi-music-note-beamed';
  const code = (r.type_code || '').toLowerCase();
  const provider = (r.provider || '').toLowerCase();

  if (provider === 'youtube' || code === 'video') { gradClass = 'cover-gradient-video'; iconClass = 'bi bi-play-circle-fill'; }
  else if (provider === 'spotify') { gradClass = 'cover-gradient-spotify'; iconClass = 'bi bi-spotify'; }
  else if (code === 'pdf') { gradClass = 'cover-gradient-pdf'; iconClass = 'bi bi-file-earmark-pdf-fill'; }
  else if (code === 'link') { gradClass = 'cover-gradient-link'; iconClass = 'bi bi-link-45deg'; }
  else if (code === 'text') { gradClass = 'cover-gradient-text'; iconClass = 'bi bi-file-text-fill'; }

  return `
    <div class="music-cover-art ${gradClass} flex-shrink-0">
      <i class="${iconClass} fs-5"></i>
    </div>
  `;
}

function abrirModalAdicionarMateriais() {
  if (!modalPickerInstance) {
    modalPickerInstance = new bootstrap.Modal(document.getElementById('modalPickerRecursos'));
  }
  modalPickerInstance.show();
}

function aplicarFiltroTipoPickerModal(typeCode) {
  filtroTipoPickerModalAtual = typeCode;
  document.querySelectorAll('#picker_modal_filter_pills .pill-filter-btn').forEach(btn => {
    btn.classList.toggle('active', btn.getAttribute('data-type') === typeCode);
  });
  filtrarPickerModal();
}

function filtrarPickerModal() {
  const termo = (document.getElementById('filtro_picker_busca').value || '').trim().toLowerCase();
  
  document.querySelectorAll('#picker_resources_list .picker-item-row').forEach(row => {
    const title = (row.getAttribute('data-title') || '').toLowerCase();
    const type = (row.getAttribute('data-type') || '').toLowerCase();
    const prov = (row.getAttribute('data-provider') || '').toLowerCase();

    const matchText = title.includes(termo);
    let matchType = true;

    if (filtroTipoPickerModalAtual !== '') {
      if (filtroTipoPickerModalAtual === 'audio' && type !== 'audio' && prov !== 'spotify') matchType = false;
      if (filtroTipoPickerModalAtual === 'video' && type !== 'video' && prov !== 'youtube') matchType = false;
      if (filtroTipoPickerModalAtual === 'pdf' && type !== 'pdf') matchType = false;
      if (filtroTipoPickerModalAtual === 'text' && type !== 'text') matchType = false;
    }

    row.style.display = (matchText && matchType) ? 'flex' : 'none';
  });
}

function adicionarRecursoNaColecao(res) {
  const container = document.getElementById('sortable_collection_resources');
  document.getElementById('empty_collection_notice')?.remove();

  // Se já foi adicionado
  if (container.querySelector(`[data-id="${res.id}"]`)) {
    if (typeof USToast !== 'undefined') {
      USToast.show('warning', 'Já Adicionado', 'Este material já está presente na coleção.');
    }
    return;
  }

  const isGlobal = (res.department_id === null || parseInt(res.department_id) === 0);
  const originBadge = isGlobal
    ? `<span class="badge badge-global-origin rounded-pill small"><i class="bi bi-globe2 me-1"></i>Global</span>`
    : `<span class="badge badge-dep-origin rounded-pill small"><i class="bi bi-building me-1"></i>${escapeHtml(res.department_name || 'Departamento')}</span>`;

  const coverHtml = getMediaCoverHtml(res);
  const currentCount = container.querySelectorAll('.collection-track-item').length + 1;

  const div = document.createElement('div');
  div.className = 'collection-track-item d-flex align-items-center justify-content-between shadow-xs';
  div.setAttribute('data-id', res.id);
  div.setAttribute('draggable', 'true');

  div.innerHTML = `
    <div class="d-flex align-items-center gap-2.5 min-w-0">
      <span class="drag-handle text-secondary fs-5" style="cursor: grab;" title="Arrastar para reordenar">
        <i class="bi bi-grip-vertical"></i>
      </span>
      <div class="track-index-badge">${currentCount}</div>
      ${coverHtml}
      <div class="min-w-0">
        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-0.5">
          <h6 class="fw-bold mb-0 text-dark-emphasis text-truncate" style="font-size: 0.90rem;">${escapeHtml(res.title)}</h6>
          ${originBadge}
          ${res.provider === 'youtube' ? '<span class="badge bg-danger text-white rounded-pill" style="font-size: 0.65rem;">YouTube</span>' : ''}
          ${res.provider === 'spotify' ? '<span class="badge bg-success text-white rounded-pill" style="font-size: 0.65rem;">Spotify</span>' : ''}
          ${res.type_name ? `<span class="badge bg-body-secondary text-secondary rounded-pill" style="font-size: 0.65rem;">${escapeHtml(res.type_name)}</span>` : ''}
        </div>
        <div class="text-secondary small text-truncate" style="font-size: 0.78rem;">
          ${escapeHtml(res.description || res.url || 'Material incluso no repertório')}
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-1 flex-shrink-0 ms-2">
      <button type="button" class="btn btn-sm btn-light border-0 rounded-circle p-2 d-flex align-items-center justify-content-center shadow-xs text-secondary" onclick="moverItem(this, -1)" title="Mover para cima" style="width: 32px; height: 32px;">
        <i class="bi bi-chevron-up"></i>
      </button>
      <button type="button" class="btn btn-sm btn-light border-0 rounded-circle p-2 d-flex align-items-center justify-content-center shadow-xs text-secondary" onclick="moverItem(this, 1)" title="Mover para baixo" style="width: 32px; height: 32px;">
        <i class="bi bi-chevron-down"></i>
      </button>
      <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2 d-flex align-items-center justify-content-center shadow-xs ms-1" onclick="removerItemColecao(this)" title="Remover da coleção" style="width: 32px; height: 32px;">
        <i class="bi bi-trash3-fill"></i>
      </button>
    </div>
  `;

  container.appendChild(div);
  initDragAndDrop();
  atualizarInputHiddenIds();

  // Feedback no botão do picker
  const pickerBtn = document.getElementById(`btn_picker_add_${res.id}`);
  if (pickerBtn) {
    pickerBtn.className = 'btn btn-sm btn-success btn-attach-music flex-shrink-0 ms-2';
    pickerBtn.innerHTML = '<i class="bi bi-check2 me-1"></i> Adicionado';
  }

  if (typeof USToast !== 'undefined') {
    USToast.show('success', 'Adicionado', `"${res.title}" inserido na coleção.`);
  } else if (typeof usShowToast === 'function') {
    usShowToast('success', 'Adicionado', `"${res.title}" inserido na coleção.`);
  }
}

function removerItemColecao(btn) {
  const item = btn.closest('.collection-track-item');
  if (item) {
    const resId = item.getAttribute('data-id');
    item.remove();
    atualizarInputHiddenIds();

    // Restaura botão no picker
    const pickerBtn = document.getElementById(`btn_picker_add_${resId}`);
    if (pickerBtn) {
      pickerBtn.className = 'btn btn-sm btn-primary btn-attach-music flex-shrink-0 ms-2';
      pickerBtn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Adicionar';
    }

    const container = document.getElementById('sortable_collection_resources');
    if (container.querySelectorAll('.collection-track-item').length === 0) {
      container.innerHTML = `
        <div id="empty_collection_notice" class="text-center py-5 text-secondary small">
          <div class="rounded-circle bg-body p-3 d-inline-flex mb-2 shadow-xs border">
            <i class="bi bi-music-note-beamed display-5 text-muted"></i>
          </div>
          <h6 class="fw-bold text-dark-emphasis mb-1">Seu repertório ainda está vazio</h6>
          <p class="text-secondary small mb-3">Adicione músicas, cifras, vídeos ou documentos da biblioteca para montar a playlist.</p>
          <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-semibold" onclick="abrirModalAdicionarMateriais()">
            <i class="bi bi-plus-lg me-1"></i> Adicionar Músicas
          </button>
        </div>
      `;
    }
  }
}

function moverItem(btn, direction) {
  const item = btn.closest('.collection-track-item');
  if (!item) return;

  if (direction === -1 && item.previousElementSibling && item.previousElementSibling.classList.contains('collection-track-item')) {
    item.parentNode.insertBefore(item, item.previousElementSibling);
  } else if (direction === 1 && item.nextElementSibling && item.nextElementSibling.classList.contains('collection-track-item')) {
    item.parentNode.insertBefore(item.nextElementSibling, item);
  }
  atualizarInputHiddenIds();
}

function atualizarInputHiddenIds() {
  const ids = [];
  let index = 1;
  document.querySelectorAll('#sortable_collection_resources .collection-track-item').forEach(el => {
    const id = el.getAttribute('data-id');
    if (id) ids.push(id);

    const badge = el.querySelector('.track-index-badge');
    if (badge) badge.textContent = index++;
  });
  
  document.getElementById('collection_resource_ids').value = ids.join(',');
  const total = ids.length;
  
  const heroCounter = document.getElementById('hero_tracks_counter');
  if (heroCounter) heroCounter.textContent = `${total} FAIXA(S)`;

  const badgeTotal = document.getElementById('badge_total_tracks');
  if (badgeTotal) badgeTotal.textContent = total;
}

// Drag and Drop Nativo com animação suave
function initDragAndDrop() {
  const container = document.getElementById('sortable_collection_resources');
  const cards = container.querySelectorAll('.collection-track-item');

  cards.forEach(card => {
    card.addEventListener('dragstart', () => {
      card.classList.add('dragging');
    });

    card.addEventListener('dragend', () => {
      card.classList.remove('dragging');
      atualizarInputHiddenIds();
    });
  });

  container.addEventListener('dragover', e => {
    e.preventDefault();
    const draggingCard = document.querySelector('.dragging');
    if (!draggingCard) return;

    const afterElement = getDragAfterElement(container, e.clientY);
    if (afterElement == null) {
      container.appendChild(draggingCard);
    } else {
      container.insertBefore(draggingCard, afterElement);
    }
  });
}

function getDragAfterElement(container, y) {
  const draggableElements = [...container.querySelectorAll('.collection-track-item:not(.dragging)')];

  return draggableElements.reduce((closest, child) => {
    const box = child.getBoundingClientRect();
    const offset = y - box.top - box.height / 2;
    if (offset < 0 && offset > closest.offset) {
      return { offset: offset, element: child };
    } else {
      return closest;
    }
  }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function salvarColecaoAjax(e) {
  e.preventDefault();
  atualizarInputHiddenIds();

  const form = document.getElementById('formCollection');
  const formData = new FormData(form);
  const btn = document.getElementById('btnSalvarColecao');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

  fetch('<?= base_url('resource/salvarColecao') ?>', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Salvar Coleção';

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
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Salvar Coleção';
    if (typeof USToast !== 'undefined') {
      USToast.show('error', 'Erro de Conexão', 'Falha ao salvar coleção.');
    }
  });
}
</script>
