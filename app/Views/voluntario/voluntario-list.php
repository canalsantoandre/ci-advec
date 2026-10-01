<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-person-hearts text-primary me-2"></i>Gestão de Voluntários
        </h3>
        <p class="text-secondary small mb-0">Cadastro de membros voluntários, departamentos, sub-áreas de atuação e indicadores de assiduidade</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Voluntários</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <!-- Filtros e Barra de Ações -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 mb-4">
      <div class="card-body p-3">
        <form method="get" action="<?= base_url('voluntario') ?>" class="row g-2 align-items-center">

          <!-- Busca por texto -->
          <div class="col-12 col-md-4 col-xl-3">
            <div class="input-group">
              <span class="input-group-text bg-body-tertiary border-end-0">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por nome, WhatsApp..." value="<?= esc($filtros['busca'] ?? '') ?>">
            </div>
          </div>

          <!-- Filtro por Departamento -->
          <div class="col-6 col-md-4 col-xl-3">
            <select name="id_departamento" class="form-select" onchange="this.form.submit()">
              <option value="">Todos os Departamentos</option>
              <?php foreach ($departamentos as $d) { ?>
                <option value="<?= $d->id_departamento ?>" <?= (isset($filtros['id_departamento']) && $filtros['id_departamento'] == $d->id_departamento) ? 'selected' : '' ?>>
                  <?= esc($d->nome) ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <!-- Filtro por Status -->
          <div class="col-6 col-md-4 col-xl-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
              <option value="">Todos os Status</option>
              <option value="1" <?= (isset($filtros['status']) && $filtros['status'] === '1') ? 'selected' : '' ?>>Ativos</option>
              <option value="0" <?= (isset($filtros['status']) && $filtros['status'] === '0') ? 'selected' : '' ?>>Inativos</option>
            </select>
          </div>

          <!-- Botões de Ação e Filtros -->
          <div class="col-12 col-xl-4 d-flex gap-2 justify-content-end align-items-center flex-wrap ms-auto mt-2 mt-xl-0">
            <!-- Botão Filtrar -->
            <button type="submit" class="btn btn-secondary rounded-3 px-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Filtrar Voluntários">
              <i class="bi bi-funnel-fill"></i>
            </button>

            <!-- Botão Limpar Filtro -->
            <?php if (!empty($filtros['busca']) || !empty($filtros['id_departamento']) || isset($filtros['status']) && $filtros['status'] !== '') { ?>
              <a href="<?= base_url('voluntario') ?>" class="btn btn-outline-secondary rounded-3 px-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Limpar Filtros">
                <i class="bi bi-x-lg"></i>
              </a>
            <?php } ?>

            <!-- Botão Relatório de Desempenho -->
            <a href="<?= base_url('voluntario/desempenho') ?>" class="btn btn-outline-info rounded-3 px-3 fw-semibold text-nowrap" data-bs-toggle="tooltip" data-bs-placement="top" title="Relatório de Desempenho & Cancelamentos">
              <i class="bi bi-person-lines-fill"></i>
              <span class="d-none d-sm-inline ms-1">Desempenho</span>
            </a>

            <!-- Botão Novo Voluntário -->
            <?php if (!empty($sys_action->create)) { ?>
              <a href="<?= base_url('voluntario/novo') ?>" class="btn btn-primary rounded-3 px-3 fw-semibold shadow-sm text-nowrap" data-bs-toggle="tooltip" data-bs-placement="top" title="Cadastrar Novo Voluntário">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-sm-inline ms-1"></span>
              </a>
            <?php } ?>
          </div>

        </form>
      </div>
    </div>

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

    <!-- Tabela Principal de Voluntários -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-body py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold mb-0 text-primary">
          <i class="bi bi-people-fill me-2"></i> Lista de Voluntários (<?= count($voluntarios) ?>)
        </h5>
        <a href="<?= base_url('escala/grade') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
          <i class="bi bi-calendar-check me-1"></i> Ir para Grade Mensal
        </a>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col" class="ps-4">Voluntário</th>
                <th scope="col">Contato</th>
                <th scope="col">Departamentos & Sub-áreas</th>
                <th scope="col" class="text-center" style="width: 140px;">Assiduidade</th>
                <th scope="col" class="text-center" style="width: 100px;">Status</th>
                <th scope="col" class="text-end pe-4" style="width: 140px;">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($voluntarios)) { ?>
                <?php foreach ($voluntarios as $v) {
                  $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($v->nome) . '&background=2563eb&color=fff&size=80&bold=true';
                  $avatarSrc = !empty($v->foto_url) ? $v->foto_url : $defaultAvatar;
                  $idade = !empty($v->data_nascimento) ? date_diff(date_create($v->data_nascimento), date_create('today'))->y : null;
                ?>
                  <tr>
                    <!-- Nome & Avatar -->
                    <td class="ps-4">
                      <div class="d-flex align-items-center gap-3">
                        <img src="<?= esc($avatarSrc) ?>" alt="<?= esc($v->nome) ?>" class="rounded-circle shadow-sm border" style="width: 44px; height: 44px; object-fit: cover;" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
                        <div>
                          <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-body fs-6"><?= esc($v->nome) ?></span>
                            <?php if (!empty($v->nickname)) { ?>
                              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0 fw-bold" style="font-size: 0.72rem;" title="Nickname">
                                <i class="bi bi-tag-fill me-1"></i><?= esc($v->nickname) ?>
                              </span>
                            <?php } ?>
                          </div>
                          <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                            <?php
                            $badgeNivel = [
                              'APRENDIZ' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                              'JUNIOR'   => 'bg-info-subtle text-info border border-info-subtle',
                              'PLENO'    => 'bg-success-subtle text-success border border-success-subtle',
                              'SENIOR'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold'
                            ];
                            $nv = !empty($v->nivel_conhecimento) ? strtoupper($v->nivel_conhecimento) : 'JUNIOR';
                            $classNv = $badgeNivel[$nv] ?? 'bg-light text-dark';
                            ?>
                            <span class="badge rounded-pill px-2 py-0 <?= $classNv ?>" style="font-size: 0.68rem;" title="Nível de conhecimento">
                              <?= $nv ?>
                            </span>

                            <?php if ($idade !== null) { ?>
                              <small class="text-muted"><i class="bi bi-cake2 me-1"></i><?= $idade ?> anos</small>
                            <?php } ?>
                            <?php if (!empty($v->max_escalas_mes) && $v->max_escalas_mes > 0) { ?>
                              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0" style="font-size: 0.68rem;" title="Limite mensal de escalas">
                                <i class="bi bi-speedometer2 me-1"></i>Máx: <?= $v->max_escalas_mes ?>/mês
                              </span>
                            <?php } else { ?>
                              <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0" style="font-size: 0.68rem;" title="Sem restrição de escalas no mês">
                                <i class="bi bi-infinity me-1"></i>Ilimitado
                              </span>
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Contatos -->
                    <td>
                      <div>
                        <a href="mailto:<?= esc($v->email) ?>" class="text-decoration-none text-body small d-block text-truncate" style="max-width: 200px;">
                          <i class="bi bi-envelope me-1 text-muted"></i><?= esc($v->email) ?>
                        </a>
                        <a href="https://wa.me/55<?= preg_replace('/\D/', '', $v->telefone_whatsapp) ?>" target="_blank" class="text-decoration-none text-success small fw-semibold">
                          <i class="bi bi-whatsapp me-1"></i><?= esc($v->telefone_whatsapp) ?>
                        </a>
                      </div>
                    </td>

                    <!-- Departamentos & Áreas -->
                    <td>
                      <?php if (!empty($v->areas)) { ?>
                        <div class="d-flex flex-wrap gap-1">
                          <?php foreach ($v->areas as $va) {
                            $corDep = !empty($va->cor_identificacao) ? $va->cor_identificacao : '#2563eb';
                          ?>
                            <span class="badge rounded-pill px-2 py-1 text-white small" style="background-color: <?= esc($corDep) ?>;" title="<?= esc($va->nome_departamento) ?>">
                              <?= esc($va->nome_departamento) ?>: <strong><?= esc($va->nome_area) ?></strong>
                            </span>
                          <?php } ?>
                        </div>
                      <?php } else { ?>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1">
                          Sem áreas vinculadas
                        </span>
                      <?php } ?>
                    </td>

                    <!-- Assiduidade -->
                    <td class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="fw-bold small text-<?= ($v->taxa_assiduidade >= 80 ? 'success' : ($v->taxa_assiduidade >= 50 ? 'warning' : 'danger')) ?>">
                          <?= $v->taxa_assiduidade ?>%
                        </span>
                        <div class="progress w-100 mt-1" style="height: 5px; max-width: 80px;">
                          <div class="progress-bar bg-<?= ($v->taxa_assiduidade >= 80 ? 'success' : ($v->taxa_assiduidade >= 50 ? 'warning' : 'danger')) ?>" role="progressbar" style="width: <?= $v->taxa_assiduidade ?>%;"></div>
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;"><?= (int)$v->total_presencas ?>/<?= (int)$v->total_escalas ?> cultos</small>
                      </div>
                    </td>

                    <!-- Status -->
                    <td class="text-center">
                      <?php if ($v->status == 1) { ?>
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 small">
                          Ativo
                        </span>
                      <?php } else { ?>
                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2 py-1 small">
                          Inativo
                        </span>
                      <?php } ?>
                    </td>

                    <!-- Ações -->
                    <td class="text-end pe-4">
                      <div class="d-inline-flex gap-1">
                        <a href="<?= base_url('voluntario/dashVoluntario/' . $v->id_voluntario) ?>" class="btn btn-outline-info btn-action" title="Dashboard de Assiduidade">
                          <i class="bi bi-graph-up-arrow"></i>
                        </a>

                        <?php if (!empty($sys_action->update)) { ?>
                          <button type="button" class="btn btn-outline-warning btn-action" title="Resetar Senha para Telefone Padrão" onclick="abrirModalResetSenhaVol(<?= $v->id_voluntario ?>, '<?= esc($v->nome) ?>', '<?= esc($v->telefone_whatsapp) ?>')">
                            <i class="bi bi-key-fill"></i>
                          </button>
                          <a href="<?= base_url('voluntario/editar/' . ($v->hash_voluntario ?: $v->id_voluntario)) ?>" class="btn btn-outline-primary btn-action" title="Editar Cadastro">
                            <i class="bi bi-pencil-fill"></i>
                          </a>
                        <?php } ?>

                        <?php if (!empty($sys_action->delete)) { ?>
                          <button type="button" class="btn btn-outline-danger btn-action" title="Excluir Voluntário" onclick="confirmarExclusaoVol(<?= $v->id_voluntario ?>, '<?= esc($v->nome) ?>')">
                            <i class="bi bi-trash3-fill"></i>
                          </button>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-person-x fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-2 fw-semibold">Nenhum voluntário encontrado com os filtros selecionados.</p>
                    <?php if (!empty($sys_action->create)) { ?>
                      <a href="<?= base_url('voluntario/novo') ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="bi bi-plus-lg me-1"></i> Cadastrar Novo Voluntário
                      </a>
                    <?php } ?>
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

<!-- Modal Confirmação de Reset de Senha do Voluntário -->
<div class="modal fade" id="modalResetSenhaVol" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-warning-emphasis">
          <i class="bi bi-key-fill me-2 text-warning"></i> Resetar Senha do Voluntário
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body py-3">
        <p class="mb-2">Deseja realmente resetar a senha de acesso do voluntário <strong id="modalResetNomeVol"></strong>?</p>
        <div class="p-3 bg-body-tertiary rounded-3 border small">
          <i class="bi bi-info-circle-fill text-primary me-1"></i> A nova senha será redefinida para o número de WhatsApp cadastrado: <br>
          <strong class="text-success fs-6 mt-1 d-block font-monospace" id="modalResetFoneVol"></strong>
          <span class="text-muted">(Apenas os dígitos numéricos, sem traços ou parênteses).</span>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btnConfirmarResetSenhaSubmit" class="btn btn-warning rounded-pill px-4 fw-bold" onclick="executarResetSenhaVoluntario()">
          <i class="bi bi-check-lg me-1"></i> Confirmar Reset
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmação de Exclusão -->
<div class="modal fade" id="modalConfirmarExclusaoVol" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Exclusão
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body py-3">
        <p class="mb-0">Deseja realmente excluir o cadastro do voluntário <strong id="modalNomeVol"></strong>?</p>
        <small class="text-danger d-block mt-2">
          <i class="bi bi-info-circle me-1"></i> Esta ação removerá os vínculos e histórico de escalas associadas.
        </small>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnConfirmarDeleteVol" class="btn btn-danger rounded-pill px-4 fw-bold">
          <i class="bi bi-trash3-fill me-1"></i> Excluir Voluntário
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  let idVoluntarioParaReset = null;
  let modalResetInstance = null;

  function abrirModalResetSenhaVol(id, nome, fone) {
    idVoluntarioParaReset = id;
    document.getElementById('modalResetNomeVol').textContent = nome;
    const cleanDigits = (fone || '').replace(/\D/g, '');
    document.getElementById('modalResetFoneVol').textContent = cleanDigits || 'Telefone não cadastrado';

    if (!modalResetInstance) {
      modalResetInstance = new bootstrap.Modal(document.getElementById('modalResetSenhaVol'));
    }
    modalResetInstance.show();
  }

  function executarResetSenhaVoluntario() {
    if (!idVoluntarioParaReset) return;

    const btn = document.getElementById('btnConfirmarResetSenhaSubmit');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Resetando...';

    const formData = new FormData();
    formData.append('id_voluntario', idVoluntarioParaReset);

    fetch('<?= base_url('voluntario/resetSenha') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Confirmar Reset';
        if (modalResetInstance) modalResetInstance.hide();

        if (data.status === 'success') {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Senha Redefinida', data.message);
          } else if (typeof usShowToast === 'function') {
            usShowToast('success', 'Senha Redefinida', data.message);
          }
        } else {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Erro', data.message || 'Falha ao redefinir senha.');
          } else if (typeof usShowToast === 'function') {
            usShowToast('error', 'Erro', data.message || 'Falha ao redefinir senha.');
          }
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Confirmar Reset';
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Erro', 'Falha na conexão com o servidor.');
        } else if (typeof usShowToast === 'function') {
          usShowToast('error', 'Erro', 'Falha na conexão com o servidor.');
        }
      });
  }

  function confirmarExclusaoVol(id, nome) {
    document.getElementById('modalNomeVol').textContent = nome;
    document.getElementById('btnConfirmarDeleteVol').href = '<?= base_url('voluntario/apagar/') ?>/' + id;
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarExclusaoVol'));
    modal.show();
  }

  // Inicializa tooltips do Bootstrap
  document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>