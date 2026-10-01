<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartamentoGestorModel extends Model
{
    protected $table            = 'tb_departamento_gestor';
    protected $primaryKey       = 'id_departamento_gestor';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_departamento',
        'id_sys_usuario'
    ];

    /**
     * Verifica se o usuário atual é Administrador / Superadministrador
     */
    public static function isUserAdmin($user = null): bool
    {
        if (!$user) {
            $session = session();
            $userData = $session->get('dsh_usuario');
            $user = $userData['obj_user'] ?? null;
        }

        if (!$user) {
            return false;
        }

        if ((int)($user->id_usuario ?? 0) === 1 || (int)($user->id_perfil ?? 0) === 1) {
            return true;
        }

        $perfilModel = new PerfilModel();
        $perfil = $perfilModel->find($user->id_perfil ?? 0);
        if ($perfil) {
            $nomePerfil = strtoupper(trim($perfil->nome_perfil ?? ''));
            if (in_array($nomePerfil, ['SYSADM', 'ADMIN', 'ADMINISTRADOR', 'SUPERADMIN', 'ROOT'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna lista de IDs de departamentos aos quais o usuário está vinculado
     */
    public function getDepartamentosIdsPorUsuario(int $id_usuario): array
    {
        $db = db_connect();
        $builder = $db->table($this->table);
        $builder->select('id_departamento');
        $builder->where('id_sys_usuario', $id_usuario);

        $rows = $builder->get()->getResultArray();
        if (empty($rows)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_column($rows, 'id_departamento'))));
    }

    /**
     * Retorna objetos completos dos departamentos permitidos para o usuário (definidos na tela do departamento)
     */
    public function getDepartamentosPermitidosPorUsuario($user = null): array
    {
        if (!$user) {
            $session = session();
            $userData = $session->get('dsh_usuario');
            $user = $userData['obj_user'] ?? null;
        }

        if (!$user || empty($user->id_usuario)) {
            return [];
        }

        $departamentoModel = new DepartamentoModel();
        $ids = $this->getDepartamentosIdsPorUsuario((int)$user->id_usuario);
        if (empty($ids)) {
            return [];
        }

        return $departamentoModel->where('status', 1)
            ->whereIn('id_departamento', $ids)
            ->orderBy('nome', 'ASC')
            ->findAll();
    }

    /**
     * Valida se o usuário tem permissão para gerenciar um departamento específico
     */
    public function usuarioTemAcessoAoDepartamento($id_departamento, $user = null): bool
    {
        // Tratamento de segurança caso os argumentos sejam passados invertidos ($user, $id_departamento)
        if (is_object($id_departamento) && (is_numeric($user) || is_string($user))) {
            $temp = $id_departamento;
            $id_departamento = (int)$user;
            $user = $temp;
        }

        $id_departamento = (int)$id_departamento;

        if (!$user) {
            $session = session();
            $userData = $session->get('dsh_usuario');
            $user = $userData['obj_user'] ?? null;
        }

        if (!$user || empty($user->id_usuario)) {
            return false;
        }

        $permitidos = $this->getDepartamentosIdsPorUsuario((int)$user->id_usuario);
        return in_array($id_departamento, $permitidos);
    }

    /**
     * Retorna os IDs dos gestores (id_sys_usuario) de um departamento
     */
    public function getIdsGestoresPorDepartamento(int $id_departamento): array
    {
        $db = db_connect();
        $rows = $db->table($this->table)
            ->select('id_sys_usuario')
            ->where('id_departamento', $id_departamento)
            ->get()
            ->getResultArray();

        return array_values(array_unique(array_filter(array_map('intval', array_column($rows, 'id_sys_usuario')))));
    }

    /**
     * Retorna lista de usuários gestores vinculados a um departamento
     */
    public function getGestoresPorDepartamento(int $id_departamento): array
    {
        $db = db_connect();
        return $db->table('tb_departamento_gestor as dg')
            ->select('u.id_usuario, u.nome, u.usuario, p.nome_perfil')
            ->join('tb_sys_usuario as u', 'u.id_usuario = dg.id_sys_usuario', 'inner')
            ->join('tb_sys_perfil as p', 'p.id_perfil = u.id_perfil', 'left')
            ->where('dg.id_departamento', $id_departamento)
            ->orderBy('u.nome', 'ASC')
            ->get()
            ->getResult('object');
    }

    /**
     * Sincroniza os gestores de um departamento
     */
    public function sincronizarGestores(int $id_departamento, array $ids_sys_usuario): bool
    {
        $id_departamento = (int)$id_departamento;
        $db = db_connect();

        $db->table($this->table)->where('id_departamento', $id_departamento)->delete();

        $idsValidos = array_values(array_unique(array_filter(array_map('intval', $ids_sys_usuario), function($id) {
            return $id > 0;
        })));

        if (empty($idsValidos)) {
            return true;
        }

        $dados = [];
        foreach ($idsValidos as $id_sys_usuario) {
            $dados[] = [
                'id_departamento' => $id_departamento,
                'id_sys_usuario'  => $id_sys_usuario
            ];
        }

        $db->table($this->table)->insertBatch($dados);
        return true;
    }
}

