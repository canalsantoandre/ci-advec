<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleResourceModel extends Model
{
    protected $table            = 'schedule_resources';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'data_culto',
        'id_culto_padrao',
        'id_departamento',
        'id_area',
        'resource_id',
        'display_order',
        'created_by'
    ];

    /**
     * Retorna todos os recursos desmembrados anexados a um culto e departamento,
     * trazendo a sub-área vinculada e opcionalmente filtrando por sub-área.
     */
    public function getRecursosDoCulto(string $data_culto, int $id_culto_padrao, int $id_departamento, ?int $id_area = null): array
    {
        $db = db_connect();
        $hasArea = $db->fieldExists('id_area', 'schedule_resources');

        $builder = $db->table('schedule_resources as sr');
        if ($hasArea) {
            $builder->select('
                sr.id as schedule_resource_id,
                sr.data_culto,
                sr.id_culto_padrao,
                sr.id_departamento,
                sr.id_area,
                COALESCE(a.nome_area, "Geral (Todas as Sub-áreas)") as nome_area,
                sr.display_order,
                r.*,
                rt.name as type_name,
                rt.code as type_code,
                rt.icon as type_icon,
                rt.color as type_color
            ');
            $builder->join('tb_departamento_area as a', 'a.id_area = sr.id_area', 'left');
        } else {
            $builder->select('
                sr.id as schedule_resource_id,
                sr.data_culto,
                sr.id_culto_padrao,
                sr.id_departamento,
                0 as id_area,
                "Geral (Todas as Sub-áreas)" as nome_area,
                sr.display_order,
                r.*,
                rt.name as type_name,
                rt.code as type_code,
                rt.icon as type_icon,
                rt.color as type_color
            ');
        }

        $builder->join('resources as r', 'r.id = sr.resource_id', 'inner');
        $builder->join('resource_types as rt', 'rt.id = r.resource_type_id', 'inner');
        $builder->where('sr.data_culto', $data_culto);
        $builder->where('sr.id_culto_padrao', $id_culto_padrao);
        $builder->where('sr.id_departamento', $id_departamento);
        $builder->where('r.deleted_at', null);

        if ($hasArea && $id_area !== null) {
            if ($id_area === 0) {
                $builder->where('sr.id_area', 0);
            } else {
                // Traz os gerais (0) + os específicos da sub-área informada
                $builder->groupStart()
                    ->where('sr.id_area', 0)
                    ->orWhere('sr.id_area', $id_area)
                    ->groupEnd();
            }
        }

        $builder->orderBy('sr.display_order', 'ASC');
        $builder->orderBy('sr.id', 'ASC');

        return $builder->get()->getResult('object');
    }

    /**
     * Retorna mapa de contagem de materiais anexados no mês para o departamento
     */
    public function getResumoRecursosMes(int $id_departamento, string $data_inicio, string $data_fim): array
    {
        $db = db_connect();
        $hasArea = $db->fieldExists('id_area', 'schedule_resources');

        $builder = $db->table('schedule_resources as sr');
        if ($hasArea) {
            $builder->select('sr.data_culto, sr.id_culto_padrao, sr.id_area, COUNT(DISTINCT sr.resource_id) as total')
                ->groupBy('sr.data_culto, sr.id_culto_padrao, sr.id_area');
        } else {
            $builder->select('sr.data_culto, sr.id_culto_padrao, 0 as id_area, COUNT(DISTINCT sr.resource_id) as total')
                ->groupBy('sr.data_culto, sr.id_culto_padrao');
        }

        $rows = $builder->join('resources as r', 'r.id = sr.resource_id', 'inner')
            ->where('sr.id_departamento', $id_departamento)
            ->where('sr.data_culto >=', $data_inicio)
            ->where('sr.data_culto <=', $data_fim)
            ->where('r.deleted_at', null)
            ->get()
            ->getResult('object');

        $resumoPorCulto = [];
        $resumoPorArea  = [];

        foreach ($rows as $row) {
            $d = $row->data_culto;
            $cp = (int)$row->id_culto_padrao;
            $ar = (int)($row->id_area ?? 0);
            $tot = (int)$row->total;

            if (!isset($resumoPorCulto[$d][$cp])) {
                $resumoPorCulto[$d][$cp] = 0;
            }
            $resumoPorCulto[$d][$cp] += $tot;

            $resumoPorArea[$d][$cp][$ar] = $tot;
        }

        return [
            'por_culto' => $resumoPorCulto,
            'por_area'  => $resumoPorArea
        ];
    }

    /**
     * Anexa um ou múltiplos recursos individuais a uma escala/culto com id_area
     */
    public function anexarRecursos(string $data_culto, int $id_culto_padrao, int $id_departamento, int $id_area, array $resource_ids, ?int $created_by = null): int
    {
        $db = db_connect();
        $hasArea = $db->fieldExists('id_area', $this->table);
        $inseridos = 0;

        $maxOrderBuilder = $db->table($this->table)
            ->where('data_culto', $data_culto)
            ->where('id_culto_padrao', $id_culto_padrao)
            ->where('id_departamento', $id_departamento);

        if ($hasArea) {
            $maxOrderBuilder->where('id_area', $id_area);
        }

        $maxOrderRow = $maxOrderBuilder->selectMax('display_order')->get()->getFirstRow();

        $proximaOrdem = ($maxOrderRow && $maxOrderRow->display_order) ? (int)$maxOrderRow->display_order + 1 : 1;

        foreach ($resource_ids as $resId) {
            $id = (int)$resId;
            if ($id <= 0) continue;

            // Verifica se já está anexado
            $checkBuilder = $db->table($this->table)
                ->where('data_culto', $data_culto)
                ->where('id_culto_padrao', $id_culto_padrao)
                ->where('id_departamento', $id_departamento)
                ->where('resource_id', $id);

            if ($hasArea) {
                $checkBuilder->where('id_area', $id_area);
            }

            $jaExiste = $checkBuilder->countAllResults();

            if (!$jaExiste) {
                $insertData = [
                    'data_culto'      => $data_culto,
                    'id_culto_padrao' => $id_culto_padrao,
                    'id_departamento' => $id_departamento,
                    'resource_id'     => $id,
                    'display_order'   => $proximaOrdem++,
                    'created_by'      => $created_by
                ];

                if ($hasArea) {
                    $insertData['id_area'] = $id_area;
                }

                $db->table($this->table)->insert($insertData);
                $inseridos++;
            }
        }

        return $inseridos;
    }

    /**
     * Anexa uma Coleção na escala com id_area especificado
     */
    public function anexarColecaoNaEscala(string $data_culto, int $id_culto_padrao, int $id_departamento, int $id_area, int $collection_id, ?int $created_by = null): int
    {
        $collectionModel = new CollectionModel();
        $colecao = $collectionModel->getColecaoComRecursos($collection_id);

        if (!$colecao || empty($colecao->resources)) {
            return 0;
        }

        $resourceIds = array_map(function($r) {
            return (int)$r->id;
        }, $colecao->resources);

        return $this->anexarRecursos($data_culto, $id_culto_padrao, $id_departamento, $id_area, $resourceIds, $created_by);
    }

    /**
     * Atualiza a ordenação dos recursos de uma escala (Drag-and-Drop)
     */
    public function reordenarRecursos(string $data_culto, int $id_culto_padrao, int $id_departamento, array $ordered_resource_ids): bool
    {
        $db = db_connect();
        $ordem = 1;
        foreach ($ordered_resource_ids as $resId) {
            $id = (int)$resId;
            if ($id > 0) {
                $db->table($this->table)
                    ->where('data_culto', $data_culto)
                    ->where('id_culto_padrao', $id_culto_padrao)
                    ->where('id_departamento', $id_departamento)
                    ->where('resource_id', $id)
                    ->update(['display_order' => $ordem++]);
            }
        }
        return true;
    }

    /**
     * Remove um recurso específico da escala/culto
     */
    public function removerRecursoDaEscala(string $data_culto, int $id_culto_padrao, int $id_departamento, int $resource_id, ?int $id_area = null): bool
    {
        $db = db_connect();
        $hasArea = $db->fieldExists('id_area', $this->table);

        $builder = $db->table($this->table)
            ->where('data_culto', $data_culto)
            ->where('id_culto_padrao', $id_culto_padrao)
            ->where('id_departamento', $id_departamento)
            ->where('resource_id', $resource_id);

        if ($hasArea && $id_area !== null) {
            $builder->where('id_area', $id_area);
        }

        return (bool)$builder->delete();
    }
}
