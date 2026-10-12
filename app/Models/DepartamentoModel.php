<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartamentoModel extends Model
{
    protected $table            = 'tb_departamento';
    protected $primaryKey       = 'id_departamento';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_filial',
        'nome',
        'descricao',
        'logo_url',
        'responsavel_nome',
        'responsavel_telefone',
        'cor_identificacao',
        'notificar_whatsapp',
        'whatsapp_group_id',
        'receber_notificacao_agenda',
        'status'
    ];

    /**
     * Retorna os departamentos configurados para receber notificações da agenda de cultos via grupo do WhatsApp
     */
    public function getDepartamentosNotificacaoAgenda()
    {
        return $this->where('status', 1)
            ->where('receber_notificacao_agenda', 1)
            ->where('whatsapp_group_id IS NOT NULL')
            ->where('whatsapp_group_id !=', '')
            ->findAll();
    }

    /**
     * Retorna todos os departamentos com contadores de áreas e voluntários vinculados
     */
    public function getDepartamentosComContadores()
    {
        $db = db_connect();
        $builder = $db->table('tb_departamento as d');
        $builder->select('
            d.*,
            COUNT(DISTINCT a.id_area) as total_areas,
            COUNT(DISTINCT vda.id_voluntario) as total_voluntarios
        ');
        $builder->join('tb_departamento_area as a', 'a.id_departamento = d.id_departamento AND a.status = 1', 'left');
        $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_departamento = d.id_departamento', 'left');
        $builder->groupBy('d.id_departamento');
        $builder->orderBy('d.nome', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Retorna lista simples de departamentos ativos
     */
    public function getDepartamentosAtivos()
    {
        return $this->where('status', 1)
            ->orderBy('nome', 'ASC')
            ->findAll();
    }

    /**
     * Busca departamento por coluna
     */
    public function findByColumn($column, $value)
    {
        return $this->where($column, $value)->first();
    }
}
