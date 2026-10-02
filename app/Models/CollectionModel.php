<?php

namespace App\Models;

use CodeIgniter\Model;

class CollectionModel extends Model
{
    protected $table            = 'collections';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'title',
        'description',
        'department_id',
        'cover_url',
        'status',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes= true;

    /**
     * Lista coleções com contagem de recursos e nome do departamento
     */
    public function listarColecoes(array $filtros = []): array
    {
        $db = db_connect();
        $builder = $db->table('collections as c');
        $builder->select('
            c.*,
            d.nome as department_name,
            d.cor_identificacao as department_color,
            (SELECT COUNT(*) FROM collection_resources cr WHERE cr.collection_id = c.id) as total_resources
        ');
        $builder->join('tb_departamento as d', 'd.id_departamento = c.department_id', 'left');
        $builder->where('c.deleted_at', null);

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $builder->where('c.status', (int)$filtros['status']);
        }

        if (isset($filtros['department_or_global']) && $filtros['department_or_global'] !== null) {
            $depId = (int)$filtros['department_or_global'];
            $builder->groupStart()
                ->where('c.department_id', null)
                ->orWhere('c.department_id', $depId)
                ->groupEnd();
        } elseif (isset($filtros['department_id']) && $filtros['department_id'] !== null && $filtros['department_id'] !== '') {
            if ($filtros['department_id'] === 'global' || $filtros['department_id'] === '0') {
                $builder->where('c.department_id', null);
            } else {
                $builder->where('c.department_id', (int)$filtros['department_id']);
            }
        } elseif (isset($filtros['departamentos_permitidos'])) {
            $allowedIds = $filtros['departamentos_permitidos'];
            $builder->groupStart();
            $builder->where('c.department_id', null);
            if (!empty($allowedIds)) {
                $builder->orWhereIn('c.department_id', $allowedIds);
            }
            $builder->groupEnd();
        }

        if (!empty($filtros['busca'])) {
            $termo = trim($filtros['busca']);
            $builder->groupStart()
                ->like('c.title', $termo)
                ->orLike('c.description', $termo)
                ->groupEnd();
        }

        $builder->orderBy('c.id', 'DESC');

        return $builder->get()->getResult('object');
    }

    /**
     * Retorna a coleção com todos os recursos vinculados em ordem
     */
    public function getColecaoComRecursos(int $id)
    {
        $db = db_connect();
        $builder = $db->table('collections as c');
        $builder->select('
            c.*,
            d.nome as department_name,
            d.cor_identificacao as department_color
        ');
        $builder->join('tb_departamento as d', 'd.id_departamento = c.department_id', 'left');
        $builder->where('c.id', $id);
        $builder->where('c.deleted_at', null);

        $collection = $builder->get()->getFirstRow('object');
        if (!$collection) {
            return null;
        }

        // Recursos da coleção ordenados por order ASC
        $resBuilder = $db->table('collection_resources as cr');
        $resBuilder->select('
            cr.id as collection_resource_id,
            cr.order as resource_order,
            r.*,
            rt.name as type_name,
            rt.code as type_code,
            rt.icon as type_icon,
            rt.color as type_color
        ');
        $resBuilder->join('resources as r', 'r.id = cr.resource_id', 'inner');
        $resBuilder->join('resource_types as rt', 'rt.id = r.resource_type_id', 'inner');
        $resBuilder->where('cr.collection_id', $id);
        $resBuilder->where('r.deleted_at', null);
        $resBuilder->orderBy('cr.order', 'ASC');
        $resBuilder->orderBy('cr.id', 'ASC');

        $collection->resources = $resBuilder->get()->getResult('object');

        return $collection;
    }

    /**
     * Sincroniza os recursos de uma coleção com ordenação (Drag-and-Drop)
     */
    public function sincronizarRecursos(int $collection_id, array $resource_ids_com_ordem): bool
    {
        $db = db_connect();
        $db->table('collection_resources')->where('collection_id', $collection_id)->delete();

        if (empty($resource_ids_com_ordem)) {
            return true;
        }

        $batch = [];
        $ordem = 1;
        foreach ($resource_ids_com_ordem as $resId) {
            $id = (int)$resId;
            if ($id > 0) {
                $batch[] = [
                    'collection_id' => $collection_id,
                    'resource_id'   => $id,
                    'order'         => $ordem++
                ];
            }
        }

        if (!empty($batch)) {
            $db->table('collection_resources')->insertBatch($batch);
        }

        return true;
    }
}
