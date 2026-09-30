<?php

namespace App\Models;

use CodeIgniter\Model;

class WebhookModel extends Model
{
    protected $table            = 'tb_webhook';
    protected $primaryKey       = 'id_webhook';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_filial',
        'nome',
        'url',
        'instancia_padrao',
        'status'
    ];

    protected $useTimestamps    = false;

    /**
     * Retorna o primeiro Webhook ativo cadastrado no sistema
     */
    public function getWebhookAtivo()
    {
        return $this->where('status', 1)
                    ->orderBy('id_webhook', 'ASC')
                    ->first();
    }

    /**
     * Retorna lista de todos os Webhooks com contagem ou filtros
     */
    public function listaWebhooks($busca = null)
    {
        $builder = $this->builder();
        if (!empty($busca)) {
            $busca = trim((string)$busca);
            $builder->groupStart()
                ->like('nome', $busca)
                ->orLike('url', $busca)
                ->orLike('instancia_padrao', $busca)
                ->groupEnd();
        }

        $builder->orderBy('id_webhook', 'DESC');
        return $builder->get()->getResult('object');
    }
}
