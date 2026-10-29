<?php

namespace App\Models;

use CodeIgniter\Model;

class VoluntarioModel extends Model
{
    protected $table            = 'tb_voluntario';
    protected $primaryKey       = 'id_voluntario';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_filial',
        'hash_voluntario',
        'nome',
        'nickname',
        'nivel_conhecimento',
        'email',
        'senha',
        'primeiro_acesso',
        'force_pwd_change',
        'otp_code',
        'otp_expires_at',
        'data_ultimo_login',
        'data_ultima_senha',
        'telefone_whatsapp',
        'data_nascimento',
        'foto_url',
        'status',
        'status_aprovacao',
        'id_convite_origem',
        'max_escalas_mes',
        'redes_sociais',
        'observacao'
    ];

    /**
     * Formata tempo em segundos para exibição legível de agilidade
     */
    public static function formatarTempoResposta(?int $segundos): string
    {
        if ($segundos === null || $segundos >= 999999990 || $segundos < 0) {
            return 'Pendente';
        }
        if ($segundos < 60) {
            return $segundos . 's';
        }
        $minutos = (int)round($segundos / 60);
        if ($minutos < 60) {
            return $minutos . ' min';
        }
        $horas = floor($minutos / 60);
        $restoMin = $minutos % 60;
        if ($horas < 24) {
            return $restoMin > 0 ? "{$horas}h {$restoMin}m" : "{$horas}h";
        }
        $dias = floor($horas / 24);
        $restoHoras = $horas % 24;
        return $restoHoras > 0 ? "{$dias}d {$restoHoras}h" : "{$dias}d";
    }

    /**
     * Retorna lista completa de voluntários com filtros e resumo de áreas/escalas
     */
    public function listaVoluntarios(array $filtros = [])
    {
        // Sincroniza e marca como Omissão (-25 pts) escalas passadas não confirmadas
        (new EscalaVoluntarioModel())->marcarOmissoesPassadas();

        $db = db_connect();
        $builder = $db->table('tb_voluntario as v');
        $builder->select('
            v.*,
            COUNT(DISTINCT ev.id_escala_voluntario) as total_escalas,
            COUNT(DISTINCT CASE WHEN ev.status_presenca = 1 THEN ev.id_escala_voluntario END) as total_presencas
        ');
        $builder->join('tb_escala_voluntario as ev', 'ev.id_voluntario = v.id_voluntario', 'left');
        
        // Exclui cadastros pendentes de aprovação da lista regular
        $builder->where("COALESCE(v.status_aprovacao, 'APROVADO') !=", 'PENDENTE');

        if (!empty($filtros['id_departamento'])) {
            $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_voluntario = v.id_voluntario', 'inner');
            $builder->where('vda.id_departamento', (int)$filtros['id_departamento']);
        } elseif (isset($filtros['departamentos_permitidos'])) {
            if (empty($filtros['departamentos_permitidos'])) {
                return [];
            }
            $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_voluntario = v.id_voluntario', 'inner');
            $builder->whereIn('vda.id_departamento', (array)$filtros['departamentos_permitidos']);
        }

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $builder->where('v.status', (int)$filtros['status']);
        }

        if (!empty($filtros['busca'])) {
            $busca = trim($filtros['busca']);
            $builder->groupStart()
                ->like('v.nome', $busca)
                ->orLike('v.email', $busca)
                ->orLike('v.telefone_whatsapp', $busca)
                ->groupEnd();
        }

        $builder->groupBy('v.id_voluntario');
        $builder->orderBy('v.nome', 'ASC');

        $voluntarios = $builder->get()->getResult('object');

        // Carrega departamentos e áreas de cada voluntário
        if (!empty($voluntarios)) {
            $voluntarioAreaModel = new VoluntarioAreaModel();
            foreach ($voluntarios as &$vol) {
                $vol->areas = $voluntarioAreaModel->getAreasDoVoluntario($vol->id_voluntario);
                $vol->taxa_assiduidade = $vol->total_escalas > 0 
                    ? round(($vol->total_presencas / $vol->total_escalas) * 100) 
                    : 100;
            }
        }

        return $voluntarios;
    }

    /**
     * Busca voluntário por coluna
     */
    public function findByColumn($column, $value)
    {
        return $this->where($column, $value)->first();
    }

    /**
     * Retorna todos os departamentos que o voluntário atua (por áreas cadastradas ou escalas)
     */
    public function getDepartamentosDoVoluntario($id_voluntario)
    {
        $db = db_connect();
        
        // 1. Departamentos cadastrados via áreas
        $builder1 = $db->table('tb_voluntario_departamento_area as vda')
            ->select('d.id_departamento, d.nome as nome_departamento, d.cor_identificacao')
            ->join('tb_departamento as d', 'd.id_departamento = vda.id_departamento', 'inner')
            ->where('vda.id_voluntario', (int)$id_voluntario)
            ->where('d.status', 1);
        $deps1 = $builder1->get()->getResult('object');

        // 2. Departamentos vinculados via escalas históricas
        $builder2 = $db->table('tb_escala_voluntario as ev')
            ->select('d.id_departamento, d.nome as nome_departamento, d.cor_identificacao')
            ->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner')
            ->where('ev.id_voluntario', (int)$id_voluntario)
            ->where('d.status', 1);
        $deps2 = $builder2->get()->getResult('object');

        $departamentos = [];
        foreach (array_merge($deps1, $deps2) as $dep) {
            $idDep = (int)$dep->id_departamento;
            if (!isset($departamentos[$idDep])) {
                $departamentos[$idDep] = $dep;
            }
        }

        // Ordena alfabeticamente
        usort($departamentos, function($a, $b) {
            return strcasecmp($a->nome_departamento, $b->nome_departamento);
        });

        return array_values($departamentos);
    }

    /**
     * Estatísticas e indicadores de assiduidade por período selecionável e opcionalmente por departamento
     */
    public function getEstatisticasVoluntario($id_voluntario, $periodo = 'tudo', $id_departamento = null)
    {
        $id_voluntario = (int)$id_voluntario;
        $voluntario = $this->find($id_voluntario);

        if (!$voluntario) {
            return null;
        }

        $voluntarioAreaModel = new VoluntarioAreaModel();
        $voluntario->areas = $voluntarioAreaModel->getAreasDoVoluntario($id_voluntario);

        // Define filtro de data conforme o período
        $dataInicio = null;
        $dataFim    = date('Y-m-d');
        $labelPeriodo = 'Todo o período';

        switch ($periodo) {
            case 'mes_atual':
                $dataInicio = date('Y-m-01');
                $dataFim    = date('Y-m-t');
                $labelPeriodo = 'Mês Atual (' . date('m/Y') . ')';
                break;
            case 'mes_anterior':
                $dataInicio = date('Y-m-01', strtotime('first day of last month'));
                $dataFim    = date('Y-m-t', strtotime('last day of last month'));
                $labelPeriodo = 'Mês Anterior (' . date('m/Y', strtotime('first day of last month')) . ')';
                break;
            case 'ultimos_3_meses':
                $dataInicio = date('Y-m-01', strtotime('-2 months'));
                $dataFim    = date('Y-m-t');
                $labelPeriodo = 'Últimos 3 Meses';
                break;
            case 'ano_atual':
                $dataInicio = date('Y-01-01');
                $dataFim    = date('Y-12-31');
                $labelPeriodo = 'Ano Atual (' . date('Y') . ')';
                break;
            case 'tudo':
            default:
                $dataInicio = null;
                $labelPeriodo = 'Todo o Histórico';
                break;
        }

        // Sincroniza e marca como Omissão (-25 pts) escalas passadas não confirmadas
        (new EscalaVoluntarioModel())->marcarOmissoesPassadas();

        $departamentoInfo = null;
        if (!empty($id_departamento)) {
            $depModel = new DepartamentoModel();
            $departamentoInfo = $depModel->find((int)$id_departamento);
        }

        $db = db_connect();
        $builder = $db->table('tb_escala_voluntario as ev');
        $builder->select('
            ev.id_escala_voluntario,
            ev.status_presenca,
            ev.status_confirmacao,
            ev.justificativa_recusa,
            ev.status_analise_justificativa,
            ev.justificativa_parecer_gestor,
            ev.justificativa_analisado_por,
            ev.justificativa_analisado_em,
            ev.data_resposta,
            ev.observacao as obs_escala,
            ev.date_insert as data_escalado,
            ev.data_culto,
            COALESCE(cp.nome_culto, c.titulo_culto, "Culto") as titulo_culto,
            COALESCE(cp.horario_inicio, c.horario_inicio) as horario_inicio,
            COALESCE(cp.horario_termino, c.horario_termino) as horario_termino,
            COALESCE(cp.cor_evento, c.cor_evento, "#2563eb") as cor_evento,
            d.nome as nome_departamento,
            d.cor_identificacao as cor_departamento,
            a.nome_area
        ');
        $builder->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = ev.id_culto_padrao', 'left');
        $builder->join('tb_agenda_culto as c', 'c.id_culto = ev.id_culto', 'left');
        $builder->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');
        $builder->where('ev.id_voluntario', $id_voluntario);

        if (!empty($id_departamento)) {
            $builder->where('ev.id_departamento', (int)$id_departamento);
        }

        if ($dataInicio) {
            $builder->where('ev.data_culto >=', $dataInicio);
        }
        if ($dataFim) {
            $builder->where('ev.data_culto <=', $dataFim);
        }

        $builder->orderBy('ev.data_culto', 'DESC');
        $builder->orderBy('cp.horario_inicio', 'DESC');

        $historico = $builder->get()->getResult('object');

        $totalEscalas                = count($historico);
        $totalPresencas              = 0;
        $totalAusencias              = 0;
        $totalConfirmados            = 0;
        $totalCancelamentos          = 0;
        $totalCancelamentosAbonados   = 0;
        $totalCancelamentosPenalizados= 0;
        $totalOmissoes               = 0;
        $totalPendentes              = 0;
        $listaCancelamentos          = [];
        $distribuicaoAreas           = [];

        $totalBonusAgilidade           = 0;
        $somaTempoRespostaSegundos     = 0;
        $totalRespostasComTempo        = 0;

        foreach ($historico as $item) {
            if ($item->status_presenca == 1) {
                $totalPresencas++;
            } elseif ($item->status_presenca == 0) {
                $totalAusencias++;
            }

            // Cálculo da rapidez de resposta (agilidade)
            $tempoRespSeg = null;
            if (!empty($item->data_resposta) && !empty($item->data_escalado)) {
                $tResp = strtotime($item->data_resposta);
                $tEsc  = strtotime($item->data_escalado);
                if ($tResp !== false && $tEsc !== false) {
                    $tempoRespSeg = max(0, $tResp - $tEsc);
                    $somaTempoRespostaSegundos += $tempoRespSeg;
                    $totalRespostasComTempo++;
                }
            }

            $conf = strtoupper((string)($item->status_confirmacao ?: 'PENDENTE'));
            $stAnalise = strtoupper((string)($item->status_analise_justificativa ?: 'PENDENTE'));

            if ($conf === 'CONFIRMADO') {
                $totalConfirmados++;
                // Bônus de agilidade (+5 pts): aceitou em até 24 horas (86400 segundos)
                if ($tempoRespSeg !== null && $tempoRespSeg <= 86400) {
                    $totalBonusAgilidade++;
                }
            } elseif ($conf === 'RECUSADO') {
                $totalCancelamentos++;
                if ($stAnalise === 'ABONADO') {
                    $totalCancelamentosAbonados++;
                } else {
                    $totalCancelamentosPenalizados++;
                }
                $listaCancelamentos[] = $item;
            } elseif ($conf === 'OMISSAO' || $conf === 'NAO_CONFIRMADO') {
                $totalOmissoes++;
            } else {
                $totalPendentes++;
            }

            $areaKey = $item->nome_departamento . ' - ' . $item->nome_area;
            if (!isset($distribuicaoAreas[$areaKey])) {
                $distribuicaoAreas[$areaKey] = [
                    'departamento' => $item->nome_departamento,
                    'area'         => $item->nome_area,
                    'cor'          => $item->cor_departamento ?: '#2563eb',
                    'total'        => 0,
                    'presencas'    => 0,
                    'cancelamentos'=> 0
                ];
            }
            $distribuicaoAreas[$areaKey]['total']++;
            if ($item->status_presenca == 1) {
                $distribuicaoAreas[$areaKey]['presencas']++;
            }
            if ($conf === 'RECUSADO') {
                $distribuicaoAreas[$areaKey]['cancelamentos']++;
            }
        }

        $taxaAssiduidade      = $totalEscalas > 0 ? round(($totalPresencas / $totalEscalas) * 100, 1) : 100;
        $taxaCancelamento     = $totalEscalas > 0 ? round(($totalCancelamentos / $totalEscalas) * 100, 1) : 0;
        $tempoMedioSegundos   = $totalRespostasComTempo > 0 ? round($somaTempoRespostaSegundos / $totalRespostasComTempo) : null;
        $tempoMedioFormatado  = self::formatarTempoResposta($tempoMedioSegundos);
        $pontosBonusAgilidade = $totalBonusAgilidade * 5;
        $pontosTotais = ($totalPresencas * 15) + ($totalConfirmados * 10) + ($pontosBonusAgilidade) - ($totalCancelamentosPenalizados * 10) - ($totalOmissoes * 25);
        if ($pontosTotais < 0) $pontosTotais = 0;

        return (object)[
            'voluntario'                    => $voluntario,
            'periodo'                       => $periodo,
            'labelPeriodo'                  => $labelPeriodo,
            'id_departamento'               => $id_departamento ? (int)$id_departamento : null,
            'departamento'                  => $departamentoInfo,
            'totalEscalas'                  => $totalEscalas,
            'totalPresencas'                => $totalPresencas,
            'totalAusencias'                => $totalAusencias,
            'totalConfirmados'              => $totalConfirmados,
            'totalCancelamentos'            => $totalCancelamentos,
            'totalCancelamentosAbonados'    => $totalCancelamentosAbonados,
            'totalCancelamentosPenalizados' => $totalCancelamentosPenalizados,
            'totalPendentes'                => $totalPendentes,
            'bonus_agilidade_count'         => $totalBonusAgilidade,
            'bonus_agilidade_pontos'        => $pontosBonusAgilidade,
            'tempo_medio_resposta_segundos' => $tempoMedioSegundos,
            'tempo_medio_resposta_formatado'=> $tempoMedioFormatado,
            'pontosTotais'                  => $pontosTotais,
            'taxaAssiduidade'               => $taxaAssiduidade,
            'taxaCancelamento'              => $taxaCancelamento,
            'listaCancelamentos'            => $listaCancelamentos,
            'historicoEscalas'              => $historico,
            'distribuicaoAreas'             => array_values($distribuicaoAreas)
        ];
    }

    /**
     * Retorna relatório consolidado de desempenho dos voluntários com foco em assiduidade e cancelamentos
     */
    public function getRelatorioDesempenho(array $filtros = [])
    {
        // Sincroniza e marca como Omissão (-25 pts) escalas passadas não confirmadas
        (new EscalaVoluntarioModel())->marcarOmissoesPassadas();

        $db = db_connect();

        $periodo = $filtros['periodo'] ?? 'mes_atual';
        $dataInicio = null;
        $dataFim = null;
        $labelPeriodo = 'Mês Atual';

        switch ($periodo) {
            case 'mes_atual':
                $dataInicio = date('Y-m-01');
                $dataFim = date('Y-m-t');
                $labelPeriodo = 'Mês Atual (' . date('m/Y') . ')';
                break;
            case 'mes_anterior':
                $dataInicio = date('Y-m-01', strtotime('-1 month'));
                $dataFim = date('Y-m-t', strtotime('-1 month'));
                $labelPeriodo = 'Mês Anterior (' . date('m/Y', strtotime('-1 month')) . ')';
                break;
            case 'ultimos_3_meses':
                $dataInicio = date('Y-m-01', strtotime('-3 months'));
                $dataFim = date('Y-m-t');
                $labelPeriodo = 'Últimos 3 Meses';
                break;
            case 'ano_atual':
                $dataInicio = date('Y-01-01');
                $dataFim = date('Y-12-31');
                $labelPeriodo = 'Ano Atual (' . date('Y') . ')';
                break;
            case 'custom':
                $dataInicio = !empty($filtros['data_inicio']) ? $filtros['data_inicio'] : date('Y-m-01');
                $dataFim = !empty($filtros['data_fim']) ? $filtros['data_fim'] : date('Y-m-t');
                $labelPeriodo = date('d/m/Y', strtotime($dataInicio)) . ' a ' . date('d/m/Y', strtotime($dataFim));
                break;
            case 'tudo':
            default:
                $labelPeriodo = 'Todo o Histórico';
                break;
        }

        // Busca todos os voluntários com suas áreas
        $builderVol = $db->table('tb_voluntario as v');
        $builderVol->select('v.*');

        if (!empty($filtros['status']) && $filtros['status'] !== 'todos') {
            $builderVol->where('v.status', (int)$filtros['status']);
        }
        if (!empty($filtros['busca'])) {
            $busca = trim((string)$filtros['busca']);
            $builderVol->groupStart()
                ->like('v.nome', $busca)
                ->orLike('v.nickname', $busca)
                ->orLike('v.telefone_whatsapp', $busca)
                ->groupEnd();
        }

        $voluntarios = $builderVol->orderBy('v.nome', 'ASC')->get()->getResult('object');

        // Busca todas as escalas do período
        $builderEsc = $db->table('tb_escala_voluntario as ev');
        $builderEsc->select('
            ev.id_escala_voluntario,
            ev.id_voluntario,
            ev.id_departamento,
            ev.id_area,
            ev.data_culto,
            ev.status_presenca,
            ev.status_confirmacao,
            ev.justificativa_recusa,
            ev.status_analise_justificativa,
            ev.justificativa_parecer_gestor,
            ev.justificativa_analisado_por,
            ev.justificativa_analisado_em,
            u_analisador.nome as nome_analisador,
            ev.data_resposta,
            ev.observacao as obs_escala,
            COALESCE(cp.nome_culto, c.titulo_culto, "Culto") as titulo_culto,
            COALESCE(cp.horario_inicio, c.horario_inicio) as horario_inicio,
            COALESCE(cp.horario_termino, c.horario_termino) as horario_termino,
            d.nome as nome_departamento,
            d.cor_identificacao as cor_departamento,
            a.nome_area
        ');
        $builderEsc->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = ev.id_culto_padrao', 'left');
        $builderEsc->join('tb_agenda_culto as c', 'c.id_culto = ev.id_culto', 'left');
        $builderEsc->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner');
        $builderEsc->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');
        $builderEsc->join('tb_sys_usuario as u_analisador', 'u_analisador.id_usuario = ev.justificativa_analisado_por', 'left');

        if ($dataInicio) {
            $builderEsc->where('ev.data_culto >=', $dataInicio);
        }
        if ($dataFim) {
            $builderEsc->where('ev.data_culto <=', $dataFim);
        }
        if (!empty($filtros['id_departamento'])) {
            $builderEsc->where('ev.id_departamento', (int)$filtros['id_departamento']);
        }
        if (!empty($filtros['id_area'])) {
            $builderEsc->where('ev.id_area', (int)$filtros['id_area']);
        }
        if (!empty($filtros['status_analise']) && $filtros['status_analise'] !== 'todos') {
            $builderEsc->where('ev.status_analise_justificativa', $filtros['status_analise']);
        }

        $builderEsc->orderBy('ev.data_culto', 'DESC');
        $escalas = $builderEsc->get()->getResult('object');

        // Agrupa escalas por voluntário
        $escalasPorVoluntario = [];
        $todosCancelamentos = [];
        $kpiTotalEscalas = count($escalas);
        $kpiTotalConfirmados = 0;
        $kpiTotalCancelamentos = 0;
        $kpiCancelamentosAbonados = 0;
        $kpiCancelamentosDescontados = 0;
        $kpiCancelamentosPendentes = 0;
        $kpiTotalOmissoes = 0;
        $kpiTotalPendentes = 0;
        $kpiTotalPresencas = 0;

        foreach ($escalas as $e) {
            $idV = (int)$e->id_voluntario;
            if (!isset($escalasPorVoluntario[$idV])) {
                $escalasPorVoluntario[$idV] = [];
            }
            $escalasPorVoluntario[$idV][] = $e;

            if ($e->status_confirmacao === 'CONFIRMADO') {
                $kpiTotalConfirmados++;
            } elseif ($e->status_confirmacao === 'RECUSADO') {
                $kpiTotalCancelamentos++;
                $todosCancelamentos[] = $e;
                $stAn = strtoupper((string)($e->status_analise_justificativa ?: 'PENDENTE'));
                if ($stAn === 'ABONADO') {
                    $kpiCancelamentosAbonados++;
                } elseif ($stAn === 'DESCONTADO') {
                    $kpiCancelamentosDescontados++;
                } else {
                    $kpiCancelamentosPendentes++;
                }
            } elseif ($e->status_confirmacao === 'OMISSAO' || $e->status_confirmacao === 'NAO_CONFIRMADO') {
                $kpiTotalOmissoes++;
            } else {
                $kpiTotalPendentes++;
            }

            if ($e->status_presenca == 1) {
                $kpiTotalPresencas++;
            }
        }

        $voluntarioAreaModel = new VoluntarioAreaModel();
        $listaDesempenho = [];

        foreach ($voluntarios as $v) {
            $idV = (int)$v->id_voluntario;
            $vEscalas = $escalasPorVoluntario[$idV] ?? [];
            $vAreas = $voluntarioAreaModel->getAreasDoVoluntario($idV);

            // Filtro por departamento caso especificado
            if (!empty($filtros['id_departamento'])) {
                $temDep = false;
                foreach ($vAreas as $va) {
                    if ($va->id_departamento == $filtros['id_departamento']) {
                        $temDep = true;
                        break;
                    }
                }
                if (!$temDep && empty($vEscalas)) {
                    continue;
                }
            }

            $totEscalas = count($vEscalas);
            $totPresencas = 0;
            $totAusencias = 0;
            $totConfirmados = 0;
            $totCancelados = 0;
            $totCancAbonados = 0;
            $totCancPenalizados = 0;
            $totOmissoes = 0;
            $totPendentes = 0;
            $justificativas = [];

            foreach ($vEscalas as $ve) {
                if ($ve->status_presenca == 1) $totPresencas++;
                if ($ve->status_presenca == 0) $totAusencias++;

                if ($ve->status_confirmacao === 'CONFIRMADO') {
                    $totConfirmados++;
                } elseif ($ve->status_confirmacao === 'RECUSADO') {
                    $totCancelados++;
                    $stAnaliseVe = strtoupper((string)($ve->status_analise_justificativa ?: 'PENDENTE'));
                    if ($stAnaliseVe === 'ABONADO') {
                        $totCancAbonados++;
                    } else {
                        $totCancPenalizados++;
                    }
                    $justificativas[] = [
                        'id_escala_voluntario'         => $ve->id_escala_voluntario,
                        'data_culto'                   => $ve->data_culto,
                        'titulo_culto'                 => $ve->titulo_culto,
                        'horario_inicio'               => $ve->horario_inicio,
                        'horario_termino'              => $ve->horario_termino,
                        'departamento'                 => $ve->nome_departamento,
                        'cor_departamento'             => $ve->cor_departamento,
                        'area'                         => $ve->nome_area,
                        'justificativa'                => $ve->justificativa_recusa,
                        'status_analise_justificativa' => $ve->status_analise_justificativa ?: 'PENDENTE',
                        'justificativa_parecer_gestor' => $ve->justificativa_parecer_gestor,
                        'justificativa_analisado_por'  => $ve->justificativa_analisado_por,
                        'justificativa_analisado_em'   => $ve->justificativa_analisado_em,
                        'nome_analisador'              => $ve->nome_analisador ?? null,
                        'data_resposta'                => $ve->data_resposta
                    ];
                } elseif ($ve->status_confirmacao === 'OMISSAO' || $ve->status_confirmacao === 'NAO_CONFIRMADO') {
                    $totOmissoes++;
                } else {
                    $totPendentes++;
                }
            }

            // Filtro de apenas cancelamentos
            if (!empty($filtros['apenas_cancelamentos']) && $totCancelados === 0) {
                continue;
            }

            $taxaAssiduidade  = $totEscalas > 0 ? round(($totPresencas / $totEscalas) * 100, 1) : 100;
            $taxaCancelamento = $totEscalas > 0 ? round(($totCancelados / $totEscalas) * 100, 1) : 0;

            $listaDesempenho[] = (object)[
                'voluntario'                  => $v,
                'areas'                       => $vAreas,
                'totalEscalas'                => $totEscalas,
                'totalPresencas'              => $totPresencas,
                'totalAusencias'              => $totAusencias,
                'totalConfirmados'            => $totConfirmados,
                'totalCancelamentos'          => $totCancelados,
                'totalCancelamentosAbonados'   => $totCancAbonados,
                'totalCancelamentosPenalizados'=> $totCancPenalizados,
                'totalOmissoes'               => $totOmissoes,
                'totalPendentes'              => $totPendentes,
                'taxaAssiduidade'             => $taxaAssiduidade,
                'taxaCancelamento'            => $taxaCancelamento,
                'justificativas'              => $justificativas,
                'escalas'                     => $vEscalas
            ];
        }

        // Ordenação
        $ordem = $filtros['ordem'] ?? 'cancelamentos_desc';
        usort($listaDesempenho, function($a, $b) use ($ordem) {
            switch ($ordem) {
                case 'cancelamentos_desc':
                    if ($a->totalCancelamentos !== $b->totalCancelamentos) {
                        return $b->totalCancelamentos <=> $a->totalCancelamentos;
                    }
                    return $a->voluntario->nome <=> $b->voluntario->nome;
                case 'assiduidade_asc':
                    return $a->taxaAssiduidade <=> $b->taxaAssiduidade;
                case 'assiduidade_desc':
                    return $b->taxaAssiduidade <=> $a->taxaAssiduidade;
                case 'escalas_desc':
                    return $b->totalEscalas <=> $a->totalEscalas;
                case 'nome_asc':
                default:
                    return strcasecmp($a->voluntario->nome, $b->voluntario->nome);
            }
        });

        // Conecta nomes de voluntários aos cancelamentos gerais
        $voluntarioMap = [];
        foreach ($voluntarios as $v) {
            $voluntarioMap[$v->id_voluntario] = $v;
        }
        foreach ($todosCancelamentos as &$tc) {
            $tc->voluntario = $voluntarioMap[$tc->id_voluntario] ?? null;
        }

        return (object)[
            'periodo'               => $periodo,
            'labelPeriodo'          => $labelPeriodo,
            'dataInicio'            => $dataInicio,
            'dataFim'               => $dataFim,
            'filtros'               => $filtros,
            'kpis'                  => (object)[
                'totalVoluntarios'              => count($listaDesempenho),
                'totalEscalas'                  => $kpiTotalEscalas,
                'totalPresencas'                => $kpiTotalPresencas,
                'totalConfirmados'              => $kpiTotalConfirmados,
                'totalCancelamentos'            => $kpiTotalCancelamentos,
                'totalCancelamentosAbonados'    => $kpiCancelamentosAbonados,
                'totalCancelamentosDescontados' => $kpiCancelamentosDescontados,
                'totalCancelamentosPendentes'   => $kpiCancelamentosPendentes,
                'totalPendentes'                => $kpiTotalPendentes,
                'taxaCancelamentoGeral'         => $kpiTotalEscalas > 0 ? round(($kpiTotalCancelamentos / $kpiTotalEscalas) * 100, 1) : 0,
                'taxaAssiduidadeGeral'          => $kpiTotalEscalas > 0 ? round(($kpiTotalPresencas / $kpiTotalEscalas) * 100, 1) : 100
            ],
            'listaDesempenho'       => $listaDesempenho,
            'todosCancelamentos'    => $todosCancelamentos
        ];
    }

    /**
     * Retorna histórico de justificativas de cancelamento de um voluntário específico
     */
    public function getJustificativasCancelamento($id_voluntario, $periodo = null)
    {
        $db = db_connect();
        $builder = $db->table('tb_escala_voluntario as ev');
        $builder->select('
            ev.id_escala_voluntario,
            ev.data_culto,
            ev.status_confirmacao,
            ev.justificativa_recusa,
            ev.status_analise_justificativa,
            ev.justificativa_parecer_gestor,
            ev.justificativa_analisado_por,
            ev.justificativa_analisado_em,
            u_analisador.nome as nome_analisador,
            ev.data_resposta,
            COALESCE(cp.nome_culto, c.titulo_culto, "Culto") as titulo_culto,
            COALESCE(cp.horario_inicio, c.horario_inicio) as horario_inicio,
            COALESCE(cp.horario_termino, c.horario_termino) as horario_termino,
            d.nome as nome_departamento,
            d.cor_identificacao as cor_departamento,
            a.nome_area
        ');
        $builder->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = ev.id_culto_padrao', 'left');
        $builder->join('tb_agenda_culto as c', 'c.id_culto = ev.id_culto', 'left');
        $builder->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');
        $builder->join('tb_sys_usuario as u_analisador', 'u_analisador.id_usuario = ev.justificativa_analisado_por', 'left');
        $builder->where('ev.id_voluntario', (int)$id_voluntario);
        $builder->where('ev.status_confirmacao', 'RECUSADO');
        $builder->orderBy('ev.data_culto', 'DESC');

        return $builder->get()->getResult('object');
    }

    /**
     * Conta a quantidade de escalas de um voluntário em um determinado mês/ano
     */
    public function countEscalasMes($id_voluntario, $ano, $mes)
    {
        $dataInicio = sprintf('%04d-%02d-01', (int)$ano, (int)$mes);
        $totalDias   = (int)date('t', strtotime($dataInicio));
        $dataFim    = sprintf('%04d-%02d-%02d', (int)$ano, (int)$mes, $totalDias);

        $db = db_connect();
        return $db->table('tb_escala_voluntario')
            ->where('id_voluntario', (int)$id_voluntario)
            ->where('data_culto >=', $dataInicio)
            ->where('data_culto <=', $dataFim)
            ->countAllResults();
    }

    /**
     * Busca voluntário por número de telefone (compara dígitos limpos)
     */
    public function buscarPorTelefone($telefone)
    {
        $digitos = preg_replace('/\D/', '', (string)$telefone);
        if (empty($digitos)) {
            return null;
        }

        // Se veio com DDI 55 e tem 12 ou 13 dígitos, tenta sem 55 também
        $digitosSem55 = (strpos($digitos, '55') === 0 && strlen($digitos) >= 12) ? substr($digitos, 2) : $digitos;

        $db = db_connect();
        $builder = $db->table('tb_voluntario');
        $builder->where("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telefone_whatsapp, '(', ''), ')', ''), '-', ''), ' ', ''), '+', '') LIKE '%{$digitosSem55}%'");
        $builder->orderBy('status', 'DESC');
        $builder->orderBy('id_voluntario', 'DESC');
        $query = $builder->get();
        $voluntario = $query->getFirstRow();

        if ($voluntario) {
            $voluntarioAreaModel = new VoluntarioAreaModel();
            $voluntario->areas = $voluntarioAreaModel->getAreasDoVoluntario($voluntario->id_voluntario);
        }

        return $voluntario;
    }

    /**
     * Verifica com alta performance se um número de telefone já está cadastrado na base de voluntários
     * Utiliza índice direto em tb_voluntario(telefone_whatsapp) para tempo de resposta sub-milissegundo
     *
     * @param string $telefone
     * @return bool
     */
    public function existeTelefone($telefone)
    {
        $digitos = preg_replace('/\D/', '', (string)$telefone);
        if (empty($digitos) || strlen($digitos) < 8) {
            return false;
        }

        $digitosSem55 = (strpos($digitos, '55') === 0 && strlen($digitos) >= 12) ? substr($digitos, 2) : $digitos;

        // Formatações comuns para match indexado direto (O(1))
        $possibilidades = [
            trim($telefone),
            $digitos,
            $digitosSem55,
            '55' . $digitosSem55,
            '+55' . $digitosSem55
        ];

        if (strlen($digitosSem55) === 11) {
            $possibilidades[] = sprintf('(%s) %s-%s', substr($digitosSem55, 0, 2), substr($digitosSem55, 2, 5), substr($digitosSem55, 7));
            $possibilidades[] = sprintf('(%s)%s-%s', substr($digitosSem55, 0, 2), substr($digitosSem55, 2, 5), substr($digitosSem55, 7));
            $possibilidades[] = sprintf('%s %s-%s', substr($digitosSem55, 0, 2), substr($digitosSem55, 2, 5), substr($digitosSem55, 7));
        } elseif (strlen($digitosSem55) === 10) {
            $possibilidades[] = sprintf('(%s) %s-%s', substr($digitosSem55, 0, 2), substr($digitosSem55, 2, 4), substr($digitosSem55, 6));
            $possibilidades[] = sprintf('(%s)%s-%s', substr($digitosSem55, 0, 2), substr($digitosSem55, 2, 4), substr($digitosSem55, 6));
        }

        $possibilidades = array_values(array_unique(array_filter($possibilidades)));

        $db = db_connect();
        $builder = $db->table('tb_voluntario');
        $builder->select('id_voluntario');
        $builder->groupStart()
            ->whereIn('telefone_whatsapp', $possibilidades)
            ->orWhere("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telefone_whatsapp, '(', ''), ')', ''), '-', ''), ' ', ''), '+', '') LIKE '%{$digitosSem55}%'")
        ->groupEnd();
        $builder->limit(1);

        $row = $builder->get()->getFirstRow();

        return !empty($row);
    }

    /**
     * Reseta a senha do voluntário para os dígitos limpos do WhatsApp (padrão do sistema)
     * e obriga a troca de senha na próxima autenticação com validação OTP via WhatsApp
     */
    public function resetarSenha($id_voluntario)
    {
        $voluntario = $this->find((int)$id_voluntario);
        if (!$voluntario) {
            return false;
        }

        $digitosTelefone = preg_replace('/\D/', '', (string)$voluntario->telefone_whatsapp);
        if (empty($digitosTelefone)) {
            $digitosTelefone = '123456';
        }

        $hashSenha = password_hash($digitosTelefone, PASSWORD_BCRYPT);

        return $this->update((int)$id_voluntario, [
            'senha'             => $hashSenha,
            'primeiro_acesso'   => 1,
            'force_pwd_change'  => 1,
            'otp_code'          => null,
            'otp_expires_at'    => null,
            'data_ultima_senha' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Gera código OTP de 6 dígitos para o voluntário com validade de 10 minutos
     */
    public function gerarOtpTrocaSenha($id_voluntario)
    {
        $voluntario = $this->find((int)$id_voluntario);
        if (!$voluntario) {
            return null;
        }

        $otpCode = sprintf('%06d', mt_rand(100000, 999999));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $this->update((int)$id_voluntario, [
            'otp_code'         => $otpCode,
            'otp_expires_at'   => $expiresAt,
            'force_pwd_change' => 1
        ]);

        return $otpCode;
    }

    /**
     * Valida se o código OTP informado está correto e dentro do prazo de validade
     */
    public function validarOtpTrocaSenha($id_voluntario, $otpInformado)
    {
        $voluntario = $this->find((int)$id_voluntario);
        if (!$voluntario || empty($voluntario->otp_code) || empty($voluntario->otp_expires_at)) {
            return false;
        }

        $otpLimpo = trim((string)$otpInformado);
        if ($voluntario->otp_code !== $otpLimpo) {
            return false;
        }

        if (strtotime($voluntario->otp_expires_at) < time()) {
            return false;
        }

        return true;
    }

    /**
     * Conclui a troca de senha obrigatória via OTP e limpa as flags de bloqueio
     */
    public function concluirTrocaSenhaComOtp($id_voluntario, $novaSenha)
    {
        $hashSenha = password_hash($novaSenha, PASSWORD_BCRYPT);

        return $this->update((int)$id_voluntario, [
            'senha'             => $hashSenha,
            'primeiro_acesso'   => 0,
            'force_pwd_change'  => 0,
            'otp_code'          => null,
            'otp_expires_at'    => null,
            'data_ultima_senha' => date('Y-m-d H:i:s'),
            'data_ultimo_login' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Altera a senha do voluntário
     */
    public function alterarSenha($id_voluntario, $novaSenha)
    {
        $hashSenha = password_hash($novaSenha, PASSWORD_BCRYPT);
        return $this->update((int)$id_voluntario, [
            'senha'             => $hashSenha,
            'primeiro_acesso'   => 0,
            'force_pwd_change'  => 0,
            'otp_code'          => null,
            'otp_expires_at'    => null,
            'data_ultima_senha' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Atualiza dados permitidos do próprio perfil do voluntário
     */
    public function atualizarPerfilVoluntario($id_voluntario, array $dados)
    {
        $camposPermitidos = [
            'nickname'          => isset($dados['nickname']) ? trim((string)$dados['nickname']) : null,
            'data_nascimento'   => !empty($dados['data_nascimento']) ? $dados['data_nascimento'] : null,
            'telefone_whatsapp' => !empty($dados['telefone_whatsapp']) ? trim((string)$dados['telefone_whatsapp']) : null,
            'redes_sociais'     => isset($dados['redes_sociais']) ? $dados['redes_sociais'] : null,
        ];

        if (isset($dados['foto_url'])) {
            $camposPermitidos['foto_url'] = $dados['foto_url'];
        }

        // Remove campos nulos que não foram enviados
        $camposPermitidos = array_filter($camposPermitidos, function($v) {
            return $v !== null;
        });

        return $this->update((int)$id_voluntario, $camposPermitidos);
    }

    /**
     * Retorna ranking e métricas comparativas para o Portal do Voluntário (Gamificação / Leaderboard por Departamento)
     */
    public function getRankingVoluntariosPortal($idVoluntarioLogado, $periodo = 'mes_atual', $id_departamento = null)
    {
        // Sincroniza e marca como Omissão (-25 pts) escalas passadas não confirmadas
        (new EscalaVoluntarioModel())->marcarOmissoesPassadas();

        $db = db_connect();

        $dataInicio = null;
        $dataFim    = null;
        $labelPeriodo = 'Mês Atual';

        switch ($periodo) {
            case 'mes_atual':
                $dataInicio = date('Y-m-01');
                $dataFim    = date('Y-m-t');
                $labelPeriodo = 'Mês Atual (' . date('m/Y') . ')';
                break;
            case 'mes_anterior':
                $dataInicio = date('Y-m-01', strtotime('first day of last month'));
                $dataFim    = date('Y-m-t', strtotime('last day of last month'));
                $labelPeriodo = 'Mês Anterior (' . date('m/Y', strtotime('first day of last month')) . ')';
                break;
            case 'ano_atual':
                $dataInicio = date('Y-01-01');
                $dataFim    = date('Y-12-31');
                $labelPeriodo = 'Ano Atual (' . date('Y') . ')';
                break;
            case 'tudo':
            default:
                $dataInicio = null;
                $dataFim    = null;
                $labelPeriodo = 'Histórico Geral';
                break;
        }

        $departamentoInfo = null;
        if (!empty($id_departamento)) {
            $depModel = new DepartamentoModel();
            $departamentoInfo = $depModel->find((int)$id_departamento);
        }

        // Busca voluntários ativos
        if (!empty($id_departamento)) {
            $subqueryAreas = $db->table('tb_voluntario_departamento_area')
                ->select('id_voluntario')
                ->where('id_departamento', (int)$id_departamento);

            $subqueryEscalas = $db->table('tb_escala_voluntario')
                ->select('id_voluntario')
                ->where('id_departamento', (int)$id_departamento);

            $voluntarios = $db->table('tb_voluntario as v')
                ->where('v.status', 1)
                ->groupStart()
                    ->whereIn('v.id_voluntario', $subqueryAreas)
                    ->orWhereIn('v.id_voluntario', $subqueryEscalas)
                    ->orWhere('v.id_voluntario', (int)$idVoluntarioLogado)
                ->groupEnd()
                ->orderBy('v.nome', 'ASC')
                ->get()
                ->getResult('object');
        } else {
            $voluntarios = $db->table('tb_voluntario')
                ->where('status', 1)
                ->orderBy('nome', 'ASC')
                ->get()
                ->getResult('object');
        }

        // Busca escalas no período (e no departamento, se filtrado)
        $builderEsc = $db->table('tb_escala_voluntario');
        $builderEsc->select('id_voluntario, status_confirmacao, status_presenca, status_analise_justificativa, data_resposta, date_insert');
        if ($dataInicio) $builderEsc->where('data_culto >=', $dataInicio);
        if ($dataFim)    $builderEsc->where('data_culto <=', $dataFim);
        if (!empty($id_departamento)) {
            $builderEsc->where('id_departamento', (int)$id_departamento);
        }

        $escalas = $builderEsc->get()->getResult('object');

        $escalasPorVoluntario = [];
        foreach ($escalas as $e) {
            $idV = (int)$e->id_voluntario;
            if (!isset($escalasPorVoluntario[$idV])) {
                $escalasPorVoluntario[$idV] = [
                    'total'                  => 0,
                    'confirmados'            => 0,
                    'cancelados'             => 0,
                    'cancelados_abonados'    => 0,
                    'cancelados_penalizados' => 0,
                    'nao_confirmados'        => 0,
                    'presencas'              => 0,
                    'bonus_agilidade_count'  => 0,
                    'soma_tempo_segundos'    => 0,
                    'respostas_com_tempo'    => 0
                ];
            }
            $escalasPorVoluntario[$idV]['total']++;
            $statusConf = strtoupper((string)($e->status_confirmacao ?: 'PENDENTE'));
            $stAnalise  = strtoupper((string)($e->status_analise_justificativa ?: 'PENDENTE'));

            $tempoRespSeg = null;
            if (!empty($e->data_resposta) && !empty($e->date_insert)) {
                $tResp = strtotime($e->data_resposta);
                $tIns  = strtotime($e->date_insert);
                if ($tResp !== false && $tIns !== false) {
                    $tempoRespSeg = max(0, $tResp - $tIns);
                    $escalasPorVoluntario[$idV]['soma_tempo_segundos'] += $tempoRespSeg;
                    $escalasPorVoluntario[$idV]['respostas_com_tempo']++;
                }
            }

            if ($statusConf === 'CONFIRMADO') {
                $escalasPorVoluntario[$idV]['confirmados']++;
                // Bônus de agilidade (+5 pts): aceitou em até 24 horas (86400 segundos)
                if ($tempoRespSeg !== null && $tempoRespSeg <= 86400) {
                    $escalasPorVoluntario[$idV]['bonus_agilidade_count']++;
                }
            } elseif ($statusConf === 'RECUSADO') {
                $escalasPorVoluntario[$idV]['cancelados']++;
                if ($stAnalise === 'ABONADO') {
                    $escalasPorVoluntario[$idV]['cancelados_abonados']++;
                } else {
                    $escalasPorVoluntario[$idV]['cancelados_penalizados']++;
                }
            } elseif ($statusConf === 'OMISSAO' || $statusConf === 'NAO_CONFIRMADO') {
                $escalasPorVoluntario[$idV]['nao_confirmados']++;
            }
            if ($e->status_presenca == 1) {
                $escalasPorVoluntario[$idV]['presencas']++;
            }
        }

        $leaderboard = [];
        $meuRank = null;

        foreach ($voluntarios as $v) {
            $idV = (int)$v->id_voluntario;
            $vStats = $escalasPorVoluntario[$idV] ?? null;
            $isMe = ($idV === (int)$idVoluntarioLogado);

            $nomeExibicao = !empty($v->nickname) ? $v->nickname : (explode(' ', trim($v->nome))[0] . ' ' . (explode(' ', trim($v->nome))[count(explode(' ', trim($v->nome))) - 1] ?? ''));
            $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($v->nome) . '&background=2563eb&color=fff&size=80&bold=true';

            // Se o voluntário NÃO possui nenhuma escala no período (ou departamento), não entra no ranking (não classificado)
            if (!$vStats || $vStats['total'] === 0) {
                if ($isMe) {
                    $meuRank = (object)[
                        'id_voluntario'                 => $idV,
                        'nome_completo'                 => $v->nome,
                        'nome_exibicao'                 => $nomeExibicao,
                        'nickname'                      => $v->nickname,
                        'foto_url'                      => !empty($v->foto_url) ? $v->foto_url : $defaultAvatar,
                        'nivel_conhecimento'            => $v->nivel_conhecimento ?? 'JUNIOR',
                        'total_escalas'                 => 0,
                        'cultos_aceitos'                => 0,
                        'cultos_cancelados'             => 0,
                        'cultos_cancelados_abonados'    => 0,
                        'cultos_cancelados_penalizados' => 0,
                        'nao_confirmados'               => 0,
                        'presencas'                     => 0,
                        'bonus_agilidade_count'         => 0,
                        'bonus_agilidade_pontos'        => 0,
                        'tempo_medio_resposta_segundos' => null,
                        'tempo_medio_resposta_formatado'=> 'Pendente',
                        'taxa_assiduidade'              => 0,
                        'pontos'                        => 0,
                        'posicao'                       => null,
                        'classificado'                  => false,
                        'is_current_user'               => true
                    ];
                }
                continue;
            }

            $totEsc            = $vStats['total'];
            $totConf           = $vStats['confirmados'];
            $totCanc           = $vStats['cancelados'];
            $totCancAbonados   = $vStats['cancelados_abonados'] ?? 0;
            $totCancPenalizados= $vStats['cancelados_penalizados'] ?? 0;
            $totOmi            = $vStats['nao_confirmados'] ?? 0;
            $totPres           = $vStats['presencas'];
            $totBonusAgil      = $vStats['bonus_agilidade_count'] ?? 0;
            $ptsBonusAgil      = $totBonusAgil * 5;
            $respostasTempo    = $vStats['respostas_com_tempo'] ?? 0;
            $somaTempo         = $vStats['soma_tempo_segundos'] ?? 0;
            $tempoMedioSeg     = $respostasTempo > 0 ? round($somaTempo / $respostasTempo) : PHP_INT_MAX;
            $tempoMedioFormatado = $respostasTempo > 0 ? self::formatarTempoResposta($tempoMedioSeg) : 'Pendente';

            $taxaAssiduidade = $totEsc > 0 ? round(($totPres / $totEsc) * 100, 1) : 0;

            // Sistema de Pontuação (Score de Fidelidade):
            // +15 pts por presença cumprida
            // +10 pts por confirmação
            // +5 pts por agilidade (aceitar em até 24h)
            // -10 pts por cancelamento/recusa com desconto ativo (justificativas ABONADAS pelo gestor são isentas de desconto!)
            // -25 pts por omissão/falta não confirmada
            $pontos = ($totPres * 15) + ($totConf * 10) + ($ptsBonusAgil) - ($totCancPenalizados * 10) - ($totOmi * 25);
            if ($pontos < 0) $pontos = 0;

            $leaderboard[] = (object)[
                'id_voluntario'                 => $idV,
                'nome_completo'                 => $v->nome,
                'nome_exibicao'                 => $nomeExibicao,
                'nickname'                      => $v->nickname,
                'foto_url'                      => !empty($v->foto_url) ? $v->foto_url : $defaultAvatar,
                'nivel_conhecimento'            => $v->nivel_conhecimento ?? 'JUNIOR',
                'total_escalas'                 => $totEsc,
                'cultos_aceitos'                => $totConf,
                'cultos_cancelados'             => $totCanc,
                'cultos_cancelados_abonados'    => $totCancAbonados,
                'cultos_cancelados_penalizados' => $totCancPenalizados,
                'nao_confirmados'               => $totOmi,
                'presencas'                     => $totPres,
                'bonus_agilidade_count'         => $totBonusAgil,
                'bonus_agilidade_pontos'        => $ptsBonusAgil,
                'tempo_medio_resposta_segundos' => $tempoMedioSeg,
                'tempo_medio_resposta_formatado'=> $tempoMedioFormatado,
                'taxa_assiduidade'              => $taxaAssiduidade,
                'pontos'                        => $pontos,
                'classificado'                  => true,
                'is_current_user'               => $isMe
            ];
        }

        // Ordena por Pontos DESC, depois pelo menor tempo de resposta ASC (divisor de águas), depois por Cultos Aceitos DESC, depois por Assiduidade DESC
        usort($leaderboard, function($a, $b) {
            if ($a->pontos !== $b->pontos) {
                return $b->pontos <=> $a->pontos;
            }
            if ($a->tempo_medio_resposta_segundos !== $b->tempo_medio_resposta_segundos) {
                return $a->tempo_medio_resposta_segundos <=> $b->tempo_medio_resposta_segundos;
            }
            if ($a->cultos_aceitos !== $b->cultos_aceitos) {
                return $b->cultos_aceitos <=> $a->cultos_aceitos;
            }
            return $b->taxa_assiduidade <=> $a->taxa_assiduidade;
        });

        // Atribui posições
        $totalClassificados = count($leaderboard);

        foreach ($leaderboard as $idx => &$item) {
            $item->posicao = $idx + 1;
            if ($item->is_current_user) {
                $meuRank = $item;
            }
        }

        $percentil = null;
        if ($meuRank && !empty($meuRank->posicao) && $totalClassificados > 0) {
            $percentil = round(($meuRank->posicao / $totalClassificados) * 100);
        }

        return (object)[
            'periodo'            => $periodo,
            'labelPeriodo'       => $labelPeriodo,
            'id_departamento'    => $id_departamento ? (int)$id_departamento : null,
            'departamento'       => $departamentoInfo,
            'leaderboard'        => $leaderboard,
            'meuRank'            => $meuRank,
            'totalClassificados' => $totalClassificados,
            'totalAtivos'        => $totalClassificados,
            'percentil'          => $percentil
        ];
    }

    /**
     * Retorna o Ranking Completo de Voluntários para a Visão de Gestores
     * Permite visualizar a classificação no departamento, na sub-área,
     * e a visão geral consolidada entre todos os departamentos onde o voluntário atua.
     */
    public function getRankingGestor(array $filtros = []): object
    {
        // Sincroniza e marca como Omissão (-25 pts) escalas passadas não confirmadas
        (new EscalaVoluntarioModel())->marcarOmissoesPassadas();

        $db = db_connect();

        $periodo      = $filtros['periodo'] ?? 'mes_atual';
        $dataInicio   = null;
        $dataFim      = null;
        $labelPeriodo = 'Mês Atual';

        switch ($periodo) {
            case 'mes_atual':
                $dataInicio   = date('Y-m-01');
                $dataFim      = date('Y-m-t');
                $labelPeriodo = 'Mês Atual (' . date('m/Y') . ')';
                break;
            case 'mes_anterior':
                $dataInicio   = date('Y-m-01', strtotime('first day of last month'));
                $dataFim      = date('Y-m-t', strtotime('last day of last month'));
                $labelPeriodo = 'Mês Anterior (' . date('m/Y', strtotime('first day of last month')) . ')';
                break;
            case 'ultimos_3_meses':
                $dataInicio   = date('Y-m-01', strtotime('-2 months'));
                $dataFim      = date('Y-m-t');
                $labelPeriodo = 'Últimos 3 Meses';
                break;
            case 'ano_atual':
                $dataInicio   = date('Y-01-01');
                $dataFim      = date('Y-12-31');
                $labelPeriodo = 'Ano Atual (' . date('Y') . ')';
                break;
            case 'custom':
                $dataInicio   = !empty($filtros['data_inicio']) ? $filtros['data_inicio'] : date('Y-m-01');
                $dataFim      = !empty($filtros['data_fim']) ? $filtros['data_fim'] : date('Y-m-t');
                $labelPeriodo = date('d/m/Y', strtotime($dataInicio)) . ' a ' . date('d/m/Y', strtotime($dataFim));
                break;
            case 'tudo':
            default:
                $dataInicio   = null;
                $dataFim      = null;
                $labelPeriodo = 'Todo o Histórico';
                break;
        }

        $id_departamento        = !empty($filtros['id_departamento']) ? (int)$filtros['id_departamento'] : null;
        $id_area                = !empty($filtros['id_area']) ? (int)$filtros['id_area'] : null;
        $departamentosPermitidos = $filtros['departamentos_permitidos'] ?? [];
        $apenasMulti            = !empty($filtros['apenas_multi']);
        $apenasEscalados        = isset($filtros['apenas_escalados']) ? (bool)$filtros['apenas_escalados'] : true;
        $busca                  = trim((string)($filtros['busca'] ?? ''));

        $depModel         = new DepartamentoModel();
        $departamentoInfo = !empty($id_departamento) ? $depModel->find($id_departamento) : null;

        $areaModel = new DepartamentoAreaModel();
        $areaInfo  = !empty($id_area) ? $areaModel->find($id_area) : null;

        // 1. Busca voluntários ativos
        $builderVol = $db->table('tb_voluntario as v');
        $builderVol->select('v.*');
        if (!isset($filtros['status']) || $filtros['status'] !== 'todos') {
            $builderVol->where('v.status', 1);
        }
        if (!empty($busca)) {
            $builderVol->groupStart()
                ->like('v.nome', $busca)
                ->orLike('v.nickname', $busca)
                ->orLike('v.telefone_whatsapp', $busca)
                ->groupEnd();
        }
        $voluntarios = $builderVol->orderBy('v.nome', 'ASC')->get()->getResult('object');

        // 2. Mapeia áreas e departamentos vinculados
        $builderAreas = $db->table('tb_voluntario_departamento_area as vda')
            ->select('vda.id_voluntario, vda.id_departamento, vda.id_area, d.nome as nome_departamento, d.cor_identificacao, a.nome_area')
            ->join('tb_departamento as d', 'd.id_departamento = vda.id_departamento', 'inner')
            ->join('tb_departamento_area as a', 'a.id_area = vda.id_area', 'inner')
            ->where('d.status', 1);
        $areasRows = $builderAreas->get()->getResult('object');

        $departamentosPorVoluntario = [];
        $subareasPorVoluntario      = [];
        foreach ($areasRows as $ar) {
            $idV = (int)$ar->id_voluntario;
            $idD = (int)$ar->id_departamento;
            $idA = (int)$ar->id_area;

            if (!isset($departamentosPorVoluntario[$idV])) {
                $departamentosPorVoluntario[$idV] = [];
            }
            if (!isset($departamentosPorVoluntario[$idV][$idD])) {
                $departamentosPorVoluntario[$idV][$idD] = [
                    'id_departamento'   => $idD,
                    'nome'              => $ar->nome_departamento,
                    'cor_identificacao' => $ar->cor_identificacao ?: '#2563eb',
                    'areas'             => []
                ];
            }
            $departamentosPorVoluntario[$idV][$idD]['areas'][$idA] = $ar->nome_area;

            if (!isset($subareasPorVoluntario[$idV])) {
                $subareasPorVoluntario[$idV] = [];
            }
            $subareasPorVoluntario[$idV][$idA] = [
                'id_area'           => $idA,
                'nome_area'         => $ar->nome_area,
                'id_departamento'   => $idD,
                'nome_departamento' => $ar->nome_departamento,
                'cor_identificacao' => $ar->cor_identificacao ?: '#2563eb'
            ];
        }

        // 3. Busca todas as escalas do período
        $builderEsc = $db->table('tb_escala_voluntario as ev');
        $builderEsc->select('
            ev.id_escala_voluntario,
            ev.id_voluntario,
            ev.id_departamento,
            ev.id_area,
            ev.data_culto,
            ev.date_insert,
            ev.data_resposta,
            ev.status_confirmacao,
            ev.status_presenca,
            ev.status_analise_justificativa,
            d.nome as nome_departamento,
            d.cor_identificacao,
            a.nome_area
        ');
        $builderEsc->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner');
        $builderEsc->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');

        if ($dataInicio) {
            $builderEsc->where('ev.data_culto >=', $dataInicio);
        }
        if ($dataFim) {
            $builderEsc->where('ev.data_culto <=', $dataFim);
        }
        if (!empty($departamentosPermitidos)) {
            $builderEsc->whereIn('ev.id_departamento', $departamentosPermitidos);
        }

        $escalas = $builderEsc->get()->getResult('object');

        // 4. Estrutura de métricas por voluntário: [id_voluntario][id_departamento][id_area]
        $statsPorVoluntario = [];
        $kpiCancelamentosPendentesTot = 0;
        $kpiCancelamentosAbonadosTot  = 0;
        $kpiCancelamentosDescontadosTot = 0;

        foreach ($escalas as $e) {
            $idV = (int)$e->id_voluntario;
            $idD = (int)$e->id_departamento;
            $idA = (int)$e->id_area;

            // Registra departamento e sub-área caso não estivesse cadastrado previamente
            if (!isset($departamentosPorVoluntario[$idV][$idD])) {
                $departamentosPorVoluntario[$idV][$idD] = [
                    'id_departamento'   => $idD,
                    'nome'              => $e->nome_departamento,
                    'cor_identificacao' => $e->cor_identificacao ?: '#2563eb',
                    'areas'             => []
                ];
            }
            $departamentosPorVoluntario[$idV][$idD]['areas'][$idA] = $e->nome_area;

            if (!isset($subareasPorVoluntario[$idV][$idA])) {
                $subareasPorVoluntario[$idV][$idA] = [
                    'id_area'           => $idA,
                    'nome_area'         => $e->nome_area,
                    'id_departamento'   => $idD,
                    'nome_departamento' => $e->nome_departamento,
                    'cor_identificacao' => $e->cor_identificacao ?: '#2563eb'
                ];
            }

            if (!isset($statsPorVoluntario[$idV])) {
                $statsPorVoluntario[$idV] = [
                    'geral' => [
                        'total'                  => 0,
                        'confirmados'            => 0,
                        'cancelados'             => 0,
                        'cancelados_abonados'    => 0,
                        'cancelados_penalizados' => 0,
                        'nao_confirmados'        => 0,
                        'presencas'              => 0,
                        'bonus_agilidade_count'  => 0,
                        'soma_tempo_segundos'    => 0,
                        'respostas_com_tempo'    => 0
                    ],
                    'departamentos' => [],
                    'subareas'      => []
                ];
            }

            if (!isset($statsPorVoluntario[$idV]['departamentos'][$idD])) {
                $statsPorVoluntario[$idV]['departamentos'][$idD] = [
                    'id_departamento'        => $idD,
                    'nome'                   => $e->nome_departamento,
                    'cor_identificacao'      => $e->cor_identificacao ?: '#2563eb',
                    'total'                  => 0,
                    'confirmados'            => 0,
                    'cancelados'             => 0,
                    'cancelados_abonados'    => 0,
                    'cancelados_penalizados' => 0,
                    'nao_confirmados'        => 0,
                    'presencas'              => 0,
                    'bonus_agilidade_count'  => 0,
                    'soma_tempo_segundos'    => 0,
                    'respostas_com_tempo'    => 0
                ];
            }

            if (!isset($statsPorVoluntario[$idV]['subareas'][$idA])) {
                $statsPorVoluntario[$idV]['subareas'][$idA] = [
                    'id_area'                => $idA,
                    'nome_area'              => $e->nome_area,
                    'id_departamento'        => $idD,
                    'nome_departamento'      => $e->nome_departamento,
                    'cor_identificacao'      => $e->cor_identificacao ?: '#2563eb',
                    'total'                  => 0,
                    'confirmados'            => 0,
                    'cancelados'             => 0,
                    'cancelados_abonados'    => 0,
                    'cancelados_penalizados' => 0,
                    'nao_confirmados'        => 0,
                    'presencas'              => 0,
                    'bonus_agilidade_count'  => 0,
                    'soma_tempo_segundos'    => 0,
                    'respostas_com_tempo'    => 0
                ];
            }

            // Incrementa estatísticas gerais
            $statsPorVoluntario[$idV]['geral']['total']++;
            $statsPorVoluntario[$idV]['departamentos'][$idD]['total']++;
            $statsPorVoluntario[$idV]['subareas'][$idA]['total']++;

            $statusConf = strtoupper((string)($e->status_confirmacao ?: 'PENDENTE'));
            $stAnalise  = strtoupper((string)($e->status_analise_justificativa ?: 'PENDENTE'));

            // Medição da agilidade de resposta (em segundos)
            $tempoRespSeg = null;
            if (!empty($e->data_resposta)) {
                $inicioConvocacao = !empty($e->date_insert) ? strtotime($e->date_insert) : null;
                $fimResposta      = strtotime($e->data_resposta);
                if ($inicioConvocacao && $fimResposta && $fimResposta >= $inicioConvocacao) {
                    $tempoRespSeg = max(0, $fimResposta - $inicioConvocacao);
                }
            }

            if ($statusConf === 'CONFIRMADO') {
                $statsPorVoluntario[$idV]['geral']['confirmados']++;
                $statsPorVoluntario[$idV]['departamentos'][$idD]['confirmados']++;
                $statsPorVoluntario[$idV]['subareas'][$idA]['confirmados']++;

                if ($tempoRespSeg !== null) {
                    $statsPorVoluntario[$idV]['geral']['soma_tempo_segundos'] += $tempoRespSeg;
                    $statsPorVoluntario[$idV]['geral']['respostas_com_tempo']++;

                    $statsPorVoluntario[$idV]['departamentos'][$idD]['soma_tempo_segundos'] += $tempoRespSeg;
                    $statsPorVoluntario[$idV]['departamentos'][$idD]['respostas_com_tempo']++;

                    $statsPorVoluntario[$idV]['subareas'][$idA]['soma_tempo_segundos'] += $tempoRespSeg;
                    $statsPorVoluntario[$idV]['subareas'][$idA]['respostas_com_tempo']++;

                    // Bônus de agilidade de +5 pontos se aceitou a escala em até 24 horas (86400 segundos)
                    if ($tempoRespSeg <= 86400) {
                        $statsPorVoluntario[$idV]['geral']['bonus_agilidade_count']++;
                        $statsPorVoluntario[$idV]['departamentos'][$idD]['bonus_agilidade_count']++;
                        $statsPorVoluntario[$idV]['subareas'][$idA]['bonus_agilidade_count']++;
                    }
                }
            } elseif ($statusConf === 'RECUSADO') {
                $statsPorVoluntario[$idV]['geral']['cancelados']++;
                $statsPorVoluntario[$idV]['departamentos'][$idD]['cancelados']++;
                $statsPorVoluntario[$idV]['subareas'][$idA]['cancelados']++;

                if ($stAnalise === 'ABONADO') {
                    $statsPorVoluntario[$idV]['geral']['cancelados_abonados']++;
                    $statsPorVoluntario[$idV]['departamentos'][$idD]['cancelados_abonados']++;
                    $statsPorVoluntario[$idV]['subareas'][$idA]['cancelados_abonados']++;
                    $kpiCancelamentosAbonadosTot++;
                } else {
                    $statsPorVoluntario[$idV]['geral']['cancelados_penalizados']++;
                    $statsPorVoluntario[$idV]['departamentos'][$idD]['cancelados_penalizados']++;
                    $statsPorVoluntario[$idV]['subareas'][$idA]['cancelados_penalizados']++;
                    if ($stAnalise === 'DESCONTADO') {
                        $kpiCancelamentosDescontadosTot++;
                    } else {
                        $kpiCancelamentosPendentesTot++;
                    }
                }
            } elseif ($statusConf === 'OMISSAO' || $statusConf === 'NAO_CONFIRMADO') {
                $statsPorVoluntario[$idV]['geral']['nao_confirmados']++;
                $statsPorVoluntario[$idV]['departamentos'][$idD]['nao_confirmados']++;
                $statsPorVoluntario[$idV]['subareas'][$idA]['nao_confirmados']++;
            }

            if ($e->status_presenca == 1) {
                $statsPorVoluntario[$idV]['geral']['presencas']++;
                $statsPorVoluntario[$idV]['departamentos'][$idD]['presencas']++;
                $statsPorVoluntario[$idV]['subareas'][$idA]['presencas']++;
            }
        }

        // Função auxiliar de pontuação (Presença: +15, Confirmação: +10, Bônus Agilidade: +5, Cancelamento Penalizado: -10, Falta: -25)
        $calcularPontos = function($pres, $conf, $bonusAgil, $cancPenalizado, $omi) {
            $pts = ($pres * 15) + ($conf * 10) + ($bonusAgil * 5) - ($cancPenalizado * 10) - ($omi * 25);
            return max(0, $pts);
        };

        // 5. Calcula pontuações e rankings em cada dimensão:
        //    a) Global (todos os departamentos)
        //    b) Por Departamento
        //    c) Por Sub-área

        $voluntariosGlobal   = []; // [id_voluntario => pontos]
        $voluntariosPorDep   = []; // [id_departamento][id_voluntario => pontos]
        $voluntariosPorArea  = []; // [id_area][id_voluntario => pontos]

        foreach ($voluntarios as $v) {
            $idV = (int)$v->id_voluntario;
            $s   = $statsPorVoluntario[$idV]['geral'] ?? null;
            if ($s && $s['total'] > 0) {
                $tMedioSeg = ($s['respostas_com_tempo'] > 0) ? (int)round($s['soma_tempo_segundos'] / $s['respostas_com_tempo']) : PHP_INT_MAX;
                $voluntariosGlobal[$idV] = [
                    'pontos'               => $calcularPontos($s['presencas'], $s['confirmados'], $s['bonus_agilidade_count'], $s['cancelados_penalizados'], $s['nao_confirmados']),
                    'presencas'            => $s['presencas'],
                    'confirmados'          => $s['confirmados'],
                    'bonus_agilidade_count'=> $s['bonus_agilidade_count'],
                    'tempo_medio_segundos' => $tMedioSeg,
                    'assiduidade'          => round(($s['presencas'] / $s['total']) * 100, 1),
                    'nome'                 => $v->nome
                ];
            }

            // Departamentos
            $depsDoVol = $departamentosPorVoluntario[$idV] ?? [];
            foreach ($depsDoVol as $idD => $dInfo) {
                $sDep = $statsPorVoluntario[$idV]['departamentos'][$idD] ?? null;
                $tot  = $sDep ? $sDep['total'] : 0;
                $pres = $sDep ? $sDep['presencas'] : 0;
                $conf = $sDep ? $sDep['confirmados'] : 0;
                $cancPen = $sDep ? $sDep['cancelados_penalizados'] : 0;
                $omi  = $sDep ? $sDep['nao_confirmados'] : 0;
                $bonusAgil = $sDep ? $sDep['bonus_agilidade_count'] : 0;
                $tMedioDep = ($sDep && $sDep['respostas_com_tempo'] > 0) ? (int)round($sDep['soma_tempo_segundos'] / $sDep['respostas_com_tempo']) : PHP_INT_MAX;

                $pts = $calcularPontos($pres, $conf, $bonusAgil, $cancPen, $omi);
                $voluntariosPorDep[$idD][$idV] = [
                    'pontos'               => $pts,
                    'presencas'            => $pres,
                    'confirmados'          => $conf,
                    'bonus_agilidade_count'=> $bonusAgil,
                    'tempo_medio_segundos' => $tMedioDep,
                    'assiduidade'          => $tot > 0 ? round(($pres / $tot) * 100, 1) : 0,
                    'total'                => $tot,
                    'nome'                 => $v->nome
                ];
            }

            // Subáreas
            $areasDoVol = $subareasPorVoluntario[$idV] ?? [];
            foreach ($areasDoVol as $idA => $aInfo) {
                $sArea = $statsPorVoluntario[$idV]['subareas'][$idA] ?? null;
                $tot   = $sArea ? $sArea['total'] : 0;
                $pres  = $sArea ? $sArea['presencas'] : 0;
                $conf  = $sArea ? $sArea['confirmados'] : 0;
                $cancPen = $sArea ? $sArea['cancelados_penalizados'] : 0;
                $omi   = $sArea ? $sArea['nao_confirmados'] : 0;
                $bonusAgil = $sArea ? $sArea['bonus_agilidade_count'] : 0;
                $tMedioArea = ($sArea && $sArea['respostas_com_tempo'] > 0) ? (int)round($sArea['soma_tempo_segundos'] / $sArea['respostas_com_tempo']) : PHP_INT_MAX;

                $pts = $calcularPontos($pres, $conf, $bonusAgil, $cancPen, $omi);
                $voluntariosPorArea[$idA][$idV] = [
                    'pontos'               => $pts,
                    'presencas'            => $pres,
                    'confirmados'          => $conf,
                    'bonus_agilidade_count'=> $bonusAgil,
                    'tempo_medio_segundos' => $tMedioArea,
                    'assiduidade'          => $tot > 0 ? round(($pres / $tot) * 100, 1) : 0,
                    'total'                => $tot,
                    'nome'                 => $v->nome
                ];
            }
        }

        // Ordena e mapeia posições relativas (Desempate por Rapidez / Tempo Médio de Resposta)
        $funcaoOrdenacao = function(&$arr) {
            uasort($arr, function($a, $b) {
                if ($a['pontos'] !== $b['pontos']) {
                    return $b['pontos'] <=> $a['pontos'];
                }
                // Divisor de águas: rapidez na aceitação da convocação (menor tempo médio em primeiro)
                $tA = $a['tempo_medio_segundos'] ?? PHP_INT_MAX;
                $tB = $b['tempo_medio_segundos'] ?? PHP_INT_MAX;
                if ($tA !== $tB) {
                    return $tA <=> $tB;
                }
                if ($a['presencas'] !== $b['presencas']) {
                    return $b['presencas'] <=> $a['presencas'];
                }
                if ($a['confirmados'] !== $b['confirmados']) {
                    return $b['confirmados'] <=> $a['confirmados'];
                }
                return strcasecmp($a['nome'], $b['nome']);
            });
        };

        $funcaoOrdenacao($voluntariosGlobal);
        $rankGlobalMap = [];
        $rankIdx = 1;
        foreach ($voluntariosGlobal as $idV => $dados) {
            $rankGlobalMap[$idV] = [
                'posicao' => $rankIdx++,
                'total'   => count($voluntariosGlobal),
                'pontos'  => $dados['pontos']
            ];
        }

        $rankDepMap = [];
        foreach ($voluntariosPorDep as $idD => &$vList) {
            $funcaoOrdenacao($vList);
            $rIdx = 1;
            foreach ($vList as $idV => $dados) {
                $rankDepMap[$idD][$idV] = [
                    'posicao'     => $rIdx++,
                    'total'       => count($vList),
                    'pontos'      => $dados['pontos'],
                    'escalas'     => $dados['total'],
                    'presencas'   => $dados['presencas'],
                    'assiduidade' => $dados['assiduidade']
                ];
            }
        }

        $rankAreaMap = [];
        foreach ($voluntariosPorArea as $idA => &$vList) {
            $funcaoOrdenacao($vList);
            $rIdx = 1;
            foreach ($vList as $idV => $dados) {
                $rankAreaMap[$idA][$idV] = [
                    'posicao'     => $rIdx++,
                    'total'       => count($vList),
                    'pontos'      => $dados['pontos'],
                    'escalas'     => $dados['total'],
                    'presencas'   => $dados['presencas'],
                    'assiduidade' => $dados['assiduidade']
                ];
            }
        }

        // 6. Monta a lista do Leaderboard para o Escopo Selecionado (Filtro Atual)
        $leaderboard = [];

        foreach ($voluntarios as $v) {
            $idV         = (int)$v->id_voluntario;
            $depsVinculo = $departamentosPorVoluntario[$idV] ?? [];
            $areasVinculo = $subareasPorVoluntario[$idV] ?? [];

            // Valida filtro por departamento / sub-área
            if (!empty($id_departamento)) {
                $atuaNesteDep = isset($depsVinculo[$id_departamento]);
                if (!$atuaNesteDep) {
                    continue;
                }
                if (!empty($id_area)) {
                    $atuaNestaArea = isset($areasVinculo[$id_area]);
                    if (!$atuaNestaArea) {
                        continue;
                    }
                }
            }

            $isMulti = count($depsVinculo) > 1;
            if ($apenasMulti && !$isMulti) {
                continue;
            }

            // Obtém as métricas no escopo filtrado
            if (!empty($id_area)) {
                $sEscopo = $statsPorVoluntario[$idV]['subareas'][$id_area] ?? [
                    'total' => 0, 'confirmados' => 0, 'cancelados' => 0, 'nao_confirmados' => 0, 'presencas' => 0,
                    'bonus_agilidade_count' => 0, 'soma_tempo_segundos' => 0, 'respostas_com_tempo' => 0
                ];
            } elseif (!empty($id_departamento)) {
                $sEscopo = $statsPorVoluntario[$idV]['departamentos'][$id_departamento] ?? [
                    'total' => 0, 'confirmados' => 0, 'cancelados' => 0, 'nao_confirmados' => 0, 'presencas' => 0,
                    'bonus_agilidade_count' => 0, 'soma_tempo_segundos' => 0, 'respostas_com_tempo' => 0
                ];
            } else {
                $sEscopo = $statsPorVoluntario[$idV]['geral'] ?? [
                    'total' => 0, 'confirmados' => 0, 'cancelados' => 0, 'nao_confirmados' => 0, 'presencas' => 0,
                    'bonus_agilidade_count' => 0, 'soma_tempo_segundos' => 0, 'respostas_com_tempo' => 0
                ];
            }

            if ($apenasEscalados && $sEscopo['total'] === 0) {
                continue;
            }

            $totEsc            = $sEscopo['total'];
            $totConf           = $sEscopo['confirmados'];
            $totCanc           = $sEscopo['cancelados'];
            $totCancAbonados   = $sEscopo['cancelados_abonados'] ?? 0;
            $totCancPenalizados= $sEscopo['cancelados_penalizados'] ?? 0;
            $totOmi            = $sEscopo['nao_confirmados'];
            $totPres           = $sEscopo['presencas'];
            $bonusAgilidade    = $sEscopo['bonus_agilidade_count'] ?? 0;
            $bonusPontos       = $bonusAgilidade * 5;

            $respComTempo      = $sEscopo['respostas_com_tempo'] ?? 0;
            $tempoMedioSeg     = $respComTempo > 0 ? (int)round(($sEscopo['soma_tempo_segundos'] ?? 0) / $respComTempo) : PHP_INT_MAX;
            $tempoMedioFormat  = ($tempoMedioSeg !== PHP_INT_MAX) ? self::formatarTempoResposta($tempoMedioSeg) : '--';

            $taxaAssiduidade = $totEsc > 0 ? round(($totPres / $totEsc) * 100, 1) : 0;
            $pontosEscopo    = $calcularPontos($totPres, $totConf, $bonusAgilidade, $totCancPenalizados, $totOmi);

            // Métricas globais consolidadas
            $sGeral = $statsPorVoluntario[$idV]['geral'] ?? [
                'total' => 0, 'confirmados' => 0, 'cancelados' => 0, 'cancelados_abonados' => 0, 'cancelados_penalizados' => 0, 'nao_confirmados' => 0, 'presencas' => 0,
                'bonus_agilidade_count' => 0
            ];
            $pontosGerais = $calcularPontos($sGeral['presencas'], $sGeral['confirmados'], ($sGeral['bonus_agilidade_count'] ?? 0), ($sGeral['cancelados_penalizados'] ?? 0), $sGeral['nao_confirmados']);

            // Detalha breakdown por departamento para este voluntário
            $breakdownDeps = [];
            foreach ($depsVinculo as $idD => $dInfo) {
                $rankInfo = $rankDepMap[$idD][$idV] ?? null;
                $sDep     = $statsPorVoluntario[$idV]['departamentos'][$idD] ?? null;
                $totD     = $sDep ? $sDep['total'] : 0;
                $presD    = $sDep ? $sDep['presencas'] : 0;
                $confD    = $sDep ? $sDep['confirmados'] : 0;
                $cancD    = $sDep ? $sDep['cancelados'] : 0;
                $cancAbonD= $sDep ? ($sDep['cancelados_abonados'] ?? 0) : 0;
                $cancPenD = $sDep ? ($sDep['cancelados_penalizados'] ?? 0) : 0;
                $omiD     = $sDep ? $sDep['nao_confirmados'] : 0;
                $bonusD   = $sDep ? ($sDep['bonus_agilidade_count'] ?? 0) : 0;
                $ptsD     = $calcularPontos($presD, $confD, $bonusD, $cancPenD, $omiD);

                // Subáreas deste departamento onde o voluntário atua
                $subareasDep = [];
                foreach ($dInfo['areas'] as $idA => $nomeArea) {
                    $rankA = $rankAreaMap[$idA][$idV] ?? null;
                    $sA    = $statsPorVoluntario[$idV]['subareas'][$idA] ?? null;
                    $totA  = $sA ? $sA['total'] : 0;
                    $presA = $sA ? $sA['presencas'] : 0;
                    $confA = $sA ? $sA['confirmados'] : 0;
                    $cancA = $sA ? $sA['cancelados'] : 0;
                    $cancAbonA = $sA ? ($sA['cancelados_abonados'] ?? 0) : 0;
                    $cancPenA  = $sA ? ($sA['cancelados_penalizados'] ?? 0) : 0;
                    $omiA  = $sA ? $sA['nao_confirmados'] : 0;
                    $bonusA = $sA ? ($sA['bonus_agilidade_count'] ?? 0) : 0;
                    $ptsA  = $calcularPontos($presA, $confA, $bonusA, $cancPenA, $omiA);

                    $subareasDep[] = (object)[
                        'id_area'                       => $idA,
                        'nome_area'                     => $nomeArea,
                        'posicao'                       => $rankA['posicao'] ?? null,
                        'total_subarea'                 => $rankA['total'] ?? count($rankAreaMap[$idA] ?? []),
                        'pontos'                        => $ptsA,
                        'bonus_agilidade_count'         => $bonusA,
                        'total_escalas'                 => $totA,
                        'presencas'                     => $presA,
                        'cancelados_abonados'           => $cancAbonA,
                        'cancelados_penalizados'        => $cancPenA,
                        'taxa_assiduidade'              => $totA > 0 ? round(($presA / $totA) * 100, 1) : 0
                    ];
                }

                $breakdownDeps[] = (object)[
                    'id_departamento'               => $idD,
                    'nome_departamento'             => $dInfo['nome'],
                    'cor_identificacao'             => $dInfo['cor_identificacao'],
                    'posicao'                       => $rankInfo['posicao'] ?? null,
                    'total_departamento'            => $rankInfo['total'] ?? count($rankDepMap[$idD] ?? []),
                    'pontos'                        => $ptsD,
                    'bonus_agilidade_count'         => $bonusD,
                    'total_escalas'                 => $totD,
                    'presencas'                     => $presD,
                    'confirmados'                   => $confD,
                    'cancelados'                    => $cancD,
                    'cancelados_abonados'           => $cancAbonD,
                    'cancelados_penalizados'        => $cancPenD,
                    'nao_confirmados'               => $omiD,
                    'taxa_assiduidade'              => $totD > 0 ? round(($presD / $totD) * 100, 1) : 0,
                    'subareas'                      => $subareasDep
                ];
            }

            $nomeExibicao  = !empty($v->nickname) ? $v->nickname : (explode(' ', trim($v->nome))[0] . ' ' . (explode(' ', trim($v->nome))[count(explode(' ', trim($v->nome))) - 1] ?? ''));
            $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($v->nome) . '&background=2563eb&color=fff&size=80&bold=true';

            $leaderboard[] = (object)[
                'id_voluntario'                 => $idV,
                'nome_completo'                 => $v->nome,
                'nome_exibicao'                 => $nomeExibicao,
                'nickname'                      => $v->nickname,
                'foto_url'                      => !empty($v->foto_url) ? $v->foto_url : $defaultAvatar,
                'nivel_conhecimento'            => $v->nivel_conhecimento ?: 'JUNIOR',
                'telefone_whatsapp'             => $v->telefone_whatsapp,
                'status'                        => $v->status,
                'is_multidepartamento'          => $isMulti,
                'total_departamentos_atuacao'   => count($depsVinculo),
                'departamentos_vinculados'      => array_values($depsVinculo),
                // Métricas do Escopo Atual (o que a tabela/filtro está exibindo)
                'total_escalas'                 => $totEsc,
                'cultos_aceitos'                => $totConf,
                'cultos_cancelados'             => $totCanc,
                'cultos_cancelados_abonados'    => $totCancAbonados,
                'cultos_cancelados_penalizados' => $totCancPenalizados,
                'nao_confirmados'               => $totOmi,
                'presencas'                     => $totPres,
                'taxa_assiduidade'              => $taxaAssiduidade,
                'pontos'                        => $pontosEscopo,
                // Agilidade e Tempo de Resposta
                'bonus_agilidade_count'         => $bonusAgilidade,
                'bonus_agilidade_pontos'        => $bonusPontos,
                'tempo_medio_resposta_segundos' => $tempoMedioSeg,
                'tempo_medio_resposta_formatado'=> $tempoMedioFormat,
                // Visão Geral e Relativa
                'posicao_global'                => $rankGlobalMap[$idV]['posicao'] ?? null,
                'total_geral_voluntarios'       => count($rankGlobalMap),
                'pontos_globais'                => $pontosGerais,
                'breakdown_departamentos'       => $breakdownDeps
            ];
        }

        // Ordena o Leaderboard pelo escopo atual com desempate pela rapidez de aceite
        usort($leaderboard, function($a, $b) {
            if ($a->pontos !== $b->pontos) {
                return $b->pontos <=> $a->pontos;
            }
            // Divisor de águas: rapidez na aceitação da convocação (menor tempo médio de resposta primeiro)
            if ($a->tempo_medio_resposta_segundos !== $b->tempo_medio_resposta_segundos) {
                return $a->tempo_medio_resposta_segundos <=> $b->tempo_medio_resposta_segundos;
            }
            if ($a->presencas !== $b->presencas) {
                return $b->presencas <=> $a->presencas;
            }
            if ($a->cultos_aceitos !== $b->cultos_aceitos) {
                return $b->cultos_aceitos <=> $a->cultos_aceitos;
            }
            if ($a->taxa_assiduidade !== $b->taxa_assiduidade) {
                return $b->taxa_assiduidade <=> $a->taxa_assiduidade;
            }
            return strcasecmp($a->nome_completo, $b->nome_completo);
        });

        // Atribui posições do Leaderboard
        $pos = 1;
        foreach ($leaderboard as &$item) {
            $item->posicao = $pos++;
        }

        // Pódio Top 3
        $top3 = array_slice($leaderboard, 0, 3);

        // Indicadores KPIs do Ranking
        $kpiEscalasTotais = 0;
        $kpiPresencas     = 0;
        $kpiPontos        = 0;
        $multidepCount    = 0;

        foreach ($leaderboard as $it) {
            $kpiEscalasTotais += $it->total_escalas;
            $kpiPresencas     += $it->presencas;
            $kpiPontos        += $it->pontos;
            if ($it->is_multidepartamento) {
                $multidepCount++;
            }
        }

        $mediaAssiduidade = count($leaderboard) > 0 ? round(array_sum(array_column($leaderboard, 'taxa_assiduidade')) / count($leaderboard), 1) : 0;

        return (object)[
            'periodo'                => $periodo,
            'labelPeriodo'           => $labelPeriodo,
            'dataInicio'             => $dataInicio,
            'dataFim'                => $dataFim,
            'id_departamento'        => $id_departamento,
            'departamento'           => $departamentoInfo,
            'id_area'                => $id_area,
            'area'                   => $areaInfo,
            'filtros'                => $filtros,
            'leaderboard'            => $leaderboard,
            'top3'                   => $top3,
            'totalClassificados'     => count($leaderboard),
            'totalMultidepartamento' => $multidepCount,
            'kpis'                   => (object)[
                'totalEscalas'                  => $kpiEscalasTotais,
                'totalPresencas'                => $kpiPresencas,
                'totalPontos'                   => $kpiPontos,
                'mediaAssiduidade'              => $mediaAssiduidade,
                'totalCancelamentosPendentes'   => $kpiCancelamentosPendentesTot,
                'totalCancelamentosAbonados'    => $kpiCancelamentosAbonadosTot,
                'totalCancelamentosDescontados' => $kpiCancelamentosDescontadosTot
            ]
        ];
    }

    /**
     * Retorna a ficha comparativa completa de ranking de um voluntário
     * (posição global, em cada departamento e em cada sub-área que atua)
     */
    public function getRankingDetalhesVoluntario(int $id_voluntario, string $periodo = 'mes_atual', array $departamentosPermitidos = []): ?object
    {
        $dadosRanking = $this->getRankingGestor([
            'periodo'                 => $periodo,
            'departamentos_permitidos'=> $departamentosPermitidos,
            'apenas_escalados'        => false
        ]);

        foreach ($dadosRanking->leaderboard as $item) {
            if ($item->id_voluntario === $id_voluntario) {
                return (object)[
                    'voluntario'    => $item,
                    'periodo'       => $periodo,
                    'labelPeriodo'  => $dadosRanking->labelPeriodo,
                    'dataInicio'    => $dadosRanking->dataInicio,
                    'dataFim'       => $dadosRanking->dataFim
                ];
            }
        }

        // Se não estava no ranking ainda, busca cadastro e tenta montar
        $voluntario = $this->find($id_voluntario);
        if (!$voluntario) {
            return null;
        }

        $departamentos = $this->getDepartamentosDoVoluntario($id_voluntario);
        $voluntarioAreaModel = new VoluntarioAreaModel();
        $areas = $voluntarioAreaModel->getAreasDoVoluntario($id_voluntario);

        $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($voluntario->nome) . '&background=2563eb&color=fff&size=80&bold=true';
        $nomeExibicao  = !empty($voluntario->nickname) ? $voluntario->nickname : (explode(' ', trim($voluntario->nome))[0] . ' ' . (explode(' ', trim($voluntario->nome))[count(explode(' ', trim($voluntario->nome))) - 1] ?? ''));

        return (object)[
            'voluntario' => (object)[
                'id_voluntario'               => (int)$voluntario->id_voluntario,
                'nome_completo'               => $voluntario->nome,
                'nome_exibicao'               => $nomeExibicao,
                'nickname'                    => $voluntario->nickname,
                'foto_url'                    => !empty($voluntario->foto_url) ? $voluntario->foto_url : $defaultAvatar,
                'nivel_conhecimento'          => $voluntario->nivel_conhecimento ?: 'JUNIOR',
                'telefone_whatsapp'           => $voluntario->telefone_whatsapp,
                'status'                      => $voluntario->status,
                'is_multidepartamento'        => count($departamentos) > 1,
                'total_departamentos_atuacao' => count($departamentos),
                'total_escalas'               => 0,
                'cultos_aceitos'              => 0,
                'cultos_cancelados'           => 0,
                'nao_confirmados'             => 0,
                'presencas'                   => 0,
                'taxa_assiduidade'            => 0,
                'pontos'                      => 0,
                'posicao'                     => null,
                'posicao_global'              => null,
                'total_geral_voluntarios'     => count($dadosRanking->leaderboard),
                'breakdown_departamentos'     => []
            ],
            'periodo'      => $periodo,
            'labelPeriodo' => $dadosRanking->labelPeriodo,
            'dataInicio'   => $dadosRanking->dataInicio,
            'dataFim'      => $dadosRanking->dataFim
        ];
    }

    /**
     * Retorna lista de voluntários disponíveis para um determinado culto aplicando a Regra de Disponibilidade Total
     * (Voluntários que marcaram o culto OU que não possuem nenhuma restrição cadastrada)
     */
    public function getVoluntariosDisponiveisPorCulto($id_culto_padrao, $id_departamento = null, $id_area = null, $onlyActive = true)
    {
        $voluntarioCultoModel = new VoluntarioCultoModel();
        return $voluntarioCultoModel->getVoluntariosDisponiveisPorCulto($id_culto_padrao, $id_departamento, $id_area, $onlyActive);
    }

    /**
     * Retorna lista de voluntários pendentes de aprovação (Self-onboarding)
     */
    public function listaPendentesAprovacao(array $departamentosPermitidosIds = [], array $filtros = [])
    {
        $db = db_connect();
        $builder = $db->table('tb_voluntario as v');
        $builder->select('
            v.*,
            vda.id_departamento,
            d.nome as nome_departamento,
            d.cor_identificacao as cor_departamento,
            GROUP_CONCAT(DISTINCT a.nome_area ORDER BY a.nome_area SEPARATOR ", ") as nome_area,
            GROUP_CONCAT(DISTINCT a.id_area ORDER BY a.id_area SEPARATOR ",") as ids_areas,
            c.token as token_convite,
            c.tipo as tipo_convite,
            c.capacidade_maxima,
            c.usos_realizados
        ');
        $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_voluntario = v.id_voluntario', 'left');
        $builder->join('tb_departamento as d', 'd.id_departamento = vda.id_departamento', 'left');
        $builder->join('tb_departamento_area as a', 'a.id_area = vda.id_area', 'left');
        $builder->join('tb_departamento_convite as c', 'c.id_convite = v.id_convite_origem', 'left');
        $builder->where('v.status_aprovacao', 'PENDENTE');

        if (!empty($filtros['id_departamento'])) {
            $builder->where('vda.id_departamento', (int)$filtros['id_departamento']);
        } elseif (!empty($departamentosPermitidosIds)) {
            $builder->whereIn('vda.id_departamento', (array)$departamentosPermitidosIds);
        }

        if (!empty($filtros['busca'])) {
            $busca = trim($filtros['busca']);
            $builder->groupStart()
                ->like('v.nome', $busca)
                ->orLike('v.nickname', $busca)
                ->orLike('v.email', $busca)
                ->orLike('v.telefone_whatsapp', $busca)
                ->groupEnd();
        }

        $builder->groupBy('v.id_voluntario');
        $builder->orderBy('v.date_insert', 'DESC');
        return $builder->get()->getResult('object');
    }

    /**
     * Contagem de cadastros pendentes de aprovação (únicos por voluntário)
     */
    public function contarPendentes(array $departamentosPermitidosIds = [])
    {
        if (empty($departamentosPermitidosIds)) {
            return 0;
        }

        $db = db_connect();
        $builder = $db->table('tb_voluntario as v');
        $builder->select('COUNT(DISTINCT v.id_voluntario) as total');
        $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_voluntario = v.id_voluntario', 'left');
        $builder->where('v.status_aprovacao', 'PENDENTE');
        $builder->whereIn('vda.id_departamento', (array)$departamentosPermitidosIds);

        $row = $builder->get()->getFirstRow();
        return $row ? (int)$row->total : 0;
    }

    /**
     * Aprova o cadastro do voluntário que realizou self-onboarding e sincroniza suas sub-áreas
     */
    public function aprovarVoluntario($idVoluntario, array $dadosAprovacao = [])
    {
        $idVoluntario = (int)$idVoluntario;
        if ($idVoluntario <= 0) {
            return false;
        }

        $voluntario = $this->find($idVoluntario);
        if (!$voluntario) {
            return false;
        }

        $digitosTelefone = preg_replace('/\D/', '', (string)$voluntario->telefone_whatsapp);
        if (empty($digitosTelefone)) {
            $digitosTelefone = '123456';
        }
        $hashSenha = password_hash($digitosTelefone, PASSWORD_BCRYPT);

        $dadosUpdate = [
            'status'             => 1,
            'status_aprovacao'   => 'APROVADO',
            'senha'              => $hashSenha,
            'primeiro_acesso'    => 1,
            'force_pwd_change'   => 1,
            'data_ultima_senha'  => date('Y-m-d H:i:s')
        ];

        if (isset($dadosAprovacao['nivel_conhecimento'])) {
            $dadosUpdate['nivel_conhecimento'] = $dadosAprovacao['nivel_conhecimento'];
        }
        if (isset($dadosAprovacao['max_escalas_mes'])) {
            $dadosUpdate['max_escalas_mes'] = max(0, (int)$dadosAprovacao['max_escalas_mes']);
        }
        if (isset($dadosAprovacao['observacao'])) {
            $dadosUpdate['observacao'] = trim((string)$dadosAprovacao['observacao']);
        }

        $db = db_connect();
        $db->table('tb_voluntario')->where('id_voluntario', $idVoluntario)->update($dadosUpdate);

        // Atualiza e sincroniza sub-áreas selecionadas na aprovação
        $idDep = !empty($dadosAprovacao['id_departamento']) ? (int)$dadosAprovacao['id_departamento'] : null;
        $areasParaSincronizar = [];

        if (!empty($dadosAprovacao['id_areas']) && is_array($dadosAprovacao['id_areas'])) {
            $areasParaSincronizar = array_values(array_unique(array_filter(array_map('intval', $dadosAprovacao['id_areas']))));
        } elseif (!empty($dadosAprovacao['id_area'])) {
            $raw = is_array($dadosAprovacao['id_area']) ? $dadosAprovacao['id_area'] : [$dadosAprovacao['id_area']];
            $areasParaSincronizar = array_values(array_unique(array_filter(array_map('intval', $raw))));
        }

        if (!empty($areasParaSincronizar) && $idDep) {
            $volAreaModel = new VoluntarioAreaModel();
            $volAreaModel->sincronizarAreas($idVoluntario, $areasParaSincronizar, [$idDep]);
        }

        return true;
    }

    /**
     * Rejeita o cadastro pendente do voluntário
     */
    public function rejeitarVoluntario($idVoluntario, $motivo = '')
    {
        $voluntario = $this->find((int)$idVoluntario);
        if (!$voluntario) {
            return false;
        }

        return $this->update((int)$idVoluntario, [
            'status'           => 0,
            'status_aprovacao' => 'REJEITADO',
            'observacao'       => trim((string)($voluntario->observacao ? $voluntario->observacao . "\n" : '') . "Rejeitado: " . $motivo)
        ]);
    }
}


