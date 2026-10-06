<?php

namespace App\Controllers;

use App\Models\EscalaVoluntarioModel;
use App\Models\DepartamentoModel;
use App\Models\DepartamentoAreaModel;
use App\Models\DepartamentoGestorModel;
use App\Models\VoluntarioModel;
use App\Models\VoluntarioAreaModel;
use App\Models\VoluntarioCultoModel;
use App\Models\CultoPadraoModel;
use App\Models\ScheduleResourceModel;
use App\Models\SessionModel;
use App\Services\WebhookService;

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

        $departamentoModel       = new DepartamentoModel();
        $departamentoAreaModel   = new DepartamentoAreaModel();
        $departamentoGestorModel = new DepartamentoGestorModel();
        $escalaModel             = new EscalaVoluntarioModel();
        $voluntarioAreaModel     = new VoluntarioAreaModel();
        $cultoPadraoModel        = new CultoPadraoModel();

        // 1. Departamentos Permitidos ao Gestor
        $isAdmin                    = DepartamentoGestorModel::isUserAdmin($data['usuario']);
        $departamentos              = $departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentos, 'id_departamento');

        if (empty($departamentos)) {
            session()->setFlashdata('error', 'Seu usuário não possui acesso a nenhum departamento.');
            return redirect()->to('voluntario');
        }

        // Se departamento não foi especificado na rota ou não for permitido, usa o primeiro departamento permitido
        $id_departamento = (int)$id_departamento;
        if ($id_departamento <= 0 || !in_array($id_departamento, $departamentosPermitidosIds)) {
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
        $agoraDataHora = date('Y-m-d H:i:s');
        $totalPendentesFuturos = 0;

        foreach ($escalasMes as $esc) {
            $dIso = $esc->data_culto;
            $idCp = (int)$esc->id_culto_padrao;
            $idAr = (int)$esc->id_area;
            $escalasPorDataECultoPadrao[$dIso][$idCp][$idAr][] = $esc;
            
            $statusConf = strtoupper((string)($esc->status_confirmacao ?: 'PENDENTE'));
            if ($statusConf !== 'RECUSADO') {
                $totalEscalasPreenchidas++;
                $voluntariosUnicosEscalados[$esc->id_voluntario] = true;
            }

            // Conta pendentes de confirmação em cultos estritamente futuros
            $horaIni = !empty($esc->horario_inicio) ? $esc->horario_inicio : '00:00:00';
            $dataHoraCulto = $esc->data_culto . ' ' . $horaIni;
            if ($statusConf === 'PENDENTE' && $dataHoraCulto > $agoraDataHora) {
                $totalPendentesFuturos++;
            }
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
                    
                    // Verifica se há alguma escala ATIVA (não recusada) no culto
                    $temAlgumaEscala = false;
                    $temPendenteNoCulto = false;
                    if (!empty($escalasDesteCulto)) {
                        foreach ($escalasDesteCulto as $arrEscalasArea) {
                            foreach ($arrEscalasArea as $eCheck) {
                                $stCheck = strtoupper((string)($eCheck->status_confirmacao ?: 'PENDENTE'));
                                if ($stCheck !== 'RECUSADO') {
                                    $temAlgumaEscala = true;
                                }
                                if ($stCheck === 'PENDENTE') {
                                    $horaCheck = !empty($cp->horario_inicio) ? $cp->horario_inicio : '00:00:00';
                                    if ($dataIso . ' ' . $horaCheck > $agoraDataHora) {
                                        $temPendenteNoCulto = true;
                                    }
                                }
                            }
                        }
                    }

                    if ($temAlgumaEscala) {
                        $cultosComEscala++;
                    } else {
                        $cultosSemEscala++;
                    }

                    $cultoObj = (object)[
                        'id_culto_padrao'      => (int)$cp->id_culto_padrao,
                        'titulo_culto'         => $cp->nome_culto,
                        'horario_inicio'       => $cp->horario_inicio,
                        'horario_termino'      => $cp->horario_termino,
                        'cor_evento'           => !empty($cp->cor_evento) ? $cp->cor_evento : '#2563eb',
                        'data_culto'           => $dataIso,
                        'escalasPorArea'       => $escalasDesteCulto,
                        'temPendenteConfirmar' => $temPendenteNoCulto
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

        // 8. Resumo de Materiais Anexados da Biblioteca no Mês
        $scheduleResourceModel = new ScheduleResourceModel();
        $recursosResumo = $scheduleResourceModel->getResumoRecursosMes((int)$id_departamento, $dataInicioMes, $dataFimMes);

        // 9. Informações de Data e Meses Anteriores com Escala
        $hojeIso            = date('Y-m-d');
        $anoAtual           = (int)date('Y');
        $mesAtual           = (int)date('m');
        $isAnoAtual         = ($ano === $anoAtual);
        $isMesAtual         = ($ano === $anoAtual && $mes === $mesAtual);
        $isMesPassado       = ($ano < $anoAtual || ($ano === $anoAtual && $mes < $mesAtual));
        $podeEditarPassado  = !empty($data['sys_action']->update_past);

        // Atualização automática de omissões retroativas
        $escalaModel->marcarOmissoesPassadas();

        // Busca apenas os meses passados que possuem escalas para o departamento no ano
        $mesesPassadosComEscala = $escalaModel->getMesesPassadosComEscala($id_departamento, $ano);

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
        $data['totalPendentesFuturos']    = $totalPendentesFuturos;
        $data['percentualPreenchimento']  = min(100, $percentualPreenchimento);
        $data['voluntariosDepartamento']  = $voluntariosDoDepartamento;
        $data['recursosContagemPorCulto'] = $recursosResumo['por_culto'];
        $data['recursosContagemPorArea']  = $recursosResumo['por_area'];

        // Variáveis de Controle de UI / Bloqueio Retroativo
        $data['hojeIso']                  = $hojeIso;
        $data['anoAtual']                 = $anoAtual;
        $data['mesAtual']                 = $mesAtual;
        $data['isAnoAtual']               = $isAnoAtual;
        $data['isMesAtual']               = $isMesAtual;
        $data['isMesPassado']             = $isMesPassado;
        $data['podeEditarPassado']        = $podeEditarPassado;
        $data['mesesPassadosComEscala']   = $mesesPassadosComEscala;

        $data['content_view'] = view('escala/escala-grade', $data);
        return view('_layout', $data);
    }

    /**
     * AJAX: Salva / Encaixa um ou múltiplos voluntários em uma sub-área do culto
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
            $observacao      = trim((string)$this->request->getPost('observacao'));

            // Validação de Bloqueio de Edição Retroativa
            $hojeIso = date('Y-m-d');
            if (!empty($data_culto) && $data_culto < $hojeIso && empty($data['sys_action']->update_past)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Bloqueio Retroativo: Você não possui permissão para cadastrar ou editar escalas em datas passadas.'
                ]);
            }

            // Trata múltiplos IDs de voluntários recebidos via array ou string separada por vírgula
            $rawVoluntarios = $this->request->getPost('id_voluntarios') ?: $this->request->getPost('id_voluntario');
            $idsVoluntarios = [];

            if (is_array($rawVoluntarios)) {
                $idsVoluntarios = array_map('intval', $rawVoluntarios);
            } elseif (is_string($rawVoluntarios)) {
                $idsVoluntarios = array_map('intval', explode(',', $rawVoluntarios));
            } else {
                $idsVoluntarios = [(int)$rawVoluntarios];
            }

            $idsVoluntarios = array_values(array_unique(array_filter($idsVoluntarios, function($id) { return $id > 0; })));

            if (empty($data_culto) || $id_culto_padrao <= 0 || $id_departamento <= 0 || $id_area <= 0 || empty($idsVoluntarios)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Selecione a data, culto, sub-área e pelo menos um voluntário.']);
            }

            // Valida permissão do gestor para o departamento
            $departamentoGestorModel = new DepartamentoGestorModel();
            if (!$departamentoGestorModel->usuarioTemAcessoAoDepartamento($id_departamento, $data['usuario'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Você não possui permissão para gerenciar escalas deste departamento.']);
            }

            $voluntarioModel = new VoluntarioModel();
            $escalaModel     = new EscalaVoluntarioModel();
            $db              = db_connect();

            $anoCulto = (int)date('Y', strtotime($data_culto));
            $mesCulto = (int)date('m', strtotime($data_culto));

            $escaladosSucesso = 0;
            $erros = [];

            foreach ($idsVoluntarios as $id_voluntario) {
                $voluntario = $voluntarioModel->find($id_voluntario);
                if (!$voluntario) continue;

                // Validação de Conflito de Agenda em Outro Departamento no mesmo culto/data
                $conflito = $escalaModel->getConflitoOutroDepartamento($id_voluntario, $data_culto, $id_culto_padrao, $id_departamento);
                if ($conflito) {
                    $deptoUpper = strtoupper(trim($conflito->nome_departamento));
                    $areaUpper  = strtoupper(trim($conflito->nome_area));
                    $cultoNome  = !empty($conflito->titulo_culto) ? trim($conflito->titulo_culto) : '';
                    $horaCulto  = !empty($conflito->horario_inicio) ? substr($conflito->horario_inicio, 0, 5) : '';
                    $detalheCulto = '';
                    if ($cultoNome && $horaCulto) {
                        $detalheCulto = " ({$cultoNome} às {$horaCulto})";
                    } elseif ($horaCulto) {
                        $detalheCulto = " (às {$horaCulto})";
                    } elseif ($cultoNome) {
                        $detalheCulto = " ({$cultoNome})";
                    }
                    $erros[] = "O voluntário '{$voluntario->nome}' não pode ser escalado, pois já tem uma agenda confirmada para este dia/culto{$detalheCulto}:<br>• {$deptoUpper} {$areaUpper}";
                    continue;
                }

                // Validação de Limite Máximo Mensal de Escalas
                if (!empty($voluntario->max_escalas_mes) && (int)$voluntario->max_escalas_mes > 0) {
                    $maxPermitido = (int)$voluntario->max_escalas_mes;

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
                            $erros[] = "Voluntário '{$voluntario->nome}' atingiu o limite mensal ({$totalMes}/{$maxPermitido}).";
                            continue;
                        }
                    }
                }

                $id_escala = $escalaModel->escalarVoluntario($data_culto, $id_culto_padrao, $id_departamento, $id_area, $id_voluntario, $observacao);
                if ($id_escala) {
                    $escaladosSucesso++;
                }
            }

            if ($escaladosSucesso > 0) {
                $msg = ($escaladosSucesso === 1) 
                    ? '1 voluntário escalado com sucesso!' 
                    : "{$escaladosSucesso} voluntários escalados com sucesso!";
                
                if (!empty($erros)) {
                    $msg .= ' Atenção: ' . implode(' ', $erros);
                }

                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => $msg,
                    'total'     => $escaladosSucesso
                ]);
            }

            $erroMsg = !empty($erros) ? implode(' ', $erros) : 'Não foi possível escalar os voluntários selecionados.';
            return $this->response->setJSON(['status' => 'error', 'message' => $erroMsg]);
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
            $escala = $escalaModel->find($id_escala_voluntario);

            if (!$escala) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Escala não encontrada.']);
            }

            // Validação de Bloqueio Retroativo
            $hojeIso = date('Y-m-d');
            if (!empty($escala->data_culto) && $escala->data_culto < $hojeIso && empty($data['sys_action']->update_past)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Bloqueio Retroativo: Você não possui permissão para remover escalas de datas passadas.'
                ]);
            }

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
            $escala = $escalaModel->find($id_escala_voluntario);

            if (!$escala) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Escala não encontrada.']);
            }

            // Validação de Bloqueio Retroativo
            $hojeIso = date('Y-m-d');
            if (!empty($escala->data_culto) && $escala->data_culto < $hojeIso && empty($data['sys_action']->update_past)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Bloqueio Retroativo: Você não possui permissão para alterar presença em escalas de datas passadas.'
                ]);
            }

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
        $data = $this->session();
        $id_departamento = (int)$this->request->getGet('id_departamento');
        $id_area         = (int)$this->request->getGet('id_area');
        $data_culto      = (string)$this->request->getGet('data_culto');
        $id_culto_padrao = (int)$this->request->getGet('id_culto_padrao');

        // Valida se o gestor tem acesso ao departamento
        $departamentoGestorModel = new DepartamentoGestorModel();
        if (!$departamentoGestorModel->usuarioTemAcessoAoDepartamento($id_departamento, $data['usuario'])) {
            return $this->response->setJSON([
                'status'              => 'error',
                'message'             => 'Acesso negado para este departamento.',
                'vinculados_area'     => [],
                'outros_departamento' => []
            ]);
        }

        $anoCulto = !empty($data_culto) ? (int)date('Y', strtotime($data_culto)) : (int)date('Y');
        $mesCulto = !empty($data_culto) ? (int)date('m', strtotime($data_culto)) : (int)date('m');

        $voluntarioAreaModel   = new VoluntarioAreaModel();
        $voluntarioModel       = new VoluntarioModel();
        $voluntarioCultoModel  = new VoluntarioCultoModel();
        $escalaVoluntarioModel = new EscalaVoluntarioModel();
        
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

        // Calcula total de escalas do mês, limite, disponibilidade do culto e conflito de agenda em outro departamento
        $enriquecerVoluntario = function(&$lista) use ($voluntarioModel, $voluntarioCultoModel, $escalaVoluntarioModel, $anoCulto, $mesCulto, $id_culto_padrao, $data_culto, $id_departamento) {
            foreach ($lista as &$v) {
                $totalNoMes = $voluntarioModel->countEscalasMes($v->id_voluntario, $anoCulto, $mesCulto);
                $maxMes = isset($v->max_escalas_mes) ? (int)$v->max_escalas_mes : 0;

                $v->total_escalas_mes = $totalNoMes;
                $v->max_escalas_mes   = $maxMes;
                $v->atingiu_limite    = ($maxMes > 0 && $totalNoMes >= $maxMes);

                // Disponibilidade de Cultos (Regra de Disponibilidade Total)
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

                // Conflito de agenda em outro departamento para esta mesma data e culto
                $conflito = null;
                if (!empty($data_culto) && $id_culto_padrao > 0) {
                    $conflito = $escalaVoluntarioModel->getConflitoOutroDepartamento((int)$v->id_voluntario, (string)$data_culto, (int)$id_culto_padrao, (int)$id_departamento);
                }
                $v->tem_conflito_agenda   = !empty($conflito);
                $v->conflito_departamento = $conflito ? strtoupper(trim($conflito->nome_departamento)) : null;
                $v->conflito_subarea      = $conflito ? strtoupper(trim($conflito->nome_area)) : null;
                $v->conflito_culto        = $conflito ? trim((string)($conflito->titulo_culto ?? '')) : null;
                $v->conflito_horario      = $conflito && !empty($conflito->horario_inicio) ? substr((string)$conflito->horario_inicio, 0, 5) : null;
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

        $departamentoGestorModel    = new DepartamentoGestorModel();
        $departamentosPermitidos    = $departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        if (empty($departamentosPermitidos)) {
            return redirect()->to('accessdeny');
        }

        $id_departamento = (int)$id_departamento;
        if ($id_departamento <= 0 || !in_array($id_departamento, $departamentosPermitidosIds)) {
            $id_departamento = (int)$departamentosPermitidos[0]->id_departamento;
        }

        $ano = (int)($ano ?: date('Y'));
        $mes = (int)($mes ?: date('m'));

        $departamentoModel     = new DepartamentoModel();
        $departamentoAreaModel = new DepartamentoAreaModel();
        $escalaModel           = new EscalaVoluntarioModel();
        $cultoPadraoModel      = new CultoPadraoModel();

        $departamento = $departamentoModel->find($id_departamento);
        if (!$departamento) {
            $departamento = $departamentosPermitidos[0];
            $id_departamento = $departamento->id_departamento;
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

    /**
     * Retorna a lista de voluntários com escalas pendentes de confirmação em cultos futuros
     */
    public function getPendentesConfirmacao()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso não autorizado.']);
        }

        $id_departamento = (int)$this->request->getGet('id_departamento');
        $ano = (int)($this->request->getGet('ano') ?: date('Y'));
        $mes = (int)($this->request->getGet('mes') ?: date('m'));

        if ($id_departamento <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Departamento inválido.']);
        }

        $departamentoGestorModel = new DepartamentoGestorModel();
        $departamentosPermitidos = $departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');
        if (!in_array($id_departamento, $departamentosPermitidosIds)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado para este departamento.']);
        }

        $escalaModel = new EscalaVoluntarioModel();
        $dataInicioMes = sprintf('%04d-%02d-01', $ano, $mes);
        $totalDias = (int)date('t', strtotime($dataInicioMes));
        $dataFimMes = sprintf('%04d-%02d-%02d', $ano, $mes, $totalDias);

        $escalasMes = $escalaModel->getEscalasMesPorDepartamento($id_departamento, $dataInicioMes, $dataFimMes);

        $agoraDataHora = date('Y-m-d H:i:s');
        $hoje = date('Y-m-d');
        $porVoluntario = [];
        $totalEscalasPendentes = 0;

        foreach ($escalasMes as $esc) {
            $statusConf = strtoupper((string)($esc->status_confirmacao ?: 'PENDENTE'));
            if ($statusConf !== 'PENDENTE') {
                continue;
            }

            $horaInicio = !empty($esc->horario_inicio) ? $esc->horario_inicio : '00:00:00';
            $cultoDataHora = $esc->data_culto . ' ' . $horaInicio;
            if ($cultoDataHora <= $agoraDataHora) {
                // Ignora cultos já ocorridos
                continue;
            }

            $idVol = (int)$esc->id_voluntario;
            $jaEnviadoHoje = (!empty($esc->data_ultimo_lembrete) && date('Y-m-d', strtotime($esc->data_ultimo_lembrete)) === $hoje);
            $horaUltimoLembrete = !empty($esc->data_ultimo_lembrete) ? date('H:i', strtotime($esc->data_ultimo_lembrete)) : null;

            if (!isset($porVoluntario[$idVol])) {
                $porVoluntario[$idVol] = [
                    'id_voluntario'        => $idVol,
                    'nome_voluntario'      => $esc->nome_voluntario,
                    'nickname'             => $esc->nickname ?? '',
                    'telefone_whatsapp'    => $esc->telefone_whatsapp ?? '',
                    'nome_departamento'    => $esc->nome_departamento ?? '',
                    'ja_enviado_hoje'      => $jaEnviadoHoje,
                    'hora_ultimo_lembrete' => $horaUltimoLembrete,
                    'ids_escala'           => [],
                    'escalas'              => []
                ];
            }

            if ($jaEnviadoHoje) {
                $porVoluntario[$idVol]['ja_enviado_hoje'] = true;
                $porVoluntario[$idVol]['hora_ultimo_lembrete'] = $horaUltimoLembrete;
            }

            $porVoluntario[$idVol]['ids_escala'][] = (int)$esc->id_escala_voluntario;
            $porVoluntario[$idVol]['escalas'][] = [
                'id_escala_voluntario' => (int)$esc->id_escala_voluntario,
                'nome_area'            => $esc->nome_area,
                'titulo_culto'         => $esc->titulo_culto ?: 'Culto',
                'data_culto'           => $esc->data_culto,
                'data_formatada'       => date('d/m/Y', strtotime($esc->data_culto)),
                'horario_inicio'       => substr($horaInicio, 0, 5),
                'horario_termino'      => !empty($esc->horario_termino) ? substr($esc->horario_termino, 0, 5) : '',
                'data_ultimo_lembrete' => $esc->data_ultimo_lembrete,
                'ja_enviado_hoje'      => $jaEnviadoHoje,
                'hora_ultimo_lembrete' => $horaUltimoLembrete,
            ];

            $totalEscalasPendentes++;
        }

        $listaVoluntarios = array_values($porVoluntario);

        return $this->response->setJSON([
            'status'        => 'success',
            'pendentes'     => $listaVoluntarios,
            'total'         => count($listaVoluntarios),
            'total_escalas' => $totalEscalasPendentes
        ]);
    }

    /**
     * Helper para formatar o texto do lembrete WhatsApp consolidando todas as escalas do voluntário
     */
    private function formatarMensagemCobranca(?string $template, object $vol, array $escalas): string
    {
        $defaultTemplate = "ADVEC: Olá, *[Nome]*! 🙌\n\n"
            . "Você possui escala(s) no departamento *[Departamento]*:\n"
            . "[Cultos]\n\n"
            . "Por favor, acesse o link abaixo para *confirmar sua presença* ou informar uma justificativa:\n"
            . "👉 [Link]\n\n"
            . "Contamos com você! Deus abençoe! 🙏";

        $msg = !empty(trim((string)$template)) ? trim((string)$template) : $defaultTemplate;

        $primeiroNome = explode(' ', trim($vol->nome_voluntario))[0];
        $linkPortal = base_url('portal/agenda');
        $departamentoNome = $vol->nome_departamento ?? 'ADVec';

        if (count($escalas) === 1) {
            $e = (object)$escalas[0];
            $dataFormatada = date('d/m/Y', strtotime($e->data_culto));
            $horaFormatada = substr($e->horario_inicio, 0, 5);
            $areaNome = $e->nome_area;
            $cultoNome = $e->titulo_culto;

            $blocoCultos = "🏛️ *Culto:* {$cultoNome}\n"
                         . "📅 *Data:* {$dataFormatada}\n"
                         . "⏰ *Horário:* {$horaFormatada} ({$areaNome})";
        } else {
            $linhasCultos = [];
            $areasNomes = [];
            foreach ($escalas as $escItem) {
                $e = (object)$escItem;
                $dataFormatada = date('d/m/Y', strtotime($e->data_culto));
                $horaFormatada = substr($e->horario_inicio, 0, 5);
                $linhasCultos[] = "• 📅 *{$dataFormatada} ({$horaFormatada})* - {$e->titulo_culto} ({$e->nome_area})";
                $areasNomes[$e->nome_area] = true;
            }
            $blocoCultos = "🏛️ *Cultos Escalados (" . count($escalas) . "):*\n" . implode("\n", $linhasCultos);
            $areaNome = implode(', ', array_keys($areasNomes));
            $cultoNome = count($escalas) . " Cultos";
            $dataFormatada = "Várias datas";
            $horaFormatada = "Vários horários";
        }

        // Se o usuário utilizou as tags antigas [Culto], [Data], [Horário], substitui o bloco completo se houver múltiplos cultos
        if (count($escalas) > 1 && strpos($msg, '[Cultos]') === false) {
            $msg = str_replace(
                [
                    "🏛️ *Culto:* [Culto]\n📅 *Data:* [Data]\n⏰ *Horário:* [Horário]",
                    "🏛️ *Culto:* [Culto]\n📅 *Data:* [Data]\n⏰ *Horário:* [Horario]",
                    "*Culto:* [Culto]\n*Data:* [Data]\n*Horário:* [Horário]"
                ],
                $blocoCultos,
                $msg
            );
        }

        $trocas = [
            '[Nome]'            => $primeiroNome,
            '[Nome Completo]'   => $vol->nome_voluntario,
            '[Departamento]'    => $departamentoNome,
            '[Cultos]'          => $blocoCultos,
            '[Sub-área]'        => $areaNome,
            '[Sub-area]'        => $areaNome,
            '[Subarea]'         => $areaNome,
            '[Area]'            => $areaNome,
            '[Culto]'           => (count($escalas) === 1 ? $cultoNome : $blocoCultos),
            '[Nome do Culto]'   => (count($escalas) === 1 ? $cultoNome : $blocoCultos),
            '[Data]'            => (count($escalas) === 1 ? $dataFormatada : ''),
            '[DD/MM/AAAA]'      => (count($escalas) === 1 ? $dataFormatada : ''),
            '[Horário]'         => (count($escalas) === 1 ? $horaFormatada : ''),
            '[Horario]'         => (count($escalas) === 1 ? $horaFormatada : ''),
            '[HH:MM]'           => (count($escalas) === 1 ? $horaFormatada : ''),
            '[Link]'            => $linkPortal,
            '[Link do Portal]'  => $linkPortal,
        ];

        $msgFinal = str_replace(array_keys($trocas), array_values($trocas), $msg);
        $msgFinal = preg_replace("/\n{3,}/", "\n\n", $msgFinal);

        return trim($msgFinal);
    }

    /**
     * Envia cobrança / lembrete de confirmação via WhatsApp consolidando todas as escalas pendentes do voluntário
     */
    public function enviarCobrancaIndividual()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permissão negada.']);
        }

        $id_escala = (int)$this->request->getPost('id_escala_voluntario');
        $id_voluntario = (int)$this->request->getPost('id_voluntario');
        $modeloMensagem = $this->request->getPost('modelo_mensagem');

        $db = db_connect();

        if ($id_voluntario <= 0 && $id_escala > 0) {
            $rowEsc = $db->table('tb_escala_voluntario')->select('id_voluntario, id_departamento, data_culto')->where('id_escala_voluntario', $id_escala)->get()->getFirstRow();
            if ($rowEsc) {
                $id_voluntario = (int)$rowEsc->id_voluntario;
                $id_departamento = (int)$rowEsc->id_departamento;
            }
        }

        if ($id_voluntario <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Voluntário não informado.']);
        }

        $agoraDataHora = date('Y-m-d H:i:s');
        $hoje = date('Y-m-d');

        // Busca todas as escalas pendentes futuras deste voluntário
        $builder = $db->table('tb_escala_voluntario as ev')
            ->select('
                ev.*,
                v.nome as nome_voluntario,
                v.telefone_whatsapp,
                d.nome as nome_departamento,
                a.nome_area,
                COALESCE(cp.nome_culto, "Culto") as titulo_culto,
                COALESCE(cp.horario_inicio, "00:00:00") as horario_inicio,
                COALESCE(cp.horario_termino, "00:00:00") as horario_termino
            ')
            ->join('tb_voluntario as v', 'v.id_voluntario = ev.id_voluntario', 'inner')
            ->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner')
            ->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner')
            ->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = ev.id_culto_padrao', 'left')
            ->where('ev.id_voluntario', $id_voluntario)
            ->where('ev.status_confirmacao', 'PENDENTE')
            ->orderBy('ev.data_culto', 'ASC')
            ->orderBy('cp.horario_inicio', 'ASC');

        if (!empty($id_departamento)) {
            $builder->where('ev.id_departamento', $id_departamento);
        }

        $todasEscalas = $builder->get()->getResult('object');

        // Filtra apenas cultos futuros
        $escalasFuturas = [];
        $jaEnviadoHoje = false;
        $horaEnvioHoje = null;
        $volInfo = null;

        foreach ($todasEscalas as $e) {
            $cultoDt = $e->data_culto . ' ' . $e->horario_inicio;
            if ($cultoDt > $agoraDataHora) {
                $escalasFuturas[] = $e;
                $volInfo = $e;
                if (!empty($e->data_ultimo_lembrete) && date('Y-m-d', strtotime($e->data_ultimo_lembrete)) === $hoje) {
                    $jaEnviadoHoje = true;
                    $horaEnvioHoje = date('H:i', strtotime($e->data_ultimo_lembrete));
                }
            }
        }

        if (empty($escalasFuturas) || !$volInfo) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nenhuma escala futura pendente encontrada para este voluntário.']);
        }

        if (empty($volInfo->telefone_whatsapp)) {
            return $this->response->setJSON(['status' => 'error', 'message' => "O voluntário '{$volInfo->nome_voluntario}' não possui número de WhatsApp cadastrado."]);
        }

        if ($jaEnviadoHoje) {
            return $this->response->setJSON([
                'status'  => 'warning',
                'message' => "O lembrete para {$volInfo->nome_voluntario} já foi enviado hoje às {$horaEnvioHoje}. Para evitar excesso de mensagens, é permitido apenas um envio por dia."
            ]);
        }

        $mensagem = $this->formatarMensagemCobranca($modeloMensagem, $volInfo, $escalasFuturas);

        $envio = WebhookService::dispararMensagemWhatsApp($volInfo->telefone_whatsapp, $mensagem);

        if ($envio['success']) {
            $idsAtualizar = array_column($escalasFuturas, 'id_escala_voluntario');
            $db->table('tb_escala_voluntario')
                ->whereIn('id_escala_voluntario', $idsAtualizar)
                ->update(['data_ultimo_lembrete' => date('Y-m-d H:i:s')]);

            $qtd = count($escalasFuturas);
            $msgSucesso = ($qtd > 1) 
                ? "Lembrete com {$qtd} escalas enviado com sucesso para {$volInfo->nome_voluntario} em 1 mensagem!"
                : "Lembrete enviado com sucesso para {$volInfo->nome_voluntario}!";

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $msgSucesso
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => "Falha ao enviar WhatsApp: " . ($envio['message'] ?? 'Erro no envio.')
            ]);
        }
    }

    /**
     * Processa o envio em lote agrupando escalas por voluntário (1 única mensagem por voluntário)
     */
    public function enviarCobrancaLote()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permissão negada.']);
        }

        $idsVoluntario = $this->request->getPost('ids_voluntario');
        $idsEscala = $this->request->getPost('ids_escala');
        $modeloMensagem = $this->request->getPost('modelo_mensagem');

        $db = db_connect();
        $builder = $db->table('tb_escala_voluntario as ev')
            ->select('
                ev.*,
                v.nome as nome_voluntario,
                v.telefone_whatsapp,
                d.nome as nome_departamento,
                a.nome_area,
                COALESCE(cp.nome_culto, "Culto") as titulo_culto,
                COALESCE(cp.horario_inicio, "00:00:00") as horario_inicio
            ')
            ->join('tb_voluntario as v', 'v.id_voluntario = ev.id_voluntario', 'inner')
            ->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner')
            ->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner')
            ->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = ev.id_culto_padrao', 'left')
            ->where('ev.status_confirmacao', 'PENDENTE');

        if (!empty($idsVoluntario) && is_array($idsVoluntario)) {
            $idsVoluntario = array_slice(array_map('intval', $idsVoluntario), 0, 10);
            $builder->whereIn('ev.id_voluntario', $idsVoluntario);
        } elseif (!empty($idsEscala) && is_array($idsEscala)) {
            $idsEscala = array_map('intval', $idsEscala);
            $builder->whereIn('ev.id_escala_voluntario', $idsEscala);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nenhum voluntário selecionado para envio.']);
        }

        $escalas = $builder->orderBy('ev.data_culto', 'ASC')->get()->getResult('object');

        $agoraDataHora = date('Y-m-d H:i:s');
        $hoje = date('Y-m-d');

        // Agrupa por voluntário
        $gruposPorVoluntario = [];
        foreach ($escalas as $esc) {
            $cultoDataHora = $esc->data_culto . ' ' . $esc->horario_inicio;
            if ($cultoDataHora <= $agoraDataHora) {
                continue;
            }

            $idV = (int)$esc->id_voluntario;
            if (!isset($gruposPorVoluntario[$idV])) {
                $gruposPorVoluntario[$idV] = [
                    'vol'     => $esc,
                    'escalas' => []
                ];
            }
            $gruposPorVoluntario[$idV]['escalas'][] = $esc;
        }

        // Limita a 10 voluntários por requisição
        $gruposPorVoluntario = array_slice($gruposPorVoluntario, 0, 10, true);

        $enviados = 0;
        $falhas = 0;
        $detalhes = [];

        foreach ($gruposPorVoluntario as $idV => $grupo) {
            $vol = $grupo['vol'];
            $listaEscalas = $grupo['escalas'];

            if (empty($vol->telefone_whatsapp)) {
                $falhas++;
                $detalhes[] = "{$vol->nome_voluntario}: Sem WhatsApp cadastrado.";
                continue;
            }

            // Verifica se algum já foi enviado hoje
            $jaEnviadoHoje = false;
            $horaEnvio = '';
            foreach ($listaEscalas as $e) {
                if (!empty($e->data_ultimo_lembrete) && date('Y-m-d', strtotime($e->data_ultimo_lembrete)) === $hoje) {
                    $jaEnviadoHoje = true;
                    $horaEnvio = date('H:i', strtotime($e->data_ultimo_lembrete));
                    break;
                }
            }

            if ($jaEnviadoHoje) {
                $falhas++;
                $detalhes[] = "{$vol->nome_voluntario}: Lembrete já enviado hoje às {$horaEnvio} (ignorado).";
                continue;
            }

            $mensagem = $this->formatarMensagemCobranca($modeloMensagem, $vol, $listaEscalas);

            $envio = WebhookService::dispararMensagemWhatsApp($vol->telefone_whatsapp, $mensagem);
            if ($envio['success']) {
                $enviados++;
                $qtdEsc = count($listaEscalas);
                $detalhes[] = "{$vol->nome_voluntario}: Enviado com sucesso ({$qtdEsc} escala" . ($qtdEsc > 1 ? 's' : '') . " em 1 mensagem).";

                $idsEscalasVol = array_column($listaEscalas, 'id_escala_voluntario');
                $db->table('tb_escala_voluntario')
                    ->whereIn('id_escala_voluntario', $idsEscalasVol)
                    ->update(['data_ultimo_lembrete' => date('Y-m-d H:i:s')]);
            } else {
                $falhas++;
                $detalhes[] = "{$vol->nome_voluntario}: Falha no envio.";
            }
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'enviados' => $enviados,
            'falhas'   => $falhas,
            'detalhes' => $detalhes,
            'message'  => "Lote processado: {$enviados} voluntário(s) notificado(s)" . ($falhas > 0 ? ", {$falhas} ignorado(s)/falha(s)." : ".")
        ]);
    }
}
