<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{

    protected $table      = 'tb_sys_usuario';
    protected $primaryKey = 'id_usuario';
    protected $returnType     = 'object';
    protected $allowedFields = [
        'usuario', 'nome', 'senha', 'status_usuario', 'id_perfil', 'alterar_senha', 'force_pwd_change', 'otp_code', 'otp_expires_at', 'senha_usuario', 'data_ultimo_login', 'data_ultima_senha', 'hash_user'
    ];


    public function listaUsuarios()
    {

        $db = db_connect();
        $builder = $db->table('tb_sys_usuario as a');

        $builder->join('tb_sys_perfil as b', 'b.id_perfil = a.id_perfil ', 'inner');
        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
    }

    public function findByColumn($column, $value)
    {
        $builder = $this->db->table('tb_sys_usuario');
        $builder->where($column, $value);
        $row = $builder->get();

        //$row = $row->getResult($this->tempReturnType);
        return $row->getFirstRow(); //$row;
    }

    public function consultaUsuario($usuario)
    {

        $db = db_connect();
        $builder = $db->table('tb_sys_usuario as usu');
        $builder->select('*, 
        "" perfil_acesso');
        $builder->join('tb_sys_perfil as sper', 'sper.id_perfil = usu.id_perfil', 'inner');
        $builder->where('usu.usuario', $usuario);
        $query = $builder->get();

        return $query->getFirstRow();
    }

    public function consulta ($id_usuario, $id_modulo_pai){
        $db = db_connect();
        $builder = $db->table('tb_sys_usuario as usu');
        $builder->select('
        sper.id_perfil,
        scat.nome_categoria_modulo, 
        scat.exibir_titulo, 
        smod.id_modulo, ifnull(smod.id_modulo_pai,0) as id_modulo_pai, smod.nome_modulo, 
        smod.icon_class_modulo, 
        smod.uri_modulo, 
        scat.ordem_exibicao_categoria, 
        smod.ordem_exibicao_modulo, 
        "" modulo_acao,
        "" subitem');

        $builder->join('tb_sys_perfil as sper', 'sper.id_perfil = usu.id_perfil', 'inner');
        $builder->join('tb_sys_perfil_modulo as spmo', 'spmo.id_perfil = sper.id_perfil', 'inner');
        $builder->join('tb_sys_modulo as smod', 'smod.id_modulo = spmo.id_modulo', 'inner');
        $builder->join('tb_sys_categoria_modulo as scat', 'scat.id_categoria_modulo = smod.id_categoria_modulo', 'inner');
        $builder->where(' usu.id_usuario', $id_usuario);
        $builder->where(' smod.status_modulo', 1);
        $builder->where(' smod.id_modulo_pai', $id_modulo_pai);
        $builder->orderBy('scat.ordem_exibicao_categoria, smod.ordem_exibicao_modulo');
        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
               
    }
    public function retornaPerfilAcesso($id_usuario)
    {
/*
        $db = db_connect();
        $builder = $db->table('tb_usuario as usu');
        $builder->select('
        sper.id_perfil,
        scat.nome_categoria_modulo, 
        scat.exibir_titulo, 
        smod.id_modulo, smod.id_modulo_pai, smod.nome_modulo, 
        smod.icon_class_modulo, 
        smod.uri_modulo, 
        scat.ordem_exibicao_categoria, 
        smod.ordem_exibicao_modulo, 
        "" modulo_acao,
        "" subitem');

        $builder->join('tb_sys_perfil as sper', 'sper.id_perfil = usu.id_perfil', 'inner');
        $builder->join('tb_sys_perfil_modulo as spmo', 'spmo.id_perfil = sper.id_perfil', 'inner');
        $builder->join('tb_sys_modulo as smod', 'smod.id_modulo = spmo.id_modulo', 'inner');
        $builder->join('tb_sys_categoria_modulo as scat', 'scat.id_categoria_modulo = smod.id_categoria_modulo', 'inner');
        $builder->where(' usu.id_usuario', $id_usuario);
        $builder->where(' smod.status_modulo', 1);
        $builder->where(' smod.id_modulo_pai', null);
        $builder->orderBy('scat.ordem_exibicao_categoria, smod.ordem_exibicao_modulo');
        $query = $builder->get();

        $perfil = $query->getResult($this->tempReturnType);*/
        $perfil = $this->consulta($id_usuario, null);

        $perfilModel = new \App\Models\PerfilModuloAcaoModel ();
        foreach ($perfil as $row) {

            $c = $perfilModel->consultaModuloAcao ($row->id_perfil, $row->id_modulo);
            $row->modulo_acao = $c;

            $subitem = $this->consulta ($id_usuario,$row->id_modulo);
            $row->subitem = $subitem;

            $result[] = $row;
        }

        return $result;
    }

}

