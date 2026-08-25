<!-- Content Header (Page header) -->
<div class="app-content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6">
        <h3 class="mb-0 fw-bold">
          <i class="bi bi-journal-plus text-primary me-2"></i><?= !empty($cultoPadrao) ? 'Editar Tipo de Culto' : 'Cadastrar Tipo de Culto' ?>
        </h3>
        <p class="text-secondary small mb-0">Configure os parâmetros do culto padrão para geração rápida na agenda</p>
      </div>
      <div class="col-sm-6 text-sm-end">
        <ol class="breadcrumb float-sm-end mb-2">
          <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('cultopadrao') ?>">Tipos de Culto</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= !empty($cultoPadrao) ? 'Editar' : 'Novo' ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="app-content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-lg-8">

        <div class="card card-outline card-primary shadow-sm border-0 rounded-4">
          <div class="card-header bg-body py-3">
            <h5 class="card-title fw-bold mb-0 text-primary">
              <i class="bi bi-journal-bookmark-fill me-2"></i>Informações do Tipo de Culto
            </h5>
          </div>

          <div class="card-body p-4">
            <form method="post" action="<?= base_url('cultopadrao/salvar') ?>">
              <input type="hidden" name="id_culto_padrao" value="<?= esc($cultoPadrao->id_culto_padrao ?? 0) ?>">

              <div class="mb-3">
                <label for="nome_culto" class="form-label fw-semibold">Nome do Culto / Evento Padrão <span class="text-danger">*</span></label>
                <input type="text" name="nome_culto" id="nome_culto" class="form-control" value="<?= esc($cultoPadrao->nome_culto ?? '') ?>" placeholder="Ex: Culto da Vitória" required>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="dia_semana" class="form-label fw-semibold">Dia da Semana do Culto <span class="text-danger">*</span></label>
                  <select name="dia_semana" id="dia_semana" class="form-select" required>
                    <?php foreach ($diasSemana as $numDia => $nomeDia) { ?>
                      <option value="<?= $numDia ?>" <?= (isset($cultoPadrao->dia_semana) && $cultoPadrao->dia_semana == $numDia) ? 'selected' : '' ?>>
                        <?= esc($nomeDia) ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="status_culto" class="form-label fw-semibold">Status do Modelo</label>
                  <select name="status_culto" id="status_culto" class="form-select">
                    <option value="1" <?= (!isset($cultoPadrao->status_culto) || $cultoPadrao->status_culto == 1) ? 'selected' : '' ?>>Ativo (Disponível na Agenda)</option>
                    <option value="0" <?= (isset($cultoPadrao->status_culto) && $cultoPadrao->status_culto == 0) ? 'selected' : '' ?>>Inativo</option>
                  </select>
                </div>
              </div>

              <!-- Configuração de Recorrência Mensal -->
              <div class="card bg-body-tertiary border-0 rounded-3 p-3 mb-3">
                <h6 class="fw-bold text-primary mb-2">
                  <i class="bi bi-repeat me-1"></i> Regra de Recorrência no Mês
                </h6>
                <div class="row g-3">
                  <div class="col-md-7">
                    <label for="tipo_recorrencia" class="form-label fw-semibold">Frequência no Mês</label>
                    <select name="tipo_recorrencia" id="tipo_recorrencia" class="form-select">
                      <?php 
                        $tipoAtual = $cultoPadrao->tipo_recorrencia ?? 'todas';
                        foreach ($tiposRecorrencia as $chave => $rotulo) { 
                      ?>
                        <option value="<?= $chave ?>" <?= ($tipoAtual === $chave ? 'selected' : '') ?>>
                          <?= esc($rotulo) ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="col-md-5" id="divPosicaoSemana" style="display: <?= in_array($tipoAtual, ['apenas_posicao', 'exceto_posicao']) ? 'block' : 'none' ?>;">
                    <label for="posicao_semana" class="form-label fw-semibold">Ocorrência no Mês</label>
                    <select name="posicao_semana" id="posicao_semana" class="form-select">
                      <option value="1" <?= (isset($cultoPadrao->posicao_semana) && $cultoPadrao->posicao_semana == 1) ? 'selected' : '' ?>>1ª (Primeira do Mês)</option>
                      <option value="2" <?= (isset($cultoPadrao->posicao_semana) && $cultoPadrao->posicao_semana == 2) ? 'selected' : '' ?>>2ª (Segunda do Mês)</option>
                      <option value="3" <?= (isset($cultoPadrao->posicao_semana) && $cultoPadrao->posicao_semana == 3) ? 'selected' : '' ?>>3ª (Terceira do Mês)</option>
                      <option value="4" <?= (isset($cultoPadrao->posicao_semana) && $cultoPadrao->posicao_semana == 4) ? 'selected' : '' ?>>4ª (Quarta do Mês)</option>
                      <option value="5" <?= (isset($cultoPadrao->posicao_semana) && $cultoPadrao->posicao_semana == 5) ? 'selected' : '' ?>>5ª (Quinta do Mês)</option>
                    </select>
                  </div>
                </div>
                <small class="text-secondary d-block mt-2">
                  <i class="bi bi-info-circle me-1"></i> A regra de recorrência é aplicada pelo gerador de agenda para posicionar o culto apenas nos dias corretos.
                </small>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="horario_inicio" class="form-label fw-semibold">Horário de Início <span class="text-danger">*</span></label>
                  <input type="time" name="horario_inicio" id="horario_inicio" class="form-control" value="<?= esc(isset($cultoPadrao->horario_inicio) ? substr($cultoPadrao->horario_inicio, 0, 5) : '19:00') ?>" required>
                </div>

                <div class="col-md-6">
                  <label for="horario_termino" class="form-label fw-semibold">Horário de Término <span class="text-danger">*</span></label>
                  <input type="time" name="horario_termino" id="horario_termino" class="form-control" value="<?= esc(isset($cultoPadrao->horario_termino) ? substr($cultoPadrao->horario_termino, 0, 5) : '21:00') ?>" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Cor de Destaque no Calendário</label>
                <?php $currentCor = $cultoPadrao->cor_evento ?? '#2563eb'; ?>
                <div class="d-flex flex-wrap gap-2">
                  <input type="radio" class="btn-check" name="cor_evento" id="cor1" value="#2563eb" <?= ($currentCor == '#2563eb' ? 'checked' : '') ?>>
                  <label class="btn btn-outline-primary rounded-pill px-3" for="cor1">Azul</label>

                  <input type="radio" class="btn-check" name="cor_evento" id="cor2" value="#16a34a" <?= ($currentCor == '#16a34a' ? 'checked' : '') ?>>
                  <label class="btn btn-outline-success rounded-pill px-3" for="cor2">Verde</label>

                  <input type="radio" class="btn-check" name="cor_evento" id="cor3" value="#dc2626" <?= ($currentCor == '#dc2626' ? 'checked' : '') ?>>
                  <label class="btn btn-outline-danger rounded-pill px-3" for="cor3">Vermelho</label>

                  <input type="radio" class="btn-check" name="cor_evento" id="cor4" value="#d97706" <?= ($currentCor == '#d97706' ? 'checked' : '') ?>>
                  <label class="btn btn-outline-warning rounded-pill px-3" for="cor4">Laranja</label>

                  <input type="radio" class="btn-check" name="cor_evento" id="cor5" value="#7c3aed" <?= ($currentCor == '#7c3aed' ? 'checked' : '') ?>>
                  <label class="btn btn-outline-secondary rounded-pill px-3" for="cor5">Roxo</label>
                </div>
              </div>

              <div class="mb-4">
                <label for="descricao" class="form-label fw-semibold">Descrição / Observações</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="3" placeholder="Informações ou recomendações do culto padrão..."><?= esc($cultoPadrao->descricao ?? '') ?></textarea>
              </div>

              <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <a href="<?= base_url('cultopadrao') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                  <i class="bi bi-arrow-left me-1"></i> Cancelar / Voltar
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                  <i class="bi bi-save me-1"></i> Salvar Tipo de Culto
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const selTipo = document.getElementById('tipo_recorrencia');
    const divPos  = document.getElementById('divPosicaoSemana');
    if (selTipo && divPos) {
      selTipo.addEventListener('change', function() {
        const val = this.value;
        if (val === 'apenas_posicao' || val === 'exceto_posicao') {
          divPos.style.display = 'block';
        } else {
          divPos.style.display = 'none';
        }
      });
    }
  });
</script>
