<?php

namespace App\Models;

use CodeIgniter\Model;

class CultoPadraoModel extends Model
{
    protected $table            = 'tb_culto_padrao';
    protected $primaryKey       = 'id_culto_padrao';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_filial',
        'nome_culto',
        'dia_semana',
        'horario_inicio',
        'horario_termino',
        'descricao',
        'cor_evento',
        'tipo_recorrencia',
        'posicao_semana',
        'status_culto'
    ];

    protected $useTimestamps    = false;

    /**
     * Retorna array formatado com os dias da semana em português
     */
    public static function getDiasSemana()
    {
        return [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado'
        ];
    }

    /**
     * Retorna o nome por extenso do dia da semana
     */
    public static function getNomeDiaSemana($dia)
    {
        $dias = self::getDiasSemana();
        return $dias[(int)$dia] ?? 'Dia Não Definido';
    }

    /**
     * Retorna lista de tipos de recorrência disponíveis
     */
    public static function getTiposRecorrencia()
    {
        return [
            'todas'                      => 'Todas as Semanas do Mês',
            'apenas_posicao'             => 'Apenas em uma Ocorrência Específica (ex: 3ª Quinta)',
            'exceto_posicao'            => 'Todas as Semanas EXCETO uma Ocorrência Específica',
            'santa_ceia_domingo'        => 'Santa Ceia Domingo (1º Domingo ou 2º Domingo se dia 1)',
            'exceto_santa_ceia_domingo' => 'Todos os Domingos EXCETO no Domingo de Santa Ceia'
        ];
    }

    /**
     * Regra Especial de Santa Ceia no Domingo:
     * Ocorre no 1º domingo do mês, EXCETO se o 1º domingo for o Dia 01 (vai para o 2º domingo / Dia 08).
     */
    public static function isSantaCeiaDomingo($dataIso)
    {
        $time = strtotime($dataIso);
        $numDiaSemana = (int)date('w', $time);
        if ($numDiaSemana !== 0) return false;

        $diaNumero = (int)date('j', $time);
        $ano = (int)date('Y', $time);
        $mes = (int)date('m', $time);

        // Verifica qual dia da semana foi o dia 1 do mês
        $primeiroDiaMesW = (int)date('w', strtotime(sprintf('%04d-%02d-01', $ano, $mes)));

        if ($primeiroDiaMesW === 0) {
            // O dia 01 do mês foi domingo! A Santa Ceia é no 2º domingo (dia 08)
            return ($diaNumero === 8);
        } else {
            // O 1º domingo caiu entre o dia 02 e o dia 07 -> Santa Ceia é neste 1º domingo!
            return ($diaNumero >= 2 && $diaNumero <= 7);
        }
    }

    /**
     * Valida se um modelo de culto padrão deve ocorrer em uma determinada data ISO (YYYY-MM-DD)
     */
    public static function isOcorrenciaCultoValida($cultoPadrao, $dataIso)
    {
        $time = strtotime($dataIso);
        $numDiaSemana = (int)date('w', $time);

        if ((int)$cultoPadrao->dia_semana !== $numDiaSemana) {
            return false;
        }

        $diaNumero = (int)date('j', $time);
        $posicaoNoMes = (int)ceil($diaNumero / 7);

        $tipo = $cultoPadrao->tipo_recorrencia ?? 'todas';
        $posicaoTarget = (int)($cultoPadrao->posicao_semana ?? 0);

        switch ($tipo) {
            case 'todas':
                return true;

            case 'apenas_posicao':
                return ($posicaoNoMes === $posicaoTarget);

            case 'exceto_posicao':
                return ($posicaoNoMes !== $posicaoTarget);

            case 'santa_ceia_domingo':
                return self::isSantaCeiaDomingo($dataIso);

            case 'exceto_santa_ceia_domingo':
                return !self::isSantaCeiaDomingo($dataIso);

            default:
                return true;
        }
    }

    /**
     * Verifica se o culto padrão possui agendamentos vinculados na tabela tb_agenda_culto
     */
    public function emUsoNaAgenda($id_culto_padrao)
    {
        $db = db_connect();

        if ($db->fieldExists('id_culto_padrao', 'tb_agenda_culto')) {
            $count = $db->table('tb_agenda_culto')
                ->where('id_culto_padrao', $id_culto_padrao)
                ->where('status_culto', 1)
                ->countAllResults();

            if ($count > 0) {
                return true;
            }
        }

        $cultoPadrao = $this->find($id_culto_padrao);
        if ($cultoPadrao) {
            $countNome = $db->table('tb_agenda_culto')
                ->where('titulo_culto', $cultoPadrao->nome_culto)
                ->where('status_culto', 1)
                ->countAllResults();

            if ($countNome > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna lista de cultos padrão ativos ordenados pelo dia da semana
     */
    public function getCultosPadraoAtivos()
    {
        return $this->where('status_culto', 1)
            ->orderBy('dia_semana', 'ASC')
            ->orderBy('horario_inicio', 'ASC')
            ->findAll();
    }
}
