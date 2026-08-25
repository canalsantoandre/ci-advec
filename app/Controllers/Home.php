<?php

namespace App\Controllers;

use App\Models\CultoModel;
use App\Models\CultoConvidadoModel;
use App\Models\ConvidadoModel;
use App\Models\SessionModel;

class Home extends BaseController
{
    private function session()
    {
        /* ------------------------------------------------ */
        /* RECUPERA ACESSOS E DADOS DE ACAO DAS TELAS */
        /* ------------------------------------------------ */
        $data = [];
        $session = new \App\Models\SessionModel();
        $data = $session->retornaSessao($data, 'dashboard/');

        return $data;
    }

    public function index()
    {
        $data = $this->session();

        if ($data['usuario']->content_view_default == 'sysadm') {
            $data['content_view'] = $this->sysadm($data);
        } else {

            $data['content_view'] = $this->grade($data); //view($data['usuario']->content_view_default, $data);
        }
        return view('_layout', $data);
    }


    public function sysadm($data)
    {
        return  view($data['usuario']->content_view_default, $data);
    }

    public function grade($data, $ano = null, $mes = null)
    {

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

        return view('agenda/agenda-grade', $data);
    }
}
