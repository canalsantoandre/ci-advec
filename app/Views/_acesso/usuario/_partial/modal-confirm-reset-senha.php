<form id="frm_usuario_reset" name="frm_usuario_reset" data-url="<?= base_url('/usuario/resetSenha') ?>" role="form" method="post" action="">
  <input type="hidden" value="<?= esc($usr->hash_user); ?>" name="hash_user" id="hash_user">

  <div class="modal-content rounded-4 border-0 shadow-lg">
    <div class="modal-header bg-warning text-dark rounded-top-4">
      <h5 class="modal-title fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Reiniciar Senha</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body p-4">
      <p class="mb-0 fs-6 text-body">
        Deseja confirmar a reinicialização da senha deste usuário? A senha será redefinida para o padrão de sistema.
      </p>
    </div>
    <div class="modal-footer border-top-0">
      <button class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal" type="button">Cancelar</button>
      <button class="btn btn-warning text-dark fw-bold rounded-pill px-4" data-bs-dismiss="modal" type="button" id="btnConfirmaReset" name="btnConfirmaReset">
        <i class="bi bi-check-lg me-1"></i> Confirmar
      </button>
    </div>
  </div>
</form>