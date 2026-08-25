<?php namespace App\Models;

use CodeIgniter\Model;

class PerfilModuloModel extends Model{

	protected $table      = 'tb_sys_perfil_modulo';
    protected $returnType     = 'object';
    protected $allowedFields = ['id_perfil','id_modulo'];
	
}