<?php

namespace App\Models;

use CodeIgniter\Model;

class CultoConvidadoModel extends Model
{
    protected $table            = 'tb_culto_convidado';
    protected $primaryKey       = 'id_culto_convidado';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_culto',
        'id_convidado',
        'status_presenca',
        'observacao'
    ];

    /**
     * Retorna lista de convidados vinculados a um culto com seus dados pessoais e de função
     */
    public function getConvidadosDoCulto($id_culto)
    {
        $builder = $this->db->table('tb_culto_convidado as cc');
        $builder->select('
            cc.id_culto_convidado,
            cc.id_culto,
            cc.id_convidado,
            cc.status_presenca,
            cc.observacao as obs_presenca,
            c.nome_convidado,
            c.nome_convidado_visualizacao,
            c.hash_convidado,
            c.email,
            c.telefone,
            c.nick_instagram,
            c.url_foto_instagram,
            f.nm_funcao_eclesiastica
        ');
        $builder->join('tb_convidado as c', 'c.id_convidado = cc.id_convidado', 'inner');
        $builder->join('tb_funcao_eclesiastica as f', 'f.id_funcao_eclesiastica = c.id_funcao_eclesiastica', 'left');
        $builder->where('cc.id_culto', $id_culto);
        $builder->orderBy('c.nome_convidado', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Alias para getConvidadosDoCulto
     */
    public function getConvidadosPorCulto($id_culto)
    {
        return $this->getConvidadosDoCulto($id_culto);
    }

    /**
     * Vincular múltiplos convidados a um culto
     */
    public function vincularConvidados($id_culto, array $arrIdsConvidados)
    {
        $vinculados = 0;
        foreach ($arrIdsConvidados as $id_convidado) {
            $id_convidado = (int)$id_convidado;
            if ($id_convidado <= 0) continue;

            $exists = $this->db->table($this->table)
                ->where('id_culto', $id_culto)
                ->where('id_convidado', $id_convidado)
                ->countAllResults();

            if ($exists == 0) {
                $this->db->table($this->table)->insert([
                    'id_culto'        => $id_culto,
                    'id_convidado'    => $id_convidado,
                    'status_presenca' => 1
                ]);
                $vinculados++;
            }
        }
        return $vinculados;
    }

    /**
     * Alterna status de presença (1 = Presente, 0 = Ausente)
     */
    public function alternarPresenca($id_culto_convidado, $status_presenca)
    {
        return $this->db->table($this->table)
            ->where('id_culto_convidado', $id_culto_convidado)
            ->update(['status_presenca' => (int)$status_presenca]);
    }

    /**
     * estatísticas do convidado para o Dashboard
     */
    public function getDashConvidadoStats($id_convidado)
    {
        // Convidado info
        $convidado = $this->db->table('tb_convidado as c')
            ->select('c.*, f.nm_funcao_eclesiastica')
            ->join('tb_funcao_eclesiastica as f', 'f.id_funcao_eclesiastica = c.id_funcao_eclesiastica', 'left')
            ->where('c.id_convidado', $id_convidado)
            ->get()->getFirstRow();

        if (!$convidado) {
            return null;
        }

        // Cultos com presença do convidado
        $cultosPresente = $this->db->table('tb_culto_convidado as cc')
            ->select('cc.status_presenca, cc.observacao as obs_presenca, cult.*')
            ->join('tb_agenda_culto as cult', 'cult.id_culto = cc.id_culto', 'inner')
            ->where('cc.id_convidado', $id_convidado)
            ->where('cc.status_presenca', 1)
            ->where('cult.status_culto', 1)
            ->orderBy('cult.data_culto', 'DESC')
            ->get()->getResult('object');

        // Cultos agendados em que ele esteve ausente
        $cultosAusente = $this->db->table('tb_culto_convidado as cc')
            ->select('cc.status_presenca, cc.observacao as obs_presenca, cult.*')
            ->join('tb_agenda_culto as cult', 'cult.id_culto = cc.id_culto', 'inner')
            ->where('cc.id_convidado', $id_convidado)
            ->where('cc.status_presenca', 0)
            ->where('cult.status_culto', 1)
            ->orderBy('cult.data_culto', 'DESC')
            ->get()->getResult('object');

        // Total de cultos que ocorreram até hoje no sistema
        $totalCultosRealizados = $this->db->table('tb_agenda_culto')
            ->where('status_culto', 1)
            ->where('data_culto <=', date('Y-m-d'))
            ->countAllResults();

        $totalPresencas = count($cultosPresente);
        $totalAusencias = count($cultosAusente);

        $percentualAssiduidade = $totalCultosRealizados > 0 
            ? round(($totalPresencas / $totalCultosRealizados) * 100, 1) 
            : 0;

        return (object)[
            'convidado'              => $convidado,
            'cultosPresente'         => $cultosPresente,
            'cultosAusente'          => $cultosAusente,
            'totalPresencas'         => $totalPresencas,
            'totalAusencias'        => $totalAusencias,
            'totalCultosRealizados'  => $totalCultosRealizados,
            'percentualAssiduidade'  => $percentualAssiduidade
        ];
    }

    public function getEstatisticasConvidado($id_convidado)
    {
        return $this->getDashConvidadoStats($id_convidado);
    }

    public function getEstastiticasConvidado($id_convidado)
    {
        return $this->getDashConvidadoStats($id_convidado);
    }
}
