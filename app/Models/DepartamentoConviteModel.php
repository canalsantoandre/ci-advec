<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartamentoConviteModel extends Model
{
    protected $table            = 'tb_departamento_convite';
    protected $primaryKey       = 'id_convite';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'token',
        'tipo',
        'id_departamento',
        'id_usuario_criador',
        'telefone',
        'capacidade_maxima',
        'usos_realizados',
        'status',
        'expires_at'
    ];

    /**
     * Gera um token único hexadecimal seguro
     */
    public function gerarTokenUnico()
    {
        do {
            $token = bin2hex(random_bytes(16));
            $existe = $this->where('token', $token)->first();
        } while ($existe);

        return $token;
    }

    /**
     * Cria um novo convite (DIRETO ou LOTE)
     */
    public function criarConvite($idDepartamento, $idUsuarioCriador, $tipo = 'DIRETO', $telefone = null, $capacidade = 1, $diasValidade = 30)
    {
        $token = $this->gerarTokenUnico();
        $tipo = strtoupper(trim((string)$tipo)) === 'LOTE' ? 'LOTE' : 'DIRETO';
        
        $capacidadeMaxima = ($tipo === 'DIRETO') ? 1 : max(1, (int)$capacidade);
        $telefoneLimpo = !empty($telefone) ? trim((string)$telefone) : null;
        
        $expiresAt = null;
        if ($diasValidade > 0) {
            $expiresAt = date('Y-m-d 23:59:59', strtotime("+{$diasValidade} days"));
        }

        $dados = [
            'token'              => $token,
            'tipo'               => $tipo,
            'id_departamento'    => (int)$idDepartamento,
            'id_usuario_criador' => (int)$idUsuarioCriador,
            'telefone'           => $telefoneLimpo,
            'capacidade_maxima'  => $capacidadeMaxima,
            'usos_realizados'    => 0,
            'status'             => 'ATIVO',
            'expires_at'         => $expiresAt
        ];

        $idConvite = $this->insert($dados);
        if ($idConvite) {
            return $this->find($idConvite);
        }

        return null;
    }

    /**
     * Busca convite por token com detalhes do departamento e criador
     */
    public function buscarPorToken($token)
    {
        $db = db_connect();
        $builder = $db->table('tb_departamento_convite as c');
        $builder->select('
            c.*,
            d.nome as nome_departamento,
            d.descricao as descricao_departamento,
            d.cor_identificacao as cor_departamento,
            d.logo_url as logo_departamento,
            u.nome as nome_criador
        ');
        $builder->join('tb_departamento as d', 'd.id_departamento = c.id_departamento', 'inner');
        $builder->join('tb_sys_usuario as u', 'u.id_usuario = c.id_usuario_criador', 'left');
        $builder->where('c.token', trim((string)$token));

        return $builder->get()->getFirstRow();
    }

    /**
     * Valida se o convite está ativo, dentro da validade e com capacidade disponível
     */
    public function validarToken($token)
    {
        $convite = $this->buscarPorToken($token);
        if (!$convite) {
            return [
                'valido'   => false,
                'motivo'   => 'Convite não encontrado ou link inválido.',
                'convite'  => null
            ];
        }

        // Verifica status
        if ($convite->status !== 'ATIVO') {
            return [
                'valido'   => false,
                'motivo'   => 'Este link de convite foi encerrado ou já foi utilizado.',
                'convite'  => $convite
            ];
        }

        // Verifica validade de data
        if (!empty($convite->expires_at) && strtotime($convite->expires_at) < time()) {
            $this->update($convite->id_convite, ['status' => 'EXPIRADO']);
            $convite->status = 'EXPIRADO';
            return [
                'valido'   => false,
                'motivo'   => 'Este link de convite expirou o prazo limite de validade.',
                'convite'  => $convite
            ];
        }

        // Verifica capacidade de usos
        if ($convite->usos_realizados >= $convite->capacidade_maxima) {
            $this->update($convite->id_convite, ['status' => 'ENCERRADO']);
            $convite->status = 'ENCERRADO';
            return [
                'valido'   => false,
                'motivo'   => 'A capacidade máxima de cadastros deste convite foi atingida.',
                'convite'  => $convite
            ];
        }

        return [
            'valido'   => true,
            'motivo'   => 'Convite válido.',
            'convite'  => $convite
        ];
    }

    /**
     * Incrementa o contador de usos realizados e encerra o convite se atingir o limite
     */
    public function incrementarUso($idConvite)
    {
        $convite = $this->find((int)$idConvite);
        if (!$convite) {
            return false;
        }

        $novosUsos = (int)$convite->usos_realizados + 1;
        $novoStatus = ($novosUsos >= (int)$convite->capacidade_maxima) ? 'ENCERRADO' : $convite->status;

        return $this->update((int)$idConvite, [
            'usos_realizados' => $novosUsos,
            'status'          => $novoStatus
        ]);
    }

    /**
     * Encerra manualmente o uso do link pelo líder antes de atingir a capacidade
     */
    public function encerrarConvite($idConvite, $idUsuario = null)
    {
        $convite = $this->find((int)$idConvite);
        if (!$convite) {
            return false;
        }

        return $this->update((int)$idConvite, [
            'status' => 'ENCERRADO'
        ]);
    }

    /**
     * Lista convites de departamentos gerenciados pelo líder
     */
    public function listarConvites(array $departamentosIds, array $filtros = [])
    {
        if (empty($departamentosIds)) {
            return [];
        }

        $db = db_connect();
        $builder = $db->table('tb_departamento_convite as c');
        $builder->select('
            c.*,
            d.nome as nome_departamento,
            d.cor_identificacao as cor_departamento,
            u.nome as nome_criador
        ');
        $builder->join('tb_departamento as d', 'd.id_departamento = c.id_departamento', 'inner');
        $builder->join('tb_sys_usuario as u', 'u.id_usuario = c.id_usuario_criador', 'left');
        $builder->whereIn('c.id_departamento', (array)$departamentosIds);

        if (!empty($filtros['id_departamento'])) {
            $builder->where('c.id_departamento', (int)$filtros['id_departamento']);
        }

        if (!empty($filtros['tipo'])) {
            $builder->where('c.tipo', strtoupper(trim((string)$filtros['tipo'])));
        }

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $builder->where('c.status', strtoupper(trim((string)$filtros['status'])));
        }

        $builder->orderBy('c.date_insert', 'DESC');

        $convites = $builder->get()->getResult('object');

        foreach ($convites as &$c) {
            $c->percentual_uso = $c->capacidade_maxima > 0 
                ? min(100, round(($c->usos_realizados / $c->capacidade_maxima) * 100))
                : 0;
            $c->link_completo = base_url('convite/' . $c->token);
        }

        return $convites;
    }
}
