<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Gestão, frequência e assiduidade dos convidados especiais</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Lista de Convidados</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    
    <!-- Action Bar & Add Button -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
          <i class="bi bi-people-fill me-1"></i> Total: <?= count($convidados) ?> Convidados
        </span>
        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2">
          <i class="bi bi-calendar-check me-1"></i> Cultos Realizados: <?= $totalCultosGeral ?>
        </span>
      </div>

      <?php if ($sys_action->create) { ?>
        <a href="<?= base_url('convidado/novo') ?>" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
          <i class="bi bi-person-plus-fill me-1"></i> Incluir Novo Convidado
        </a>
      <?php } ?>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')) { ?>
      <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
      </div>
    <?php } ?>
    <?php if (session()->getFlashdata('error')) { ?>
      <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
      </div>
    <?php } ?>

    <!-- Card de Filtros Avançados -->
    <div class="card bg-body-tertiary border-0 rounded-4 shadow-sm mb-4">
      <div class="card-body p-3">
        <div class="row g-3 align-items-center">
          
          <!-- Filtro: Pesquisa por Nome/Instagram -->
          <div class="col-md-4">
            <label for="searchGuest" class="form-label fw-semibold small text-secondary mb-1">Pesquisar Convidado</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input type="text" class="form-control form-control-sm border-start-0" id="searchGuest" placeholder="Buscar por nome, exibição ou @instagram...">
            </div>
          </div>

          <!-- Filtro: Função Eclesiástica -->
          <div class="col-md-4">
            <label for="filterFuncao" class="form-label fw-semibold small text-secondary mb-1">Função Eclesiástica</label>
            <select class="form-select form-select-sm" id="filterFuncao">
              <option value="">Todas as Funções Eclesiásticas</option>
              <?php if (!empty($funcoesEclesiasticas)) { ?>
                <?php foreach ($funcoesEclesiasticas as $func) { ?>
                  <option value="<?= esc($func->nm_funcao_eclesiastica) ?>"><?= esc($func->nm_funcao_eclesiastica) ?></option>
                <?php } ?>
              <?php } ?>
            </select>
          </div>

          <!-- Filtro: Performance de Assiduidade -->
          <div class="col-md-4">
            <label for="filterPerformance" class="form-label fw-semibold small text-secondary mb-1">Performance de Presenças</label>
            <select class="form-select form-select-sm" id="filterPerformance">
              <option value="">Todas as Performances</option>
              <option value="alta">Alta Assiduidade (≥ 70%)</option>
              <option value="media">Média Assiduidade (40% a 69%)</option>
              <option value="baixa">Baixa / Sem Presenças (&lt; 40%)</option>
            </select>
          </div>

        </div>
      </div>
    </div>

    <!-- Tabela Principal de Convidados -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="tblConvidados">
            <thead class="table-light">
              <tr>
                <th scope="col" class="ps-4">Convidado</th>
                <th scope="col">Função Eclesiástica</th>
                <th scope="col">Assiduidade & Presenças</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-end pe-4" style="width: 140px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($convidados as $row) { 
                $presencas = (int)$row->total_presencas;
                $pct = $totalCultosGeral > 0 ? round(($presencas / $totalCultosGeral) * 100, 1) : 0;

                // Nível de performance para filtros JS
                $perfGroup = 'baixa';
                if ($pct >= 70) {
                  $perfGroup = 'alta';
                } elseif ($pct >= 40) {
                  $perfGroup = 'media';
                }

                // Avatar URL ou iniciais com fallback UI Avatars
                $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($row->nome_convidado) . '&background=2563eb&color=fff&size=90';
                $avatarUrl = !empty($row->url_foto_instagram) ? esc($row->url_foto_instagram) : $defaultAvatar;
              ?>
                <tr data-funcao="<?= esc($row->nm_funcao_eclesiastica ?? 'Sem Função') ?>" data-performance="<?= $perfGroup ?>" data-nome="<?= esc(mb_strtolower($row->nome_convidado)) ?>" data-exibicao="<?= esc(mb_strtolower($row->nome_convidado_visualizacao ?? '')) ?>" data-nick="<?= esc(mb_strtolower($row->nick_instagram ?? '')) ?>">
                  
                  <!-- Convidado (Foto / Iniciais & Nomes) -->
                  <td class="ps-4">
                    <div class="d-flex align-items-center gap-3">
                      <div class="position-relative">
                        <img src="<?= $avatarUrl ?>" class="rounded-circle shadow-sm border border-2 border-white" style="width: 48px; height: 48px; object-fit: cover;" alt="Avatar" onError="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
                      </div>
                      <div>
                        <div class="fw-bold text-body">
                          <a href="<?= base_url('convidado/editar/' . $row->hash_convidado) ?>" class="text-decoration-none text-body hover-primary">
                            <?= esc($row->nome_convidado); ?>
                          </a>
                        </div>
                        <?php if (!empty($row->nome_convidado_visualizacao) && $row->nome_convidado_visualizacao !== $row->nome_convidado) { ?>
                          <small class="text-secondary d-block">Apelido: <?= esc($row->nome_convidado_visualizacao) ?></small>
                        <?php } ?>
                        <?php if (!empty($row->nick_instagram)) { ?>
                          <small class="d-block">
                            <a href="https://www.instagram.com/<?= str_replace('@', '', $row->nick_instagram); ?>" class="text-decoration-none text-danger small" target="_blank">
                              <i class="bi bi-instagram me-1"></i>@<?= esc(str_replace('@', '', $row->nick_instagram)) ?>
                            </a>
                          </small>
                        <?php } ?>
                      </div>
                    </div>
                  </td>

                  <!-- Função Eclesiástica -->
                  <td>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                      <i class="bi bi-person-badge me-1"></i><?= esc($row->nm_funcao_eclesiastica ?? 'Sem Função'); ?>
                    </span>
                  </td>

                  <!-- Assiduidade & Presenças -->
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <?php if ($pct >= 70) { ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold" title="Alta frequência nos cultos">
                          <i class="bi bi-graph-up-arrow me-1"></i><?= $pct ?>% (<?= $presencas ?> <?= $presencas == 1 ? 'culto' : 'cultos' ?>)
                        </span>
                      <?php } elseif ($pct >= 40) { ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-bold" title="Frequência moderada">
                          <i class="bi bi-dash-circle me-1"></i><?= $pct ?>% (<?= $presencas ?> <?= $presencas == 1 ? 'culto' : 'cultos' ?>)
                        </span>
                      <?php } else { ?>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-2" title="Baixa frequência">
                          <i class="bi bi-circle me-1"></i><?= $pct ?>% (<?= $presencas ?> <?= $presencas == 1 ? 'culto' : 'cultos' ?>)
                        </span>
                      <?php } ?>
                    </div>
                  </td>

                  <!-- Status -->
                  <td class="text-center">
                    <span class="badge rounded-pill bg-<?= ($row->status_convidado == 1 ? 'success-subtle text-success' : 'danger-subtle text-danger') ?> px-3 py-1">
                      <?= ($row->status_convidado == 1 ? 'Ativo' : 'Inativo') ?>
                    </span>
                  </td>

                  <!-- Ações -->
                  <td class="text-end pe-4">
                    <div class="btn-group btn-group-sm">
                      <?php if ($sys_action->read) { ?>
                        <a href="<?= base_url('agenda/dashConvidado/' . $row->id_convidado) ?>" class="btn btn-outline-info" title="Ver Dashboard de Assiduidade">
                          <i class="bi bi-graph-up-arrow"></i>
                        </a>
                        <a href="<?= base_url('convidado/editar/' . $row->hash_convidado) ?>" class="btn btn-outline-warning" title="Editar">
                          <i class="bi bi-pencil-square"></i>
                        </a>
                      <?php } ?>
                      <?php if ($sys_action->delete) { ?>
                        <button type="button" class="btn btn-outline-danger btnConfirmarDelete" data-titulo="Confirmar exclusão" data-url="<?= base_url('geral/getModalDelete') ?>" data-link="<?= base_url('convidado/apagar/' . $row->hash_convidado) ?>" data-mensagem="Deseja confirmar a exclusão deste convidado <strong><?= esc($row->nome_convidado) ?></strong>?" title="Apagar" data-bs-toggle="modal" data-bs-target="#divModalConfirmaDelete">
                          <i class="bi bi-trash"></i>
                        </button>
                      <?php } ?>
                    </div>
                  </td>

                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput  = document.getElementById('searchConvidadoInput');
    const filterFuncao = document.getElementById('filterFuncaoSelect');
    const filterPerf   = document.getElementById('filterPerformanceSelect');

    function applyFilters() {
      const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
      const funcaoId = (filterFuncao ? filterFuncao.value : '').toString();
      const perfVal  = (filterPerf ? filterPerf.value : '').toString();

      document.querySelectorAll('#tblConvidados tbody tr.item-convidado').forEach(row => {
        const nome   = row.getAttribute('data-nome') || '';
        const funcao = (row.getAttribute('data-id-funcao') || '').toString();
        const pres   = parseInt(row.getAttribute('data-presencas') || '0', 10);

        const matchName   = !q || nome.includes(q);
        const matchFuncao = !funcaoId || funcao === funcaoId;
        
        let matchPerf = true;
        if (perfVal === 'alta') {
          matchPerf = (pres >= 5);
        } else if (perfVal === 'media') {
          matchPerf = (pres >= 1 && pres <= 4);
        } else if (perfVal === 'baixa') {
          matchPerf = (pres === 0);
        }

        if (matchName && matchFuncao && matchPerf) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterFuncao) filterFuncao.addEventListener('change', applyFilters);
    if (filterPerf) filterPerf.addEventListener('change', applyFilters);

    // Bootstrap Modal delete trigger handler
    if (typeof jQuery !== 'undefined') {
      $(document).on("click", ".btnConfirmarDelete", function (e) {
        e.preventDefault();
        if (typeof FrameworkModal !== 'undefined') {
          FrameworkModal.getModalDelete($(this));
        }
      });
    }
  });
</script>