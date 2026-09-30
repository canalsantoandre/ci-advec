<?php
$isEdit = !empty($voluntario);
$idVol  = $isEdit ? $voluntario->id_voluntario : 0;
$defaultAvatar = 'https://ui-avatars.com/api/?name=' . ($isEdit ? urlencode($voluntario->nome) : 'Novo Voluntario') . '&background=2563eb&color=fff&size=150&bold=true';
$avatarSrc = $isEdit && !empty($voluntario->foto_url) ? $voluntario->foto_url : $defaultAvatar;
$idade = $isEdit && !empty($voluntario->data_nascimento) ? date_diff(date_create($voluntario->data_nascimento), date_create('today'))->y : null;
?>

<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-person-hearts text-primary me-2"></i><?= $isEdit ? 'Editar Voluntário' : 'Novo Voluntário' ?>
        </h3>
        <p class="text-secondary small mb-0">Cadastro completo, vínculos de departamentos, sub-áreas de atuação e assiduidade</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('voluntario') ?>">Voluntários</a></li>
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
      <!-- COLUNA ESQUERDA: PROFILE HERO CARD -->
      <!-- ========================================== -->
      <div class="col-lg-4">
        <div class="card card-outline card-primary shadow-sm border-0 rounded-4 sticky-lg-top" style="top: 80px;">
          <div class="card-body text-center p-4">

            <!-- Avatar Container com Preview Instantâneo -->
            <div class="position-relative d-inline-block mb-3">
              <img src="<?= esc($avatarSrc) ?>" id="imgAvatarPreview" class="rounded-circle shadow border border-3 border-white" style="width: 130px; height: 130px; object-fit: cover;" alt="Foto de Perfil" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>';">

              <span class="position-absolute bottom-0 end-0 badge rounded-circle p-2 bg-<?= ($isEdit && $voluntario->status == 0 ? 'danger' : 'success') ?> border border-2 border-white" id="badgeStatusPreview" title="Status">
                <span class="visually-hidden">Status</span>
              </span>
            </div>

            <!-- Identificação -->
            <h5 class="fw-bold mb-1 text-body" id="previewNome"><?= $isEdit ? esc($voluntario->nome) : 'Nome do Voluntário' ?></h5>

            <div class="mb-2">
              <span class="text-muted small" id="previewEmail">
                <i class="bi bi-envelope me-1"></i><?= $isEdit ? esc($voluntario->email) : 'email@exemplo.com' ?>
              </span>
            </div>

            <div class="mb-3">
              <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-2 fw-semibold" id="previewWhats">
                <i class="bi bi-whatsapp me-1"></i><?= $isEdit ? esc($voluntario->telefone_whatsapp) : '(00) 00000-0000' ?>
              </span>
            </div>

            <?php if ($idade !== null) { ?>
              <div class="text-muted small mb-3">
                <i class="bi bi-cake2 me-1 text-primary"></i> <strong><?= $idade ?> anos</strong> (Nasc: <?= date('d/m/Y', strtotime($voluntario->data_nascimento)) ?>)
              </div>
            <?php } ?>

            <?php if ($isEdit && !empty($stats)) { ?>
              <hr class="my-3 opacity-25">

              <div class="row g-2 text-center mb-3">
                <div class="col-6">
                  <div class="p-2 bg-body-tertiary rounded-3 border">
                    <span class="d-block fs-5 fw-bold text-primary"><?= $stats->totalEscalas ?></span>
                    <small class="text-muted" style="font-size: 0.75rem;">Escalações</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-2 bg-body-tertiary rounded-3 border">
                    <span class="d-block fs-5 fw-bold text-<?= ($stats->taxaAssiduidade >= 80 ? 'success' : ($stats->taxaAssiduidade >= 50 ? 'warning' : 'danger')) ?>"><?= $stats->taxaAssiduidade ?>%</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Assiduidade</small>
                  </div>
                </div>
              </div>

              <div class="d-grid gap-2">
                <a href="<?= base_url('voluntario/dashVoluntario/' . $voluntario->id_voluntario) ?>" class="btn btn-outline-info rounded-pill fw-semibold btn-sm">
                  <i class="bi bi-graph-up-arrow me-1"></i> Ver Dashboard Detalhado
                </a>
                <button type="button" class="btn btn-outline-warning rounded-pill fw-semibold btn-sm" onclick="abrirModalResetSenhaForm(<?= $voluntario->id_voluntario ?>, '<?= esc(addslashes($voluntario->nome)) ?>', '<?= esc(addslashes($voluntario->telefone_whatsapp)) ?>')">
                  <i class="bi bi-key-fill me-1"></i> Resetar Senha de Acesso
                </button>
              </div>
            <?php } ?>

          </div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- COLUNA DIREITA: FORMULÁRIO EM ABAS -->
      <!-- ========================================== -->
      <div class="col-lg-8">
        <div class="card card-outline card-primary shadow-sm border-0 rounded-4">

          <!-- Header com Abas -->
          <div class="card-header bg-body p-0 border-bottom">
            <ul class="nav nav-tabs card-header-tabs m-0 border-bottom-0" id="voluntarioTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold py-3 px-4" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados-pane" type="button" role="tab">
                  <i class="bi bi-person-lines-fill me-2 text-primary"></i>Informaçõese
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold py-3 px-4" id="areas-tab" data-bs-toggle="tab" data-bs-target="#areas-pane" type="button" role="tab">
                  <i class="bi bi-diagram-3-fill me-2 text-info"></i>Sub-áreas
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold py-3 px-4" id="cultos-tab" data-bs-toggle="tab" data-bs-target="#cultos-pane" type="button" role="tab">
                  <i class="bi bi-calendar2-week-fill me-2 text-warning"></i>Disponibilidade
                </button>
              </li>
              <?php if ($isEdit) { ?>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-bold py-3 px-4" id="metricas-tab" data-bs-toggle="tab" data-bs-target="#metricas-pane" type="button" role="tab">
                    <i class="bi bi-graph-up me-2 text-success"></i>Métricas
                  </button>
                </li>
              <?php } ?>
            </ul>
          </div>

          <form method="post" action="<?= base_url('voluntario/salvar') ?>" enctype="multipart/form-data" id="formVoluntario">
            <input type="hidden" name="id_voluntario" value="<?= $idVol ?>">

            <div class="card-body p-4">
              <div class="tab-content" id="voluntarioTabsContent">

                <!-- ========================================== -->
                <!-- ABA 1: DADOS PESSOAIS E CONTATO -->
                <!-- ========================================== -->
                <div class="tab-pane fade show active" id="dados-pane" role="tabpanel" tabindex="0">

                  <h6 class="fw-bold text-primary mb-3">
                    <i class="bi bi-card-heading me-1"></i> Informações Principais
                  </h6>

                  <div class="row g-3 mb-4">
                    <div class="col-md-6">
                      <label for="nome" class="form-label fw-semibold">Nome Completo <span class="text-danger">*</span></label>
                      <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Gabriel Santos Silva" value="<?= $isEdit ? esc($voluntario->nome) : '' ?>" required maxlength="150">
                    </div>

                    <div class="col-md-3">
                      <label for="nickname" class="form-label fw-semibold">
                        <i class="bi bi-tag-fill text-primary me-1"></i> Nickname
                      </label>
                      <input type="text" name="nickname" id="nickname" class="form-control" placeholder="Ex: Gabi, Juninho..." value="<?= ($isEdit && !empty($voluntario->nickname)) ? esc($voluntario->nickname) : '' ?>" maxlength="100">
                      <small class="text-muted" style="font-size: 0.72rem;">Nome de preferência para escalas</small>
                    </div>

                    <div class="col-md-3">
                      <label for="nivel_conhecimento" class="form-label fw-semibold">
                        <i class="bi bi-award-fill text-warning me-1"></i> Nível de Conhecimento
                      </label>
                      <?php $nivelAtual = $isEdit ? ($voluntario->nivel_conhecimento ?? 'JUNIOR') : 'JUNIOR'; ?>
                      <select name="nivel_conhecimento" id="nivel_conhecimento" class="form-select">
                        <option value="APRENDIZ" <?= ($nivelAtual === 'APRENDIZ') ? 'selected' : '' ?>>APRENDIZ (Iniciante)</option>
                        <option value="JUNIOR" <?= ($nivelAtual === 'JUNIOR') ? 'selected' : '' ?>>JUNIOR (Em desenvolvimento)</option>
                        <option value="PLENO" <?= ($nivelAtual === 'PLENO') ? 'selected' : '' ?>>PLENO (Autônomo)</option>
                        <option value="SENIOR" <?= ($nivelAtual === 'SENIOR') ? 'selected' : '' ?>>SENIOR (Referência / Líder)</option>
                      </select>
                    </div>

                    <div class="col-md-4">
                      <label for="email" class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                      <input type="email" name="email" id="email" class="form-control" placeholder="gabriel@exemplo.com" value="<?= $isEdit ? esc($voluntario->email) : '' ?>" required maxlength="150">
                    </div>

                    <div class="col-md-4">
                      <label for="telefone_whatsapp" class="form-label fw-semibold">Telefone / WhatsApp <span class="text-danger">*</span></label>
                      <input type="text" name="telefone_whatsapp" id="telefone_whatsapp" class="form-control" placeholder="(11) 99999-9999" value="<?= $isEdit ? esc($voluntario->telefone_whatsapp) : '' ?>" required maxlength="30">
                    </div>

                    <div class="col-md-4">
                      <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                      <select name="status" id="status" class="form-select" required>
                        <option value="1" <?= (!$isEdit || $voluntario->status == 1) ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= ($isEdit && $voluntario->status == 0) ? 'selected' : '' ?>>Inativo</option>
                      </select>
                    </div>

                    <div class="col-md-4">
                      <label for="data_nascimento" class="form-label fw-semibold">Data de Nascimento <span class="text-danger">*</span></label>
                      <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" value="<?= $isEdit ? esc($voluntario->data_nascimento) : '' ?>" required>
                    </div>

                    <div class="col-md-4">
                      <label for="foto_file" class="form-label fw-semibold">Foto de Perfil (Upload)</label>
                      <input type="file" name="foto_file" id="foto_file" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-4">
                      <label for="max_escalas_mes" class="form-label fw-semibold">
                        <i class="bi bi-speedometer2 text-primary me-1"></i> Limite de Escalas por Mês
                      </label>
                      <div class="input-group">
                        <input type="number" name="max_escalas_mes" id="max_escalas_mes" class="form-control" min="0" max="60" placeholder="0 = Sem limite" value="<?= $isEdit ? (int)($voluntario->max_escalas_mes ?? 0) : '0' ?>">
                        <span class="input-group-text bg-body-tertiary small text-muted">escalas/mês</span>
                      </div>
                      <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                        <strong>0</strong> = Ilimitado (várias). Ou informe o teto (ex: 2 ou 4).
                      </small>
                    </div>

                    <div class="col-12">
                      <label for="foto_url_manual" class="form-label fw-semibold">Ou URL Direta da Foto (Opcional)</label>
                      <input type="url" name="foto_url_manual" id="foto_url_manual" class="form-control" placeholder="https://exemplo.com/foto.jpg" value="<?= ($isEdit && !empty($voluntario->foto_url) && filter_var($voluntario->foto_url, FILTER_VALIDATE_URL)) ? esc($voluntario->foto_url) : '' ?>">
                    </div>

                    <div class="col-12">
                      <label for="observacao" class="form-label fw-semibold">Observações Gerais</label>
                      <textarea name="observacao" id="observacao" rows="2" class="form-control" placeholder="Disponibilidade de horários, talentos adicionais, restrições..."><?= $isEdit ? esc($voluntario->observacao) : '' ?></textarea>
                    </div>
                  </div>

                  <!-- Redes Sociais Dinâmicas -->
                  <div class="card bg-body-tertiary border-0 rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <div>
                        <h6 class="fw-bold mb-0 text-body">
                          <i class="bi bi-share-fill text-primary me-2"></i> Redes Sociais & Portfólio
                        </h6>
                        <small class="text-muted">Adicione links de redes sociais do voluntário (Instagram, LinkedIn, YouTube, TikTok, etc.)</small>
                      </div>
                      <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="adicionarRedeSocial()">
                        <i class="bi bi-plus-lg me-1"></i> Adicionar Rede
                      </button>
                    </div>

                    <div id="containerRedesSociais" class="d-flex flex-column gap-2">
                      <?php if (!empty($redesSociais)) { ?>
                        <?php foreach ($redesSociais as $index => $r) { ?>
                          <div class="row g-2 align-items-center rede-social-item">
                            <div class="col-md-4">
                              <select name="rede_nome[]" class="form-select form-select-sm">
                                <option value="Instagram" <?= ($r['rede'] === 'Instagram') ? 'selected' : '' ?>>Instagram</option>
                                <option value="LinkedIn" <?= ($r['rede'] === 'LinkedIn') ? 'selected' : '' ?>>LinkedIn</option>
                                <option value="TikTok" <?= ($r['rede'] === 'TikTok') ? 'selected' : '' ?>>TikTok</option>
                                <option value="YouTube" <?= ($r['rede'] === 'YouTube') ? 'selected' : '' ?>>YouTube</option>
                                <option value="Facebook" <?= ($r['rede'] === 'Facebook') ? 'selected' : '' ?>>Facebook</option>
                                <option value="Outro" <?= ($r['rede'] === 'Outro') ? 'selected' : '' ?>>Outro</option>
                              </select>
                            </div>
                            <div class="col-md-7">
                              <input type="text" name="rede_link[]" class="form-control form-control-sm" placeholder="Ex: @usuario ou https://..." value="<?= esc($r['link']) ?>">
                            </div>
                            <div class="col-md-1 text-center">
                              <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removerRedeSocial(this)" title="Remover">
                                <i class="bi bi-trash3"></i>
                              </button>
                            </div>
                          </div>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </div>

                </div>

                <!-- ========================================== -->
                <!-- ABA 2: DEPARTAMENTOS E SUB-ÁREAS (N:N) -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="areas-pane" role="tabpanel" tabindex="0">
                  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                      <h6 class="fw-bold text-primary mb-1">
                        <i class="bi bi-diagram-3-fill me-1"></i> Vinculação de Departamentos e Sub-áreas
                      </h6>
                      <p class="text-secondary small mb-0">Selecione uma ou mais sub-áreas em que este voluntário atuará nos cultos.</p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold" id="badgeTotalAreasSelecionadas">
                        <i class="bi bi-check2-all me-1"></i> <?= count($areasSelecionadasIds) ?> selecionada(s)
                      </span>
                      <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="marcarTodasAreas(true)">
                        Marcar Todas
                      </button>
                      <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="marcarTodasAreas(false)">
                        Limpar Seleção
                      </button>
                    </div>
                  </div>

                  <?php if (!empty($departamentosComAreas)) { ?>
                    <div class="row g-3">
                      <?php foreach ($departamentosComAreas as $dep) {
                        $corDep = !empty($dep->cor_identificacao) ? $dep->cor_identificacao : '#2563eb';
                        $totalAreasDep = count($dep->areas);
                      ?>
                        <div class="col-md-6">
                          <div class="card border rounded-4 h-100 shadow-sm overflow-hidden dep-card-group" data-dep-id="<?= $dep->id_departamento ?>">
                            <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: <?= esc($corDep) ?>15; border-left: 4px solid <?= esc($corDep) ?>;">
                              <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-circle p-1" style="background-color: <?= esc($corDep) ?>; width: 10px; height: 10px;"></span>
                                <span class="fw-bold text-body"><?= esc($dep->nome) ?></span>
                              </div>
                              <div class="d-flex align-items-center gap-1">
                                <span class="badge rounded-pill me-1" style="background-color: <?= esc($corDep) ?>; color: #fff; font-size: 0.7rem;">
                                  <?= $totalAreasDep ?> áreas
                                </span>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" style="font-size: 0.75rem; color: <?= esc($corDep) ?>;" onclick="toggleTodasAreasDepartamento(<?= $dep->id_departamento ?>)" title="Marcar/Desmarcar todas deste departamento">
                                  Alternar Todas
                                </button>
                              </div>
                            </div>

                            <div class="card-body p-3">
                              <?php if (!empty($dep->areas)) { ?>
                                <div class="d-flex flex-column gap-2">
                                  <?php foreach ($dep->areas as $area) {
                                    $isChecked = in_array($area->id_area, $areasSelecionadasIds);
                                  ?>
                                    <label class="custom-checkbox-card d-flex align-items-start gap-2 p-2 rounded-3 border user-select-none <?= $isChecked ? 'bg-primary-subtle border-primary' : 'bg-body' ?>" for="area_check_<?= $area->id_area ?>" style="cursor: pointer; transition: all 0.15s ease;">
                                      <input class="form-check-input mt-1 flex-shrink-0 dep-area-checkbox" type="checkbox" name="areas[]" value="<?= $area->id_area ?>" id="area_check_<?= $area->id_area ?>" data-dep-id="<?= $dep->id_departamento ?>" <?= $isChecked ? 'checked' : '' ?> style="cursor: pointer;">
                                      <div class="flex-grow-1">
                                        <div class="fw-bold text-body small"><?= esc($area->nome_area) ?></div>
                                        <?php if (!empty($area->descricao)) { ?>
                                          <div class="text-secondary fw-normal" style="font-size: 0.75rem;"><?= esc($area->descricao) ?></div>
                                        <?php } ?>
                                      </div>
                                    </label>
                                  <?php } ?>
                                </div>
                              <?php } else { ?>
                                <small class="text-muted d-block py-2 text-center">Nenhuma sub-área cadastrada.</small>
                              <?php } ?>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                  <?php } else { ?>
                    <div class="alert alert-warning border-0 rounded-3">
                      Nenhum departamento cadastrado no sistema ainda. Cadastre departamentos primeiro.
                    </div>
                  <?php } ?>
                </div>

                <!-- ========================================== -->
                <!-- ABA: DISPONIBILIDADE DE CULTOS (N:N) -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="cultos-pane" role="tabpanel" tabindex="0">
                  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                      <h6 class="fw-bold text-primary mb-1">
                        <i class="bi bi-calendar2-week-fill me-1 text-warning"></i> Disponibilidade
                      </h6>
                      <p class="text-secondary small mb-0">Selecione os cultos e horários em que este voluntário tem disponibilidade para atuar.</p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                      <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-bold" id="badgeTotalCultosSelecionados">
                        <i class="bi bi-check2-all me-1"></i> <?= count($cultosSelecionadosIds ?? []) ?> selecionado(s)
                      </span>
                      <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="marcarTodosCultos(true)">
                        Marcar Todos
                      </button>
                      <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="marcarTodosCultos(false)">
                        Limpar Seleção
                      </button>
                    </div>
                  </div>

                  <!-- Banner Informativo da Regra do Coringa -->
                  <div class="alert alert-primary-subtle border border-primary-subtle rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
                    <i class="bi bi-info-circle-fill text-primary fs-4 flex-shrink-0 mt-1"></i>
                    <div>
                      <strong class="d-block text-body mb-1">Regra da Disponibilidade Total (Coringa):</strong>
                      <div class="small text-secondary">
                        <p class="mb-1">&bull; Se <strong>nenhum culto</strong> for marcado, o voluntário será considerado apto para servir em <strong>TODOS os cultos</strong> (disponibilidade total / sem restrições).</p>
                        <p class="mb-0">&bull; Se <strong>um ou mais cultos</strong> forem marcados, o voluntário ficará restrito exclusivamente aos cultos especificamente selecionados.</p>
                      </div>
                    </div>
                  </div>

                  <?php if (!empty($cultosPadrao)) { ?>
                    <div class="row g-3">
                      <?php
                      $diasNomes = [
                        0 => 'Domingo',
                        1 => 'Segunda-feira',
                        2 => 'Terça-feira',
                        3 => 'Quarta-feira',
                        4 => 'Quinta-feira',
                        5 => 'Sexta-feira',
                        6 => 'Sábado'
                      ];
                      foreach ($cultosPadrao as $cp) {
                        $isChecked = in_array((int)$cp->id_culto_padrao, $cultosSelecionadosIds ?? []);
                        $nomeDia = $diasNomes[(int)$cp->dia_semana] ?? 'Culto';
                        $horaInicio = substr($cp->horario_inicio, 0, 5);
                        $horaTermino = substr($cp->horario_termino, 0, 5);
                        $corEvento = !empty($cp->cor_evento) ? $cp->cor_evento : '#2563eb';
                        // Formato: [Dia da Semana] - [Horário] - [Nome do Culto]
                        $labelFormatado = "{$nomeDia} - {$horaInicio} - {$cp->nome_culto}";
                      ?>
                        <div class="col-md-6">
                          <label class="custom-checkbox-card d-flex align-items-center gap-3 p-3 rounded-4 border user-select-none h-100 <?= $isChecked ? 'bg-primary-subtle border-primary shadow-xs' : 'bg-body' ?>" for="culto_check_<?= $cp->id_culto_padrao ?>" style="cursor: pointer; transition: all 0.15s ease; border-left: 4px solid <?= esc($corEvento) ?> !important;">
                            <input class="form-check-input mt-0 flex-shrink-0 culto-checkbox" type="checkbox" name="cultos[]" value="<?= $cp->id_culto_padrao ?>" id="culto_check_<?= $cp->id_culto_padrao ?>" <?= $isChecked ? 'checked' : '' ?> style="cursor: pointer; width: 1.25rem; height: 1.25rem;">
                            <div class="flex-grow-1">
                              <div class="fw-bold text-body small"><?= esc($labelFormatado) ?></div>
                              <div class="text-secondary small d-flex align-items-center gap-2 mt-1" style="font-size: 0.75rem;">
                                <span><i class="bi bi-clock me-1"></i><?= $horaInicio ?> às <?= $horaTermino ?></span>
                                <?php if (!empty($cp->descricao)) { ?>
                                  <span>&bull;</span>
                                  <span class="text-truncate" style="max-width: 180px;"><?= esc($cp->descricao) ?></span>
                                <?php } ?>
                              </div>
                            </div>
                          </label>
                        </div>
                      <?php } ?>
                    </div>
                  <?php } else { ?>
                    <div class="alert alert-warning border-0 rounded-3">
                      Nenhum tipo de culto cadastrado no sistema ainda.
                    </div>
                  <?php } ?>
                </div>

                <!-- ========================================== -->
                <!-- ABA: MÉTRICAS E ASSIDUIDADE (SE EDIÇÃO) -->
                <!-- ========================================== -->
                <?php if ($isEdit && !empty($stats)) { ?>
                  <div class="tab-pane fade" id="metricas-pane" role="tabpanel" tabindex="0">

                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                      <div>
                        <h6 class="fw-bold text-primary mb-1">
                          <i class="bi bi-graph-up-arrow me-1"></i> Resumo de Participação & Assiduidade
                        </h6>
                        <small class="text-muted">Indicadores calculados com base nas escalações confirmadas nos cultos</small>
                      </div>

                      <!-- Seletor de Período -->
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="<?= base_url("voluntario/editar/{$voluntario->id_voluntario}?periodo=mes_atual#metricas-pane") ?>" class="btn <?= ($periodoSelecionado === 'mes_atual') ? 'btn-primary' : 'btn-outline-secondary' ?>">Mês Atual</a>
                        <a href="<?= base_url("voluntario/editar/{$voluntario->id_voluntario}?periodo=ultimos_3_meses#metricas-pane") ?>" class="btn <?= ($periodoSelecionado === 'ultimos_3_meses') ? 'btn-primary' : 'btn-outline-secondary' ?>">Últimos 3 Meses</a>
                        <a href="<?= base_url("voluntario/editar/{$voluntario->id_voluntario}?periodo=ano_atual#metricas-pane") ?>" class="btn <?= ($periodoSelecionado === 'ano_atual') ? 'btn-primary' : 'btn-outline-secondary' ?>">Ano Atual</a>
                        <a href="<?= base_url("voluntario/editar/{$voluntario->id_voluntario}?periodo=tudo#metricas-pane") ?>" class="btn <?= ($periodoSelecionado === 'tudo') ? 'btn-primary' : 'btn-outline-secondary' ?>">Todo Período</a>
                      </div>
                    </div>

                    <!-- Cards de Estatísticas -->
                    <div class="row g-3 mb-4">
                      <div class="col-md-4">
                        <div class="small-box text-bg-primary shadow-sm mb-0">
                          <div class="inner p-3">
                            <h3 class="fw-bold mb-1"><?= $stats->totalEscalas ?></h3>
                            <p class="mb-0">Escalações no Período</p>
                          </div>
                          <div class="small-box-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="small-box text-bg-success shadow-sm mb-0">
                          <div class="inner p-3">
                            <h3 class="fw-bold mb-1"><?= $stats->totalPresencas ?></h3>
                            <p class="mb-0">Presenças Confirmadas</p>
                          </div>
                          <div class="small-box-icon">
                            <i class="bi bi-check-circle-fill"></i>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="small-box text-bg-info shadow-sm mb-0">
                          <div class="inner p-3">
                            <h3 class="fw-bold mb-1"><?= $stats->taxaAssiduidade ?>%</h3>
                            <p class="mb-0">Taxa de Assiduidade</p>
                          </div>
                          <div class="small-box-icon">
                            <i class="bi bi-pie-chart-fill"></i>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Distribuição por Sub-áreas -->
                    <?php if (!empty($stats->distribuicaoAreas)) { ?>
                      <h6 class="fw-bold text-body mb-2">
                        <i class="bi bi-pie-chart me-1 text-primary"></i> Atuação por Departamento & Sub-área
                      </h6>
                      <div class="row g-2 mb-4">
                        <?php foreach ($stats->distribuicaoAreas as $dArea) { ?>
                          <div class="col-md-6">
                            <div class="p-2 border rounded-3 d-flex justify-content-between align-items-center">
                              <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill" style="background-color: <?= esc($dArea['cor']) ?>; width: 10px; height: 10px;"></span>
                                <strong class="small"><?= esc($dArea['departamento']) ?>: <?= esc($dArea['area']) ?></strong>
                              </div>
                              <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                                <?= $dArea['total'] ?> escalas (<?= $dArea['presencas'] ?> presenças)
                              </span>
                            </div>
                          </div>
                        <?php } ?>
                      </div>
                    <?php } ?>

                    <!-- Histórico de Cultos Escalados -->
                    <h6 class="fw-bold text-body mb-2">
                      <i class="bi bi-clock-history me-1 text-primary"></i> Histórico Recente de Escalas (<?= count($stats->historicoEscalas) ?>)
                    </h6>
                    <div class="table-responsive border rounded-3">
                      <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                          <tr>
                            <th class="ps-3">Data / Culto</th>
                            <th>Departamento & Sub-área</th>
                            <th class="text-center">Presença</th>
                            <th class="pe-3">Observação</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($stats->historicoEscalas)) { ?>
                            <?php foreach ($stats->historicoEscalas as $h) { ?>
                              <tr>
                                <td class="ps-3">
                                  <strong class="text-body d-block"><?= date('d/m/Y', strtotime($h->data_culto)) ?> - <?= esc($h->titulo_culto) ?></strong>
                                  <small class="text-muted"><i class="bi bi-clock me-1"></i><?= substr($h->horario_inicio, 0, 5) ?> às <?= substr($h->horario_termino, 0, 5) ?></small>
                                </td>
                                <td>
                                  <span class="badge rounded-pill text-white px-2 py-1" style="background-color: <?= esc($h->cor_departamento ?: '#2563eb') ?>;">
                                    <?= esc($h->nome_departamento) ?>: <?= esc($h->nome_area) ?>
                                  </span>
                                </td>
                                <td class="text-center">
                                  <?php if ($h->status_presenca == 1) { ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i>Presente</span>
                                  <?php } else { ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1"><i class="bi bi-x-circle me-1"></i>Ausente</span>
                                  <?php } ?>
                                </td>
                                <td class="pe-3 text-muted"><?= esc($h->obs_escala ?: '-') ?></td>
                              </tr>
                            <?php } ?>
                          <?php } else { ?>
                            <tr>
                              <td colspan="4" class="text-center py-3 text-muted">Nenhuma escala registrada no período selecionado.</td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>

                  </div>
                <?php } ?>

              </div>
            </div>

            <div class="card-footer bg-body py-3 d-flex justify-content-between align-items-center">
              <a href="<?= base_url('voluntario') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Lista
              </a>
              <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Salvar Alterações' : 'Cadastrar Voluntário' ?>
              </button>
            </div>
          </form>

        </div>
      </div>

    </div>

  </div>
</div>

<script>
  // Live Preview Bindings
  document.getElementById('nome')?.addEventListener('input', function() {
    document.getElementById('previewNome').textContent = this.value || 'Nome do Voluntário';
  });
  document.getElementById('email')?.addEventListener('input', function() {
    document.getElementById('previewEmail').innerHTML = '<i class="bi bi-envelope me-1"></i>' + (this.value || 'email@exemplo.com');
  });
  document.getElementById('telefone_whatsapp')?.addEventListener('input', function() {
    document.getElementById('previewWhats').innerHTML = '<i class="bi bi-whatsapp me-1"></i>' + (this.value || '(00) 00000-0000');
  });
  document.getElementById('foto_file')?.addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
      const reader = new FileReader();
      reader.onload = function(ev) {
        document.getElementById('imgAvatarPreview').src = ev.target.result;
      };
      reader.readAsDataURL(e.target.files[0]);
    }
  });

  // Atualiza contador e destaque visual das áreas
  function atualizarContadorAreas() {
    const checkboxes = document.querySelectorAll('.dep-area-checkbox');
    let totalChecked = 0;
    checkboxes.forEach(chk => {
      const parent = chk.closest('.custom-checkbox-card');
      if (chk.checked) {
        totalChecked++;
        if (parent) {
          parent.classList.add('bg-primary-subtle', 'border-primary');
          parent.classList.remove('bg-body');
        }
      } else {
        if (parent) {
          parent.classList.remove('bg-primary-subtle', 'border-primary');
          parent.classList.add('bg-body');
        }
      }
    });

    const badge = document.getElementById('badgeTotalAreasSelecionadas');
    if (badge) {
      badge.innerHTML = `<i class="bi bi-check2-all me-1"></i> ${totalChecked} selecionada(s)`;
    }
  }

  // Event listener nos checkboxes de sub-áreas
  document.querySelectorAll('.dep-area-checkbox').forEach(chk => {
    chk.addEventListener('change', atualizarContadorAreas);
  });

  // Marcar / Desmarcar todas as áreas globalmente
  function marcarTodasAreas(marcar) {
    document.querySelectorAll('.dep-area-checkbox').forEach(chk => {
      chk.checked = marcar;
    });
    atualizarContadorAreas();
  }

  // Alternar todas as áreas de um departamento específico
  function toggleTodasAreasDepartamento(depId) {
    const checks = document.querySelectorAll(`.dep-area-checkbox[data-dep-id="${depId}"]`);
    const total = checks.length;
    let marcados = 0;
    checks.forEach(c => {
      if (c.checked) marcados++;
    });

    const novoStatus = (marcados < total);
    checks.forEach(c => {
      c.checked = novoStatus;
    });
    atualizarContadorAreas();
  }

  // Atualiza contador e destaque visual dos cultos
  function atualizarContadorCultos() {
    const checkboxes = document.querySelectorAll('.culto-checkbox');
    let totalChecked = 0;
    checkboxes.forEach(chk => {
      const parent = chk.closest('.custom-checkbox-card');
      if (chk.checked) {
        totalChecked++;
        if (parent) {
          parent.classList.add('bg-primary-subtle', 'border-primary', 'shadow-xs');
          parent.classList.remove('bg-body');
        }
      } else {
        if (parent) {
          parent.classList.remove('bg-primary-subtle', 'border-primary', 'shadow-xs');
          parent.classList.add('bg-body');
        }
      }
    });

    const badge = document.getElementById('badgeTotalCultosSelecionados');
    if (badge) {
      if (totalChecked === 0) {
        badge.className = 'badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-2 fw-bold';
        badge.innerHTML = `<i class="bi bi-asterisk me-1"></i> Todos (Disponibilidade Total)`;
      } else {
        badge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-bold';
        badge.innerHTML = `<i class="bi bi-check2-all me-1"></i> ${totalChecked} selecionado(s)`;
      }
    }
  }

  // Event listener nos checkboxes de cultos
  document.querySelectorAll('.culto-checkbox').forEach(chk => {
    chk.addEventListener('change', atualizarContadorCultos);
  });

  // Marcar / Desmarcar todos os cultos
  function marcarTodosCultos(marcar) {
    document.querySelectorAll('.culto-checkbox').forEach(chk => {
      chk.checked = marcar;
    });
    atualizarContadorCultos();
  }

  // Inicializa estado visual
  document.addEventListener('DOMContentLoaded', function() {
    atualizarContadorAreas();
    atualizarContadorCultos();
  });

  // Dynamic Social Media Links
  function adicionarRedeSocial() {
    const container = document.getElementById('containerRedesSociais');
    const div = document.createElement('div');
    div.className = 'row g-2 align-items-center rede-social-item';
    div.innerHTML = `
      <div class="col-md-4">
        <select name="rede_nome[]" class="form-select form-select-sm">
          <option value="Instagram">Instagram</option>
          <option value="LinkedIn">LinkedIn</option>
          <option value="TikTok">TikTok</option>
          <option value="YouTube">YouTube</option>
          <option value="Facebook">Facebook</option>
          <option value="Outro">Outro</option>
        </select>
      </div>
      <div class="col-md-7">
        <input type="text" name="rede_link[]" class="form-control form-control-sm" placeholder="Ex: @usuario ou https://...">
      </div>
      <div class="col-md-1 text-center">
        <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removerRedeSocial(this)" title="Remover">
          <i class="bi bi-trash3"></i>
        </button>
      </div>
    `;
    container.appendChild(div);
  }

  function removerRedeSocial(btn) {
    btn.closest('.rede-social-item').remove();
  }

  // Preserve hash tab navigation
  if (window.location.hash) {
    const triggerEl = document.querySelector(`button[data-bs-target="${window.location.hash}"]`);
    if (triggerEl) {
      const tab = new bootstrap.Tab(triggerEl);
      tab.show();
    }
  }

  // Reset Senha Modal Handler
  function abrirModalResetSenhaForm(id, nome, telefone) {
    document.getElementById('reset_id_voluntario_form').value = id;
    document.getElementById('reset_nome_voluntario_form').textContent = nome;
    document.getElementById('reset_tel_voluntario_form').textContent = telefone || 'Telefone não cadastrado';
    const modal = new bootstrap.Modal(document.getElementById('modalResetSenhaForm'));
    modal.show();
  }

  function confirmarResetSenhaForm() {
    const id = document.getElementById('reset_id_voluntario_form').value;
    const btn = document.getElementById('btnConfirmarResetSenhaForm');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Redefinindo...';

    fetch('<?= base_url('voluntario/resetSenha') ?>/' + id, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        const modalEl = document.getElementById('modalResetSenhaForm');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        if (data.status) {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Senha Resetada!',
              html: `${data.message}<br><br><strong>Nova senha padrão:</strong> <code>${data.nova_senha_padrao}</code>`,
              confirmButtonColor: '#2563eb'
            });
          } else {
            alert(data.message + '\nNova senha padrão: ' + data.nova_senha_padrao);
          }
        } else {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'Erro',
              text: data.message || 'Erro ao resetar senha.'
            });
          } else {
            alert('Erro: ' + (data.message || 'Erro ao resetar senha.'));
          }
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        alert('Erro de comunicação com o servidor.');
      });
  }
</script>

<!-- Modal Resetar Senha Form -->
<div class="modal fade" id="modalResetSenhaForm" tabindex="-1" aria-labelledby="modalResetSenhaFormLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-warning-subtle border-0">
        <h5 class="modal-title fw-bold text-warning-emphasis" id="modalResetSenhaFormLabel">
          <i class="bi bi-key-fill me-2"></i> Resetar Senha do Voluntário
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="reset_id_voluntario_form" value="">
        <p class="mb-2">Você está prestes a redefinir a senha do voluntário:</p>
        <div class="p-3 bg-body-tertiary rounded-3 border mb-3">
          <div class="fw-bold fs-6 text-body" id="reset_nome_voluntario_form">-</div>
          <small class="text-muted"><i class="bi bi-whatsapp me-1 text-success"></i> <span id="reset_tel_voluntario_form">-</span></small>
        </div>
        <div class="alert alert-info border-0 rounded-3 small mb-0">
          <i class="bi bi-info-circle-fill me-1"></i> A senha será redefinida para os dígitos do número de telefone (WhatsApp) do voluntário (apenas números, sem espaços e pontuação).
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning rounded-pill px-4 fw-semibold" id="btnConfirmarResetSenhaForm" onclick="confirmarResetSenhaForm()">
          <i class="bi bi-check2-circle me-1"></i> Confirmar Reset
        </button>
      </div>
    </div>
  </div>
</div>