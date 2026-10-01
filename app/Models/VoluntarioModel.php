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
        'max_escalas_mes',
        'redes_sociais',
        'observacao'
    ];

    /**
     * Retorna lista completa de voluntários com filtros e resumo de áreas/escalas
     */
    public function listaVoluntarios(array $filtros = [])
    {
        $db = db_connect();
        $builder = $db->table('tb_voluntario as v');
        $builder->select('
            v.*,
            COUNT(DISTINCT ev.id_escala_voluntario) as total_escalas,
            COUNT(DISTINCT CASE WHEN ev.status_presenca = 1 THEN ev.id_escala_voluntario END) as total_presencas
        ');
        $builder->join('tb_escala_voluntario as ev', 'ev.id_voluntario = v.id_voluntario', 'left');
        
        if (!empty($filtros['id_departamento'])) {
            $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_voluntario = v.id_voluntario', 'inner');
            $builder->where('vda.id_departamento', (int)$filtros['id_departamento']);
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

        $totalEscalas       = count($historico);
        $totalPresencas     = 0;
        $totalAusencias     = 0;
        $totalConfirmados   = 0;
        $totalCancelamentos = 0;
        $totalPendentes     = 0;
        $listaCancelamentos = [];
        $distribuicaoAreas  = [];

        foreach ($historico as $item) {
            if ($item->status_presenca == 1) {
                $totalPresencas++;
            } elseif ($item->status_presenca == 0) {
                $totalAusencias++;
            }

            if ($item->status_confirmacao === 'CONFIRMADO') {
                $totalConfirmados++;
            } elseif ($item->status_confirmacao === 'RECUSADO') {
                $totalCancelamentos++;
                $listaCancelamentos[] = $item;
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
            if ($item->status_confirmacao === 'RECUSADO') {
                $distribuicaoAreas[$areaKey]['cancelamentos']++;
            }
        }

        $taxaAssiduidade   = $totalEscalas > 0 ? round(($totalPresencas / $totalEscalas) * 100, 1) : 100;
        $taxaCancelamento  = $totalEscalas > 0 ? round(($totalCancelamentos / $totalEscalas) * 100, 1) : 0;

        return (object)[
            'voluntario'         => $voluntario,
            'periodo'            => $periodo,
            'labelPeriodo'       => $labelPeriodo,
            'id_departamento'    => $id_departamento ? (int)$id_departamento : null,
            'departamento'       => $departamentoInfo,
            'totalEscalas'       => $totalEscalas,
            'totalPresencas'     => $totalPresencas,
            'totalAusencias'     => $totalAusencias,
            'totalConfirmados'   => $totalConfirmados,
            'totalCancelamentos' => $totalCancelamentos,
            'totalPendentes'     => $totalPendentes,
            'taxaAssiduidade'    => $taxaAssiduidade,
            'taxaCancelamento'   => $taxaCancelamento,
            'listaCancelamentos' => $listaCancelamentos,
            'historicoEscalas'   => $historico,
            'distribuicaoAreas'  => array_values($distribuicaoAreas)
        ];
    }

    /**
     * Retorna relatório consolidado de desempenho dos voluntários com foco em assiduidade e cancelamentos
     */
    public function getRelatorioDesempenho(array $filtros = [])
    {
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

        $builderEsc->orderBy('ev.data_culto', 'DESC');
        $escalas = $builderEsc->get()->getResult('object');

        // Agrupa escalas por voluntário
        $escalasPorVoluntario = [];
        $todosCancelamentos = [];
        $kpiTotalEscalas = count($escalas);
        $kpiTotalConfirmados = 0;
        $kpiTotalCancelamentos = 0;
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
            $totPendentes = 0;
            $justificativas = [];

            foreach ($vEscalas as $ve) {
                if ($ve->status_presenca == 1) $totPresencas++;
                if ($ve->status_presenca == 0) $totAusencias++;

                if ($ve->status_confirmacao === 'CONFIRMADO') {
                    $totConfirmados++;
                } elseif ($ve->status_confirmacao === 'RECUSADO') {
                    $totCancelados++;
                    $justificativas[] = [
                        'id_escala_voluntario' => $ve->id_escala_voluntario,
                        'data_culto'           => $ve->data_culto,
                        'titulo_culto'         => $ve->titulo_culto,
                        'horario_inicio'       => $ve->horario_inicio,
                        'horario_termino'      => $ve->horario_termino,
                        'departamento'         => $ve->nome_departamento,
                        'cor_departamento'     => $ve->cor_departamento,
                        'area'                 => $ve->nome_area,
                        'justificativa'        => $ve->justificativa_recusa,
                        'data_resposta'        => $ve->data_resposta
                    ];
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
                'voluntario'         => $v,
                'areas'              => $vAreas,
                'totalEscalas'       => $totEscalas,
                'totalPresencas'     => $totPresencas,
                'totalAusencias'     => $totAusencias,
                'totalConfirmados'   => $totConfirmados,
                'totalCancelamentos' => $totCancelados,
                'totalPendentes'     => $totPendentes,
                'taxaAssiduidade'    => $taxaAssiduidade,
                'taxaCancelamento'   => $taxaCancelamento,
                'justificativas'     => $justificativas,
                'escalas'            => $vEscalas
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
                'totalVoluntarios'      => count($listaDesempenho),
                'totalEscalas'          => $kpiTotalEscalas,
                'totalPresencas'        => $kpiTotalPresencas,
                'totalConfirmados'      => $kpiTotalConfirmados,
                'totalCancelamentos'    => $kpiTotalCancelamentos,
                'totalPendentes'        => $kpiTotalPendentes,
                'taxaCancelamentoGeral' => $kpiTotalEscalas > 0 ? round(($kpiTotalCancelamentos / $kpiTotalEscalas) * 100, 1) : 0,
                'taxaAssiduidadeGeral'  => $kpiTotalEscalas > 0 ? round(($kpiTotalPresencas / $kpiTotalEscalas) * 100, 1) : 100
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
        $query = $builder->get();
        $voluntario = $query->getFirstRow();

        if ($voluntario) {
            $voluntarioAreaModel = new VoluntarioAreaModel();
            $voluntario->areas = $voluntarioAreaModel->getAreasDoVoluntario($voluntario->id_voluntario);
        }

        return $voluntario;
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
        $builderEsc->select('id_voluntario, status_confirmacao, status_presenca');
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
                    'total'        => 0,
                    'confirmados'  => 0,
                    'cancelados'   => 0,
                    'presencas'    => 0
                ];
            }
            $escalasPorVoluntario[$idV]['total']++;
            if ($e->status_confirmacao === 'CONFIRMADO') {
                $escalasPorVoluntario[$idV]['confirmados']++;
            } elseif ($e->status_confirmacao === 'RECUSADO') {
                $escalasPorVoluntario[$idV]['cancelados']++;
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
                        'id_voluntario'      => $idV,
                        'nome_completo'      => $v->nome,
                        'nome_exibicao'      => $nomeExibicao,
                        'nickname'           => $v->nickname,
                        'foto_url'           => !empty($v->foto_url) ? $v->foto_url : $defaultAvatar,
                        'nivel_conhecimento' => $v->nivel_conhecimento ?? 'JUNIOR',
                        'total_escalas'      => 0,
                        'cultos_aceitos'     => 0,
                        'cultos_cancelados'  => 0,
                        'presencas'          => 0,
                        'taxa_assiduidade'   => 0,
                        'pontos'             => 0,
                        'posicao'            => null,
                        'classificado'       => false,
                        'is_current_user'    => true
                    ];
                }
                continue;
            }

            $totEsc = $vStats['total'];
            $totConf = $vStats['confirmados'];
            $totCanc = $vStats['cancelados'];
            $totPres = $vStats['presencas'];

            $taxaAssiduidade = $totEsc > 0 ? round(($totPres / $totEsc) * 100, 1) : 0;

            // Sistema de Pontuação (Score de Fidelidade):
            // +15 pts por presença cumprida
            // +10 pts por confirmação
            // -10 pts por cancelamento/recusa
            $pontos = ($totPres * 15) + ($totConf * 10) - ($totCanc * 10);
            if ($pontos < 0) $pontos = 0;

            $leaderboard[] = (object)[
                'id_voluntario'      => $idV,
                'nome_completo'      => $v->nome,
                'nome_exibicao'      => $nomeExibicao,
                'nickname'           => $v->nickname,
                'foto_url'           => !empty($v->foto_url) ? $v->foto_url : $defaultAvatar,
                'nivel_conhecimento' => $v->nivel_conhecimento ?? 'JUNIOR',
                'total_escalas'      => $totEsc,
                'cultos_aceitos'     => $totConf,
                'cultos_cancelados'  => $totCanc,
                'presencas'          => $totPres,
                'taxa_assiduidade'   => $taxaAssiduidade,
                'pontos'             => $pontos,
                'classificado'       => true,
                'is_current_user'    => $isMe
            ];
        }

        // Ordena por Pontos DESC, depois por Cultos Aceitos DESC, depois por Assiduidade DESC
        usort($leaderboard, function($a, $b) {
            if ($a->pontos !== $b->pontos) {
                return $b->pontos <=> $a->pontos;
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
     * Retorna lista de voluntários disponíveis para um determinado culto aplicando a Regra do Coringa
     * (Voluntários que marcaram o culto OU que não possuem nenhuma restrição cadastrada)
     */
    public function getVoluntariosDisponiveisPorCulto($id_culto_padrao, $id_departamento = null, $id_area = null, $onlyActive = true)
    {
        $voluntarioCultoModel = new VoluntarioCultoModel();
        return $voluntarioCultoModel->getVoluntariosDisponiveisPorCulto($id_culto_padrao, $id_departamento, $id_area, $onlyActive);
    }
}

