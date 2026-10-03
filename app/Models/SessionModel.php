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
        $idModulo = $modulo ? ($modulo->id_modulo ?? 0) : 0;
        $idPerfil = $data['usuario']->id_perfil ?? 1;
        $data["sys_action"] = $model->consultaModuloAcao ($idPerfil, $idModulo);

        /* -------------------------------------------------------- */
        /* VALIDA OS ACESSOS DESTE MODULO */
        /* -------------------------------------------------------- */
        $action = is_array($data['sys_action']) ? array_column($data['sys_action'], 'modulo_acao') : [];
        $obj = new \stdClass();

        // Se for SysAdm (id_perfil = 1) e o módulo ainda não tiver ações no banco, concede acesso padrão
        $isSysAdm = ($idPerfil == 1);

        // ->geral
        $obj->create = is_int(array_search('create', $action)) || $isSysAdm;
        $obj->read = is_int(array_search('read', $action)) || $isSysAdm;
        $obj->update= is_int(array_search('update', $action)) || $isSysAdm;
        $obj->delete= is_int(array_search('delete', $action)) || $isSysAdm;
        $obj->update_past = is_int(array_search('update_past', $action)) || $isSysAdm;

        // -> convidado
        $obj->linkfotos_inserir= is_int(array_search('linkfotos_inserir', $action)) || $isSysAdm;
        $obj->linkfotos_excluir= is_int(array_search('linkfotos_excluir', $action)) || $isSysAdm;
        $obj->linkfotos_visualizar= is_int(array_search('linkfotos_visualizar', $action)) || $isSysAdm;

        // ->usuario
        $obj->reset_password= is_int(array_search('reset_password', $action)) || $isSysAdm;

        // ->voluntario / convites
        $obj->send_invite = is_int(array_search('send_invite', $action)) || $isSysAdm;
        $obj->approve_invite = is_int(array_search('approve_invite', $action)) || $isSysAdm;

        $data['sys_action'] = $obj;
        /* -------------------------------------------------------- */

        return $data;
    }
}