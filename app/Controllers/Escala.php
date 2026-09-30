<?php

namespace App\Controllers;

use App\Models\EscalaVoluntarioModel;
use App\Models\DepartamentoModel;
use App\Models\DepartamentoAreaModel;
use App\Models\VoluntarioModel;
use App\Models\VoluntarioAreaModel;
use App\Models\VoluntarioCultoModel;
use App\Models\CultoPadraoModel;
use App\Models\SessionModel;

class Escala extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'escala/');
        return $data;
    }

    /**
     * Tela Principal de Agendamento e Grade Mensal de Escalas por Departamento
     */
    public function index($id_departamento = null, $ano = null, $mes = null)
    {
        return $this->grade($id_departamento, $ano, $mes);
    }

    /**
     * Visão Dinâmica de Grade Mensal de Escalas por Departamento
     * Independente de tb_agenda_culto: projeta os cultos padrão ativos dinamicamente em tempo real
     */
    public function grade($id_departamento = null, $ano = null, $mes = null)
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $departamentoModel     = new DepartamentoModel();
        $departamentoAreaModel = new DepartamentoAreaModel();
        $escalaModel           = new EscalaVoluntarioModel();
        $voluntarioAreaModel   = new VoluntarioAreaModel();
        $cultoPadraoModel      = new CultoPadraoModel();

        // 1. Departamentos Ativos
        $departamentos = $departamentoModel->getDepartamentosAtivos();
        if (empty($departamentos)) {
            session()->setFlashdata('error', 'Nenhum departamento cadastrado. Cadastre um departamento primeiro.');
            return redirect()->to('departamento/novo');
        }

        // Se departamento não foi especificado na rota, usa o primeiro departamento ativo
        $id_departamento = (int)$id_departamento;
        if ($id_departamento <= 0) {
            $id_departamento = (int)$departamentos[0]->id_departamento;
        }

        // Recupera dados do departamento selecionado
        $departamentoSelecionado = null;
        foreach ($departamentos as $dep) {
            if ($dep->id_departamento == $id_departamento) {
                $departamentoSelecionado = $dep;
                break;
            }
        }
        if (!$departamentoSelecionado) {
            $departamentoSelecionado = $departamentos[0];
            $id_departamento = (int)$departamentoSelecionado->id_departamento;
        }

        // 2. Sub-áreas do Departamento Selecionado
        $areasDepartamento = $departamentoAreaModel->getAreasPorDepartamento($id_departamento, true);

        // 3. Parâmetros de Data (Ano e Mês)
        $ano = (int)($ano ?: date('Y'));
        $mes = (int)($mes ?: date('m'));

        if ($mes < 1) $mes = 1;
        if ($mes > 12) $mes = 12;

        $primeiroDia   = sprintf('%04d-%02d-01', $ano, $mes);
        $totalDias     = (int)date('t', strtotime($primeiroDia));
        $dataInicioMes = sprintf('%04d-%02d-01', $ano, $mes);
        $dataFimMes    = sprintf('%04d-%02d-%02d', $ano, $mes, $totalDias);

        // 4. Cultos Padrão / Tipos de Culto Ativos (Consultados em tempo real)
        $cultosPadraoAtivos = $cultoPadraoModel->getCultosPadraoAtivos();

        // 5. Escalas cadastradas no mês para o departamento
        $escalasMes = $escalaModel->getEscalasMesPorDepartamento($id_departamento, $dataInicioMes, $dataFimMes);

        // Indexa escalas por [data_culto][id_culto_padrao][id_area][]
        $escalasPorDataECultoPadrao = [];
        $totalEscalasPreenchidas = 0;
        $voluntariosUnicosEscalados = [];

        foreach ($escalasMes as $esc) {
            $dIso = $esc->data_culto;
            $idCp = (int)$esc->id_culto_padrao;
            $idAr = (int)$esc->id_area;
            $escalasPorDataECultoPadrao[$dIso][$idCp][$idAr][] = $esc;
            $totalEscalasPreenchidas++;
            $voluntariosUnicosEscalados[$esc->id_voluntario] = true;
        }

        // 6. Projeta os Cultos Dinamicamente Dia a Dia no Mês
        $totalCultosMes = 0;
        $cultosComEscala = 0;
        $cultosSemEscala = 0;

        $diasGrade = [];
        $diasSemanaNomes = [
            0 => 'domingo',
            1 => 'segunda-feira',
            2 => 'terça-feira',
            3 => 'quarta-feira',
            4 => 'quinta-feira',
            5 => 'sexta-feira',
            6 => 'sábado'
        ];

        for ($d = 1; $d <= $totalDias; $d++) {
            $dataIso      = sprintf('%04d-%02d-%02d', $ano, $mes, $d);
            $numDiaSemana = (int)date('w', strtotime($dataIso));

            $cultosDoDia = [];
            foreach ($cultosPadraoAtivos as $cp) {
                if (CultoPadraoModel::isOcorrenciaCultoValida($cp, $dataIso)) {
                    $totalCultosMes++;
                    $escalasDesteCulto = $escalasPorDataECultoPadrao[$dataIso][$cp->id_culto_padrao] ?? [];
                    $temAlgumaEscala = !empty($escalasDesteCulto);

                    if ($temAlgumaEscala) {
                        $cultosComEscala++;
                    } else {
                        $cultosSemEscala++;
                    }

                    $cultoObj = (object)[
                        'id_culto_padrao' => (int)$cp->id_culto_padrao,
                        'titulo_culto'    => $cp->nome_culto,
                        'horario_inicio'  => $cp->horario_inicio,
                        'horario_termino' => $cp->horario_termino,
                        'cor_evento'      => !empty($cp->cor_evento) ? $cp->cor_evento : '#2563eb',
                        'data_culto'      => $dataIso,
                        'escalasPorArea'  => $escalasDesteCulto
                    ];

                    $cultosDoDia[] = $cultoObj;
                }
            }

            $diasGrade[] = [
                'dia_num'          => sprintf('%02d', $d),
                'data_formatada'   => sprintf('%02d/%02d/%04d', $d, $mes, $ano),
                'data_iso'         => $dataIso,
                'num_dia_semana'   => $numDiaSemana,
                'nome_dia_semana'  => $diasSemanaNomes[$numDiaSemana],
                'is_fim_semana'    => ($numDiaSemana === 0 || $numDiaSemana === 6),
                'cultos_agendados' => $cultosDoDia
            ];
        }

        $totalAreas = count($areasDepartamento);
        $totalSlotsPossiveis = $totalCultosMes * max(1, $totalAreas);
        $percentualPreenchimento = $totalSlotsPossiveis > 0 
            ? round(($totalEscalasPreenchidas / $totalSlotsPossiveis) * 100) 
            : 0;

        // 7. Todos os voluntários ativos vinculados ao departamento selecionado
        $voluntariosDoDepartamento = $voluntarioAreaModel->getVoluntariosPorDepartamentoEArea($id_departamento, null, true);

        $data['departamentos']            = $departamentos;
        $data['id_departamento']          = $id_departamento;
        $data['departamentoSelecionado']  = $departamentoSelecionado;
        $data['areasDepartamento']        = $areasDepartamento;
        $data['ano']                      = $ano;
        $data['mes']                      = $mes;
        $data['totalDias']                = $totalDias;
        $data['diasGrade']                = $diasGrade;
        $data['totalCultosMes']           = $totalCultosMes;
        $data['cultosComEscala']          = $cultosComEscala;
        $data['cultosSemEscala']          = $cultosSemEscala;
        $data['totalEscalasPreenchidas']  = $totalEscalasPreenchidas;
        $data['totalVoluntariosUnicos']   = count($voluntariosUnicosEscalados);
        $data['percentualPreenchimento']  = min(100, $percentualPreenchimento);
        $data['voluntariosDepartamento']  = $voluntariosDoDepartamento;

        $data['content_view'] = view('escala/escala-grade', $data);
        return view('_layout', $data);
    }

    /**
     * AJAX: Salva / Encaixa voluntário em uma sub-área do culto
     */
    public function salvarEscala()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create) && empty($data['sys_action']->update)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        try {
            $data_culto      = trim((string)$this->request->getPost('data_culto'));
            $id_culto_padrao = (int)$this->request->getPost('id_culto_padrao');
            $id_departamento = (int)$this->request->getPost('id_departamento');
            $id_area         = (int)$this->request->getPost('id_area');
            $id_voluntario   = (int)$this->request->getPost('id_voluntario');
            $observacao      = trim((string)$this->request->getPost('observacao'));

            if (empty($data_culto) || $id_culto_padrao <= 0 || $id_departamento <= 0 || $id_area <= 0 || $id_voluntario <= 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Selecione a data, culto, sub-área e o voluntário.']);
            }

            // Validação de Limite Máximo Mensal de Escalas
            $voluntarioModel = new VoluntarioModel();
            $voluntario = $voluntarioModel->find($id_voluntario);

            if ($voluntario && !empty($voluntario->max_escalas_mes) && (int)$voluntario->max_escalas_mes > 0) {
                $maxPermitido = (int)$voluntario->max_escalas_mes;
                $anoCulto = (int)date('Y', strtotime($data_culto));
                $mesCulto = (int)date('m', strtotime($data_culto));

                // Verifica se já está escalado nesta mesma vaga
                $db = db_connect();
                $jaEscaladoAqui = $db->table('tb_escala_voluntario')
                    ->where('data_culto', $data_culto)
                    ->where('id_culto_padrao', $id_culto_padrao)
                    ->where('id_departamento', $id_departamento)
                    ->where('id_area', $id_area)
                    ->where('id_voluntario', $id_voluntario)
                    ->countAllResults();

                if (!$jaEscaladoAqui) {
                    $totalMes = $voluntarioModel->countEscalasMes($id_voluntario, $anoCulto, $mesCulto);
                    if ($totalMes >= $maxPermitido) {
                        return $this->response->setJSON([
                            'status'  => 'error',
                            'message' => "O voluntário '{$voluntario->nome}' já atingiu o limite mensal de {$maxPermitido} escala(s) no mês ({$totalMes} já agendadas)."
                        ]);
                    }
                }
            }

            $escalaModel = new EscalaVoluntarioModel();
            $id_escala = $escalaModel->escalarVoluntario($data_culto, $id_culto_padrao, $id_departamento, $id_area, $id_voluntario, $observacao);

            if ($id_escala) {
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => 'Voluntário escalado com sucesso!',
                    'id_escala' => $id_escala
                ]);
            }

            return $this->response->setJSON(['status' => 'error', 'message' => 'Não foi possível escalar o voluntário.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao salvar escala: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Remove voluntário da escala
     */
    public function removerEscala()
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        try {
            $id_escala_voluntario = (int)$this->request->getPost('id_escala_voluntario');
            if ($id_escala_voluntario <= 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Escala inválida.']);
            }

            $escalaModel = new EscalaVoluntarioModel();
            $escalaModel->removerEscala($id_escala_voluntario);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Voluntário removido da escala com sucesso!']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao remover escala: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Alterna confirmação/presença do voluntário (1=Presente, 0=Ausente, 2=Pendente)
     */
    public function alternarPresenca()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        try {
            $id_escala_voluntario = (int)$this->request->getPost('id_escala_voluntario');
            $status_presenca      = (int)$this->request->getPost('status_presenca');

            $escalaModel = new EscalaVoluntarioModel();
            $escalaModel->alternarPresenca($id_escala_voluntario, $status_presenca);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Status de presença atualizado!']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao atualizar presença: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Retorna voluntários vinculados a uma sub-área e departamento com status do limite mensal e disponibilidade do culto
     */
    public function getVoluntariosPorArea()
    {
        $id_departamento = (int)$this->request->getGet('id_departamento');
        $id_area         = (int)$this->request->getGet('id_area');
        $data_culto      = (string)$this->request->getGet('data_culto');
        $id_culto_padrao = (int)$this->request->getGet('id_culto_padrao');

        $anoCulto = !empty($data_culto) ? (int)date('Y', strtotime($data_culto)) : (int)date('Y');
        $mesCulto = !empty($data_culto) ? (int)date('m', strtotime($data_culto)) : (int)date('m');

        $voluntarioAreaModel  = new VoluntarioAreaModel();
        $voluntarioModel      = new VoluntarioModel();
        $voluntarioCultoModel = new VoluntarioCultoModel();
        
        // Voluntários com vínculo direto na área
        $vinculadosArea = $voluntarioAreaModel->getVoluntariosPorDepartamentoEArea($id_departamento, $id_area, true);
        $idsVinculadosArea = array_map(function($v) { return $v->id_voluntario; }, $vinculadosArea);

        // Outros voluntários do mesmo departamento
        $outrosDepartamento = $voluntarioAreaModel->getVoluntariosPorDepartamentoEArea($id_departamento, null, true);
        $outros = [];
        foreach ($outrosDepartamento as $v) {
            if (!in_array($v->id_voluntario, $idsVinculadosArea)) {
                $outros[] = $v;
            }
        }

        // Calcula total de escalas do mês, limite e disponibilidade do culto para cada voluntário
        $enriquecerVoluntario = function(&$lista) use ($voluntarioModel, $voluntarioCultoModel, $anoCulto, $mesCulto, $id_culto_padrao) {
            foreach ($lista as &$v) {
                $totalNoMes = $voluntarioModel->countEscalasMes($v->id_voluntario, $anoCulto, $mesCulto);
                $maxMes = isset($v->max_escalas_mes) ? (int)$v->max_escalas_mes : 0;

                $v->total_escalas_mes = $totalNoMes;
                $v->max_escalas_mes   = $maxMes;
                $v->atingiu_limite    = ($maxMes > 0 && $totalNoMes >= $maxMes);

                // Disponibilidade de Cultos (Regra do Coringa)
                if ($id_culto_padrao > 0) {
                    $statusDisp = $voluntarioCultoModel->getStatusDisponibilidadeVoluntario($v->id_voluntario, $id_culto_padrao);
                    $v->disponivel_culto     = $statusDisp['disponivel'];
                    $v->tipo_disponibilidade  = $statusDisp['tipo'];
                    $v->label_disponibilidade = $statusDisp['label'];
                } else {
                    $v->disponivel_culto     = true;
                    $v->tipo_disponibilidade  = 'TOTAL';
                    $v->label_disponibilidade = 'Disponível';
                }
            }
        };

        $enriquecerVoluntario($vinculadosArea);
        $enriquecerVoluntario($outros);

        return $this->response->setJSON([
            'status'             => 'success',
            'vinculados_area'    => $vinculadosArea,
            'outros_departamento'=> $outros
        ]);
    }

    /**
     * Relatório para Impressão da Grade de Escalas
     */
    public function imprimir($id_departamento = null, $ano = null, $mes = null)
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $id_departamento = (int)$id_departamento;
        $ano = (int)($ano ?: date('Y'));
        $mes = (int)($mes ?: date('m'));

        $departamentoModel     = new DepartamentoModel();
        $departamentoAreaModel = new DepartamentoAreaModel();
        $escalaModel           = new EscalaVoluntarioModel();
        $cultoPadraoModel      = new CultoPadraoModel();

        $departamento = $departamentoModel->find($id_departamento);
        if (!$departamento) {
            $departamento = $departamentoModel->first();
            $id_departamento = $departamento ? $departamento->id_departamento : 1;
        }

        $areas = $departamentoAreaModel->getAreasPorDepartamento($id_departamento, true);

        $primeiroDia   = sprintf('%04d-%02d-01', $ano, $mes);
        $totalDias     = (int)date('t', strtotime($primeiroDia));
        $dataInicioMes = sprintf('%04d-%02d-01', $ano, $mes);
        $dataFimMes    = sprintf('%04d-%02d-%02d', $ano, $mes, $totalDias);

        $cultosPadraoAtivos = $cultoPadraoModel->getCultosPadraoAtivos();
        $escalasMes = $escalaModel->getEscalasMesPorDepartamento($id_departamento, $dataInicioMes, $dataFimMes);

        $escalasPorDataECultoPadrao = [];
        foreach ($escalasMes as $esc) {
            $dIso = $esc->data_culto;
            $idCp = (int)$esc->id_culto_padrao;
            $idAr = (int)$esc->id_area;
            $escalasPorDataECultoPadrao[$dIso][$idCp][$idAr][] = $esc;
        }

        $diasGrade = [];
        $diasSemanaNomes = [
            0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira',
            3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado'
        ];

        $mesesNomesExtenso = [
            1 => 'JANEIRO', 2 => 'FEVEREIRO', 3 => 'MARÇO', 4 => 'ABRIL',
            5 => 'MAIO', 6 => 'JUNHO', 7 => 'JULHO', 8 => 'AGOSTO',
            9 => 'SETEMBRO', 10 => 'OUTUBRO', 11 => 'NOVEMBRO', 12 => 'DEZEMBRO'
        ];

        for ($d = 1; $d <= $totalDias; $d++) {
            $dataIso      = sprintf('%04d-%02d-%02d', $ano, $mes, $d);
            $numDiaSemana = (int)date('w', strtotime($dataIso));

            $cultosDoDia = [];
            foreach ($cultosPadraoAtivos as $cp) {
                if (CultoPadraoModel::isOcorrenciaCultoValida($cp, $dataIso)) {
                    $cultosDoDia[] = (object)[
                        'id_culto_padrao' => (int)$cp->id_culto_padrao,
                        'titulo_culto'    => $cp->nome_culto,
                        'horario_inicio'  => $cp->horario_inicio,
                        'horario_termino' => $cp->horario_termino,
                        'cor_evento'      => !empty($cp->cor_evento) ? $cp->cor_evento : '#2563eb',
                        'data_culto'      => $dataIso,
                        'escalasPorArea'  => $escalasPorDataECultoPadrao[$dataIso][$cp->id_culto_padrao] ?? []
                    ];
                }
            }

            $diasGrade[] = [
                'dia_num'          => sprintf('%02d', $d),
                'data_formatada'   => sprintf('%02d/%02d/%04d', $d, $mes, $ano),
                'data_iso'         => $dataIso,
                'num_dia_semana'   => $numDiaSemana,
                'nome_dia_semana'  => $diasSemanaNomes[$numDiaSemana],
                'is_fim_semana'    => ($numDiaSemana === 0 || $numDiaSemana === 6),
                'cultos_agendados' => $cultosDoDia
            ];
        }

        $data['departamento']   = $departamento;
        $data['areas']          = $areas;
        $data['ano']            = $ano;
        $data['mes']            = $mes;
        $data['nomeMesExtenso'] = $mesesNomesExtenso[$mes];
        $data['diasGrade']      = $diasGrade;

        return view('escala/escala-imprimir', $data);
    }
}
