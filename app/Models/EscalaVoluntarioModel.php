<?php

namespace App\Models;

use CodeIgniter\Model;

class EscalaVoluntarioModel extends Model
{
    protected $table            = 'tb_escala_voluntario';
    protected $primaryKey       = 'id_escala_voluntario';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'data_culto',
        'id_culto_padrao',
        'id_culto',
        'id_departamento',
        'id_area',
        'id_voluntario',
        'status_presenca',
        'status_confirmacao',
        'justificativa_recusa',
        'data_resposta',
        'observacao'
    ];

    /**
     * Retorna todas as escalas de uma determinada data e culto padrão, opcionalmente filtrado por departamento
     */
    public function getEscalasDoCulto($data_culto, $id_culto_padrao, $id_departamento = null)
    {
        $db = db_connect();
        $builder = $db->table('tb_escala_voluntario as ev');
        $builder->select('
            ev.*,
            v.nome as nome_voluntario,
            v.nickname,
            v.nivel_conhecimento,
            v.email as email_voluntario,
            v.telefone_whatsapp,
            v.foto_url,
            v.hash_voluntario,
            a.nome_area,
            d.nome as nome_departamento,
            d.cor_identificacao
        ');
        $builder->join('tb_voluntario as v', 'v.id_voluntario = ev.id_voluntario', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');
        $builder->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner');
        $builder->where('ev.data_culto', $data_culto);
        $builder->where('ev.id_culto_padrao', (int)$id_culto_padrao);

        if (!empty($id_departamento)) {
            $builder->where('ev.id_departamento', (int)$id_departamento);
        }

        $builder->orderBy('a.nome_area', 'ASC');
        $builder->orderBy('v.nome', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Verifica se o voluntário já possui escala confirmada/ativa em outro departamento para a mesma data e culto
     */
    public function getConflitoOutroDepartamento(int $id_voluntario, string $data_culto, int $id_culto_padrao, int $id_departamento_atual)
    {
        if ($id_voluntario <= 0 || empty($data_culto) || $id_culto_padrao <= 0) {
            return null;
        }

        $db = db_connect();
        $builder = $db->table('tb_escala_voluntario as ev');
        $builder->select('
            ev.*,
            d.nome as nome_departamento,
            a.nome_area
        ');
        $builder->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');
        $builder->where('ev.id_voluntario', $id_voluntario);
        $builder->where('ev.data_culto', $data_culto);
        $builder->where('ev.id_culto_padrao', $id_culto_padrao);
        $builder->where('ev.id_departamento !=', $id_departamento_atual);
        $builder->groupStart()
                ->where('ev.status_confirmacao !=', 'RECUSADO')
                ->orWhere('ev.status_confirmacao IS NULL')
                ->groupEnd();
        $builder->groupStart()
                ->where('ev.status_presenca !=', 0)
                ->orWhere('ev.status_presenca IS NULL')
                ->groupEnd();

        return $builder->get()->getFirstRow('object');
    }

    /**
     * Retorna todas as escalas de um período para um departamento
     */
    public function getEscalasMesPorDepartamento($id_departamento, $dataInicio, $dataFim)
    {
        $db = db_connect();
        $builder = $db->table('tb_escala_voluntario as ev');
        $builder->select('
            ev.*,
            v.nome as nome_voluntario,
            v.nickname,
            v.nivel_conhecimento,
            v.telefone_whatsapp,
            v.foto_url,
            v.hash_voluntario,
            a.nome_area,
            cp.nome_culto as titulo_culto,
            cp.horario_inicio,
            cp.horario_termino,
            cp.cor_evento
        ');
        $builder->join('tb_voluntario as v', 'v.id_voluntario = ev.id_voluntario', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');
        $builder->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = ev.id_culto_padrao', 'left');
        $builder->where('ev.id_departamento', (int)$id_departamento);
        $builder->where('ev.data_culto >=', $dataInicio);
        $builder->where('ev.data_culto <=', $dataFim);
        $builder->orderBy('ev.data_culto', 'ASC');
        $builder->orderBy('a.nome_area', 'ASC');
        $builder->orderBy('v.nome', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Escalar voluntário em uma sub-área de um culto por data e culto padrão
     */
    public function escalarVoluntario($data_culto, $id_culto_padrao, $id_departamento, $id_area, $id_voluntario, $observacao = '')
    {
        $data_culto      = trim((string)$data_culto);
        $id_culto_padrao = (int)$id_culto_padrao;
        $id_departamento = (int)$id_departamento;
        $id_area         = (int)$id_area;
        $id_voluntario   = (int)$id_voluntario;

        if (empty($data_culto) || $id_culto_padrao <= 0 || $id_departamento <= 0 || $id_area <= 0 || $id_voluntario <= 0) {
            return false;
        }

        $db = db_connect();
        $existing = $db->table($this->table)
            ->where('data_culto', $data_culto)
            ->where('id_culto_padrao', $id_culto_padrao)
            ->where('id_departamento', $id_departamento)
            ->where('id_area', $id_area)
            ->where('id_voluntario', $id_voluntario)
            ->get()
            ->getFirstRow();

        if ($existing) {
            $db->table($this->table)
                ->where('id_escala_voluntario', $existing->id_escala_voluntario)
                ->update([
                    'observacao'      => $observacao,
                    'status_presenca' => 1
                ]);
            return $existing->id_escala_voluntario;
        }

        $db->table($this->table)->insert([
            'data_culto'      => $data_culto,
            'id_culto_padrao' => $id_culto_padrao,
            'id_departamento' => $id_departamento,
            'id_area'         => $id_area,
            'id_voluntario'   => $id_voluntario,
            'status_presenca' => 1,
            'observacao'      => $observacao
        ]);

        return $db->insertID();
    }

    /**
     * Remove escalação
     */
    public function removerEscala($id_escala_voluntario)
    {
        return $this->delete((int)$id_escala_voluntario);
    }

    /**
     * Alterna status de presença (1 = Confirmado/Presente, 0 = Ausente, 2 = Pendente)
     */
    public function alternarPresenca($id_escala_voluntario, $status_presenca)
    {
        return $this->update((int)$id_escala_voluntario, [
            'status_presenca' => (int)$status_presenca
        ]);
    }

    /**
     * Retorna lista de escalas de um voluntário específico (com dados do culto, departamento e sub-área)
     */
    public function getEscalasDoVoluntario($id_voluntario, $ano = null, $mes = null)
    {
        $db = db_connect();
        $builder = $db->table('tb_escala_voluntario as ev');
        $builder->select('
            ev.*,
            COALESCE(cp.nome_culto, c.titulo_culto, "Culto") as titulo_culto,
            COALESCE(cp.horario_inicio, c.horario_inicio) as horario_inicio,
            COALESCE(cp.horario_termino, c.horario_termino) as horario_termino,
            COALESCE(cp.cor_evento, c.cor_evento, "#2563eb") as cor_evento,
            d.nome as nome_departamento,
            d.cor_identificacao as cor_departamento,
            d.responsavel_nome as lider_departamento,
            d.responsavel_telefone as lider_telefone,
            a.nome_area
        ');
        $builder->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = ev.id_culto_padrao', 'left');
        $builder->join('tb_agenda_culto as c', 'c.id_culto = ev.id_culto', 'left');
        $builder->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');
        $builder->where('ev.id_voluntario', (int)$id_voluntario);

        if (!empty($ano) && !empty($mes)) {
            $dataInicio = sprintf('%04d-%02d-01', (int)$ano, (int)$mes);
            $totalDias   = (int)date('t', strtotime($dataInicio));
            $dataFim    = sprintf('%04d-%02d-%02d', (int)$ano, (int)$mes, $totalDias);
            $builder->where('ev.data_culto >=', $dataInicio);
            $builder->where('ev.data_culto <=', $dataFim);
        }

        $builder->orderBy('ev.data_culto', 'ASC');
        $builder->orderBy('cp.horario_inicio', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Resposta do voluntário para uma escala (CONFIRMADO ou RECUSADO com justificativa)
     */
    public function responderEscala($id_escala_voluntario, $id_voluntario, $status_confirmacao, $justificativa = null)
    {
        $status_confirmacao = strtoupper(trim((string)$status_confirmacao));
        if (!in_array($status_confirmacao, ['CONFIRMADO', 'RECUSADO', 'PENDENTE'])) {
            return false;
        }

        $escala = $this->where('id_escala_voluntario', (int)$id_escala_voluntario)
                       ->where('id_voluntario', (int)$id_voluntario)
                       ->first();

        if (!$escala) {
            return false;
        }

        $dadosUpdate = [
            'status_confirmacao'   => $status_confirmacao,
            'status_presenca'      => ($status_confirmacao === 'CONFIRMADO' ? 1 : ($status_confirmacao === 'RECUSADO' ? 0 : 2)),
            'justificativa_recusa' => ($status_confirmacao === 'RECUSADO' ? trim((string)$justificativa) : null),
            'data_resposta'        => date('Y-m-d H:i:s')
        ];

        return $this->update((int)$id_escala_voluntario, $dadosUpdate);
    }
}
