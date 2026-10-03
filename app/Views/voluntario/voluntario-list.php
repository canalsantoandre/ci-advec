<?php
if (!function_exists('renderizarRedesSociaisBadgeList')) {
  function renderizarRedesSociaisBadgeList($redesJson)
  {
    if (empty($redesJson)) return '';
    $redes = is_string($redesJson) ? json_decode($redesJson, true) : $redesJson;
    if (!is_array($redes) || empty($redes)) return '';

    $html = '<div class="d-flex flex-wrap gap-1 mt-1">';
    foreach ($redes as $r) {
      $plat = strtolower(trim((string)($r['plataforma'] ?? $r['rede'] ?? '')));
      $url  = trim((string)($r['url'] ?? $r['link'] ?? ''));
      if (empty($url)) continue;

      $icon = 'bi bi-link-45deg';
      $bg = '#6c757d';
      $href = $url;
      if (strpos($plat, 'instagram') !== false) {
        $icon = 'bi bi-instagram';
        $bg = '#E1306C';
        if (strpos($href, 'http') !== 0) $href = 'https://instagram.com/' . ltrim($href, '@');
      } elseif (strpos($plat, 'tiktok') !== false) {
        $icon = 'bi bi-tiktok';
        $bg = '#010101';
        if (strpos($href, 'http') !== 0) $href = 'https://tiktok.com/@' . ltrim($href, '@');
      } elseif (strpos($plat, 'youtube') !== false) {
        $icon = 'bi bi-youtube';
        $bg = '#FF0000';
        if (strpos($href, 'http') !== 0) $href = 'https://youtube.com/' . (strpos($href, '@') === 0 ? $href : '@' . $href);
      } elseif (strpos($plat, 'linkedin') !== false) {
        $icon = 'bi bi-linkedin';
        $bg = '#0A66C2';
        if (strpos($href, 'http') !== 0) $href = 'https://linkedin.com/in/' . ltrim($href, '@');
      } elseif (strpos($plat, 'threads') !== false) {
        $icon = 'bi bi-threads';
        $bg = '#101010';
        if (strpos($href, 'http') !== 0) $href = 'https://threads.net/@' . ltrim($href, '@');
      } elseif (strpos($plat, 'facebook') !== false) {
        $icon = 'bi bi-facebook';
        $bg = '#1877F2';
        if (strpos($href, 'http') !== 0) $href = 'https://facebook.com/' . ltrim($href, '@');
      } elseif (strpos($plat, 'twitter') !== false || strpos($plat, 'x') !== false) {
        $icon = 'bi bi-twitter-x';
        $bg = '#000000';
        if (strpos($href, 'http') !== 0) $href = 'https://x.com/' . ltrim($href, '@');
      }

      $displayNick = $url;
      if (strlen($displayNick) > 18) {
        $displayNick = substr($displayNick, 0, 16) . '...';
      }

      $html .= '<a href="' . esc($href) . '" target="_blank" rel="noopener noreferrer" class="badge rounded-pill text-white text-decoration-none d-inline-flex align-items-center gap-1 px-2 py-0.5" style="background-color:' . $bg . '; font-size:0.7rem;" title="' . esc(ucfirst($plat) . ': ' . $url) . '">';
      $html .= '<i class="' . $icon . '"></i> <span>' . esc($displayNick) . '</span>';
      $html .= '</a>';
    }
    $html .= '</div>';
    return $html;
  }
}
?>
<style>
  /* Customizações Premium para o Modal de Aprovação e Sub-áreas */
  .profile-approval-card {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(15, 23, 42, 0.02) 100%);
    border: 1px solid var(--bs-border-color);
    border-radius: 1rem;
    padding: 1rem 1.15rem;
  }

  [data-bs-theme="dark"] .profile-approval-card {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.12) 0%, rgba(30, 41, 59, 0.5) 100%);
    border-color: rgba(255, 255, 255, 0.08);
  }

  .subarea-choice-card {
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid var(--bs-border-color);
    background-color: var(--bs-body-bg);
    cursor: pointer;
    user-select: none;
    padding: 0.65rem 0.85rem;
    border-radius: 0.75rem;
  }

  .subarea-choice-card:hover {
    border-color: #2563eb;
    background-color: var(--bs-tertiary-bg);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }

  .subarea-choice-card.is-selected {
    border-color: #2563eb !important;
    background: rgba(37, 99, 235, 0.08) !important;
    box-shadow: 0 0 0 1px #2563eb;
  }

  [data-bs-theme="dark"] .subarea-choice-card.is-selected {
    background: rgba(37, 99, 235, 0.22) !important;
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 1px #60a5fa;
  }

  .subarea-choice-check {
    width: 1.15rem;
    height: 1.15rem;
    cursor: pointer;
  }

  .custom-subareas-scroll {
    max-height: 190px;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    padding: 0.65rem;
  }

  .custom-subareas-scroll::-webkit-scrollbar {
    width: 6px;
  }

  .custom-subareas-scroll::-webkit-scrollbar-thumb {
    background: rgba(100, 116, 139, 0.3);
    border-radius: 4px;
  }

  .social-pill-badge {
    padding: 0.25rem 0.6rem;
    font-size: 0.73rem;
    border-radius: 9999px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: transform 0.15s ease, opacity 0.15s ease;
  }

  .social-pill-badge:hover {
    transform: translateY(-1px);
    opacity: 0.9;
  }
</style>
<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-person-hearts text-primary me-2"></i>Gestão de Voluntários
        </h3>
        <p class="text-secondary small mb-0">Cadastro de membros voluntários, departamentos, sub-áreas de atuação e aprovação de novos cadastros</p>
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
          <input type="hidden" name="tab" id="filtroTabAtiva" value="<?= esc($abaAtiva ?? 'ativos') ?>">

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

            <!-- Botão Convidar Voluntários (Self-Onboarding) -->
            <?php if (!empty($sys_action->send_invite)) { ?>
              <button type="button" class="btn btn-outline-success rounded-3 px-3 fw-semibold text-nowrap" data-bs-toggle="modal" data-bs-target="#modalGerarConvite" title="Gerar Links de Convite para Voluntários">
                <i class="bi bi-send-plus-fill me-1"></i>
                <span class="d-none d-sm-inline">Convidar</span>
              </button>
            <?php } ?>

            <!-- Botão Relatório de Desempenho -->
            <a href="<?= base_url('voluntario/desempenho') ?>" class="btn btn-outline-info rounded-3 px-3 fw-semibold text-nowrap" data-bs-toggle="tooltip" data-bs-placement="top" title="Relatório de Desempenho & Cancelamentos">
              <i class="bi bi-person-lines-fill"></i>
              <span class="d-none d-sm-inline ms-1">Desempenho</span>
            </a>

            <!-- Botão Novo Voluntário Manual -->
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

    <!-- Abas / Tabs de Navegação de Voluntários e Cadastros Pendentes -->
    <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-body-tertiary rounded-4 shadow-sm mb-3 border" id="tabsVoluntarios" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-3 fw-bold <?= ($abaAtiva === 'ativos' || empty($abaAtiva)) ? 'active' : '' ?>" id="tab-ativos-btn" data-bs-toggle="tab" data-bs-target="#tab-ativos" type="button" role="tab" aria-controls="tab-ativos" aria-selected="true" onclick="setTabAtiva('ativos')">
          <i class="bi bi-people-fill me-1.5"></i> Voluntários Cadastrados
          <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill ms-1.5"><?= count($voluntarios) ?></span>
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-3 fw-bold position-relative <?= ($abaAtiva === 'pendentes') ? 'active' : '' ?>" id="tab-pendentes-btn" data-bs-toggle="tab" data-bs-target="#tab-pendentes" type="button" role="tab" aria-controls="tab-pendentes" aria-selected="false" onclick="setTabAtiva('pendentes')">
          <i class="bi bi-person-plus-fill me-1.5 text-warning"></i> Cadastros Pendentes (Self-Onboarding)
          <?php if (!empty($totalPendentes) && $totalPendentes > 0) { ?>
            <span class="badge bg-danger text-white rounded-pill ms-1.5 fw-bold shadow-xs">
              <?= $totalPendentes ?> novo(s)
            </span>
          <?php } else { ?>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1.5">0</span>
          <?php } ?>
        </button>
      </li>

      <?php if (!empty($sys_action->send_invite)) { ?>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-3 fw-bold <?= ($abaAtiva === 'convites') ? 'active' : '' ?>" id="tab-convites-btn" data-bs-toggle="tab" data-bs-target="#tab-convites" type="button" role="tab" aria-controls="tab-convites" aria-selected="false" onclick="setTabAtiva('convites')">
            <i class="bi bi-link-45deg me-1.5 text-success"></i> Links de Convite Gerados
            <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1.5"><?= count($convites ?? []) ?></span>
          </button>
        </li>
      <?php } ?>
    </ul>

    <!-- Conteúdo das Abas -->
    <div class="tab-content" id="tabsVoluntariosContent">

      <!-- ======================================================== -->
      <!-- ABA 1: VOLUNTÁRIOS ATIVOS / CADASTRADOS                  -->
      <!-- ======================================================== -->
      <div class="tab-pane fade <?= ($abaAtiva === 'ativos' || empty($abaAtiva)) ? 'show active' : '' ?>" id="tab-ativos" role="tabpanel" aria-labelledby="tab-ativos-btn">
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
                        <p class="mb-2 fw-semibold">Nenhum voluntário cadastrado encontrado com os filtros selecionados.</p>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- ABA 2: CADASTROS PENDENTES (SELF-ONBOARDING)             -->
      <!-- ======================================================== -->
      <div class="tab-pane fade <?= ($abaAtiva === 'pendentes') ? 'show active' : '' ?>" id="tab-pendentes" role="tabpanel" aria-labelledby="tab-pendentes-btn">
        <div class="card card-outline card-warning shadow-sm border-0 rounded-4 overflow-hidden mb-4">
          <div class="card-header bg-body py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-warning-emphasis">
              <i class="bi bi-person-plus-fill me-2 text-warning"></i> Novos Voluntários Aguardando Aprovação (<?= count($pendentes ?? []) ?>)
            </h5>
            <small class="text-muted">Revise as informações e aprove o cadastro para liberar o acesso ao portal.</small>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col" class="ps-4">Candidato</th>
                    <th scope="col">Contato</th>
                    <th scope="col">Departamento & Sub-área Solicitada</th>
                    <th scope="col">Origem do Convite</th>
                    <th scope="col">Data do Pré-Cadastro</th>
                    <th scope="col" class="text-end pe-4" style="width: 180px;">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($pendentes)) { ?>
                    <?php foreach ($pendentes as $p) {
                      $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($p->nome) . '&background=f59e0b&color=fff&size=80&bold=true';
                      $avatarSrc = !empty($p->foto_url) ? $p->foto_url : $defaultAvatar;
                      $idade = !empty($p->data_nascimento) ? date_diff(date_create($p->data_nascimento), date_create('today'))->y : null;
                      $corDep = !empty($p->cor_departamento) ? $p->cor_departamento : '#2563eb';
                    ?>
                      <tr>
                        <!-- Candidato -->
                        <td class="ps-4">
                          <div class="d-flex align-items-center gap-3">
                            <img src="<?= esc($avatarSrc) ?>" alt="<?= esc($p->nome) ?>" class="rounded-circle shadow-sm border" style="width: 46px; height: 46px; object-fit: cover;" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
                            <div>
                              <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-body fs-6"><?= esc($p->nome) ?></span>
                                <?php if (!empty($p->nickname)) { ?>
                                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0 fw-bold" style="font-size: 0.72rem;">
                                    &ldquo;<?= esc($p->nickname) ?>&rdquo;
                                  </span>
                                <?php } ?>
                              </div>
                              <div class="d-flex align-items-center gap-2 mt-1">
                                <?php if ($idade !== null) { ?>
                                  <small class="text-muted"><i class="bi bi-cake2 me-1"></i><?= $idade ?> anos (<?= date('d/m/Y', strtotime($p->data_nascimento)) ?>)</small>
                                <?php } ?>
                              </div>
                            </div>
                          </div>
                        </td>

                        <!-- Contato -->
                        <td>
                          <div>
                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $p->telefone_whatsapp) ?>" target="_blank" class="text-decoration-none text-success small fw-bold d-block">
                              <i class="bi bi-whatsapp me-1"></i><?= esc($p->telefone_whatsapp) ?>
                            </a>
                            <?php if (!empty($p->email)) { ?>
                              <span class="text-muted small d-block"><i class="bi bi-envelope me-1"></i><?= esc($p->email) ?></span>
                            <?php } ?>
                            <?= renderizarRedesSociaisBadgeList($p->redes_sociais) ?>
                          </div>
                        </td>

                        <!-- Departamento & Sub-área Solicitada -->
                        <td>
                          <div>
                            <span class="badge rounded-pill px-2.5 py-1 text-white small" style="background-color: <?= esc($corDep) ?>;">
                              <?= esc($p->nome_departamento) ?>
                            </span>
                            <div class="d-flex flex-wrap gap-1 mt-1.5">
                              <?php
                              $areasList = !empty($p->nome_area) ? explode(',', $p->nome_area) : ['Geral'];
                              foreach ($areasList as $aNome) {
                                $aNome = trim($aNome);
                                if (!empty($aNome)) {
                              ?>
                                  <span class="badge bg-body-secondary text-body border rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                    <i class="bi bi-diagram-3 me-1 text-primary"></i><?= esc($aNome) ?>
                                  </span>
                              <?php }
                              } ?>
                            </div>
                          </div>
                        </td>

                        <!-- Origem -->
                        <td>
                          <?php if ($p->tipo_convite === 'DIRETO') { ?>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 small">
                              <i class="bi bi-person-check-fill me-1"></i> Convite Direto
                            </span>
                          <?php } elseif ($p->tipo_convite === 'LOTE') { ?>
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5 small">
                              <i class="bi bi-collection-fill me-1"></i> Link em Lote
                            </span>
                          <?php } else { ?>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 small">Self-Onboarding</span>
                          <?php } ?>
                        </td>

                        <!-- Data -->
                        <td>
                          <span class="small text-body"><?= date('d/m/Y H:i', strtotime($p->date_insert)) ?></span>
                        </td>

                        <!-- Ações de Aprovação -->
                        <td class="text-end pe-4">
                          <div class="d-inline-flex gap-1.5">
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-xs d-flex align-items-center gap-1" onclick="abrirModalAprovar(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)">
                              <i class="bi bi-check-lg"></i> Revisar
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" onclick="abrirModalRejeitar(<?= $p->id_voluntario ?>, '<?= esc($p->nome) ?>')" title="Rejeitar Cadastro">
                              <i class="bi bi-x-lg"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php } ?>
                  <?php } else { ?>
                    <tr>
                      <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2 opacity-50"></i>
                        <p class="mb-1 fw-bold text-body">Tudo em dia!</p>
                        <p class="mb-0 small text-secondary">Nenhum pré-cadastro pendente de aprovação no momento.</p>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- ABA 3: LINKS DE CONVITE GERADOS                         -->
      <!-- ======================================================== -->
      <?php if (!empty($sys_action->send_invite)) { ?>
        <div class="tab-pane fade <?= ($abaAtiva === 'convites') ? 'show active' : '' ?>" id="tab-convites" role="tabpanel" aria-labelledby="tab-convites-btn">
          <div class="card card-outline card-success shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-body py-3 d-flex justify-content-between align-items-center">
              <h5 class="card-title fw-bold mb-0 text-success">
                <i class="bi bi-link-45deg me-2"></i> Links de Convite Criados (<?= count($convites ?? []) ?>)
              </h5>
              <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalGerarConvite">
                <i class="bi bi-plus-lg me-1"></i> Criar Novo Link
              </button>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" class="ps-4">Tipo & Modalidade</th>
                      <th scope="col">Departamento</th>
                      <th scope="col">Destino / Capacidade</th>
                      <th scope="col">Utilizações</th>
                      <th scope="col">Validade</th>
                      <th scope="col" class="text-center">Status</th>
                      <th scope="col" class="text-end pe-4" style="width: 200px;">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($convites)) { ?>
                      <?php foreach ($convites as $c) {
                        $isAtivo = ($c->status === 'ATIVO');
                      ?>
                        <tr>
                          <!-- Tipo -->
                          <td class="ps-4">
                            <?php if ($c->tipo === 'DIRETO') { ?>
                              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold">
                                <i class="bi bi-person-fill me-1"></i> Convite Direto (1 uso)
                              </span>
                            <?php } else { ?>
                              <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fw-bold">
                                <i class="bi bi-people-fill me-1"></i> Convite em Lote (<?= $c->capacidade_maxima ?> usos)
                              </span>
                            <?php } ?>
                          </td>

                          <!-- Departamento -->
                          <td>
                            <span class="badge rounded-pill px-2.5 py-1 text-white small" style="background-color: <?= esc($c->cor_departamento ?: '#2563eb') ?>;">
                              <?= esc($c->nome_departamento) ?>
                            </span>
                          </td>

                          <!-- Telefone ou Capacidade -->
                          <td>
                            <?php if (!empty($c->telefone)) { ?>
                              <span class="text-success small fw-semibold"><i class="bi bi-whatsapp me-1"></i><?= esc($c->telefone) ?></span>
                            <?php } else { ?>
                              <span class="text-muted small">Link genérico aberto</span>
                            <?php } ?>
                          </td>

                          <!-- Utilizações -->
                          <td>
                            <div class="d-flex align-items-center gap-2" style="max-width: 140px;">
                              <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar bg-<?= ($c->percentual_uso >= 100 ? 'danger' : 'success') ?>" role="progressbar" style="width: <?= $c->percentual_uso ?>%;"></div>
                              </div>
                              <span class="small fw-bold text-body"><?= (int)$c->usos_realizados ?>/<?= (int)$c->capacidade_maxima ?></span>
                            </div>
                          </td>

                          <!-- Validade -->
                          <td>
                            <?php if (!empty($c->expires_at)) { ?>
                              <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>Até <?= date('d/m/Y', strtotime($c->expires_at)) ?></small>
                            <?php } else { ?>
                              <small class="text-muted"><i class="bi bi-infinity me-1"></i>Indeterminado</small>
                            <?php } ?>
                          </td>

                          <!-- Status -->
                          <td class="text-center">
                            <?php if ($c->status === 'ATIVO') { ?>
                              <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 small">Ativo</span>
                            <?php } elseif ($c->status === 'EXPIRADO') { ?>
                              <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 small">Expirado</span>
                            <?php } else { ?>
                              <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5 small">Encerrado</span>
                            <?php } ?>
                          </td>

                          <!-- Ações -->
                          <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                              <!-- Botão Copiar Link -->
                              <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5" onclick="copiarLinkConvite('<?= esc($c->link_completo) ?>')" title="Copiar Link de Convite">
                                <i class="bi bi-clipboard-check me-1"></i> Copiar
                              </button>

                              <!-- Botão Encerrar Link -->
                              <?php if ($isAtivo) { ?>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2" onclick="encerrarLinkConvite(<?= $c->id_convite ?>)" title="Encerrar / Invalidar Link Antecipadamente">
                                  <i class="bi bi-slash-circle"></i>
                                </button>
                              <?php } ?>
                            </div>
                          </td>
                        </tr>
                      <?php } ?>
                    <?php } else { ?>
                      <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                          <i class="bi bi-link-45deg fs-1 d-block mb-2 opacity-50"></i>
                          <p class="mb-1 fw-bold text-body">Nenhum convite criado ainda.</p>
                          <p class="mb-0 small text-secondary">Gere links para que os voluntários façam o pré-cadastro.</p>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

    </div>

  </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DE CRIAÇÃO DE CONVITES (DIRETO E LOTE)            -->
<!-- ======================================================== -->
<div class="modal fade" id="modalGerarConvite" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-success">
          <i class="bi bi-send-plus-fill me-2"></i> Convidar Novos Voluntários
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body py-3">
        <!-- Tabs do Modal: Direto vs Lote -->
        <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-body-tertiary rounded-3 mb-3 border" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link rounded-2 fw-bold active" id="tab-convite-direto-btn" data-bs-toggle="tab" data-bs-target="#tab-convite-direto" type="button" role="tab" aria-selected="true" onclick="$('#tipoConviteInput').val('DIRETO');">
              <i class="bi bi-whatsapp me-1"></i> Convite Direto (WhatsApp)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link rounded-2 fw-bold" id="tab-convite-lote-btn" data-bs-toggle="tab" data-bs-target="#tab-convite-lote" type="button" role="tab" aria-selected="false" onclick="$('#tipoConviteInput').val('LOTE');">
              <i class="bi bi-collection-fill me-1 text-primary"></i> Convite em Lote (Capacidade)
            </button>
          </li>
        </ul>

        <form id="formGerarConviteModal">
          <input type="hidden" name="tipo" id="tipoConviteInput" value="DIRETO">

          <!-- Seleção do Departamento -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-body">Departamento *</label>
            <select name="id_departamento" id="conviteIdDepartamento" class="form-select rounded-3" required>
              <?php foreach ($departamentos as $d) { ?>
                <option value="<?= $d->id_departamento ?>"><?= esc($d->nome) ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="tab-content">
            <!-- Aba Direto -->
            <div class="tab-pane fade show active" id="tab-convite-direto" role="tabpanel">
              <div class="mb-3">
                <label class="form-label small fw-bold text-body">WhatsApp do Convidado *</label>
                <input type="tel" name="telefone" id="conviteTelefoneDireto" class="form-control rounded-3" placeholder="(11) 99999-9999">
                <small class="text-muted" style="font-size: 0.72rem;">O link gerado será de uso único e exclusivo para este número.</small>
              </div>

              <div class="form-check form-switch mb-3 p-3 bg-body-tertiary rounded-3 border">
                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="checkEnviarZap" name="enviar_whatsapp" value="1" checked>
                <label class="form-check-label small fw-bold text-body" for="checkEnviarZap">
                  <i class="bi bi-whatsapp text-success me-1"></i> Disparar convite automaticamente por WhatsApp
                </label>
              </div>
            </div>

            <!-- Aba Lote -->
            <div class="tab-pane fade" id="tab-convite-lote" role="tabpanel">
              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-bold text-body">Capacidade Máxima (Usos) *</label>
                  <input type="number" name="capacidade_maxima" id="conviteCapacidadeLote" class="form-control rounded-3" value="10" min="1" max="500">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-bold text-body">Validade (Dias) *</label>
                  <select name="dias_validade" class="form-select rounded-3">
                    <option value="7">7 dias</option>
                    <option value="15">15 dias</option>
                    <option value="30" selected>30 dias</option>
                    <option value="60">60 dias</option>
                    <option value="0">Sem expiração</option>
                  </select>
                </div>
              </div>
              <small class="text-muted d-block" style="font-size: 0.72rem;">
                O link poderá ser compartilhado em grupos da igreja e será invalidado automaticamente quando atingir o total de cadastros.
              </small>
            </div>
          </div>

          <!-- Resultado do Link Gerado (Oculto inicialmente) -->
          <div id="blocoResultadoConvite" class="d-none mt-3 p-3 bg-success-subtle border border-success-subtle rounded-3">
            <label class="form-label small fw-bold text-success-emphasis mb-1">
              <i class="bi bi-check-circle-fill me-1"></i> Link de Convite Gerado com Sucesso!
            </label>
            <div class="input-group input-group-sm mb-2">
              <input type="text" id="inputLinkGerado" class="form-control bg-white font-monospace" readonly>
              <button type="button" class="btn btn-success" onclick="copiarLinkConvite($('#inputLinkGerado').val())">
                <i class="bi bi-clipboard-check"></i> Copiar
              </button>
            </div>
            <small class="text-success-emphasis d-block" id="txtMsgZapResultado"></small>
          </div>
        </form>
      </div>

      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
        <button type="button" id="btnGerarConviteSubmit" class="btn btn-success rounded-pill px-4 fw-bold" onclick="executarGeracaoConvite()">
          <i class="bi bi-send-fill me-1"></i> Gerar Link de Convite
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DE REVISÃO E APROVAÇÃO DE CADASTRO                -->
<!-- ======================================================== -->
<div class="modal fade" id="modalRevisarAprovar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;">
    <div class="modal-content rounded-4 shadow-lg border-0">
      <div class="modal-header border-0 pb-0 pt-3 px-4">
        <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2">
          <i class="bi bi-patch-check-fill text-success fs-5"></i> <span>Revisar & Aprovar Voluntário</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body py-3 px-4">
        <!-- Hero Card do Voluntário com Departamento e Contatos -->
        <div class="profile-approval-card mb-3">
          <div class="d-flex align-items-center gap-3">
            <img src="" id="aprovAvatar" class="rounded-circle shadow-sm border border-2 flex-shrink-0" style="width: 58px; height: 58px; object-fit: cover;">
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-1.5 mb-1.5">
                <h6 class="fw-bold mb-0 text-body fs-6 text-truncate" id="aprovNome"></h6>
                <span id="aprovDepBadge" class="badge rounded-pill text-white small px-2.5 py-1 fw-bold shadow-xs">
                  <i class="bi bi-diagram-2 me-1"></i><span id="aprovDepNome">Departamento</span>
                </span>
              </div>
              <div class="d-flex flex-column gap-1 small">
                <div class="text-success fw-bold d-inline-flex align-items-center" id="aprovFone"></div>
                <div class="text-muted d-inline-flex align-items-center" id="aprovEmail"></div>
              </div>
              <div id="aprovRedesContainer" class="d-flex flex-wrap gap-1.5 mt-2"></div>
            </div>
          </div>
        </div>

        <form id="formAprovarVoluntarioModal">
          <input type="hidden" name="id_voluntario" id="aprovIdVoluntario">
          <input type="hidden" name="id_departamento" id="aprovIdDepartamento">
          <input type="hidden" name="notificar_whatsapp" id="aprovNotificarWhatsApp" value="1">

          <!-- Sub-áreas de Atuação Selecionadas -->
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1.5">
              <label class="form-label small fw-bold text-body mb-0 d-flex align-items-center gap-1.5">
                <i class="bi bi-diagram-3-fill text-primary"></i> &nbsp;&nbsp;<span>Sub-áreas de Atuação no Departamento *</span>
              </label>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold" id="aprovTotalAreasBadge">
                0 selecionada(s)
              </span>
            </div>
            <div class="bg-body-tertiary rounded-3 border custom-subareas-scroll" id="aprovContainerSubareas">
              <div class="text-center py-2 text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Carregando sub-áreas...</div>
            </div>
          </div>

          <!-- Nível de Conhecimento e Limite de Escalas -->
          <div class="row g-2.5 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold text-body mb-1">Nível de Experiência</label>
              <select name="nivel_conhecimento" id="aprovNivel" class="form-select rounded-3">
                <option value="APRENDIZ">Aprendiz</option>
                <option value="JUNIOR" selected>Júnior</option>
                <option value="PLENO">Pleno</option>
                <option value="SENIOR">Sênior</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold text-body mb-1">Máx. Escalas/Mês</label>
              <input type="number" name="max_escalas_mes" id="aprovMaxEscalas" class="form-control rounded-3" value="0" min="0">
              <small class="text-muted d-block mt-0.5" style="font-size: 0.68rem;">0 = Sem limite mensal</small>
            </div>
          </div>

          <!-- Observação Interna -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-body mb-1">Observação Interna</label>
            <textarea name="observacao" id="aprovObservacao" class="form-control rounded-3" rows="2" placeholder="Notas internas da liderança sobre o voluntário..."></textarea>
          </div>

          <!-- Card de Notificação WhatsApp com Toggle Pré-marcado e Padding Amplo -->
          <div class="card border border-success-subtle bg-success-subtle bg-opacity-25 rounded-3 p-3 mb-1" id="cardNotificacaoZapAprov">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="p-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 38px; height: 38px;">
                  <i class="bi bi-whatsapp fs-5 text-white"></i>
                </div>
                <div class="ps-1">
                  <div class="fw-bold small text-body" id="lblNotificarZap">Notificar voluntário via WhatsApp</div>
                  <div class="text-muted small" id="descNotificarZap" style="font-size: 0.74rem; line-height: 1.35;">Envia mensagem automática de boas-vindas com instruções e link de acesso.</div>
                </div>
              </div>
              <div class="form-check form-switch m-0 flex-shrink-0 ps-0 pe-1">
                <input class="form-check-input ms-0" type="checkbox" id="switchNotificarWhatsApp" checked onchange="aoAlternarSwitchZap(this)" style="cursor: pointer; width: 2.3em; height: 1.2em;">
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer Limpo com Apenas 2 Botões: Cancelar e Aprovar -->
      <div class="modal-footer border-0 pt-0 pb-3 px-4 d-flex justify-content-between align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 small fw-semibold" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i> Cancelar
        </button>

        <button type="button" id="btnConfirmarAprovacaoSubmit" class="btn btn-success rounded-pill px-4 py-2 small fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" onclick="executarAprovacaoCadastro()">
          <i class="bi bi-check-circle-fill" id="iconeBtnAprovar"></i>
          <span id="lblBtnAprovar">Aprovar</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DE REJEIÇÃO DE CADASTRO                           -->
<!-- ======================================================== -->
<div class="modal fade" id="modalRejeitarCadastro" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger">
          <i class="bi bi-x-circle-fill me-2 text-danger"></i> Rejeitar Cadastro
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body py-3">
        <p class="mb-2">Deseja realmente rejeitar a solicitação de cadastro de <strong id="rejeitarNomeVol"></strong>?</p>
        <div class="mb-3">
          <label class="form-label small fw-bold text-body">Motivo da Rejeição (Opcional)</label>
          <textarea id="rejeitarMotivoInput" class="form-control rounded-3" rows="2" placeholder="Informe o motivo..."></textarea>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btnConfirmarRejeicaoSubmit" class="btn btn-danger rounded-pill px-4 fw-bold" onclick="executarRejeicaoCadastro()">
          <i class="bi bi-trash3-fill me-1"></i> Rejeitar Cadastro
        </button>
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
  let idVoluntarioParaRejeitar = null;
  let modalResetInstance = null;
  let modalAprovarInstance = null;
  let modalRejeitarInstance = null;

  $(document).ready(function() {
    $('#conviteTelefoneDireto').mask('(00) 00000-0000');
  });

  function setTabAtiva(tab) {
    $('#filtroTabAtiva').val(tab);
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
  }

  function copiarLinkConvite(link) {
    if (!link) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(link).then(function() {
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('success', 'Link Copiado', 'O link de convite foi copiado para a área de transferência!');
        } else if (typeof usShowToast === 'function') {
          usShowToast('success', 'Link Copiado', 'O link de convite foi copiado para a área de transferência!');
        }
      }).catch(function() {
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('info', 'Link de Convite', 'Copie o link disponível no campo de texto.');
        }
      });
    } else {
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show('info', 'Link de Convite', 'Copie o link disponível no campo de texto.');
      }
    }
  }

  function executarGeracaoConvite() {
    const btn = document.getElementById('btnGerarConviteSubmit');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Gerando convite...';

    const formData = new FormData(document.getElementById('formGerarConviteModal'));

    $.ajax({
      url: '<?= base_url('voluntario/gerarConvite') ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Gerar Link de Convite';

        if (res.status === 'success') {
          $('#inputLinkGerado').val(res.link);
          if (res.whatsapp_enviado) {
            $('#txtMsgZapResultado').html('<i class="bi bi-whatsapp me-1"></i> Convite enviado diretamente para o WhatsApp do voluntário!');
          } else {
            $('#txtMsgZapResultado').text('Copie o link acima e envie para o convidado.');
          }
          $('#blocoResultadoConvite').removeClass('d-none');
          copiarLinkConvite(res.link);
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Convite Gerado', res.message || 'Link de convite gerado com sucesso!');
          }
        } else {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Atenção', res.message || 'Erro ao gerar convite.');
          }
        }
      },
      error: function(xhr) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Gerar Link de Convite';
        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao gerar convite.';
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Erro', msg);
        }
      }
    });
  }

  function encerrarLinkConvite(idConvite) {
    usConfirm({
      title: 'Encerrar Link de Convite',
      message: 'Deseja realmente encerrar este link de convite? Ninguém mais poderá se cadastrar através dele.',
      confirmText: 'Sim, Encerrar',
      type: 'warning',
      icon: 'bi-x-circle-fill text-warning'
    }, function() {
      $.post('<?= base_url('voluntario/encerrarConvite') ?>', {
        id_convite: idConvite
      }, function(res) {
        if (res.status === 'success') {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Convite Encerrado', 'O link de convite foi desativado com sucesso.');
          }
          setTimeout(function() {
            window.location.reload();
          }, 1000);
        } else {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Erro', res.message || 'Erro ao encerrar convite.');
          }
        }
      }).fail(function(xhr) {
        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao encerrar convite.';
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Erro', msg);
        }
      });
    });
  }

  function abrirModalAprovar(voluntario) {
    if (!voluntario) return;

    $('#aprovIdVoluntario').val(voluntario.id_voluntario);
    $('#aprovIdDepartamento').val(voluntario.id_departamento);
    $('#aprovNome').text(voluntario.nome);
    $('#aprovFone').html('<i class="bi bi-whatsapp me-2"></i><span>' + (voluntario.telefone_whatsapp || '') + '</span>');

    // Departamento Badge
    $('#aprovDepNome').text(voluntario.nome_departamento || 'Geral');
    if (voluntario.cor_departamento) {
      $('#aprovDepBadge').css('background-color', voluntario.cor_departamento);
    } else {
      $('#aprovDepBadge').css('background-color', '#2563eb');
    }

    if (voluntario.email) {
      $('#aprovEmail').html('<i class="bi bi-envelope me-2"></i><span>' + voluntario.email + '</span>').show();
    } else {
      $('#aprovEmail').text('').hide();
    }

    // Renderiza redes sociais do voluntário no modal
    const redesContainer = $('#aprovRedesContainer');
    redesContainer.empty();
    if (voluntario.redes_sociais) {
      let redesList = [];
      try {
        redesList = typeof voluntario.redes_sociais === 'string' ? JSON.parse(voluntario.redes_sociais) : voluntario.redes_sociais;
      } catch (e) {
        redesList = [];
      }
      if (Array.isArray(redesList) && redesList.length > 0) {
        redesList.forEach(function(r) {
          const plat = (r.plataforma || r.rede || '').toLowerCase().trim();
          const url = (r.url || r.link || '').trim();
          if (!url) return;

          let icon = 'bi bi-link-45deg';
          let bg = '#6c757d';
          let href = url;
          if (plat.includes('instagram')) {
            icon = 'bi bi-instagram';
            bg = '#E1306C';
            if (!href.startsWith('http')) href = 'https://instagram.com/' + href.replace(/^@/, '');
          } else if (plat.includes('tiktok')) {
            icon = 'bi bi-tiktok';
            bg = '#010101';
            if (!href.startsWith('http')) href = 'https://tiktok.com/@' + href.replace(/^@/, '');
          } else if (plat.includes('youtube')) {
            icon = 'bi bi-youtube';
            bg = '#FF0000';
            if (!href.startsWith('http')) href = 'https://youtube.com/' + (href.startsWith('@') ? href : '@' + href);
          } else if (plat.includes('linkedin')) {
            icon = 'bi bi-linkedin';
            bg = '#0A66C2';
            if (!href.startsWith('http')) href = 'https://linkedin.com/in/' + href.replace(/^@/, '');
          } else if (plat.includes('threads')) {
            icon = 'bi bi-threads';
            bg = '#101010';
            if (!href.startsWith('http')) href = 'https://threads.net/@' + href.replace(/^@/, '');
          } else if (plat.includes('facebook')) {
            icon = 'bi bi-facebook';
            bg = '#1877F2';
            if (!href.startsWith('http')) href = 'https://facebook.com/' + href.replace(/^@/, '');
          } else if (plat.includes('twitter') || plat.includes('x')) {
            icon = 'bi bi-twitter-x';
            bg = '#000000';
            if (!href.startsWith('http')) href = 'https://x.com/' + href.replace(/^@/, '');
          }

          let label = url;
          if (label.length > 20) label = label.substring(0, 18) + '...';

          redesContainer.append(`
            <a href="${href}" target="_blank" rel="noopener noreferrer" class="social-pill-badge text-white" style="background-color: ${bg};" title="${plat}: ${url}">
              <i class="${icon}"></i> <span>${label}</span>
            </a>
          `);
        });
      }
    }

    const defaultAv = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(voluntario.nome) + '&background=2563eb&color=fff&size=100';
    $('#aprovAvatar').attr('src', voluntario.foto_url || defaultAv);

    // Identifica IDs de sub-áreas que o voluntário escolheu no convite
    let chosenIds = [];
    if (voluntario.ids_areas) {
      chosenIds = voluntario.ids_areas.toString().split(',').map(function(s) {
        return parseInt(s.trim());
      });
    } else if (voluntario.id_area) {
      chosenIds = [parseInt(voluntario.id_area)];
    }

    // Reset Switch Zap e valores iniciais do modal (Já pré-marcado)
    $('#aprovNotificarWhatsApp').val('1');
    $('#switchNotificarWhatsApp').prop('checked', true);
    $('#lblNotificarZap').text('Notificar voluntário via WhatsApp');
    $('#descNotificarZap').text('Envia mensagem automática de boas-vindas com instruções e link de acesso.');
    $('#cardNotificacaoZapAprov').removeClass('bg-secondary-subtle border-secondary-subtle').addClass('bg-success-subtle border-success-subtle');

    // Botão de aprovação no estado original
    const btnSubmit = document.getElementById('btnConfirmarAprovacaoSubmit');
    if (btnSubmit) {
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> <span>Aprovar</span>';
    }

    const container = $('#aprovContainerSubareas');
    container.html('<div class="text-center py-2 text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Carregando sub-áreas...</div>');

    $.get('<?= base_url('voluntario/getSubareasPorDepartamento') ?>', {
      id_departamento: voluntario.id_departamento
    }, function(res) {
      container.empty();
      if (res.subareas && res.subareas.length > 0) {
        let html = '<div class="row g-2">';
        res.subareas.forEach(function(area) {
          const isChecked = chosenIds.includes(parseInt(area.id_area));
          html += `
            <div class="col-12 col-sm-6">
              <label class="subarea-choice-card d-flex align-items-center justify-content-between m-0 ${isChecked ? 'is-selected' : ''}" for="aprov_area_${area.id_area}">
                <div class="d-flex align-items-center gap-2 overflow-hidden pe-1">
                  <i class="bi bi-diagram-3 text-primary fs-6 flex-shrink-0"></i>
                  <span class="small fw-semibold text-body text-truncate">${area.nome_area}</span>
                </div>
                <input class="form-check-input subarea-choice-check mt-0 flex-shrink-0 aprov-subarea-check" type="checkbox" name="id_area[]" value="${area.id_area}" id="aprov_area_${area.id_area}" ${isChecked ? 'checked' : ''} onchange="aoMudarSubareaAprovacao(this)">
              </label>
            </div>
          `;
        });
        html += '</div>';
        container.html(html);
        atualizarContadorAprovacaoAreas();
      } else {
        container.html('<div class="text-center py-2 text-muted small">Nenhuma sub-área cadastrada para este departamento.</div>');
      }
    });

    if (!modalAprovarInstance) {
      modalAprovarInstance = new bootstrap.Modal(document.getElementById('modalRevisarAprovar'));
    }
    modalAprovarInstance.show();
  }

  function aoAlternarSwitchZap(el) {
    if (el.checked) {
      $('#aprovNotificarWhatsApp').val('1');
      $('#lblNotificarZap').text('Notificar voluntário via WhatsApp');
      $('#descNotificarZap').text('Envia mensagem automática de boas-vindas com instruções e link de acesso.');
      $('#cardNotificacaoZapAprov').removeClass('bg-secondary-subtle border-secondary-subtle').addClass('bg-success-subtle border-success-subtle');
    } else {
      $('#aprovNotificarWhatsApp').val('0');
      $('#lblNotificarZap').text('Aprovação Silenciosa');
      $('#descNotificarZap').text('Nenhuma mensagem será disparada para o voluntário.');
      $('#cardNotificacaoZapAprov').removeClass('bg-success-subtle border-success-subtle').addClass('bg-secondary-subtle border-secondary-subtle');
    }
  }

  function aoMudarSubareaAprovacao(input) {
    const card = $(input).closest('.subarea-choice-card');
    if (input.checked) {
      card.addClass('is-selected');
    } else {
      card.removeClass('is-selected');
    }
    atualizarContadorAprovacaoAreas();
  }

  function atualizarContadorAprovacaoAreas() {
    const total = $('.aprov-subarea-check:checked').length;
    $('#aprovTotalAreasBadge').text(total + ' selecionada(s)');
  }

  function executarAprovacaoCadastro() {
    const totalSelected = $('.aprov-subarea-check:checked').length;
    if (totalSelected === 0) {
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show('warning', 'Sub-área Obrigatória', 'Por favor, selecione pelo menos uma sub-área para o voluntário.');
      } else if (typeof usShowToast === 'function') {
        usShowToast('warning', 'Sub-área Obrigatória', 'Por favor, selecione pelo menos uma sub-área para o voluntário.');
      }
      return;
    }

    // Sincroniza valor do switch
    const isZapChecked = $('#switchNotificarWhatsApp').is(':checked');
    $('#aprovNotificarWhatsApp').val(isZapChecked ? '1' : '0');

    const btn = document.getElementById('btnConfirmarAprovacaoSubmit');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1.5"></div> Aprovando...';
    }

    const formData = new FormData(document.getElementById('formAprovarVoluntarioModal'));

    $.ajax({
      url: '<?= base_url('voluntario/aprovarCadastro') ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res) {
        if (modalAprovarInstance) modalAprovarInstance.hide();

        if (res.status === 'success') {
          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('success', 'Aprovado', res.message || 'Voluntário aprovado com sucesso!');
          }
          setTimeout(function() {
            window.location.reload();
          }, 1000);
        } else {
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> <span>Aprovar</span>';
          }

          if (typeof USToast !== 'undefined' && USToast.show) {
            USToast.show('error', 'Erro', res.message || 'Erro ao aprovar cadastro.');
          }
        }
      },
      error: function(xhr) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> <span>Aprovar</span>';
        }

        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao processar aprovação.';
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Erro', msg);
        }
      }
    });
  }

  function abrirModalRejeitar(id, nome) {
    idVoluntarioParaRejeitar = id;
    $('#rejeitarNomeVol').text(nome);
    $('#rejeitarMotivoInput').val('');

    if (!modalRejeitarInstance) {
      modalRejeitarInstance = new bootstrap.Modal(document.getElementById('modalRejeitarCadastro'));
    }
    modalRejeitarInstance.show();
  }

  function executarRejeicaoCadastro() {
    if (!idVoluntarioParaRejeitar) return;

    const btn = document.getElementById('btnConfirmarRejeicaoSubmit');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Rejeitando...';

    const motivo = $('#rejeitarMotivoInput').val().trim();

    $.post('<?= base_url('voluntario/rejeitarCadastro') ?>', {
      id_voluntario: idVoluntarioParaRejeitar,
      motivo: motivo
    }, function(res) {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-trash3-fill me-1"></i> Rejeitar Cadastro';
      if (modalRejeitarInstance) modalRejeitarInstance.hide();

      if (res.status === 'success') {
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('info', 'Cadastro Rejeitado', 'O cadastro foi rejeitado com sucesso.');
        }
        setTimeout(function() {
          window.location.reload();
        }, 1000);
      } else {
        if (typeof USToast !== 'undefined' && USToast.show) {
          USToast.show('error', 'Erro', res.message || 'Erro ao rejeitar.');
        }
      }
    }).fail(function(xhr) {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-trash3-fill me-1"></i> Rejeitar Cadastro';
      const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro na requisição.';
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show('error', 'Erro', msg);
      }
    });
  }

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
          USToast.show('error', 'Erro de Conexão', 'Falha na conexão com o servidor.');
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