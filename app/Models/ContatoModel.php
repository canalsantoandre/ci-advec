<?php namespace App\Models;

use CodeIgniter\Model;

class ContatoModel extends Model{

	protected $table      = 'tb_contato';

    protected $primaryKey = 'id_contato';
    protected $returnType     = 'object';
    protected $allowedFields = ['id_contato','nome','email','telefone','instagram','site','mensagem','cidade','uf','date_insert','hash_id'];
	
    public function findByColumn($args = [],$getFirstRow=false)
    {
        $builder = $this->db->table('tb_contato');
          
        foreach ($args as $key => $valor) {
            $builder->where($key, $args[$key]);
        }
        $row = $builder->get();

        $row = ($getFirstRow?$row->getFirstRow():$row->getResult($this->tempReturnType));
        return  $row;
    }

    public function lista()
    {

        $db = db_connect();
        $builder = $db->table('tb_contato as a');
        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
    }
}