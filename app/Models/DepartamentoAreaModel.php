<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartamentoAreaModel extends Model
{
    protected $table            = 'tb_departamento_area';
    protected $primaryKey       = 'id_area';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_departamento',
        'nome_area',
        'descricao',
        'status'
    ];

    /**
     * Retorna áreas de um departamento com total de voluntários vinculados
     */
    public function getAreasPorDepartamento($id_departamento, $onlyActive = false)
    {
        $db = db_connect();
        $builder = $db->table('tb_departamento_area as a');
        $builder->select('
            a.*,
            COUNT(DISTINCT vda.id_voluntario) as total_voluntarios
        ');
        $builder->join('tb_voluntario_departamento_area as vda', 'vda.id_area = a.id_area', 'left');
        $builder->where('a.id_departamento', (int)$id_departamento);
        
        if ($onlyActive) {
            $builder->where('a.status', 1);
        }

        $builder->groupBy('a.id_area');
        $builder->orderBy('a.nome_area', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Retorna estrutura completa de Departamentos com suas Áreas ativas (útil para seletores)
     */
    public function getTodasAreasAgrupadas()
    {
        $db = db_connect();
        $departamentos = $db->table('tb_departamento')
            ->where('status', 1)
            ->orderBy('nome', 'ASC')
            ->get()
            ->getResult('object');

        foreach ($departamentos as &$dep) {
            $dep->areas = $db->table('tb_departamento_area')
                ->where('id_departamento', $dep->id_departamento)
                ->where('status', 1)
                ->orderBy('nome_area', 'ASC')
                ->get()
                ->getResult('object');
        }

        return $departamentos;
    }

    /**
     * Verifica se a área está em uso em escalas
     */
    public function emUsoEmEscalas($id_area)
    {
        $db = db_connect();
        $count = $db->table('tb_escala_voluntario')
            ->where('id_area', (int)$id_area)
            ->countAllResults();

        return $count > 0;
    }
}
