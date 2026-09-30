<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Escala Mensal - <?= esc($departamento->nome) ?> - <?= $nomeMesExtenso ?> / <?= $ano ?></title>
  
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
      max-width: 297mm; /* Paisagem para acomodar colunas */
      margin: 20px auto;
      background: #fff;
      padding: 15mm 15mm;
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
      font-size: 8.5pt;
    }

    .table-print th {
      background-color: #1e3a8a !important;
      color: #ffffff !important;
      font-size: 8pt;
      font-weight: 700;
      text-transform: uppercase;
      padding: 6px 8px;
      border: 1px solid #1e3a8a;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .table-print td {
      border: 1px solid #cbd5e1;
      padding: 6px 8px;
      vertical-align: middle;
    }

    .weekend-row {
      background-color: #fef3c7 !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .badge-presenca {
      font-size: 7.5pt;
      font-weight: 700;
      padding: 2px 6px;
      border-radius: 4px;
    }

    @media print {
      .no-print-bar {
        display: none !important;
      }
      body {
        background: #fff;
      }
      .print-container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
      }
      @page {
        size: A4 landscape;
        margin: 10mm;
      }
    }
  </style>
</head>
<body>

  <!-- Barra de controle superior (oculta na impressão) -->
  <div class="no-print-bar d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-printer-fill fs-5 text-warning"></i>
      <span class="fw-bold">Visualização de Impressão - Grade de Escalas</span>
      <span class="badge bg-primary ms-2"><?= esc($departamento->nome) ?> &bull; <?= $nomeMesExtenso ?> / <?= $ano ?></span>
    </div>
    <div class="d-flex gap-2">
      <button onclick="window.print()" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold">
        <i class="bi bi-printer me-1"></i> Imprimir / Salvar em PDF
      </button>
      <a href="<?= base_url("escala/grade/{$departamento->id_departamento}/{$ano}/{$mes}") ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar à Grade
      </a>
    </div>
  </div>

  <div class="print-container">
    
    <!-- Cabeçalho Institucional -->
    <div class="print-header d-flex justify-content-between align-items-center">
      <div>
        <h3 class="fw-bold text-primary mb-0">ADVEC - GESTÃO DE VOLUNTÁRIOS</h3>
        <h5 class="fw-semibold text-secondary mb-0">Escala de Serviço: <strong><?= esc($departamento->nome) ?></strong></h5>
        <small class="text-muted">Responsável: <?= esc($departamento->responsavel_nome) ?> &bull; Tel: <?= esc($departamento->responsavel_telefone) ?></small>
      </div>
      <div class="text-end">
        <h4 class="fw-bold text-dark mb-0 font-monospace"><?= $nomeMesExtenso ?> / <?= $ano ?></h4>
        <small class="text-muted d-block">Emitido em: <?= date('d/m/Y \à\s H:i') ?></small>
      </div>
    </div>

    <!-- Tabela Matriz da Escala -->
    <table class="table-print">
      <thead>
        <tr>
          <th style="width: 65px;" class="text-center">Dia</th>
          <th style="width: 85px;">Semana</th>
          <th style="width: 140px;">Culto / Horário</th>
          <?php foreach ($areas as $a) { ?>
            <th class="text-center"><?= esc($a->nome_area) ?></th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($diasGrade as $dia) { 
          $isFimSemana = $dia['is_fim_semana'];
          $temCulto = !empty($dia['cultos_agendados']);
          if (!$temCulto && !$isFimSemana) continue;

          if ($temCulto) {
            foreach ($dia['cultos_agendados'] as $c) {
        ?>
          <tr class="<?= $isFimSemana ? 'weekend-row' : '' ?>">
            
            <td class="text-center fw-bold font-monospace">
              <?= $dia['dia_num'] ?>/<?= sprintf('%02d', $mes) ?>
            </td>

            <td class="fw-semibold text-capitalize">
              <?= $dia['nome_dia_semana'] ?>
            </td>

            <td>
              <div class="fw-bold text-dark"><?= esc($c->titulo_culto) ?></div>
              <small class="text-muted"><?= substr($c->horario_inicio, 0, 5) ?> às <?= substr($c->horario_termino, 0, 5) ?></small>
            </td>

            <?php foreach ($areas as $a) { 
              $escalas = $c->escalasPorArea[$a->id_area] ?? [];
            ?>
              <td class="text-center">
                <?php if (!empty($escalas)) { ?>
                  <?php foreach ($escalas as $esc) { 
                    $nick = trim((string)($esc->nickname ?? ''));
                    if (!empty($nick)) {
                      $nomePrint = $nick;
                    } else {
                      $partes = preg_split('/\s+/', trim((string)$esc->nome_voluntario));
                      $nomePrint = count($partes) <= 1 ? ($partes[0] ?? '') : ($partes[0] . ' ' . end($partes));
                    }
                  ?>
                    <div class="fw-bold text-dark" style="font-size: 8pt;">
                      <?= esc($nomePrint) ?>
                    </div>
                  <?php } ?>
                <?php } else { ?>
                  <span class="text-muted" style="font-size: 7.5pt;">(vago)</span>
                <?php } ?>
              </td>
            <?php } ?>

          </tr>
        <?php 
            } // Fim foreach cultos
          } else { // Fim de semana sem culto
        ?>
          <tr class="<?= $isFimSemana ? 'weekend-row' : '' ?>">
            
            <td class="text-center fw-bold font-monospace">
              <?= $dia['dia_num'] ?>/<?= sprintf('%02d', $mes) ?>
            </td>

            <td class="fw-semibold text-capitalize">
              <?= $dia['nome_dia_semana'] ?>
            </td>

            <td>
              <span class="text-muted fst-italic">Sem culto agendado</span>
            </td>

            <?php foreach ($areas as $a) { ?>
              <td class="text-center text-muted">-</td>
            <?php } ?>

          </tr>
        <?php } ?>
        <?php } ?>
      </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top text-muted small">
      <div>ADVEC Gestão - Módulo de Escalas e Voluntários</div>
      <div>Página 1 de 1</div>
    </div>

  </div>

</body>
</html>
