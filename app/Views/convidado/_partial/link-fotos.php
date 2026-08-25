<!-- Trazer via partial View a cada escolha do produto-->
<div class="table-responsive">
  <table id="tblAnexos" name="tblAnexos" class="table table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th scope="col">Link / Descrição</th>
        <th scope="col" class="text-end" style="width: 80px;">Ação</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($convidado_link_foto as $row) { ?>
        <tr>
          <td>
            <div class="callout callout-info py-2 px-3 m-0 rounded-3 border-start border-4 border-info">
              <a href="<?= esc($row->link_foto); ?>" target="_blank" class="text-decoration-none fw-semibold" title="<?= esc($row->link_foto); ?>">
                <i class="bi bi-eye me-2 text-info"></i><?= esc($row->descricao_link_foto) ?>
              </a>
            </div>
          </td>
          <td class="text-end">
            <?php if ($sys_action->linkfotos_excluir) { ?>
              <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" data-mensagem="Deseja realmente excluir o link <strong><?= esc($row->descricao_link_foto) ?></strong>?" data-url="<?= base_url('convidadolinkfoto/apagar/' . $row->id_convidado_link_foto) ?>" title="Apagar" data-bs-toggle="modal" data-bs-target="#confirmExluirLinkFoto" id="btnPreApagarLinkFoto">
                <i class="bi bi-trash"></i>
              </button>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>

      <?php if ($sys_action->linkfotos_inserir) { ?>
        <tr class="table-light">
          <td colspan="2" class="pt-3">
            <input type="hidden" id="hidIdConvidado" name="hidIdConvidado" value="<?= esc($convidado->id_convidado); ?>">
            <input type="hidden" id="hidUrlInserirLinkFoto" name="hidUrlInserirLinkFoto" value="<?= base_url('convidadolinkfoto/inserir'); ?>">
            
            <div class="row g-2">
              <div class="col-md-6">
                <input type="url" class="form-control form-control-sm" id="txtConvidadoItemLinkFoto" name="txtConvidadoItemLinkFoto" value="" placeholder="https://... (URL da foto)">
              </div>
              <div class="col-md-4">
                <input type="text" class="form-control form-control-sm" id="txtConvidadoItemLinkFotoDescricao" name="txtConvidadoItemLinkFotoDescricao" value="" placeholder="Descrição do link">
              </div>
              <div class="col-md-2 d-grid">
                <button type="button" class="btn btn-success btn-sm rounded-pill fw-bold" id="btnIncluirLinkFoto" name="btnIncluirLinkFoto" title="Adicionar Link">
                  <i class="bi bi-plus-lg me-1"></i> Adicionar
                </button>
              </div>
            </div>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<!-- Modal Confirma Exclusão Link Foto -->
<div class="modal fade" id="confirmExluirLinkFoto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Exclusão</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <span id="mensagem"></span>
      </div>
      <div class="modal-footer border-top-0">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-dismiss="modal" id="btnApagarLinkFoto" name="btnApagarLinkFoto">
          <i class="bi bi-trash me-1"></i> Confirmar Exclusão
        </button>
      </div>
    </div>
  </div>
</div>