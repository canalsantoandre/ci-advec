<!-- Content Header (Page header) -->
<div class="app-content-header py-3">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold text-dark-emphasis">
          <i class="bi bi-collection-play-fill text-primary me-2"></i>Biblioteca
        </h3>
        <p class="text-secondary small mb-0">Gestão centralizada de materiais de apoio, repertórios, vídeos e cifras</p>
      </div>
      <div class="col-sm-6">
        <div class="float-sm-end d-flex gap-2 mt-2 mt-sm-0">
          <a href="<?= base_url('resource/novaColecao') ?>" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-folder-plus me-1"></i> Nova Coleção
          </a>
          <a href="<?= base_url('resource/novo') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Novo Material
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
      <div class="col-xl-3 col-sm-6">
        <div class="card border rounded-4 shadow-sm p-3 bg-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-secondary small fw-medium">Total de Materiais</span>
              <h3 class="fw-bold mb-0 text-dark-emphasis"><?= count($recursos) ?></h3>
            </div>
            <div class="rounded-3 bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-files fs-4"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6">
        <div class="card border rounded-4 shadow-sm p-3 bg-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-secondary small fw-medium">Coleções & Playlists</span>
              <h3 class="fw-bold mb-0 text-dark-emphasis"><?= count($colecoes) ?></h3>
            </div>
            <div class="rounded-3 bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-folder2-open fs-4"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6">
        <div class="card border rounded-4 shadow-sm p-3 bg-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-secondary small fw-medium">Vídeos & Mídias</span>
              <h3 class="fw-bold mb-0 text-dark-emphasis">
                <?php 
                  echo count(array_filter($recursos, function($r) { return $r->type_code === 'video' || $r->type_code === 'audio'; }));
                ?>
              </h3>
            </div>
            <div class="rounded-3 bg-danger-subtle text-danger p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-play-circle-fill fs-4"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6">
        <div class="card border rounded-4 shadow-sm p-3 bg-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-secondary small fw-medium">PDFs & Cifras</span>
              <h3 class="fw-bold mb-0 text-dark-emphasis">
                <?php 
                  echo count(array_filter($recursos, function($r) { return $r->type_code === 'pdf' || $r->type_code === 'text'; }));
                ?>
              </h3>
            </div>
            <div class="rounded-3 bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-file-earmark-text-fill fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Card -->
    <div class="card border rounded-4 shadow-sm mb-4">
      <div class="card-body p-3">
        <form method="GET" action="<?= base_url('resource') ?>" class="row g-2 align-items-center">
          <div class="col-lg-4 col-md-6">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
              <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por título, observação ou link..." value="<?= esc($filtros['busca'] ?? '') ?>">
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">Todos os Departamentos</option>
              <option value="global" <?= ($filtros['department_id'] === 'global') ? 'selected' : '' ?>>🌐 Global (Todos)</option>
              <?php foreach ($departamentos as $dep) { ?>
                <option value="<?= $dep->id_departamento ?>" <?= ($filtros['department_id'] == $dep->id_departamento) ? 'selected' : '' ?>>
                  <?= esc($dep->nome) ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="col-lg-3 col-md-6">
            <select name="resource_type_id" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">Todos os Tipos de Mídia</option>
              <?php foreach ($tipos as $tipo) { ?>
                <option value="<?= $tipo->id ?>" <?= ($filtros['resource_type_id'] == $tipo->id) ? 'selected' : '' ?>>
                  <?= esc($tipo->name) ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="col-lg-2 col-md-6 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3">
              <i class="bi bi-filter"></i> Filtrar
            </button>
            <?php if (!empty($filtros['busca']) || !empty($filtros['department_id']) || !empty($filtros['resource_type_id'])) { ?>
              <a href="<?= base_url('resource') ?>" class="btn btn-sm btn-outline-secondary rounded-3" title="Limpar Filtros">
                <i class="bi bi-x-lg"></i>
              </a>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills custom-resource-pills mb-4" id="resourceTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill px-4" id="recursos-tab" data-bs-toggle="pill" data-bs-target="#tab-recursos" type="button" role="tab">
          <i class="bi bi-files me-1.5"></i> Materiais Avulsos (<?= count($recursos) ?>)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4" id="colecoes-tab" data-bs-toggle="pill" data-bs-target="#tab-colecoes" type="button" role="tab">
          <i class="bi bi-folder2-open me-1.5"></i> Coleções & Repertórios (<?= count($colecoes) ?>)
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="resourceTabsContent">

      <!-- TAB 1: RECURSOS AVULSOS -->
      <div class="tab-pane fade show active" id="tab-recursos" role="tabpanel">
        <?php if (empty($recursos)) { ?>
          <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-body">
            <div class="mb-3 text-secondary">
              <i class="bi bi-folder-x display-3"></i>
            </div>
            <h5 class="fw-bold text-dark-emphasis">Nenhum material encontrado</h5>
            <p class="text-secondary small mb-3">Cadastre novos materiais de apoio, links do YouTube, Spotify, PDFs ou cifras.</p>
            <div class="d-flex justify-content-center">
              <a href="<?= base_url('resource/novo') ?>" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Cadastrar Primeiro Material
              </a>
            </div>
          </div>
        <?php } else { ?>
          <div class="row g-3">
            <?php foreach ($recursos as $res) { 
              $isYoutube = ($res->provider === 'youtube');
              $isSpotify = ($res->provider === 'spotify');
              $thumb = !empty($res->thumbnail_url) ? $res->thumbnail_url : '';
            ?>
              <div class="col-xl-3 col-lg-4 col-md-6" id="card-resource-<?= $res->id ?>">
                <div class="card h-100 border rounded-4 shadow-sm resource-card transition-all overflow-hidden bg-body">
                  <!-- Thumbnail Header / Preview -->
                  <div class="resource-thumb-wrapper position-relative bg-body-tertiary d-flex align-items-center justify-content-center">
                    <?php if ($thumb) { ?>
                      <img src="<?= esc($thumb) ?>" alt="<?= esc($res->title) ?>" class="w-100 h-100 object-fit-cover" onerror="this.style.display='none'">
                    <?php } else { ?>
                      <div class="text-center text-secondary py-4">
                        <i class="<?= esc($res->type_icon) ?>" style="font-size: 2.5rem;"></i>
                      </div>
                    <?php } ?>

                    <!-- Type Badge -->
                    <span class="badge bg-<?= esc($res->type_color) ?> position-absolute top-0 start-0 m-2 rounded-pill px-2.5 py-1 shadow-xs small">
                      <i class="<?= esc($res->type_icon) ?> me-1"></i> <?= esc($res->type_name) ?>
                    </span>

                    <!-- Quick Play Overlay Button -->
                    <?php if (!empty($res->embed_url) || $res->type_code === 'text') { ?>
                      <button type="button" class="btn btn-light rounded-circle shadow position-absolute resource-play-overlay-btn" onclick="abrirModalPreview(<?= htmlspecialchars(json_encode($res), ENT_QUOTES, 'UTF-8') ?>)" title="Pré-visualizar">
                        <i class="bi bi-play-fill fs-4 text-primary"></i>
                      </button>
                    <?php } elseif (!empty($res->url)) { ?>
                      <a href="<?= esc($res->url) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light rounded-circle shadow position-absolute resource-play-overlay-btn" title="Abrir link externo">
                        <i class="bi bi-box-arrow-up-right fs-5 text-primary"></i>
                      </a>
                    <?php } ?>
                  </div>

                  <!-- Card Body -->
                  <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                      <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill small">
                          <?= $res->department_name ? esc($res->department_name) : '🌐 Global' ?>
                        </span>
                        <?php if ($res->status == 1) { ?>
                          <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.65rem;">Ativo</span>
                        <?php } else { ?>
                          <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.65rem;">Inativo</span>
                        <?php } ?>
                      </div>

                      <h6 class="fw-bold text-dark-emphasis mb-1 text-truncate" title="<?= esc(htmlspecialchars_decode($res->title)) ?>">
                        <?= esc(htmlspecialchars_decode($res->title)) ?>
                      </h6>

                      <?php if (!empty($res->description)) { ?>
                        <p class="text-secondary small mb-2 text-truncate-2" style="font-size: 0.78rem; line-height: 1.3;">
                          <?= esc(htmlspecialchars_decode($res->description)) ?>
                        </p>
                      <?php } ?>
                    </div>

                    <!-- Actions Footer -->
                    <div class="pt-2 border-top border-light-subtle d-flex align-items-center justify-content-between">
                      <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold small" onclick="abrirModalPreview(<?= htmlspecialchars(json_encode($res), ENT_QUOTES, 'UTF-8') ?>)">
                        <i class="bi bi-eye me-1"></i> Visualizar
                      </button>

                      <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0 rounded-circle" type="button" data-bs-toggle="dropdown">
                          <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 small">
                          <li>
                            <a class="dropdown-item" href="<?= base_url('resource/editar/' . $res->id) ?>">
                              <i class="bi bi-pencil me-2 text-primary"></i> Editar
                            </a>
                          </li>
                          <?php if (!empty($res->url)) { ?>
                            <li>
                              <a class="dropdown-item" href="<?= esc($res->url) ?>" target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-box-arrow-up-right me-2 text-info"></i> Abrir Link Original
                              </a>
                            </li>
                          <?php } ?>
                          <li><hr class="dropdown-divider my-1"></li>
                          <li>
                            <button class="dropdown-item text-danger" onclick="excluirRecurso(<?= $res->id ?>, '<?= esc(htmlspecialchars_decode($res->title)) ?>')">
                              <i class="bi bi-trash3 me-2"></i> Excluir
                            </button>
                          </li>
                        </ul>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php } ?>
      </div>

      <!-- TAB 2: COLEÇÕES & REPERTÓRIOS -->
      <div class="tab-pane fade" id="tab-colecoes" role="tabpanel">
        <?php if (empty($colecoes)) { ?>
          <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-body">
            <div class="mb-3 text-secondary">
              <i class="bi bi-folder-plus display-3"></i>
            </div>
            <h5 class="fw-bold text-dark-emphasis">Nenhuma coleção criada</h5>
            <p class="text-secondary small mb-3">Crie agrupadores e playlists (ex: Repertório Culto Domingo, Músicas Santa Ceia) para anexar facilmente em escalas com um clique.</p>
            <div class="d-flex justify-content-center">
              <a href="<?= base_url('resource/novaColecao') ?>" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Criar Nova Coleção
              </a>
            </div>
          </div>
        <?php } else { ?>
          <div class="row g-3">
            <?php foreach ($colecoes as $col) { ?>
              <div class="col-xl-4 col-md-6" id="card-collection-<?= $col->id ?>">
                <div class="card h-100 border rounded-4 shadow-sm p-4 d-flex flex-column justify-content-between bg-body collection-card">
                  <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                      <div class="collection-folder-icon rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                        <i class="bi bi-folder2-open fs-3"></i>
                      </div>
                      <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 small">
                        <?= $col->department_name ? esc($col->department_name) : '🌐 Global' ?>
                      </span>
                    </div>

                    <h5 class="fw-bold text-dark-emphasis mb-1"><?= esc(htmlspecialchars_decode($col->title)) ?></h5>
                    <?php if (!empty($col->description)) { ?>
                      <p class="text-secondary small mb-3"><?= esc(htmlspecialchars_decode($col->description)) ?></p>
                    <?php } ?>

                    <div class="d-flex align-items-center gap-2 mb-3">
                      <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 small">
                        <i class="bi bi-collection me-1"></i> <?= $col->total_resources ?> materiais
                      </span>
                    </div>
                  </div>

                  <div class="pt-3 border-top border-light-subtle d-flex align-items-center justify-content-between">
                    <a href="<?= base_url('resource/editarColecao/' . $col->id) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                      <i class="bi bi-pencil me-1"></i> Gerenciar Itens
                    </a>

                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="excluirColecao(<?= $col->id ?>, '<?= esc($col->title) ?>')" title="Excluir Coleção">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php } ?>
      </div>

    </div>

  </div>
</div>

<!-- Modal Interativo de Preview / Embed Player -->
<div class="modal fade" id="modalResourcePreview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 shadow border-0 overflow-hidden">
      <div class="modal-header border-0 pb-0 d-flex align-items-center justify-content-between">
        <div>
          <span class="badge bg-primary-subtle text-primary rounded-pill mb-1" id="preview_badge_type">Tipo</span>
          <h5 class="modal-title fw-bold text-dark-emphasis mb-0" id="preview_modal_title">Título do Material</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body py-3">
        <!-- Container do Player / Embed / Iframe / Texto -->
        <div id="preview_player_container" class="rounded-3 overflow-hidden bg-dark text-white d-flex align-items-center justify-content-center shadow-inner" style="min-height: 380px;">
          <!-- Injetado via JS -->
        </div>

        <div class="mt-3">
          <p class="text-secondary small mb-1" id="preview_description"></p>
          <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light-subtle">
            <span class="text-muted small" id="preview_provider_info"></span>
            <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="preview_external_link_btn">
              <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Link Original
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.resource-thumb-wrapper {
  height: 160px;
  overflow: hidden;
}
.resource-play-overlay-btn {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.2s ease-in-out;
}
.resource-card:hover .resource-play-overlay-btn {
  opacity: 1;
  transform: scale(1);
}
.resource-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.resource-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
}
.collection-folder-icon {
  width: 52px;
  height: 52px;
}
.custom-resource-pills .nav-link {
  color: var(--bs-secondary-color);
  font-weight: 600;
  transition: all 0.2s;
}
.custom-resource-pills .nav-link.active {
  background-color: var(--bs-primary);
  color: #fff;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

<script>
let modalPreviewInstance = null;

function abrirModalPreview(resource) {
  if (!modalPreviewInstance) {
    modalPreviewInstance = new bootstrap.Modal(document.getElementById('modalResourcePreview'));
  }

  document.getElementById('preview_badge_type').textContent = resource.type_name || 'Material';
  document.getElementById('preview_modal_title').textContent = resource.title || '';
  document.getElementById('preview_description').textContent = resource.description || 'Sem orientações adicionais.';
  
  const linkBtn = document.getElementById('preview_external_link_btn');
  if (resource.url) {
    linkBtn.href = resource.url;
    linkBtn.classList.remove('d-none');
  } else {
    linkBtn.classList.add('d-none');
  }

  const container = document.getElementById('preview_player_container');
  container.innerHTML = '';

  // 1. YouTube Embed
  if (resource.provider === 'youtube' && resource.embed_url) {
    container.innerHTML = `
      <iframe src="${resource.embed_url}" class="w-100" style="height: 420px; border: 0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    `;
    document.getElementById('preview_provider_info').innerHTML = '<i class="bi bi-youtube text-danger me-1"></i> Reproduzindo via YouTube';
  } 
  // 2. Spotify Embed
  else if (resource.provider === 'spotify' && resource.embed_url) {
    container.innerHTML = `
      <iframe src="${resource.embed_url}" class="w-100" style="height: 380px; border: 0; border-radius: 12px;" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
    `;
    document.getElementById('preview_provider_info').innerHTML = '<i class="bi bi-spotify text-success me-1"></i> Reproduzindo via Spotify';
  }
  // 3. Google Drive / Docs Preview
  else if (resource.provider === 'drive' && resource.embed_url) {
    container.innerHTML = `
      <iframe src="${resource.embed_url}" class="w-100" style="height: 450px; border: 0;" allow="autoplay"></iframe>
    `;
    document.getElementById('preview_provider_info').innerHTML = '<i class="bi bi-google text-primary me-1"></i> Visualização Google Drive';
  }
  // 4. Texto / Cifra / Letra
  else if (resource.type_code === 'text' || resource.content_text) {
    container.innerHTML = `
      <div class="p-4 bg-body text-body w-100 h-100 text-start overflow-auto font-monospace" style="max-height: 450px; white-space: pre-wrap; font-size: 0.90rem;">
        ${escapeHtml(resource.content_text || resource.description || '')}
      </div>
    `;
    document.getElementById('preview_provider_info').innerHTML = '<i class="bi bi-file-text me-1"></i> Texto / Cifra';
  }
  // 5. PDF Direct Viewer
  else if (resource.type_code === 'pdf' && resource.url) {
    container.innerHTML = `
      <iframe src="${resource.url}" class="w-100" style="height: 480px; border: 0;"></iframe>
    `;
    document.getElementById('preview_provider_info').innerHTML = '<i class="bi bi-file-pdf-fill text-danger me-1"></i> Documento PDF';
  }
  // Fallback Genérico
  else {
    container.innerHTML = `
      <div class="text-center p-4">
        <i class="bi bi-box-arrow-up-right display-4 mb-3 text-secondary"></i>
        <p class="mb-3">Este material é um link externo.</p>
        <a href="${resource.url}" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-4">
          <i class="bi bi-box-arrow-up-right me-1"></i> Acessar Página Externa
        </a>
      </div>
    `;
    document.getElementById('preview_provider_info').innerHTML = '<i class="bi bi-link-45deg me-1"></i> Link Externo';
  }

  modalPreviewInstance.show();
}

function excluirRecurso(id, title) {
  if (!confirm(`Tem certeza que deseja remover o material "${title}"?`)) return;

  fetch(`<?= base_url('resource/excluir') ?>/${id}`, { method: 'POST' })
    .then(r => r.json())
    .then(data => {
      if (data.status === 'success') {
        if (typeof USToast !== 'undefined') {
          USToast.show('success', 'Removido', data.message);
        } else if (typeof usShowToast === 'function') {
          usShowToast('success', 'Removido', data.message);
        }
        document.getElementById(`card-resource-${id}`)?.remove();
      } else {
        alert(data.message || 'Erro ao excluir.');
      }
    })
    .catch(() => alert('Falha na requisição.'));
}

function excluirColecao(id, title) {
  if (!confirm(`Tem certeza que deseja remover a coleção "${title}"?`)) return;

  fetch(`<?= base_url('resource/excluirColecao') ?>/${id}`, { method: 'POST' })
    .then(r => r.json())
    .then(data => {
      if (data.status === 'success') {
        if (typeof USToast !== 'undefined') {
          USToast.show('success', 'Removido', data.message);
        } else if (typeof usShowToast === 'function') {
          usShowToast('success', 'Removido', data.message);
        }
        document.getElementById(`card-collection-${id}`)?.remove();
      } else {
        alert(data.message || 'Erro ao excluir.');
      }
    })
    .catch(() => alert('Falha na requisição.'));
}

function escapeHtml(text) {
  if (!text) return '';
  return String(text).replace(/[&<>"']/g, function(m) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
  });
}
</script>
