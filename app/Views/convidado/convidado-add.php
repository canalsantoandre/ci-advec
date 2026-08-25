<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="<?= esc($sys_module->icon_class_modulo) ?> text-primary me-2"></i><?= esc($sys_module->nome_modulo) ?>
        </h3>
        <p class="text-secondary small mb-0">Cadastrar novo convidado no sistema</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('convidado') ?>"><?= esc($sys_module->nome_modulo) ?></a></li>
          <li class="breadcrumb-item active" aria-current="page">Adicionar</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-lg-10">

        <div class="card card-outline card-primary shadow-sm border-0">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-person-plus-fill me-2"></i>Informações do Novo Convidado
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" id="frm_convidado" name="frm_convidado" action="<?= base_url('/convidado/inserir') ?>">
              
              <div class="row g-3 mb-3">
                <div class="col-md-8">
                  <label for="txtConvidadoNome" class="form-label fw-semibold">Nome Completo</label>
                  <input type="text" name="txtConvidadoNome" id="txtConvidadoNome" value="" class="form-control" maxlength="100" placeholder="Ex: João da Silva" required>
                </div>
                <div class="col-md-4">
                  <label for="txtConvidadoNomeExibicao" class="form-label fw-semibold">Nome de Exibição</label>
                  <input type="text" name="txtConvidadoNomeExibicao" id="txtConvidadoNomeExibicao" value="" class="form-control" maxlength="30" placeholder="Ex: João Silva">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="txtConvidadoInstagram" class="form-label fw-semibold">@Instagram</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-instagram text-danger"></i></span>
                    <input type="text" name="txtConvidadoInstagram" id="txtConvidadoInstagram" value="" class="form-control" maxlength="30" placeholder="usuario">
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="txtConvidadoLinkFotos" class="form-label fw-semibold">Link de Fotos</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input type="url" name="txtConvidadoLinkFotos" id="txtConvidadoLinkFotos" value="" class="form-control" maxlength="200" placeholder="https://...">
                  </div>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="txtConvidadoEmail" class="form-label fw-semibold">E-mail</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="txtConvidadoEmail" id="txtConvidadoEmail" value="" class="form-control" maxlength="50" placeholder="email@exemplo.com">
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="txtConvidadoTelefone" class="form-label fw-semibold">Telefone / WhatsApp</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="text" name="txtConvidadoTelefone" id="txtConvidadoTelefone" value="" class="form-control celular" maxlength="20" placeholder="(00) 00000-0000">
                  </div>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Função Eclesiástica</label>
                  <?= $comboFuncao ?>
                </div>
                <div class="col-md-6">
                  <label for="txtConvidadoObservacao" class="form-label fw-semibold">Observações</label>
                  <textarea name="txtConvidadoObservacao" id="txtConvidadoObservacao" class="form-control" rows="2" placeholder="Observações adicionais..."></textarea>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('convidado/') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                  <i class="bi bi-arrow-left me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                  <i class="bi bi-check-lg me-1"></i> Salvar Convidado
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= base_url('templates/js') ?>/convidado/validacoes.js"></script>