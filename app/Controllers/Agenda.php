<?php

namespace App\Controllers;

use App\Models\CultoModel;
use App\Models\CultoConvidadoModel;
use App\Models\ConvidadoModel;
use App\Models\SessionModel;

class Agenda extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'agenda/');
        return $data;
    }

    /**
     * Tela Principal da Agenda com Calendário Visual
     */
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $convidadoModel = new ConvidadoModel();
        $data['convidados'] = $convidadoModel->listaConvidados();

        $db = db_connect();
        $data['funcoesEclesiasticas'] = $db->table('tb_funcao_eclesiastica')
            ->orderBy('nm_funcao_eclesiastica', 'ASC')
            ->get()
            ->getResult();

        $cultoPadraoModel = new \App\Models\CultoPadraoModel();
        $data['cultosPadrao'] = $cultoPadraoModel->getCultosPadraoAtivos();

        $data['content_view'] = view('agenda/agenda-main', $data);
        return view('_layout', $data);
    }

    /**
     * Retorna lista de eventos em JSON para o Calendário
     */
    public function events()
    {
        $start = $this->request->getGet('start');
        $end   = $this->request->getGet('end');

        $cultoModel = new CultoModel();
        $events = $cultoModel->getCultosEvents($start, $end);

        return $this->response->setJSON($events);
    }

    /**
     * Salva ou atualiza um culto/evento
     */
    public function salvarCulto()
    {
        try {
            $id_culto       = (int)$this->request->getPost('id_culto');
            $titulo_culto   = trim($this->request->getPost('titulo_culto') ?? '');
            $data_culto     = trim($this->request->getPost('data_culto') ?? '');
            $horario_inicio = trim($this->request->getPost('horario_inicio') ?? '');
            $horario_termino= trim($this->request->getPost('horario_termino') ?? '');
            $cor_evento     = trim($this->request->getPost('cor_evento') ?? '');
            $descricao      = trim($this->request->getPost('descricao') ?? '');

            if (empty($titulo_culto) || empty($data_culto) || empty($horario_inicio) || empty($horario_termino)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Preencha todos os campos obrigatórios!']);
            }

            if (strlen($horario_inicio) === 5) {
                $horario_inicio .= ':00';
            }
            if (strlen($horario_termino) === 5) {
                $horario_termino .= ':00';
            }

            $cultoModel = new CultoModel();

            $saveData = [
                'id_filial'       => 1,
                'titulo_culto'   => $titulo_culto,
                'data_culto'     => $data_culto,
                'horario_inicio' => $horario_inicio,
                'horario_termino'=> $horario_termino,
                'cor_evento'     => !empty($cor_evento) ? $cor_evento : '#2563eb',
                'descricao'      => $descricao,
                'status_culto'   => 1
            ];

            if ($id_culto > 0) {
                $cultoModel->update($id_culto, $saveData);
                $msg = 'Culto atualizado com sucesso!';
            } else {
                $id_culto = $cultoModel->insert($saveData);
                if (!$id_culto) {
                    $dbErrors = $cultoModel->errors();
                    $errorMsg = !empty($dbErrors) ? implode(', ', $dbErrors) : 'Erro de banco de dados ao salvar culto.';
                    return $this->response->setJSON(['status' => 'error', 'message' => $errorMsg]);
                }
                $msg = 'Culto cadastrado com sucesso!';
            }

            return $this->response->setJSON(['status' => 'success', 'message' => $msg, 'id_culto' => $id_culto]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao salvar culto: ' . $e->getMessage()]);
        }
    }

    /**
     * Exclui um culto
     */
    public function excluirCulto($id_culto = null)
    {
        try {
            if (!$id_culto) {
                $id_culto = (int)$this->request->getPost('id_culto');
            }

            $cultoModel = new CultoModel();
            if ($id_culto > 0) {
                $db = db_connect();
                $db->table('tb_culto_convidado')->where('id_culto', $id_culto)->delete();
                $cultoModel->delete($id_culto);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Culto excluído com sucesso!']);
            }

            return $this->response->setJSON(['status' => 'error', 'message' => 'Culto não encontrado.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao excluir culto: ' . $e->getMessage()]);
        }
    }

    /**
     * Retorna convidados vinculados a um culto
     */
    public function getConvidadosCulto($id_culto)
    {
        try {
            $cultoConvidadoModel = new CultoConvidadoModel();
            $convidados = $cultoConvidadoModel->getConvidadosDoCulto((int)$id_culto);
            return $this->response->setJSON($convidados);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Vincula múltiplos convidados a um culto
     */
    public function vincularConvidados()
    {
        try {
            $id_culto = (int)$this->request->getPost('id_culto');
            $ids_convidados = $this->request->getPost('ids_convidados');

            if (!$id_culto || empty($ids_convidados) || !is_array($ids_convidados)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Selecione ao menos um convidado.']);
            }

            $cultoConvidadoModel = new CultoConvidadoModel();
            $totalAdicionados = 0;

            foreach ($ids_convidados as $id_convidado) {
                $id_convidado = (int)$id_convidado;
                $exists = $cultoConvidadoModel->where(['id_culto' => $id_culto, 'id_convidado' => $id_convidado])->countAllResults();
                if ($exists == 0) {
                    $cultoConvidadoModel->insert([
                        'id_culto'        => $id_culto,
                        'id_convidado'    => $id_convidado,
                        'status_presenca' => 1
                    ]);
                    $totalAdicionados++;
                }
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "{$totalAdicionados} convidado(s) vinculado(s) com sucesso!"
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao vincular convidados: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove um convidado do culto
     */
    public function removerConvidado()
    {
        try {
            $id_culto_convidado = (int)$this->request->getPost('id_culto_convidado');
            $id_culto           = (int)$this->request->getPost('id_culto');
            $id_convidado       = (int)$this->request->getPost('id_convidado');

            $db = db_connect();

            if ($id_culto_convidado > 0) {
                $db->table('tb_culto_convidado')->where('id_culto_convidado', $id_culto_convidado)->delete();
                return $this->response->setJSON(['status' => 'success', 'message' => 'Convidado desvinculado com sucesso!']);
            } elseif ($id_culto > 0 && $id_convidado > 0) {
                $db->table('tb_culto_convidado')->where('id_culto', $id_culto)->where('id_convidado', $id_convidado)->delete();
                return $this->response->setJSON(['status' => 'success', 'message' => 'Convidado desvinculado com sucesso!']);
            }

            return $this->response->setJSON(['status' => 'error', 'message' => 'Parâmetros inválidos.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao remover convidado: ' . $e->getMessage()]);
        }
    }

    /**
     * Alterna o status de presença do convidado (1: Presente / 0: Ausente)
     */
    public function alternarPresenca()
    {
        try {
            $id_culto_convidado = (int)$this->request->getPost('id_culto_convidado');
            $id_culto           = (int)$this->request->getPost('id_culto');
            $id_convidado       = (int)$this->request->getPost('id_convidado');
            $status_presenca    = $this->request->getPost('status_presenca');

            $db = db_connect();

            if ($id_culto_convidado > 0) {
                $status = ($status_presenca !== null && $status_presenca !== '') ? (int)$status_presenca : null;
                if ($status === null) {
                    $curr = $db->table('tb_culto_convidado')->where('id_culto_convidado', $id_culto_convidado)->get()->getRow();
                    $status = ($curr && $curr->status_presenca == 1) ? 0 : 1;
                }
                $db->table('tb_culto_convidado')->where('id_culto_convidado', $id_culto_convidado)->update(['status_presenca' => $status]);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Presença atualizada!']);
            } elseif ($id_culto > 0 && $id_convidado > 0) {
                $curr = $db->table('tb_culto_convidado')->where('id_culto', $id_culto)->where('id_convidado', $id_convidado)->get()->getRow();
                if ($curr) {
                    $status = ($curr->status_presenca == 1) ? 0 : 1;
                    $db->table('tb_culto_convidado')->where('id_culto_convidado', $curr->id_culto_convidado)->update(['status_presenca' => $status]);
                } else {
                    $db->table('tb_culto_convidado')->insert([
                        'id_culto' => $id_culto,
                        'id_convidado' => $id_convidado,
                        'status_presenca' => 1
                    ]);
                }
                return $this->response->setJSON(['status' => 'success', 'message' => 'Presença atualizada com sucesso!']);
            }

            return $this->response->setJSON(['status' => 'error', 'message' => 'Parâmetros inválidos.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao alternar presença: ' . $e->getMessage()]);
        }
    }

    /**
     * Dashboard do Convidado (estatísticas de presença nos cultos)
     */
    public function dashConvidado($id_convidado)
    {
        $data = $this->session();

        $convidadoModel = new ConvidadoModel();
        $convidado = $convidadoModel->find($id_convidado);

        if (!$convidado) {
            return redirect()->to('convidado')->with('error', 'Convidado não encontrado.');
        }

        $cultoConvidadoModel = new CultoConvidadoModel();
        $stats = $cultoConvidadoModel->getDashConvidadoStats($id_convidado);

        $data['convidado'] = $convidado;
        $data['stats']     = $stats;

        $data['content_view'] = view('agenda/dash-convidado', $data);
        return view('_layout', $data);
    }

    /**
     * Visão Dinâmica de Grade Mensal da Agenda (Estilo Planilha / Matriz Responsiva)
     */
    public function grade($ano = null, $mes = null)
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $ano = (int)($ano ?: date('Y'));
        $mes = (int)($mes ?: date('m'));

        if ($mes < 1) $mes = 1;
        if ($mes > 12) $mes = 12;

        $primeiroDia = sprintf('%04d-%02d-01', $ano, $mes);
        $totalDias   = (int)date('t', strtotime($primeiroDia));

        $cultoModel          = new CultoModel();
        $cultoConvidadoModel = new CultoConvidadoModel();
        $convidadoModel      = new ConvidadoModel();
        $cultoPadraoModel    = new \App\Models\CultoPadraoModel();

        // Todos os convidados para o autocomplete/select
        $convidados = $convidadoModel->listaConvidados();

        $db = db_connect();
        $data['funcoesEclesiasticas'] = $db->table('tb_funcao_eclesiastica')
            ->orderBy('nm_funcao_eclesiastica', 'ASC')
            ->get()
            ->getResult();

        // Todos os tipos de cultos padrão cadastrados
        $cultosPadrao = $cultoPadraoModel->getCultosPadraoAtivos();

        // Indexa cultos padrão por dia da semana [0..6]
        $cultosPadraoPorDia = [];
        foreach ($cultosPadrao as $cp) {
            $cultosPadraoPorDia[$cp->dia_semana][] = $cp;
        }

        // Busca todos os agendamentos existentes no mês selecionado
        $dataInicioMes = sprintf('%04d-%02d-01', $ano, $mes);
        $dataFimMes    = sprintf('%04d-%02d-%02d', $ano, $mes, $totalDias);

        $db = db_connect();
        $cultosExistentes = $db->table('tb_agenda_culto as c')
            ->select('c.*')
            ->where('c.status_culto', 1)
            ->where('c.data_culto >=', $dataInicioMes)
            ->where('c.data_culto <=', $dataFimMes)
            ->orderBy('c.data_culto', 'ASC')
            ->orderBy('c.horario_inicio', 'ASC')
            ->get()
            ->getResult();

        // Indexa cultos existentes por data [YYYY-MM-DD] e calcula métricas do Dashboard
        $cultosPorData = [];
        $totalCultosMes = 0;
        $cultosComConvidado = 0;
        $cultosSemConvidado = 0;
        $totalConvidadosConfirmados = 0;

        foreach ($cultosExistentes as $c) {
            $totalCultosMes++;
            $c->convidadosVinculados = $cultoConvidadoModel->getConvidadosDoCulto($c->id_culto);
            
            if (!empty($c->convidadosVinculados)) {
                $cultosComConvidado++;
                $totalConvidadosConfirmados += count($c->convidadosVinculados);
            } else {
                $cultosSemConvidado++;
            }
            
            $cultosPorData[$c->data_culto][] = $c;
        }

        $percentualPreenchido = $totalCultosMes > 0 ? round(($cultosComConvidado / $totalCultosMes) * 100) : 0;

        $data['totalCultosMes']             = $totalCultosMes;
        $data['cultosComConvidado']         = $cultosComConvidado;
        $data['cultosSemConvidado']         = $cultosSemConvidado;
        $data['totalConvidadosConfirmados'] = $totalConvidadosConfirmados;
        $data['percentualPreenchido']       = $percentualPreenchido;

        // Monta a estrutura da grade do mês dia a dia (1..totalDias)
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
            $dataIso = sprintf('%04d-%02d-%02d', $ano, $mes, $d);
            $numDiaSemana = (int)date('w', strtotime($dataIso)); // 0=Dom, 6=Sáb

            // Filtra cultos sugeridos usando as regras de recorrência do modelo
            $sugeridos = [];
            if (!empty($cultosPadraoPorDia[$numDiaSemana])) {
                foreach ($cultosPadraoPorDia[$numDiaSemana] as $cp) {
                    if (\App\Models\CultoPadraoModel::isOcorrenciaCultoValida($cp, $dataIso)) {
                        $sugeridos[] = $cp;
                    }
                }
            }

            $diasGrade[] = [
                'dia_num'          => sprintf('%02d', $d),
                'data_formatada'   => sprintf('%02d/%02d/%04d', $d, $mes, $ano),
                'data_iso'         => $dataIso,
                'num_dia_semana'   => $numDiaSemana,
                'nome_dia_semana'  => $diasSemanaNomes[$numDiaSemana],
                'is_fim_semana'    => ($numDiaSemana === 0 || $numDiaSemana === 6),
                'cultos_agendados' => $cultosPorData[$dataIso] ?? [],
                'cultos_sugeridos' => $sugeridos
            ];
        }

        $data['ano']          = $ano;
        $data['mes']          = $mes;
        $data['totalDias']    = $totalDias;
        $data['convidados']   = $convidados;
        $data['cultosPadrao'] = $cultosPadrao;
        $data['diasGrade']    = $diasGrade;

        $data['content_view'] = view('agenda/agenda-grade', $data);
        return view('_layout', $data);
    }

    /**
     * Gera agendamentos automáticos na agenda a partir dos cultos padrão para o mês/ano informado
     */
    public function gerarGradeMes()
    {
        try {
            $ano = (int)$this->request->getPost('ano');
            $mes = (int)$this->request->getPost('mes');

            if ($ano < 2020 || $mes < 1 || $mes > 12) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Mês e ano inválidos.']);
            }

            $primeiroDia = sprintf('%04d-%02d-01', $ano, $mes);
            $totalDias   = (int)date('t', strtotime($primeiroDia));

            $cultoPadraoModel = new \App\Models\CultoPadraoModel();
            $cultosPadrao     = $cultoPadraoModel->getCultosPadraoAtivos();

            if (empty($cultosPadrao)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Nenhum tipo de culto padrão cadastrado.']);
            }

            // Indexa cultos padrão por dia da semana
            $cultosPadraoPorDia = [];
            foreach ($cultosPadrao as $cp) {
                $cultosPadraoPorDia[$cp->dia_semana][] = $cp;
            }

            $db = db_connect();
            $gerados = 0;

            for ($d = 1; $d <= $totalDias; $d++) {
                $dataIso      = sprintf('%04d-%02d-%02d', $ano, $mes, $d);
                $numDiaSemana = (int)date('w', strtotime($dataIso));

                if (!empty($cultosPadraoPorDia[$numDiaSemana])) {
                    foreach ($cultosPadraoPorDia[$numDiaSemana] as $cp) {
                        // Validação das regras de recorrência (ex: 3ª Quinta, Santa Ceia Domingo)
                        if (!\App\Models\CultoPadraoModel::isOcorrenciaCultoValida($cp, $dataIso)) {
                            continue;
                        }

                        // Previne duplicar se já existir um culto com o mesmo nome na mesma data
                        $exists = $db->table('tb_agenda_culto')
                            ->where('data_culto', $dataIso)
                            ->where('titulo_culto', $cp->nome_culto)
                            ->where('status_culto', 1)
                            ->countAllResults();

                        if ($exists == 0) {
                            $db->table('tb_agenda_culto')->insert([
                                'id_filial'       => 1,
                                'id_culto_padrao' => $cp->id_culto_padrao,
                                'titulo_culto'    => $cp->nome_culto,
                                'data_culto'      => $dataIso,
                                'horario_inicio'  => $cp->horario_inicio,
                                'horario_termino' => $cp->horario_termino,
                                'descricao'       => $cp->descricao,
                                'cor_evento'      => $cp->cor_evento ?: '#2563eb',
                                'status_culto'    => 1
                            ]);
                            $gerados++;
                        }
                    }
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Grade mensal gerada com sucesso! {$gerados} novos agendamentos criados."
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao gerar grade: ' . $e->getMessage()]);
        }
    }

    /**
     * Cria ou atualiza um agendamento rapidamente a partir da visão de Grade Mensal
     */
    public function salvarRapidoAgenda()
    {
        try {
            $id_culto       = (int)$this->request->getPost('id_culto');
            $data_culto     = (string)$this->request->getPost('data_culto');
            $titulo_culto   = trim((string)$this->request->getPost('titulo_culto'));
            $horario_inicio = (string)$this->request->getPost('horario_inicio') ?: '19:00';
            $horario_termino = (string)$this->request->getPost('horario_termino') ?: '21:00';
            $cor_evento     = (string)$this->request->getPost('cor_evento') ?: '#2563eb';
            $ids_convidados = $this->request->getPost('ids_convidados') ?: [];

            if (empty($titulo_culto) || empty($data_culto)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Título e data do culto são obrigatórios.']);
            }

            $db = db_connect();

            if ($id_culto > 0) {
                // Atualiza existente
                $db->table('tb_agenda_culto')->where('id_culto', $id_culto)->update([
                    'titulo_culto'    => $titulo_culto,
                    'horario_inicio'  => $horario_inicio,
                    'horario_termino' => $horario_termino,
                    'cor_evento'      => $cor_evento
                ]);
            } else {
                // Cria novo
                $db->table('tb_agenda_culto')->insert([
                    'id_filial'       => 1,
                    'titulo_culto'    => $titulo_culto,
                    'data_culto'      => $data_culto,
                    'horario_inicio'  => $horario_inicio,
                    'horario_termino' => $horario_termino,
                    'cor_evento'      => $cor_evento,
                    'status_culto'    => 1
                ]);
                $id_culto = $db->insertID();
            }

            // Atualiza vínculo de convidados se informado
            if (!empty($ids_convidados) && is_array($ids_convidados)) {
                $cultoConvidadoModel = new CultoConvidadoModel();
                $cultoConvidadoModel->vincularConvidados($id_culto, $ids_convidados);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Agendamento salvo com sucesso!']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao salvar: ' . $e->getMessage()]);
        }
    }

    /**
     * Gera relatório de impressão elegante em A4 / PDF da Agenda Mensal do mês consultado
     */
    public function imprimir($ano = null, $mes = null)
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $ano = (int)($ano ?: date('Y'));
        $mes = (int)($mes ?: date('m'));

        if ($ano < 2020 || $mes < 1 || $mes > 12) {
            $ano = (int)date('Y');
            $mes = (int)date('m');
        }

        $primeiroDia = sprintf('%04d-%02d-01', $ano, $mes);
        $totalDias   = (int)date('t', strtotime($primeiroDia));

        $cultoConvidadoModel = new CultoConvidadoModel();

        $dataInicioMes = sprintf('%04d-%02d-01', $ano, $mes);
        $dataFimMes    = sprintf('%04d-%02d-%02d', $ano, $mes, $totalDias);

        $db = db_connect();
        $cultosExistentes = $db->table('tb_agenda_culto as c')
            ->select('c.*')
            ->where('c.status_culto', 1)
            ->where('c.data_culto >=', $dataInicioMes)
            ->where('c.data_culto <=', $dataFimMes)
            ->orderBy('c.data_culto', 'ASC')
            ->orderBy('c.horario_inicio', 'ASC')
            ->get()
            ->getResult();

        $cultosPorData = [];
        foreach ($cultosExistentes as $c) {
            $c->convidadosVinculados = $cultoConvidadoModel->getConvidadosDoCulto($c->id_culto);
            $cultosPorData[$c->data_culto][] = $c;
        }

        $diasGrade = [];
        $diasSemanaNomes = [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado'
        ];

        $mesesNomesExtenso = [
            1 => 'JANEIRO', 2 => 'FEVEREIRO', 3 => 'MARÇO', 4 => 'ABRIL',
            5 => 'MAIO', 6 => 'JUNHO', 7 => 'JULHO', 8 => 'AGOSTO',
            9 => 'SETEMBRO', 10 => 'OUTUBRO', 11 => 'NOVEMBRO', 12 => 'DEZEMBRO'
        ];

        for ($d = 1; $d <= $totalDias; $d++) {
            $dataIso      = sprintf('%04d-%02d-%02d', $ano, $mes, $d);
            $numDiaSemana = (int)date('w', strtotime($dataIso));

            $diasGrade[] = [
                'dia_num'          => sprintf('%02d', $d),
                'data_formatada'   => sprintf('%02d/%02d/%04d', $d, $mes, $ano),
                'data_iso'         => $dataIso,
                'num_dia_semana'   => $numDiaSemana,
                'nome_dia_semana'  => $diasSemanaNomes[$numDiaSemana],
                'is_fim_semana'    => ($numDiaSemana === 0 || $numDiaSemana === 6),
                'cultos_agendados' => $cultosPorData[$dataIso] ?? []
            ];
        }

        $data['ano']               = $ano;
        $data['mes']               = $mes;
        $data['nomeMesExtenso']    = $mesesNomesExtenso[$mes];
        $data['diasGrade']         = $diasGrade;

        return view('agenda/agenda-imprimir', $data);
    }
}
