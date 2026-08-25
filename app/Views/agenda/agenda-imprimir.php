<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agenda de Cultos - <?= $nomeMesExtenso ?> / <?= $ano ?></title>
  
  <!-- Bootstrap 5 & Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
      margin: 0;
      padding: 0;
    }

    .no-print-bar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: #1e293b;
      color: #fff;
      padding: 12px 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .print-container {
      max-width: 210mm;
      margin: 20px auto;
      background: #fff;
      padding: 20mm 15mm;
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      border-radius: 8px;
    }

    .print-header {
      border-bottom: 3px solid #1e3a8a;
      padding-bottom: 12px;
      margin-bottom: 20px;
    }

    .table-print {
      width: 100%;
      border-collapse: collapse;
    }

    .table-print th {
      background-color: #1e3a8a !important;
      color: #ffffff !important;
      font-size: 10pt;
      font-weight: 700;
      text-transform: uppercase;
      padding: 8px 12px;
      border: 1px solid #1e3a8a;
    }

    .table-print td {
      padding: 8px 12px;
      border: 1px solid #cbd5e1;
      font-size: 9.5pt;
      vertical-align: top;
    }

    .table-print tr:nth-child(even) td {
      background-color: #f8fafc;
    }

    .fim-semana-row td {
      background-color: #fffbeb !important;
    }

    .guest-item {
      font-weight: 600;
      color: #0f172a;
      line-height: 1.4;
    }

    .culto-item {
      font-weight: 700;
      color: #1e3a8a;
    }

    /* Regras Estritas de Impressão A4 / PDF */
    @page {
      size: A4 portrait;
      margin: 10mm;
    }

    @media print {
      .no-print, .no-print-bar {
        display: none !important;
      }

      body {
        background: #fff !important;
        color: #000 !important;
      }

      .print-container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
      }

      .print-header {
        border-bottom: 2px solid #000 !important;
      }

      .table-print th {
        background-color: #000 !important;
        color: #fff !important;
        border: 1px solid #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .table-print td {
        border: 1px solid #64748b !important;
        font-size: 9pt !important;
      }

      .fim-semana-row td {
        background-color: #fef3c7 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      tr {
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body>

  <!-- Barra de Ação Superior (Invisível na Impressão) -->
  <div class="no-print-bar no-print d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <a href="<?= base_url("agenda/grade/{$ano}/{$mes}") ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar para a Grade
      </a>
      <span class="text-white-50">|</span>
      <span class="fw-bold text-white"><i class="bi bi-file-earmark-pdf me-1"></i> Pré-visualização de Impressão A4</span>
    </div>
    <div>
      <button type="button" onclick="window.print()" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow">
        <i class="bi bi-printer-fill me-1"></i> Imprimir / Salvar em PDF
      </button>
    </div>
  </div>

  <!-- Folha de Impressão A4 -->
  <div class="print-container">
    
    <!-- Cabeçalho Oficial -->
    <div class="print-header d-flex align-items-center justify-content-between">
      <div>
        <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: -0.5px; color: #1e3a8a;">
          ADVEC &bull; Assembleia de Deus Vitória em Cristo
        </h4>
        <div class="text-secondary small fw-semibold">Sistema Oficial de Gestão de Agenda e Convidados</div>
      </div>
      <div class="text-end">
        <div class="badge bg-primary fs-6 px-3 py-2 text-uppercase font-monospace fw-bold" style="background-color: #1e3a8a !important;">
          <?= $nomeMesExtenso ?> / <?= $ano ?>
        </div>
        <div class="text-muted small mt-1">Gerado em: <?= date('d/m/Y H:i') ?></div>
      </div>
    </div>

    <!-- Tabela da Agenda do Mês -->
    <table class="table-print">
      <thead>
        <tr>
          <th style="width: 110px;" class="text-center">Data</th>
          <th style="width: 130px;">Dia Semana</th>
          <th>Culto / Horário</th>
          <th>Convidado(s) Escalado(s)</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          $hasAnyCulto = false;
          foreach ($diasGrade as $dia) { 
            if (empty($dia['cultos_agendados'])) continue;
            $hasAnyCulto = true;
            $isFimSemana = $dia['is_fim_semana'];
            $rowClass = $isFimSemana ? 'fim-semana-row' : '';
        ?>
          <tr class="<?= $rowClass ?>">
            
            <!-- Data -->
            <td class="text-center font-monospace fw-bold">
              <?= $dia['data_formatada'] ?>
            </td>

            <!-- Dia da Semana -->
            <td class="fw-semibold text-capitalize">
              <?= $dia['nome_dia_semana'] ?>
            </td>

            <!-- Cultos do Dia -->
            <td>
              <?php foreach ($dia['cultos_agendados'] as $idx => $c) { ?>
                <div class="<?= $idx > 0 ? 'mt-2 pt-2 border-top' : '' ?>">
                  <div class="culto-item">
                    <?= esc($c->titulo_culto) ?>
                  </div>
                  <div class="small text-secondary font-monospace">
                    <?= substr($c->horario_inicio, 0, 5) ?>h - <?= substr($c->horario_termino, 0, 5) ?>h
                  </div>
                </div>
              <?php } ?>
            </td>

            <!-- Convidados Escalados (Apenas nomes, um abaixo do outro) -->
            <td>
              <?php foreach ($dia['cultos_agendados'] as $idx => $c) { ?>
                <div class="<?= $idx > 0 ? 'mt-2 pt-2 border-top' : '' ?>">
                  <?php if (!empty($c->convidadosVinculados)) { ?>
                    <div class="d-flex flex-column gap-1">
                      <?php foreach ($c->convidadosVinculados as $conv) { ?>
                        <div class="guest-item">
                          &bull; <?= esc($conv->nome_convidado) ?>
                          <?php if (!empty($conv->nm_funcao_eclesiastica)) { ?>
                            <small class="text-secondary opacity-75 fw-normal">(<?= esc($conv->nm_funcao_eclesiastica) ?>)</small>
                          <?php } ?>
                        </div>
                      <?php } ?>
                    </div>
                  <?php } else { ?>
                    <span class="text-muted italic small">— Nenhum convidado escalado —</span>
                  <?php } ?>
                </div>
              <?php } ?>
            </td>

          </tr>
        <?php } ?>

        <?php if (!$hasAnyCulto) { ?>
          <tr>
            <td colspan="4" class="text-center py-4 text-muted italic">
              Nenhum culto agendado para o mês de <?= $nomeMesExtenso ?> / <?= $ano ?>.
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <!-- Rodapé de Impressão -->
    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center text-muted small">
      <div>Assembleia de Deus Vitória em Cristo &bull; Relatório de Escala Mensal</div>
      <div>Página 1</div>
    </div>

  </div>

</body>
</html>
