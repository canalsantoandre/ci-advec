<?php
  $isEdit = !empty($departamento);
  $idDep  = $isEdit ? $departamento->id_departamento : 0;
  $cor    = $isEdit && !empty($departamento->cor_identificacao) ? $departamento->cor_identificacao : '#2563eb';
  $defaultLogo = 'https://ui-avatars.com/api/?name=' . ($isEdit ? urlencode($departamento->nome) : 'Departamento') . '&background=' . str_replace('#', '', $cor) . '&color=fff&size=150&bold=true';
  $logoSrc = $isEdit && !empty($departamento->logo_url) ? $departamento->logo_url : $defaultLogo;
?>

<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-diagram-3-fill text-primary me-2"></i><?= $isEdit ? 'Editar Departamento' : 'Novo Departamento' ?>
        </h3>
        <p class="text-secondary small mb-0">Cadastro do departamento e gerenciamento de suas sub-áreas de atuação</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('departamento') ?>">Departamentos</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Editar' : 'Novo' ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')) { ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
        <div><?= session()->getFlashdata('success') ?></div>
      </div>
    <?php } ?>
    <?php if (session()->getFlashdata('error')) { ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <div><?= session()->getFlashdata('error') ?></div>
      </div>
    <?php } ?>

    <div class="row g-4">
      
      <!-- ========================================== -->
      <!-- COLUNA ESQUERDA: CARD DE PREVIEW HERO -->
      <!-- ========================================== -->
      <div class="col-lg-4">
        <div class="card card-outline card-primary shadow-sm border-0 rounded-4 sticky-lg-top" style="top: 80px;">
          <div class="card-body text-center p-4">
            
            <!-- Logo Preview -->
            <div class="position-relative d-inline-block mb-3">
              <img src="<?= esc($logoSrc) ?>" id="imgLogoPreview" class="rounded-4 shadow border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover;" alt="Logo do Departamento" onerror="this.onerror=null;this.src='<?= $defaultLogo ?>';">
              
              <span class="position-absolute bottom-0 end-0 badge rounded-circle p-2 bg-<?= ($isEdit && $departamento->status == 0 ? 'danger' : 'success') ?> border border-2 border-white" id="badgeStatusPreview" title="Status">
                <span class="visually-hidden">Status</span>
              </span>
            </div>

            <h5 class="fw-bold mb-1 text-body" id="previewNomeDep"><?= $isEdit ? esc($departamento->nome) : 'Nome do Departamento' ?></h5>
            <p class="text-secondary small mb-3" id="previewDescDep"><?= $isEdit && !empty($departamento->descricao) ? esc($departamento->descricao) : 'Descrição do departamento...' ?></p>

            <div class="d-flex justify-content-center gap-2 mb-3">
              <span class="badge rounded-pill px-3 py-2 fw-semibold text-white" id="badgeCorPreview" style="background-color: <?= esc($cor) ?>;">
                <i class="bi bi-palette me-1"></i> Cor Identificadora
              </span>
            </div>

            <hr class="my-3 opacity-25">

            <div class="text-start small">
              <div class="mb-2">
                <span class="text-muted d-block">Responsável:</span>
                <strong class="text-body" id="previewRespNome"><?= $isEdit ? esc($departamento->responsavel_nome) : '-' ?></strong>
              </div>
              <div>
                <span class="text-muted d-block">WhatsApp:</span>
                <strong class="text-success" id="previewRespTel"><?= $isEdit ? esc($departamento->responsavel_telefone) : '-' ?></strong>
              </div>
            </div>

            <?php if ($isEdit) { ?>
              <hr class="my-3 opacity-25">
              <div class="d-grid gap-2">
                <a href="<?= base_url('escala/grade/' . $departamento->id_departamento) ?>" class="btn btn-outline-primary rounded-pill fw-semibold btn-sm">
                  <i class="bi bi-calendar-check me-1"></i> Abrir Grade deste Departamento
                </a>
              </div>
            <?php } ?>

          </div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- COLUNA DIREITA: FORMULÁRIO E SUB-ÁREAS -->
      <!-- ========================================== -->
      <div class="col-lg-8">
        
        <!-- Formulário de Dados Principais -->
        <form method="post" action="<?= base_url('departamento/salvar') ?>" enctype="multipart/form-data" id="formDepartamento" class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
          <input type="hidden" name="id_departamento" id="id_departamento" value="<?= $idDep ?>">

          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-info-circle-fill me-2"></i> Dados Principais do Departamento
            </h5>
          </div>

          <div class="card-body p-4">
            <div class="row g-3">
              
              <div class="col-md-8">
                <label for="nome" class="form-label fw-semibold">Nome do Departamento <span class="text-danger">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Comunicação, Transmissão, Louvor" value="<?= $isEdit ? esc($departamento->nome) : '' ?>" required maxlength="150">
              </div>

              <div class="col-md-4">
                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                  <option value="1" <?= (!$isEdit || $departamento->status == 1) ? 'selected' : '' ?>>Ativo</option>
                  <option value="0" <?= ($isEdit && $departamento->status == 0) ? 'selected' : '' ?>>Inativo</option>
                </select>
              </div>

              <div class="col-12">
                <label for="descricao" class="form-label fw-semibold">Descrição / Finalidade</label>
                <textarea name="descricao" id="descricao" rows="2" class="form-control" placeholder="Breve resumo da atuação do departamento..."><?= $isEdit ? esc($departamento->descricao) : '' ?></textarea>
              </div>

              <div class="col-md-6">
                <label for="responsavel_nome" class="form-label fw-semibold">Nome do Responsável / Líder <span class="text-danger">*</span></label>
                <input type="text" name="responsavel_nome" id="responsavel_nome" class="form-control" placeholder="Ex: João da Silva" value="<?= $isEdit ? esc($departamento->responsavel_nome) : '' ?>" required maxlength="150">
              </div>

              <div class="col-md-6">
                <label for="responsavel_telefone" class="form-label fw-semibold">Telefone / WhatsApp do Líder <span class="text-danger">*</span></label>
                <input type="text" name="responsavel_telefone" id="responsavel_telefone" class="form-control" placeholder="(11) 99999-9999" value="<?= $isEdit ? esc($departamento->responsavel_telefone) : '' ?>" required maxlength="30">
              </div>

              <div class="col-md-4">
                <label for="cor_identificacao" class="form-label fw-semibold">Cor de Identificação</label>
                <div class="input-group">
                  <input type="color" name="cor_identificacao" id="cor_identificacao" class="form-control form-control-color" value="<?= esc($cor) ?>" title="Escolha a cor do departamento">
                  <input type="text" id="cor_hex_display" class="form-control font-monospace" value="<?= esc($cor) ?>" readonly>
                </div>
              </div>

              <div class="col-md-8">
                <label for="logo_file" class="form-label fw-semibold">Upload do Logo / Imagem</label>
                <input type="file" name="logo_file" id="logo_file" class="form-control" accept="image/*">
                <small class="text-muted">Formatos: JPG, PNG ou WEBP. Ou informe a URL abaixo.</small>
              </div>

              <div class="col-12">
                <label for="logo_url_manual" class="form-label fw-semibold">Ou URL Externa do Logo (Opcional)</label>
                <input type="url" name="logo_url_manual" id="logo_url_manual" class="form-control" placeholder="https://exemplo.com/logo.png" value="<?= ($isEdit && !empty($departamento->logo_url) && filter_var($departamento->logo_url, FILTER_VALIDATE_URL)) ? esc($departamento->logo_url) : '' ?>">
              </div>

            </div>
          </div>

          <div class="card-footer bg-body py-3 d-flex justify-content-between align-items-center">
            <a href="<?= base_url('departamento') ?>" class="btn btn-outline-secondary rounded-pill px-4">
              <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
              <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Salvar Alterações' : 'Salvar e Continuar' ?>
            </button>
          </div>
        </form>

        <!-- Seção de Gestão de Sub-áreas (Disponível quando o departamento já existe) -->
        <?php if ($isEdit) { ?>
          <div class="card card-outline card-info shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-body py-3 d-flex justify-content-between align-items-center">
              <div>
                <h5 class="card-title fw-bold mb-0 text-info">
                  <i class="bi bi-grid-fill me-2"></i> Sub-áreas de Atuação deste Departamento
                </h5>
                <small class="text-muted">Ex: No departamento Comunicação, as sub-áreas são Fotografia, Reels, Telão, etc.</small>
              </div>
              <button type="button" class="btn btn-info btn-sm text-white rounded-pill px-3 fw-bold shadow-sm" onclick="abrirModalArea()">
                <i class="bi bi-plus-lg me-1"></i> Adicionar Sub-área
              </button>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelaSubareas">
                  <thead class="table-light">
                    <tr>
                      <th class="ps-4">Nome da Sub-área</th>
                      <th>Descrição</th>
                      <th class="text-center">Voluntários Vinculados</th>
                      <th class="text-center">Status</th>
                      <th class="text-end pe-4" style="width: 120px;">Ações</th>
                    </tr>
                  </thead>
                  <tbody id="tbodyAreas">
                    <?php if (!empty($areas)) { ?>
                      <?php foreach ($areas as $a) { ?>
                        <tr id="rowArea_<?= $a->id_area ?>">
                          <td class="ps-4 fw-bold text-body">
                            <i class="bi bi-chevron-right text-primary me-1 small"></i>
                            <span class="nome-area-label"><?= esc($a->nome_area) ?></span>
                          </td>
                          <td>
                            <small class="text-secondary desc-area-label"><?= esc($a->descricao ?? '-') ?></small>
                          </td>
                          <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">
                              <?= (int)$a->total_voluntarios ?> voluntários
                            </span>
                          </td>
                          <td class="text-center status-area-cell">
                            <?php if ($a->status == 1) { ?>
                              <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Ativo</span>
                            <?php } else { ?>
                              <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">Inativo</span>
                            <?php } ?>
                          </td>
                          <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                              <button type="button" class="btn btn-outline-primary btn-action" title="Editar Sub-área" onclick="editarArea(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)">
                                <i class="bi bi-pencil-fill"></i>
                              </button>
                              <button type="button" class="btn btn-outline-danger btn-action" title="Excluir Sub-área" onclick="excluirArea(<?= $a->id_area ?>, '<?= esc($a->nome_area) ?>')">
                                <i class="bi bi-trash3-fill"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      <?php } ?>
                    <?php } else { ?>
                      <tr id="rowNenhumaArea">
                        <td colspan="5" class="text-center py-4 text-muted">
                          <i class="bi bi-diagram-2 fs-3 d-block mb-1 opacity-50"></i>
                          Nenhuma sub-área cadastrada para este departamento ainda.
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php } else { ?>
          <div class="alert alert-info border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-info-circle-fill fs-2 text-info"></i>
              <div>
                <h6 class="fw-bold mb-1">Sub-áreas de Atuação</h6>
                <p class="mb-0 text-secondary small">Após salvar as informações básicas do departamento, você poderá cadastrar e gerenciar as sub-áreas (ex: Fotografia, Câmera 1, Cenas, etc.).</p>
              </div>
            </div>
          </div>
        <?php } ?>

      </div>

    </div>

  </div>
</div>

<!-- Modal para Adicionar / Editar Sub-área -->
<div class="modal fade" id="modalFormArea" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-primary" id="modalAreaTitle">
          <i class="bi bi-grid-fill me-2"></i> Sub-área de Atuação
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <form id="formSalvarSubarea" onsubmit="salvarAreaAjax(event)">
        <input type="hidden" name="id_area" id="modal_id_area" value="0">
        <input type="hidden" name="id_departamento" value="<?= $idDep ?>">

        <div class="modal-body py-3">
          <div class="mb-3">
            <label for="modal_nome_area" class="form-label fw-semibold">Nome da Sub-área <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nome_area" id="modal_nome_area" placeholder="Ex: Fotografia, Câmera 1, Telão" required maxlength="150">
          </div>

          <div class="mb-3">
            <label for="modal_descricao_area" class="form-label fw-semibold">Descrição / Atribuições</label>
            <textarea class="form-control" name="descricao" id="modal_descricao_area" rows="2" placeholder="O que o voluntário desta área faz durante o culto..."></textarea>
          </div>

          <div class="mb-2">
            <label for="modal_status_area" class="form-label fw-semibold">Status</label>
            <select class="form-select" name="status" id="modal_status_area">
              <option value="1">Ativo</option>
              <option value="0">Inativo</option>
            </select>
          </div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnSalvarAreaSubmit">
            <i class="bi bi-check-lg me-1"></i> Salvar Sub-área
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Confirmação de Exclusão de Sub-área -->
<div class="modal fade" id="modalConfirmarExcluirArea" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Exclusão
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body py-3">
        <p class="mb-0">Deseja realmente excluir a sub-área <strong id="modalNomeSubareaExcluir"></strong>?</p>
        <small class="text-danger d-block mt-2">
          <i class="bi bi-info-circle me-1"></i> Esta ação removerá a sub-área deste departamento.
        </small>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btnConfirmarExcluirAreaSubmit" class="btn btn-danger rounded-pill px-4 fw-bold">
          <i class="bi bi-trash3-fill me-1"></i> Excluir Sub-área
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // Live preview bindings
  document.getElementById('nome')?.addEventListener('input', function() {
    document.getElementById('previewNomeDep').textContent = this.value || 'Nome do Departamento';
  });
  document.getElementById('descricao')?.addEventListener('input', function() {
    document.getElementById('previewDescDep').textContent = this.value || 'Descrição do departamento...';
  });
  document.getElementById('responsavel_nome')?.addEventListener('input', function() {
    document.getElementById('previewRespNome').textContent = this.value || '-';
  });
  document.getElementById('responsavel_telefone')?.addEventListener('input', function() {
    document.getElementById('previewRespTel').textContent = this.value || '-';
  });
  document.getElementById('cor_identificacao')?.addEventListener('input', function() {
    const cor = this.value;
    document.getElementById('cor_hex_display').value = cor;
    document.getElementById('badgeCorPreview').style.backgroundColor = cor;
  });
  document.getElementById('logo_file')?.addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
      const reader = new FileReader();
      reader.onload = function(ev) {
        document.getElementById('imgLogoPreview').src = ev.target.result;
      };
      reader.readAsDataURL(e.target.files[0]);
    }
  });

  // Modal Area Management
  let modalAreaInstance = null;
  let modalDeleteAreaInstance = null;
  let idAreaParaExcluir = null;

  function abrirModalArea() {
    document.getElementById('modal_id_area').value = '0';
    document.getElementById('modal_nome_area').value = '';
    document.getElementById('modal_descricao_area').value = '';
    document.getElementById('modal_status_area').value = '1';
    document.getElementById('modalAreaTitle').innerHTML = '<i class="bi bi-plus-circle-fill me-2"></i> Nova Sub-área';
    
    if (!modalAreaInstance) {
      modalAreaInstance = new bootstrap.Modal(document.getElementById('modalFormArea'));
    }
    modalAreaInstance.show();
  }

  function editarArea(area) {
    document.getElementById('modal_id_area').value = area.id_area;
    document.getElementById('modal_nome_area').value = area.nome_area;
    document.getElementById('modal_descricao_area').value = area.descricao || '';
    document.getElementById('modal_status_area').value = area.status;
    document.getElementById('modalAreaTitle').innerHTML = '<i class="bi bi-pencil-fill me-2"></i> Editar Sub-área';
    
    if (!modalAreaInstance) {
      modalAreaInstance = new bootstrap.Modal(document.getElementById('modalFormArea'));
    }
    modalAreaInstance.show();
  }

  function salvarAreaAjax(e) {
    e.preventDefault();
    const form = document.getElementById('formSalvarSubarea');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSalvarAreaSubmit');
    btn.disabled = true;

    fetch('<?= base_url('departamento/salvarArea') ?>', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      if (data.status === 'success') {
        renderizarAreas(data.areas);
        modalAreaInstance.hide();
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('success', 'Sub-área Salva', data.message);
        } else if (typeof usShowToast === 'function') {
          usShowToast('success', 'Sub-área Salva', data.message);
        }
      } else {
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Atenção', data.message || 'Erro ao salvar sub-área.');
        } else if (typeof usShowToast === 'function') {
          usShowToast('error', 'Atenção', data.message || 'Erro ao salvar sub-área.');
        }
      }
    })
    .catch(err => {
      btn.disabled = false;
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show('error', 'Erro de Conexão', 'Falha ao comunicar com o servidor.');
      }
    });
  }

  function excluirArea(id_area, nome) {
    idAreaParaExcluir = id_area;
    document.getElementById('modalNomeSubareaExcluir').textContent = nome;

    if (!modalDeleteAreaInstance) {
      modalDeleteAreaInstance = new bootstrap.Modal(document.getElementById('modalConfirmarExcluirArea'));
    }
    modalDeleteAreaInstance.show();
  }

  document.getElementById('btnConfirmarExcluirAreaSubmit')?.addEventListener('click', function() {
    if (!idAreaParaExcluir) return;

    const formData = new FormData();
    formData.append('id_area', idAreaParaExcluir);

    fetch('<?= base_url('departamento/excluirArea') ?>', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (modalDeleteAreaInstance) modalDeleteAreaInstance.hide();
      if (data.status === 'success') {
        renderizarAreas(data.areas);
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('success', 'Sub-área Removida', data.message);
        } else if (typeof usShowToast === 'function') {
          usShowToast('success', 'Sub-área Removida', data.message);
        }
      } else {
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Não foi possível excluir', data.message || 'Não foi possível excluir a sub-área.');
        } else if (typeof usShowToast === 'function') {
          usShowToast('error', 'Não foi possível excluir', data.message || 'Não foi possível excluir a sub-área.');
        }
      }
    })
    .catch(err => {
      if (modalDeleteAreaInstance) modalDeleteAreaInstance.hide();
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show('error', 'Erro de Conexão', 'Falha ao comunicar com o servidor.');
      }
    });
  });

  function renderizarAreas(areas) {
    const tbody = document.getElementById('tbodyAreas');
    if (!tbody) return;

    if (!areas || areas.length === 0) {
      tbody.innerHTML = `
        <tr id="rowNenhumaArea">
          <td colspan="5" class="text-center py-4 text-muted">
            <i class="bi bi-diagram-2 fs-3 d-block mb-1 opacity-50"></i>
            Nenhuma sub-área cadastrada para este departamento ainda.
          </td>
        </tr>`;
      return;
    }

    let html = '';
    areas.forEach(a => {
      const statusBadge = a.status == 1 
        ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Ativo</span>'
        : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">Inativo</span>';

      const jsonStr = JSON.stringify(a).replace(/"/g, '&quot;');

      html += `
        <tr id="rowArea_${a.id_area}">
          <td class="ps-4 fw-bold text-body">
            <i class="bi bi-chevron-right text-primary me-1 small"></i>
            <span class="nome-area-label">${escapeHtml(a.nome_area)}</span>
          </td>
          <td>
            <small class="text-secondary desc-area-label">${escapeHtml(a.descricao || '-')}</small>
          </td>
          <td class="text-center">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">
              ${parseInt(a.total_voluntarios || 0)} voluntários
            </span>
          </td>
          <td class="text-center status-area-cell">
            ${statusBadge}
          </td>
          <td class="text-end pe-4">
            <div class="d-inline-flex gap-1">
              <button type="button" class="btn btn-outline-primary btn-action" title="Editar Sub-área" onclick="editarArea(${jsonStr})">
                <i class="bi bi-pencil-fill"></i>
              </button>
              <button type="button" class="btn btn-outline-danger btn-action" title="Excluir Sub-área" onclick="excluirArea(${a.id_area}, '${escapeHtml(a.nome_area)}')">
                <i class="bi bi-trash3-fill"></i>
              </button>
            </div>
          </td>
        </tr>`;
    });
    tbody.innerHTML = html;
  }

  function escapeHtml(text) {
    if (!text) return '';
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
</script>
