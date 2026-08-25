<?php namespace App\Models;

use CodeIgniter\Model;

class EmpreendedorModel extends Model{

	protected $table      = 'tb_empreendedor';

    protected $primaryKey = 'id_empreendedor';
    protected $returnType     = 'object';
    protected $allowedFields = ['id_empreendedor','nome','email','telefone','ramo_atividade','instagram','site','mensagem','qtde_convidado','cidade','uf','date_insert','hash_id'];
	
    public function findByColumn($args = [],$getFirstRow=false)
    {
        $builder = $this->db->table('tb_empreendedor');
          
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
        $builder = $db->table('tb_empreendedor as a');
        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
    }
}