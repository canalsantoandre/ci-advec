<?php

namespace App\Models;

use CodeIgniter\Model;

class ConvidadoLinkFotoModel extends Model
{

    protected $table      = 'tb_convidado_link_foto';
    protected $primaryKey = 'id_convidado_link_foto';
    protected $returnType     = 'object';
    protected $allowedFields = ['id_convidado', 'descricao_link_foto', 'link_foto'];

    public function findByColumn($column, $value)
    {
        $builder = $this->db->table('tb_convidado_link_foto');
        $builder->where($column, $value);
        $row = $builder->get();

        //$row = $row->getResult($this->tempReturnType);
        return $row->getFirstRow(); //$row;
    }

    public function getConvidadoLinkFoto($id_convidado)
    {

        $db = db_connect();
        $builder = $db->table('tb_convidado_link_foto as clf');
        $builder->where(' clf.id_convidado', $id_convidado);

        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
    }
}
