<?php namespace App\Models;

use CodeIgniter\Model;

class FuncaoModel extends Model{

	protected $table      = 'tb_funcao_eclesiastica';
    protected $primaryKey = 'id_funcao_eclesiastica';
    protected $returnType     = 'object';
    protected $allowedFields = ['id_funcao_eclesiastica','nm_funcao_eclesiastica','nm_sigla','cor','icon','ordem'];
	

    public function retornaFuncao($id_funcao_eclesiastica)
    {

        $builder = $this->db->table('tb_funcao_eclesiastica');

        $row = $builder->get();
        $lista = $row->getResult($this->tempReturnType);

        helper('form');
        $array = [];
        $array[0] = '-- Função --';
        foreach ($lista as $item) {
            $array[$item->id_funcao_eclesiastica] = $item->nm_funcao_eclesiastica;
        }
        return form_dropdown('id_funcao_eclesiastica', $array, $id_funcao_eclesiastica, 'id="id_funcao_eclesiastica" class="form-control"');
    }

}