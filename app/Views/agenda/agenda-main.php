<!-- FullCalendar 6 Bundle & Locales (pt-br) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/pt-br.global.min.js"></script>

<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-calendar-event text-primary me-2"></i>Agenda de Cultos & Eventos
        </h3>
        <p class="text-secondary small mb-0">Controle de cultos, horários e agendamento de convidados</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Agenda</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    
    <!-- Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
          <i class="bi bi-sun-fill me-1"></i> Finais de semana em destaque
        </span>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
          <i class="bi bi-palette-fill me-1"></i> Cores personalizadas por culto
        </span>
      </div>

      <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= base_url('agenda/imprimir') ?>" target="_blank" class="btn btn-outline-dark rounded-pill px-3 fw-semibold" id="btnImprimirAgendaCal" title="Imprimir / Salvar PDF do mês consultado">
          <i class="bi bi-printer-fill me-1"></i> Imprimir Agenda (PDF)
        </a>
        <a href="<?= base_url('agenda/grade') ?>" class="btn btn-outline-success rounded-pill px-3 fw-bold">
          <i class="bi bi-grid-3x3-gap-fill me-1"></i> Grade Mensal Dinâmica (Modo Tabela)
        </a>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" id="btnNovoCulto">
          <i class="bi bi-plus-lg me-1"></i> Cadastrar Novo Culto / Evento
        </button>
      </div>
    </div>

    <!-- Calendar Card -->
    <div class="card card-outline card-primary shadow-sm border-0 mb-4">
      <div class="card-body p-3">
        <div id="calendar"></div>
      </div>
    </div>

  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: NOVO / EDITAR CULTO -->
<!-- ========================================== -->
<div class="modal fade" id="modalCulto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-primary text-white rounded-top-4 py-3">
        <h5 class="modal-title fw-bold" id="modalCultoTitle">
          <i class="bi bi-calendar-plus me-2"></i>Cadastrar Culto / Evento
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formCulto">
        <div class="modal-body p-4">
          <input type="hidden" name="id_culto" id="culto_id" value="0">

          <?php if (!empty($cultosPadrao)) { ?>
            <div class="mb-3 p-3 bg-light border rounded-3">
              <label for="selectCultoPadrao" class="form-label fw-bold text-primary mb-1">
                <i class="bi bi-journal-bookmark-fill me-1"></i> Carregar Modelo de Culto Pré-Cadastrado
              </label>
              <select class="form-select" id="selectCultoPadrao">
                <option value="">-- Selecionar Modelo ou Digitar Manualmente --</option>
                <?php foreach ($cultosPadrao as $cp) { 
                  $nomeDia = \App\Models\CultoPadraoModel::getNomeDiaSemana($cp->dia_semana);
                ?>
                  <option value="<?= $cp->id_culto_padrao ?>"
                          data-titulo="<?= esc($cp->nome_culto) ?>"
                          data-inicio="<?= esc(substr($cp->horario_inicio, 0, 5)) ?>"
                          data-termino="<?= esc(substr($cp->horario_termino, 0, 5)) ?>"
                          data-cor="<?= esc($cp->cor_evento) ?>"
                          data-descricao="<?= esc($cp->descricao) ?>">
                    <?= esc($cp->nome_culto) ?> (<?= esc($nomeDia) ?>s às <?= substr($cp->horario_inicio, 0, 5) ?>)
                  </option>
                <?php } ?>
              </select>
            </div>
          <?php } ?>

          <div class="mb-3">
            <label for="culto_titulo" class="form-label fw-semibold">Título do Culto / Evento</label>
            <input type="text" class="form-control" name="titulo_culto" id="culto_titulo" placeholder="Ex: Culto de Celebração" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-12">
              <label for="culto_data" class="form-label fw-semibold">Data do Culto</label>
              <input type="date" class="form-control" name="data_culto" id="culto_data" required>
            </div>
            <div class="col-md-6">
              <label for="culto_inicio" class="form-label fw-semibold">Horário Início</label>
              <input type="time" class="form-control" name="horario_inicio" id="culto_inicio" value="19:00" required>
            </div>
            <div class="col-md-6">
              <label for="culto_termino" class="form-label fw-semibold">Horário Término</label>
              <input type="time" class="form-control" name="horario_termino" id="culto_termino" value="21:00" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Cor de Destaque no Calendário</label>
            <div class="d-flex flex-wrap gap-2">
              <input type="radio" class="btn-check" name="cor_evento" id="cor1" value="#2563eb" checked>
              <label class="btn btn-outline-primary rounded-pill px-3" for="cor1">Azul</label>

              <input type="radio" class="btn-check" name="cor_evento" id="cor2" value="#16a34a">
              <label class="btn btn-outline-success rounded-pill px-3" for="cor2">Verde</label>

              <input type="radio" class="btn-check" name="cor_evento" id="cor3" value="#dc2626">
              <label class="btn btn-outline-danger rounded-pill px-3" for="cor3">Vermelho</label>

              <input type="radio" class="btn-check" name="cor_evento" id="cor4" value="#d97706">
              <label class="btn btn-outline-warning rounded-pill px-3" for="cor4">Laranja</label>

              <input type="radio" class="btn-check" name="cor_evento" id="cor5" value="#7c3aed">
              <label class="btn btn-outline-secondary rounded-pill px-3" for="cor5">Roxo</label>
            </div>
          </div>

          <div class="mb-3">
            <label for="culto_descricao" class="form-label fw-semibold">Descrição / Observações</label>
            <textarea class="form-control" name="descricao" id="culto_descricao" rows="2" placeholder="Ex: Culto especial da família com santa ceia"></textarea>
          </div>
        </div>

        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-save me-1"></i> Salvar Culto
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: GERENCIAR CONVIDADOS DO CULTO -->
<!-- ========================================== -->
<div class="modal fade" id="modalConvidados" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-primary text-white rounded-top-4 py-3">
        <div>
          <h5 class="modal-title fw-bold" id="modalConvidadosTitle">
            <i class="bi bi-people-fill me-2"></i>Gerenciar Convidados do Culto
          </h5>
          <small id="modalConvidadosSub" class="text-white-50"></small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <input type="hidden" id="culto_selecionado_id" value="0">

        <!-- Card de Vinculação com Pesquisa e Filtro por Função Eclesiástica -->
        <div class="card bg-body-tertiary border-0 rounded-3 mb-4 shadow-sm">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="fw-bold mb-0 text-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Vincular Convidados ao Culto
              </h6>
              <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold" id="btnToggleSelectAll">
                <i class="bi bi-check2-all me-1"></i>Selecionar Visíveis
              </button>
            </div>
            
            <!-- Barra de Filtros (Pesquisa por Nome e Função Eclesiástica) -->
            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                  <input type="text" class="form-control form-control-sm border-start-0" id="searchConvidadoInput" placeholder="Pesquisar por nome ou instagram...">
                </div>
              </div>
              <div class="col-md-6">
                <select class="form-select form-select-sm" id="filterFuncaoEclesiastica">
                  <option value="">Todas as Funções Eclesiásticas</option>
                  <?php if (!empty($funcoesEclesiasticas)) { ?>
                    <?php foreach ($funcoesEclesiasticas as $func) { ?>
                      <option value="<?= $func->id_funcao_eclesiastica ?>"><?= esc($func->nm_funcao_eclesiastica) ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>

            <form id="formVincularConvidados">
              <!-- Lista de Checkboxes de Convidados -->
              <div class="list-group border rounded-3 overflow-auto mb-3 bg-white" style="max-height: 180px;" id="listConvidadosCheckboxes">
                <?php if (!empty($convidados)) { ?>
                  <?php foreach ($convidados as $conv) { ?>
                    <label class="list-group-item d-flex align-items-center gap-2 item-convidado-select py-2 border-bottom-0" data-id-funcao="<?= $conv->id_funcao_eclesiastica ?>" data-nome="<?= esc(mb_strtolower($conv->nome_convidado)) ?>" data-nick="<?= esc(mb_strtolower($conv->nick_instagram ?? '')) ?>">
                      <input class="form-check-input me-2 chkConvidadoItem" type="checkbox" name="ids_convidados[]" value="<?= $conv->id_convidado ?>">
                      <div class="flex-grow-1">
                        <div class="fw-semibold text-body small mb-0"><?= esc($conv->nome_convidado) ?></div>
                        <small class="text-secondary" style="font-size: 0.75rem;">
                          <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-0 me-1"><?= esc($conv->nm_funcao_eclesiastica ?? 'Sem Função') ?></span>
                          <?= !empty($conv->nick_instagram) ? '&bull; @' . esc($conv->nick_instagram) : '' ?>
                        </small>
                      </div>
                    </label>
                  <?php } ?>
                <?php } else { ?>
                  <div class="p-3 text-center text-muted small">Nenhum convidado cadastrado no sistema.</div>
                <?php } ?>
              </div>

              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold btn-sm">
                  <i class="bi bi-link-45deg me-1"></i> Vincular Selecionados
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tabela de Convidados Vinculados -->
        <h6 class="fw-bold mb-3">
          <i class="bi bi-list-check me-1"></i> Convidados Confirmados para este Culto
        </h6>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Convidado</th>
                <th scope="col" class="text-center">Presença</th>
                <th scope="col" class="text-end">Ações</th>
              </tr>
            </thead>
            <tbody id="tbodyConvidadosCulto">
              <!-- Renderizado via AJAX -->
            </tbody>
          </table>
        </div>

      </div>

      <div class="modal-footer border-top-0 pt-0 px-4 pb-4 justify-content-between">
        <button type="button" class="btn btn-outline-danger rounded-pill px-3" id="btnExcluirCulto">
          <i class="bi bi-trash me-1"></i> Excluir Culto
        </button>
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
      </div>

    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: CONFIRMAÇÃO DE EXCLUSÃO DE CULTO -->
<!-- ========================================== -->
<div class="modal fade" id="modalConfirmExcluirCulto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-danger text-white rounded-top-4 py-3">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Exclusão do Culto
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <p class="mb-0 text-body fs-6">Tem certeza que deseja excluir este culto e todas as suas vinculações de convidados?</p>
      </div>
      <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold" id="btnConfirmarExcluirCulto">
          <i class="bi bi-trash me-1"></i> Sim, Excluir
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: CONFIRMAÇÃO DE REMOÇÃO DE CONVIDADO -->
<!-- ========================================== -->
<div class="modal fade" id="modalConfirmRemoverConvidado" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-warning text-dark rounded-top-4 py-3">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-person-x-fill me-2"></i>Desvincular Convidado
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <p class="mb-0 text-body fs-6">Deseja remover este convidado do culto selecionado?</p>
        <input type="hidden" id="remover_convidado_id" value="0">
      </div>
      <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold" id="btnConfirmarRemoverConvidado">
          <i class="bi bi-person-dash me-1"></i> Desvincular Convidado
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Estilos para destacar os Finais de Semana (Sábado e Domingo) -->
<style>
  #calendar {
    width: 100% !important;
    min-height: 680px;
  }
  .fc-day-sat, .fc-day-sun {
    background-color: rgba(220, 38, 38, 0.04) !important;
  }
  .fc-day-sat .fc-col-header-cell-cushion, 
  .fc-day-sun .fc-col-header-cell-cushion {
    color: #dc2626 !important;
    font-weight: 700;
  }

  .fc-event {
    border-radius: 0.5rem !important;
    padding: 3px 6px !important;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }
  .cursor-pointer {
    cursor: pointer;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const modalCultoEl = new bootstrap.Modal(document.getElementById('modalCulto'));
    const modalConvidadosEl = new bootstrap.Modal(document.getElementById('modalConvidados'));
    const modalConfirmExcluirCultoEl = new bootstrap.Modal(document.getElementById('modalConfirmExcluirCulto'));
    const modalRemoverConvidadoEl = new bootstrap.Modal(document.getElementById('modalConfirmRemoverConvidado'));

    // Helper Toast
    function notify(type, title, message) {
      if (typeof USToast !== 'undefined' && USToast.show) {
        USToast.show(type, title, message);
      } else if (typeof $.messageAlert === 'function') {
        var tipoMap = { 'success': 1, 'error': 2, 'warning': 3, 'info': 4 };
        $.messageAlert({
          tipoMensagem: tipoMap[type] || 4,
          mensagemDestaque: title,
          mensagem: message,
          temporizador: 5000
        });
      }
    }

    // Configuração do FullCalendar em Português
    const calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'pt-br',
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listMonth'
      },
      buttonText: {
        today:    'Hoje',
        month:    'Mês',
        week:     'Semana',
        list:     'Lista'
      },
      events: '<?= base_url('agenda/events') ?>',
      selectable: true,
      select: function(info) {
        document.getElementById('culto_id').value = '0';
        document.getElementById('culto_titulo').value = '';
        document.getElementById('culto_data').value = info.startStr;
        document.getElementById('culto_inicio').value = '19:00';
        document.getElementById('culto_termino').value = '21:00';
        document.getElementById('culto_descricao').value = '';
        if (document.getElementById('selectCultoPadrao')) document.getElementById('selectCultoPadrao').value = '';
        document.getElementById('modalCultoTitle').innerHTML = '<i class="bi bi-calendar-plus me-2"></i>Cadastrar Culto / Evento';
        modalCultoEl.show();
      },
      eventClick: function(info) {
        const props = info.event.extendedProps;
        const id_culto = info.event.id;

        document.getElementById('culto_selecionado_id').value = id_culto;
        document.getElementById('modalConvidadosSub').innerText = `${props.titulo_culto} - ${props.data_culto} (${props.horario_inicio} às ${props.horario_termino})`;
        
        carregarConvidadosDoCulto(id_culto);
        modalConvidadosEl.show();
      },
      datesSet: function(info) {
        // Atualiza dinamicamente o link de impressão para o mês e ano atualmente exibidos no calendário
        const viewDate = info.view.currentStart;
        const currentYear = viewDate.getFullYear();
        const currentMonth = viewDate.getMonth() + 1;

        const btnPrint = document.getElementById('btnImprimirAgendaCal');
        if (btnPrint) {
          btnPrint.href = `<?= base_url('agenda/imprimir') ?>/${currentYear}/${currentMonth}`;
        }
      }
    });

    calendar.render();

    // Auto-preenchimento ao selecionar Modelo de Culto Pré-Cadastrado
    const selectCultoPadrao = document.getElementById('selectCultoPadrao');
    if (selectCultoPadrao) {
      selectCultoPadrao.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (!opt || !opt.value) return;

        document.getElementById('culto_titulo').value = opt.getAttribute('data-titulo') || '';
        document.getElementById('culto_inicio').value = opt.getAttribute('data-inicio') || '19:00';
        document.getElementById('culto_termino').value = opt.getAttribute('data-termino') || '21:00';
        document.getElementById('culto_descricao').value = opt.getAttribute('data-descricao') || '';

        const cor = opt.getAttribute('data-cor') || '#2563eb';
        const radioCor = document.querySelector(`input[name="cor_evento"][value="${cor}"]`);
        if (radioCor) radioCor.checked = true;
      });
    }

    // Botão Manual "Novo Culto"
    document.getElementById('btnNovoCulto').addEventListener('click', function() {
      document.getElementById('culto_id').value = '0';
      document.getElementById('culto_titulo').value = '';
      document.getElementById('culto_data').value = new Date().toISOString().split('T')[0];
      document.getElementById('culto_inicio').value = '19:00';
      document.getElementById('culto_termino').value = '21:00';
      document.getElementById('culto_descricao').value = '';
      if (selectCultoPadrao) selectCultoPadrao.value = '';
      document.getElementById('modalCultoTitle').innerHTML = '<i class="bi bi-calendar-plus me-2"></i>Cadastrar Culto / Evento';
      modalCultoEl.show();
    });

    // Submissão do Form de Culto
    document.getElementById('formCulto').addEventListener('submit', function(e) {
      e.preventDefault();
      const btnSubmit = this.querySelector('button[type="submit"]');
      const originalText = btnSubmit.innerHTML;
      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

      const formData = new FormData(this);

      fetch('<?= base_url('agenda/salvarCulto') ?>', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        if (!res.ok) {
          throw new Error('HTTP Error ' + res.status);
        }
        return res.json();
      })
      .then(data => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalText;

        if (data.status === 'success') {
          modalCultoEl.hide();
          calendar.refetchEvents();
          notify('success', 'Agenda de Cultos', data.message || 'Culto salvo com sucesso!');
        } else {
          notify('error', 'Agenda de Cultos', data.message || 'Erro ao salvar o culto.');
        }
      })
      .catch(err => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalText;
        console.error(err);
        notify('error', 'Falha na Requisição', 'Erro ao processar requisição: ' + err.message);
      });
    });

    // Filtros de Pesquisa e Função Eclesiástica nos Convidados
    const searchInput = document.getElementById('searchConvidadoInput');
    const filterFuncao = document.getElementById('filterFuncaoEclesiastica');

    function filtrarListaConvidados() {
      const query = (searchInput.value || '').toLowerCase().trim();
      const funcaoId = (filterFuncao.value || '').toString();

      document.querySelectorAll('#listConvidadosCheckboxes .item-convidado-select').forEach(item => {
        const nome = item.getAttribute('data-nome') || '';
        const nick = item.getAttribute('data-nick') || '';
        const func = (item.getAttribute('data-id-funcao') || '').toString();

        const matchText = !query || nome.includes(query) || nick.includes(query);
        const matchFunc = !funcaoId || func === funcaoId;

        if (matchText && matchFunc) {
          item.classList.remove('d-none');
          item.classList.add('d-flex');
        } else {
          item.classList.remove('d-flex');
          item.classList.add('d-none');
        }
      });
    }

    if (searchInput) searchInput.addEventListener('input', filtrarListaConvidados);
    if (filterFuncao) filterFuncao.addEventListener('change', filtrarListaConvidados);

    // Botão Selecionar Visíveis
    document.getElementById('btnToggleSelectAll').addEventListener('click', function() {
      const visibleCheckboxes = document.querySelectorAll('#listConvidadosCheckboxes .item-convidado-select:not(.d-none) .chkConvidadoItem');
      const allChecked = Array.from(visibleCheckboxes).every(chk => chk.checked);

      visibleCheckboxes.forEach(chk => {
        chk.checked = !allChecked;
      });
    });

    // Carregar Lista de Convidados do Culto
    function carregarConvidadosDoCulto(id_culto) {
      const tbody = document.getElementById('tbodyConvidadosCulto');
      tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3"><div class="spinner-border text-primary spinner-border-sm me-2"></div> Carregando convidados...</td></tr>';

      fetch(`<?= base_url('agenda/getConvidadosCulto/') ?>/${id_culto}`)
      .then(res => {
        if (!res.ok) {
          throw new Error('Erro HTTP ' + res.status);
        }
        return res.json();
      })
      .then(data => {
        if (!Array.isArray(data) || data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4"><i class="bi bi-people fs-4 d-block mb-1"></i>Nenhum convidado vinculado a este culto ainda.</td></tr>';
          return;
        }

        let html = '';
        data.forEach(c => {
          const isPresente = c.status_presenca == 1;
          html += `
            <tr>
              <td>
                <div class="fw-bold">${c.nome_convidado}</div>
                <small class="text-secondary">${c.nm_funcao_eclesiastica || 'Sem Função'} ${c.nick_instagram ? ' &bull; @' + c.nick_instagram : ''}</small>
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-sm ${isPresente ? 'btn-success' : 'btn-outline-secondary'} rounded-pill px-3 btnTogglePresenca" data-id="${c.id_culto_convidado}" data-status="${isPresente ? 0 : 1}">
                  <i class="bi ${isPresente ? 'bi-check-circle-fill' : 'bi-circle'} me-1"></i> ${isPresente ? 'Presente' : 'Ausente'}
                </button>
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <a href="<?= base_url('agenda/dashConvidado') ?>/${c.id_convidado}" class="btn btn-outline-info" title="Ver Dashboard de Assiduidade">
                    <i class="bi bi-graph-up-arrow"></i>
                  </a>
                  <button type="button" class="btn btn-outline-danger btnRemoverConvidado" data-id="${c.id_culto_convidado}" title="Desvincular">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `;
        });
        tbody.innerHTML = html;
        bindConvidadosEvents();
      })
      .catch(err => {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-3">Erro ao carregar convidados.</td></tr>';
        notify('error', 'Convidados do Culto', 'Erro ao carregar convidados: ' + err.message);
      });
    }

    // Submissão do Form de Vinculação de Convidados
    document.getElementById('formVincularConvidados').addEventListener('submit', function(e) {
      e.preventDefault();
      const id_culto = document.getElementById('culto_selecionado_id').value;
      const checkedBoxes = document.querySelectorAll('.chkConvidadoItem:checked');
      const selectedIds = Array.from(checkedBoxes).map(chk => chk.value);

      if (selectedIds.length === 0) {
        notify('warning', 'Seleção Obrigatória', 'Selecione ao menos um convidado na lista.');
        return;
      }

      const formData = new FormData();
      formData.append('id_culto', id_culto);
      selectedIds.forEach(id => formData.append('ids_convidados[]', id));

      fetch('<?= base_url('agenda/vincularConvidados') ?>', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          // Limpa as seleções de checkbox após vincular com sucesso
          checkedBoxes.forEach(chk => chk.checked = false);
          carregarConvidadosDoCulto(id_culto);
          calendar.refetchEvents();
          notify('success', 'Agenda de Cultos', data.message);
        } else {
          notify('error', 'Agenda de Cultos', data.message || 'Erro ao vincular convidados.');
        }
      });
    });

    // Eventos de clique para Alternar Presença e Remover Convidado
    function bindConvidadosEvents() {
      document.querySelectorAll('.btnTogglePresenca').forEach(btn => {
        btn.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const status = this.getAttribute('data-status');
          const id_culto = document.getElementById('culto_selecionado_id').value;

          const formData = new FormData();
          formData.append('id_culto_convidado', id);
          formData.append('status_presenca', status);

          fetch('<?= base_url('agenda/alternarPresenca') ?>', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            carregarConvidadosDoCulto(id_culto);
            notify('info', 'Status de Presença', data.message || 'Status atualizado!');
          });
        });
      });

      document.querySelectorAll('.btnRemoverConvidado').forEach(btn => {
        btn.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          document.getElementById('remover_convidado_id').value = id;
          modalRemoverConvidadoEl.show();
        });
      });
    }

    // Confirmar Remoção de Convidado
    document.getElementById('btnConfirmarRemoverConvidado').addEventListener('click', function() {
      const id = document.getElementById('remover_convidado_id').value;
      const id_culto = document.getElementById('culto_selecionado_id').value;

      const formData = new FormData();
      formData.append('id_culto_convidado', id);

      fetch('<?= base_url('agenda/removerConvidado') ?>', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        modalRemoverConvidadoEl.hide();
        carregarConvidadosDoCulto(id_culto);
        calendar.refetchEvents();
        notify('success', 'Agenda de Cultos', data.message || 'Convidado removido.');
      });
    });

    // Modal de Confirmação para Excluir Culto
    document.getElementById('btnExcluirCulto').addEventListener('click', function() {
      modalConfirmExcluirCultoEl.show();
    });

    document.getElementById('btnConfirmarExcluirCulto').addEventListener('click', function() {
      const id_culto = document.getElementById('culto_selecionado_id').value;
      const formData = new FormData();
      formData.append('id_culto', id_culto);

      fetch('<?= base_url('agenda/excluirCulto') ?>', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        modalConfirmExcluirCultoEl.hide();
        modalConvidadosEl.hide();

        if (data.status === 'success') {
          calendar.refetchEvents();
          notify('success', 'Agenda de Cultos', 'Culto excluído com sucesso!');
        } else {
          notify('error', 'Agenda de Cultos', data.message || 'Erro ao excluir culto.');
        }
      });
    });
  });
</script>
