<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\JwtService;

class ApiClientModel extends Model
{
    protected $table            = 'tb_api_client';
    protected $primaryKey       = 'id_api_client';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'nome_aplicacao',
        'client_id',
        'client_secret',
        'jwt_secret',
        'tempo_expiracao',
        'limite_requisicoes_minuto',
        'status',
        'ultimo_acesso',
        'total_requisicoes',
        'descricao',
        'date_insert',
        'date_update'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'date_insert';
    protected $updatedField     = 'date_update';
    protected $dateFormat       = 'datetime';

    /**
     * Busca cliente ativo por Client ID
     */
    public function buscarAtivoPorClientId(string $clientId)
    {
        return $this->where('client_id', trim($clientId))
                    ->where('status', 1)
                    ->first();
    }

    /**
     * Busca cliente por Client ID independente de status
     */
    public function buscarPorClientId(string $clientId)
    {
        return $this->where('client_id', trim($clientId))->first();
    }

    /**
     * Registra o acesso do cliente, atualizando último acesso e incrementando o total de requisições
     */
    public function registrarAcesso(int $idApiClient): void
    {
        $db = db_connect();
        $db->table($this->table)
            ->where('id_api_client', $idApiClient)
            ->set('total_requisicoes', 'total_requisicoes + 1', false)
            ->set('ultimo_acesso', date('Y-m-d H:i:s'))
            ->update();
    }

    /**
     * Gera novas chaves criptográficas para um cliente existente (Rotação de Credenciais)
     */
    public function rotacionarChaves(int $idApiClient): ?array
    {
        $client = $this->find($idApiClient);
        if (!$client) {
            return null;
        }

        $novoSecret = 'sec_' . JwtService::gerarChaveAleatoria(24);
        $novoJwtSecret = JwtService::gerarChaveAleatoria(32);

        $this->update($idApiClient, [
            'client_secret' => $novoSecret,
            'jwt_secret'    => $novoJwtSecret
        ]);

        return [
            'client_secret' => $novoSecret,
            'jwt_secret'    => $novoJwtSecret
        ];
    }

    /**
     * Alterna o status do cliente (Ativo / Inativo)
     */
    public function toggleStatus(int $idApiClient, ?int $novoStatus = null): int
    {
        $client = $this->find($idApiClient);
        if (!$client) {
            return 0;
        }

        $status = ($novoStatus !== null) ? $novoStatus : ((int)$client->status === 1 ? 0 : 1);
        $this->update($idApiClient, ['status' => $status]);
        return $status;
    }

    /**
     * Lista todos os clientes com filtro opcional por busca de texto
     */
    public function listarClientes(?string $busca = null): array
    {
        $builder = $this->builder();
        if (!empty($busca)) {
            $busca = trim($busca);
            $builder->groupStart()
                ->like('nome_aplicacao', $busca)
                ->orLike('client_id', $busca)
                ->orLike('descricao', $busca)
                ->groupEnd();
        }

        $builder->orderBy('id_api_client', 'DESC');
        return $builder->get()->getResult('object');
    }
}
