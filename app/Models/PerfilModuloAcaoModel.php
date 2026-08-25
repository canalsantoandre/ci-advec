<?php namespace App\Models;

use CodeIgniter\Model;

class PerfilModuloAcaoModel extends Model{

	protected $table      = 'tb_sys_perfil_modulo_acao';
    protected $returnType     = 'object';
    protected $allowedFields = ['id_perfil','id_modulo','modulo_acao'];
	
    public function consultaModuloAcao($id_perfil, $id_modulo)
    {
        $db = db_connect();
        $builder = $db->table('tb_sys_perfil_modulo_acao');
        $builder->select('*');
        $builder->where('id_perfil =', $id_perfil);
        $builder->where('id_modulo =', $id_modulo);
        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
    }
}