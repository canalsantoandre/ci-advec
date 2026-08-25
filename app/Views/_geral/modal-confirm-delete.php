<div class="modal-content rounded-4 border-0 shadow-lg">
  <div class="modal-header bg-danger text-white rounded-top-4">
    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($geral_titulo) ?></h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body p-4 fs-6 text-body">
    <?= $geral_mensagem ?>
  </div>
  <div class="modal-footer border-top-0">
    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
    <a href="<?= esc($geral_link) ?>" title="Confirmar" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold">
      <i class="bi bi-trash me-1"></i> Confirmar Exclusão
    </a>
  </div>
</div>