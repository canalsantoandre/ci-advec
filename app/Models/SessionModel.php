<?php namespace App\Models;

use CodeIgniter\Model;

class SessionModel extends Model{

    public function retornaSessao($data, $uri_modulo)
    {
        $session = session();
        $data['usuario'] = $session->get('dsh_usuario')['obj_user'];

        $moduloModel = new \App\Models\ModuloModel ();
        $modulo = $moduloModel->findByColumn('uri_modulo',$uri_modulo);
        $data['sys_module'] = $modulo;
        
        $model = new \App\Models\PerfilModuloAcaoModel ();
        $data["sys_action"] = $model->consultaModuloAcao ($data['usuario']->id_perfil, $modulo->id_modulo);

        /* -------------------------------------------------------- */
        /* VALIDA OS ACESSOS DESTE MODULO */
        /* -------------------------------------------------------- */
        $action = array_column($data['sys_action'], 'modulo_acao');
        $obj = new \stdClass();

        // ->geral
        $obj->create = is_int(array_search('create', $action));
        $obj->read = is_int(array_search('read', $action));
        $obj->update= is_int(array_search('update', $action));
        $obj->delete= is_int(array_search('delete', $action));

        // -> convidado
        $obj->linkfotos_inserir= is_int(array_search('linkfotos_inserir', $action));
        $obj->linkfotos_excluir= is_int(array_search('linkfotos_excluir', $action));
        $obj->linkfotos_visualizar= is_int(array_search('linkfotos_visualizar', $action));

        // ->usuario
        $obj->reset_password= is_int(array_search('reset_password', $action));

        $data['sys_action'] = $obj;
        /* -------------------------------------------------------- */

        return $data; //$row;
    }
}