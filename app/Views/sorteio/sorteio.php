<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold text-primary">
          <i class="bi bi-gift-fill me-2"></i>Sorteio de Prêmios ADVEC
        </h3>
        <p class="text-secondary small mb-0">Painel interativo para sorteios de participantes do encontro</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Sorteio</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">

        <div class="card card-outline card-warning shadow-lg border-0 rounded-4 my-4 p-4 p-md-5">
          <div class="card-body">
            <div class="mb-4">
              <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-4 py-2 fs-6 mb-3 fw-bold">
                <i class="bi bi-trophy-fill me-1"></i> Sorteio Oficial
              </span>
              <h2 class="fw-bold mb-2">Sorteio de Participantes</h2>
              <p class="text-secondary">Clique no botão abaixo para realizar o sorteio em tempo real</p>
            </div>

            <!-- Winner Display Card -->
            <div id="winnerCard" class="card bg-body-tertiary border-dashed border-2 p-4 my-4 rounded-4" style="min-height: 160px; display: flex; align-items: center; justify-content: center;">
              <div id="winnerText">
                <i class="bi bi-stars text-warning fs-1 d-block mb-2"></i>
                <h4 class="text-muted fw-normal mb-0">Aguardando início do sorteio...</h4>
              </div>
            </div>

            <div class="d-flex justify-content-center gap-3">
              <button id="btnRealizarSorteio" class="btn btn-warning btn-lg rounded-pill px-5 py-3 shadow fw-bold text-dark fs-5">
                <i class="bi bi-play-circle-fill me-2"></i> REALIZAR SORTEIO
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const btnSorteio = document.getElementById('btnRealizarSorteio');
    const winnerCard = document.getElementById('winnerCard');
    const winnerText = document.getElementById('winnerText');

    if (btnSorteio && winnerCard) {
      btnSorteio.addEventListener('click', function() {
        btnSorteio.disabled = true;
        winnerText.innerHTML = `
          <div class="spinner-border text-warning mb-3" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Sorteando...</span>
          </div>
          <h4 class="fw-bold text-primary mb-0 animate-pulse">Sorteando ganhador...</h4>
        `;

        setTimeout(function() {
          winnerText.innerHTML = `
            <div class="animate-bounce">
              <i class="bi bi-trophy-fill text-warning fs-1 d-block mb-2"></i>
              <h3 class="fw-bold text-success mb-1">Ganhador Selecionado!</h3>
              <p class="text-secondary small mb-0">Verifique a lista de inscritos para confirmar a entrega</p>
            </div>
          `;
          btnSorteio.disabled = false;
        }, 2500);
      });
    }
  });
</script>
