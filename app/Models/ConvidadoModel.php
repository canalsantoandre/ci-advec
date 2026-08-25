<?php

namespace App\Models;

use CodeIgniter\Model;

class ConvidadoModel extends Model
{

    protected $table      = 'tb_convidado';
    protected $primaryKey = 'id_convidado';
    protected $returnType     = 'object';
    protected $allowedFields = [
        'hash_convidado','nome_convidado_visualizacao', 'nome_convidado', 'email', 'telefone', 'status_convidado', 'id_funcao_eclesiastica', 'data_nascimento', 'link_fotos','nick_instagram', 'observacao'
    ];

    public function listaConvidados()
    {
        $db = db_connect();
        $builder = $db->table('tb_convidado as a');
        $builder->select('
            a.*,
            b.nm_funcao_eclesiastica,
            COUNT(DISTINCT CASE WHEN cc.status_presenca = 1 THEN cc.id_culto END) as total_presencas,
            COUNT(DISTINCT cc.id_culto) as total_cultos_agendados
        ');
        $builder->join('tb_funcao_eclesiastica as b', 'b.id_funcao_eclesiastica = a.id_funcao_eclesiastica', 'left');
        $builder->join('tb_culto_convidado as cc', 'cc.id_convidado = a.id_convidado', 'left');
        $builder->groupBy('a.id_convidado');
        $builder->orderBy('a.nome_convidado', 'ASC');

        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
    }

    public function findByColumn($column, $value)
    {
        $builder = $this->db->table('tb_convidado');
        $builder->where($column, $value);
        $row = $builder->get();

        //$row = $row->getResult($this->tempReturnType);
        return $row->getFirstRow(); //$row;
    }

    public function consultaConvidado($convidado)
    {

        $db = db_connect();
        $builder = $db->table('tb_convidado as usu');
        $builder->select('*, 
        "" perfil_acesso');
        $builder->join('tb_sys_perfil as sper', 'sper.id_perfil = usu.id_perfil', 'inner');
        $builder->where('usu.convidado', $convidado);
        $query = $builder->get();

        return $query->getFirstRow();
    }

    public function retornaPerfilAcesso($id_convidado)
    {

        $db = db_connect();
        $builder = $db->table('tb_convidado as usu');
        $builder->select('
        sper.id_perfil,
        scat.nome_categoria_modulo, 
        scat.exibir_titulo, 
        smod.id_modulo, smod.nome_modulo, 
        smod.icon_class_modulo, 
        smod.uri_modulo, 
        scat.ordem_exibicao_categoria, 
        smod.ordem_exibicao_modulo, 
        "" modulo_acao');

        $builder->join('tb_sys_perfil as sper', 'sper.id_perfil = usu.id_perfil', 'inner');
        $builder->join('tb_sys_perfil_modulo as spmo', 'spmo.id_perfil = sper.id_perfil', 'inner');
        $builder->join('tb_sys_modulo as smod', 'smod.id_modulo = spmo.id_modulo', 'inner');
        $builder->join('tb_sys_categoria_modulo as scat', 'scat.id_categoria_modulo = smod.id_categoria_modulo', 'inner');
        $builder->where(' usu.id_convidado', $id_convidado);
        $builder->where(' smod.status_modulo ', 1);
        $builder->orderBy('scat.ordem_exibicao_categoria, smod.ordem_exibicao_modulo');
        $query = $builder->get();

        $perfil = $query->getResult($this->tempReturnType);

        $perfilModel = new \App\Models\PerfilModuloAcaoModel ();
        foreach ($perfil as $row) {
            $c = $perfilModel->consultaModuloAcao ($row->id_perfil, $row->id_modulo,);
            $row->modulo_acao = $c;
            $result[] = $row;
        }
        return $result;
    }
}

