<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Gestão de perfil e informações do convidado</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('convidado') ?>"><?= esc($sys_module->nome_modulo) ?></a></li>
          <li class="breadcrumb-item active" aria-current="page">Editar Perfil</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">

    <form method="post" id="frm_convidado" name="frm_convidado" action="<?= base_url('/convidado/atualizar') ?>">
      <input type="hidden" name="id_convidado" id="id_convidado" value="<?= esc($convidado->id_convidado) ?>">
      <input type="hidden" name="hash_convidado" id="hash_convidado" value="<?= esc($convidado->hash_convidado); ?>">

      <div class="row g-4">

        <!-- ========================================== -->
        <!-- COLUNA ESQUERDA: PROFILE HERO CARD -->
        <!-- ========================================== -->
        <div class="col-lg-4">
          <div class="card card-outline card-primary shadow-sm border-0 rounded-4 sticky-lg-top" style="top: 80px;">
            <div class="card-body text-center p-4">
              
              <!-- Avatar Container com Preview Instantâneo -->
              <div class="position-relative d-inline-block mb-3">
                <?php 
                  $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($convidado->nome_convidado) . '&background=2563eb&color=fff&size=150';
                  $avatarUrl = !empty($convidado->url_foto_instagram) ? $convidado->url_foto_instagram : $defaultAvatar;
                ?>
                <img src="<?= esc($avatarUrl) ?>" id="imgAvatarPreview" class="rounded-circle shadow border border-3 border-white" style="width: 140px; height: 140px; object-fit: cover;" alt="Foto de Perfil" onError="this.onerror=null;this.src='<?= $defaultAvatar ?>';">
                
                <span class="position-absolute bottom-0 end-0 badge rounded-circle p-2 bg-<?= ($convidado->status_convidado == 1 ? 'success' : 'danger') ?> border border-2 border-white" title="<?= ($convidado->status_convidado == 1 ? 'Ativo' : 'Inativo') ?>">
                  <span class="visually-hidden">Status</span>
                </span>
              </div>

              <!-- Identificação -->
              <h5 class="fw-bold mb-1 text-body"><?= esc($convidado->nome_convidado) ?></h5>
              <?php if (!empty($convidado->nome_convidado_visualizacao)) { ?>
                <p class="text-muted small mb-2">(<?= esc($convidado->nome_convidado_visualizacao) ?>)</p>
              <?php } ?>

              <div class="mb-3">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                  <i class="bi bi-person-badge-fill me-1"></i><?= esc($convidado->nm_funcao_eclesiastica ?? 'Sem Função') ?>
                </span>
              </div>

              <?php if (!empty($convidado->nick_instagram)) { ?>
                <div class="mb-3">
                  <a href="https://instagram.com/<?= str_replace('@', '', $convidado->nick_instagram) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                    <i class="bi bi-instagram me-1"></i> @<?= esc(str_replace('@', '', $convidado->nick_instagram)) ?>
                  </a>
                </div>
              <?php } ?>

              <hr class="my-3 opacity-25">

              <!-- Atalhos Rápidos -->
              <div class="d-grid gap-2">
                <a href="<?= base_url('agenda/dashConvidado/' . $convidado->id_convidado) ?>" class="btn btn-outline-info rounded-pill fw-semibold btn-sm">
                  <i class="bi bi-graph-up-arrow me-1"></i> Ver Dashboard de Assiduidade
                </a>
              </div>

            </div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- COLUNA DIREITA: FORMULÁRIO COMPLETO -->
        <!-- ========================================== -->
        <div class="col-lg-8">
          <div class="card card-outline card-primary shadow-sm border-0 rounded-4">
            
            <!-- Header com Abas -->
            <div class="card-header bg-body p-0 border-bottom">
              <ul class="nav nav-tabs card-header-tabs m-0 border-bottom-0" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active fw-bold py-3 px-4" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados-pane" type="button" role="tab">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Dados Pessoais
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-bold py-3 px-4" id="midia-tab" data-bs-toggle="tab" data-bs-target="#midia-pane" type="button" role="tab">
                    <i class="bi bi-camera-fill me-2 text-danger"></i>Foto & Redes
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link fw-bold py-3 px-4" id="anexos-tab" data-bs-toggle="tab" data-bs-target="#anexos-pane" type="button" role="tab">
                    <i class="bi bi-folder-fill me-2 text-warning"></i>Fotos & Obs
                  </button>
                </li>
              </ul>
            </div>

            <div class="card-body p-4">
              <div class="tab-content" id="profileTabsContent">

                <!-- ABA 1: DADOS PESSOAIS -->
                <div class="tab-pane fade show active" id="dados-pane" role="tabpanel" tabindex="0">
                  
                  <h6 class="fw-bold text-primary mb-3">
                    <i class="bi bi-card-heading me-1"></i> Informações Principais
                  </h6>

                  <div class="row g-3 mb-3">
                    <div class="col-md-7">
                      <label for="txtConvidadoNome" class="form-label fw-semibold">Nome Completo <span class="text-danger">*</span></label>
                      <input type="text" name="txtConvidadoNome" id="txtConvidadoNome" value="<?= esc($convidado->nome_convidado); ?>" class="form-control" maxlength="100" required placeholder="Ex: Pr. Antonio Filho">
                    </div>

                    <div class="col-md-5">
                      <label for="txtConvidadoNomeExibicao" class="form-label fw-semibold">Nome de Exibição / Apelido</label>
                      <input type="text" name="txtConvidadoNomeExibicao" id="txtConvidadoNomeExibicao" value="<?= esc($convidado->nome_convidado_visualizacao); ?>" class="form-control" maxlength="30" placeholder="Ex: Pr. Antonio">
                    </div>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Função Eclesiástica</label>
                      <?= $comboFuncao ?>
                    </div>

                    <div class="col-md-6">
                      <label for="txtConvidadoDataNascimento" class="form-label fw-semibold">Data de Nascimento</label>
                      <input type="date" name="txtConvidadoDataNascimento" id="txtConvidadoDataNascimento" value="<?= esc($convidado->data_nascimento); ?>" class="form-control">
                    </div>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label for="txtConvidadoEmail" class="form-label fw-semibold">E-mail de Contato</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="txtConvidadoEmail" id="txtConvidadoEmail" value="<?= esc($convidado->email); ?>" class="form-control" maxlength="50" placeholder="nome@exemplo.com">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <label for="txtConvidadoTelefone" class="form-label fw-semibold">Telefone / WhatsApp</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                        <input type="text" name="txtConvidadoTelefone" id="txtConvidadoTelefone" value="<?= esc($convidado->telefone); ?>" class="form-control celular" maxlength="20" placeholder="(00) 00000-0000">
                      </div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="cboConvidadoStatus" class="form-label fw-semibold">Status do Cadastro</label>
                    <select class="form-select" name="cboConvidadoStatus" id="cboConvidadoStatus">
                      <option value="1" <?= ($convidado->status_convidado == 1 ? "selected" : ""); ?>>Ativo (Disponível para agendamento)</option>
                      <option value="0" <?= ($convidado->status_convidado == 0 ? "selected" : ""); ?>>Inativo</option>
                    </select>
                  </div>

                </div>

                <!-- ABA 2: FOTO & REDES SOCIAIS -->
                <div class="tab-pane fade" id="midia-pane" role="tabpanel" tabindex="0">
                  
                  <h6 class="fw-bold text-danger mb-3">
                    <i class="bi bi-image me-1"></i> Foto de Perfil & Redes Sociais
                  </h6>

                  <div class="mb-4">
                    <label for="txtConvidadoFotoUrl" class="form-label fw-semibold">URL da Foto do Perfil</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person-square text-primary"></i></span>
                      <input type="url" name="txtConvidadoFotoUrl" id="txtConvidadoFotoUrl" value="<?= esc($convidado->url_foto_instagram); ?>" class="form-control" placeholder="https://exemplo.com/foto.jpg ou URL do Instagram">
                    </div>
                    <small class="text-secondary">Cole a URL direta da imagem para exibir a foto de perfil do convidado no sistema.</small>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label for="txtConvidadoInstagram" class="form-label fw-semibold">Usuário do @Instagram</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-instagram text-danger"></i></span>
                        <input type="text" name="txtConvidadoInstagram" id="txtConvidadoInstagram" value="<?= esc(str_replace('@', '', $convidado->nick_instagram)); ?>" class="form-control" maxlength="30" placeholder="usuario_instagram">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <label for="txtConvidadoLinkFotos" class="form-label fw-semibold">Link Principal de Galeria de Fotos</label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <a href="<?= esc($convidado->link_fotos); ?>" target="_blank" class="text-decoration-none" title="Abrir link">
                            <i class="bi bi-link-45deg fs-5"></i>
                          </a>
                        </span>
                        <input type="url" name="txtConvidadoLinkFotos" id="txtConvidadoLinkFotos" value="<?= esc($convidado->link_fotos); ?>" class="form-control" maxlength="200" placeholder="https://drive.google.com/...">
                      </div>
                    </div>
                  </div>

                </div>

                <!-- ABA 3: FOTOS ADICIONAIS & OBSERVAÇÕES -->
                <div class="tab-pane fade" id="anexos-pane" role="tabpanel" tabindex="0">
                  
                  <?php if ($sys_action->linkfotos_visualizar) { ?>
                    <h6 class="fw-bold text-warning mb-3">
                      <i class="bi bi-images me-1"></i> Galeria de Links Adicionais
                    </h6>
                    <div class="mb-4">
                      <div class="accordion" id="accordionAnexoOP">
                        <div class="accordion-item border shadow-sm rounded-3">
                          <h2 class="accordion-header" id="headingFotos">
                            <button class="accordion-button bg-light fw-bold text-dark rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                              <i class="bi bi-paperclip text-primary me-2"></i> Links de Fotos Anexas
                            </button>
                          </h2>
                          <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingFotos" data-bs-parent="#accordionAnexoOP">
                            <div class="accordion-body" id="divLinkFotos">
                              <?= $LinkFotos; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>

                  <h6 class="fw-bold text-secondary mb-3">
                    <i class="bi bi-card-text me-1"></i> Observações & Notas Internas
                  </h6>
                  <div class="mb-3">
                    <textarea name="txtConvidadoObservacao" id="txtConvidadoObservacao" class="form-control" rows="4" placeholder="Adicione anotações ou observações sobre o convidado..."><?= esc($convidado->observacao); ?></textarea>
                  </div>

                </div>

              </div>
            </div>

            <!-- Footer com Ações -->
            <div class="card-footer bg-body border-top p-3 d-flex justify-content-between align-items-center">
              <a href="<?= base_url('convidado') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Cancelar / Voltar
              </a>
              <?php if ($sys_action->update) { ?>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                  <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                </button>
              <?php } ?>
            </div>

          </div>
        </div>

      </div>
    </form>

  </div>
</div>

<script type="text/javascript" src="<?= base_url('templates/js') ?>/convidado/validacoes.js"></script>
<script type="text/javascript" src="<?= base_url('templates/js') ?>/convidado/validacoes_link_foto.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const inputFotoUrl = document.getElementById('txtConvidadoFotoUrl');
    const imgPreview = document.getElementById('imgAvatarPreview');
    const defaultAvatar = '<?= $defaultAvatar ?>';

    if (inputFotoUrl && imgPreview) {
      inputFotoUrl.addEventListener('input', function() {
        const val = this.value.trim();
        if (val) {
          imgPreview.src = val;
        } else {
          imgPreview.src = defaultAvatar;
        }
      });
    }
  });
</script>