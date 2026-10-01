<?php

namespace App\Models;

use CodeIgniter\Model;

class VoluntarioAreaModel extends Model
{
    protected $table            = 'tb_voluntario_departamento_area';
    protected $primaryKey       = 'id_voluntario_area';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_voluntario',
        'id_departamento',
        'id_area'
    ];

    /**
     * Retorna todas as áreas e departamentos vinculados a um voluntário
     */
    public function getAreasDoVoluntario($id_voluntario)
    {
        $db = db_connect();
        $builder = $db->table('tb_voluntario_departamento_area as vda');
        $builder->select('
            vda.*,
            d.nome as nome_departamento,
            d.cor_identificacao,
            a.nome_area
        ');
        $builder->join('tb_departamento as d', 'd.id_departamento = vda.id_departamento', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = vda.id_area', 'inner');
        $builder->where('vda.id_voluntario', (int)$id_voluntario);
        $builder->orderBy('d.nome', 'ASC');
        $builder->orderBy('a.nome_area', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Retorna array simples com os IDs das áreas vinculadas [1, 2, 5]
     */
    public function getIdsAreasDoVoluntario($id_voluntario)
    {
        $db = db_connect();
        $rows = $db->table($this->table)
            ->select('id_area')
            ->where('id_voluntario', (int)$id_voluntario)
            ->get()
            ->getResultArray();

        return array_column($rows, 'id_area');
    }

    /**
     * Sincroniza vínculos de áreas e departamentos do voluntário
     * $areaIds: array de IDs de áreas selecionadas
     */
    public function sincronizarAreas($id_voluntario, array $areaIds)
    {
        $id_voluntario = (int)$id_voluntario;
        $db = db_connect();

        // Remove vínculos existentes
        $db->table($this->table)->where('id_voluntario', $id_voluntario)->delete();

        if (empty($areaIds)) {
            return 0;
        }

        // Busca o id_departamento correspondente para cada id_area selecionado
        $areasInfo = $db->table('tb_departamento_area')
            ->select('id_area, id_departamento')
            ->whereIn('id_area', $areaIds)
            ->get()
            ->getResult('object');

        $inseridos = 0;
        foreach ($areasInfo as $item) {
            $db->table($this->table)->insert([
                'id_voluntario'   => $id_voluntario,
                'id_departamento' => $item->id_departamento,
                'id_area'         => $item->id_area
            ]);
            $inseridos++;
        }

        return $inseridos;
    }

    /**
     * Retorna lista de voluntários vinculados a um departamento e/ou área
     */
    public function getVoluntariosPorDepartamentoEArea($id_departamento = null, $id_area = null, $onlyActive = true)
    {
        $db = db_connect();
        $builder = $db->table('tb_voluntario as v');
        $builder->select('
            v.id_voluntario,
            v.nome,
            v.nickname,
            v.nivel_conhecimento,
            v.max_escalas_mes,
            v.email,
            v.telefone_whatsapp,
            v.foto_url,
            v.status,
            v.hash_voluntario,
            GROUP_CONCAT(DISTINCT a.nome_area ORDER BY a.nome_area SEPARATOR ", ") as nome_area
        ');
        $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_voluntario = v.id_voluntario', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = vda.id_area', 'left');

        if (!empty($id_departamento)) {
            $builder->where('vda.id_departamento', (int)$id_departamento);
        }

        if (!empty($id_area)) {
            $builder->where('vda.id_area', (int)$id_area);
        }

        if ($onlyActive) {
            $builder->where('v.status', 1);
        }

        $builder->groupBy('v.id_voluntario');
        $builder->orderBy('v.nome', 'ASC');

        return $builder->get()->getResult('object');
    }
}
