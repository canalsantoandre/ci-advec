<?php

namespace App\Models;

use CodeIgniter\Model;

class ResourceTypeModel extends Model
{
    protected $table            = 'resource_types';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'name',
        'code',
        'icon',
        'color'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Retorna todos os tipos de recursos disponíveis
     */
    public function getTiposAtivos(): array
    {
        return $this->orderBy('id', 'ASC')->findAll();
    }
}
