<?php namespace App\Models;

use CodeIgniter\Model;

class ModuloModel extends Model{

	protected $table      = 'tb_sys_modulo';
    protected $returnType     = 'object';
    protected $allowedFields = ['id_modulo','id_modulo_pai','ordem_exibicao_modulo','id_categoria_modulo','nome_modulo','icon_class_modulo','uri_modulo','status_modulo'];
	
    public function findByColumn($column, $value)
    {
        $builder = $this->db->table('tb_sys_modulo');
        $builder->where($column, $value);
        $row = $builder->get();

        //$row = $row->getResult($this->tempReturnType);
        return $row->getFirstRow(); //$row;
    }
}