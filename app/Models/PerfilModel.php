<?php namespace App\Models;

use CodeIgniter\Model;

class PerfilModel extends Model {

    protected $table      = 'tb_sys_perfil';
    protected $primaryKey = 'id_perfil';
    protected $returnType     = 'object';
    protected $allowedFields = ['id_perfil', 'nome_perfil', 'content_view_default', 'status_perfil'];

    public function getProximoIdPerfil()
    {
        $db = db_connect();
        $maxRow = $db->table('tb_sys_perfil')->selectMax('id_perfil')->get()->getFirstRow();
        return (!empty($maxRow->id_perfil)) ? ($maxRow->id_perfil + 1) : 1;
    }

    public function perfilModulo($id_perfil, $id_modulo_pai=null) {
        $db = db_connect();
        $builder = $db->table('tb_sys_modulo as m');
        $builder->select('c.nome_categoria_modulo, 
                            m.id_modulo, 
                            m.icon_class_modulo, 
                            m.nome_modulo, 
                            ifnull(a.id_modulo,0) as id_modulo_perfil, 
                            "" as perfil_acoes,
                            "" as subitem');
        $builder->join('tb_sys_categoria_modulo as c','c.id_categoria_modulo = m.id_categoria_modulo','inner');
        $builder->join('tb_sys_perfil_modulo as a ','a.id_modulo = m.id_modulo and a.id_perfil = '.$id_perfil,'left');        
        $builder->where('m.id_modulo_pai', $id_modulo_pai);
        $builder->orderBy('c.ordem_exibicao_categoria, m.ordem_exibicao_modulo');
        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
    }

    public function perfilModuloAcao($id_modulo, $id_perfil) {
        $db = db_connect();
        $builder = $db->table('tb_sys_modulo_acao as ma');
        $builder->select('ma.id_modulo, ma.modulo_acao, ifnull(pma.modulo_acao,0) as perfil_acao');
        $builder->join('tb_sys_perfil_modulo_acao as pma','pma.id_modulo = ma.id_modulo and pma.modulo_acao = ma.modulo_acao and pma.id_perfil = '.$id_perfil,'left');        
        $builder->where('ma.id_modulo', $id_modulo);
        $builder->orderBy('ma.modulo_acao');
        $query = $builder->get();

        return $query->getResult($this->tempReturnType);
    }

    public function retornaPerfil($id_perfil) {
        $builder = $this->db->table('tb_sys_perfil');
        $builder->where('status_perfil', 1);
        $row = $builder->get();
        $lista = $row->getResult($this->tempReturnType);

        helper('form');
        $array = [];
        $array[0] = '-- Selecione o Perfil --';
        foreach ($lista as $item) {
            $array[$item->id_perfil] = $item->nome_perfil;
        }
        return form_dropdown('id_perfil', $array, $id_perfil, 'id="id_perfil" class="form-select" required');
    }
}