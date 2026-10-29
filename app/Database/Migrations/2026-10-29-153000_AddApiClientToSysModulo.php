<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApiClientToSysModulo extends Migration
{
    public function up()
    {
        $db = db_connect();

        $existe = $db->table('tb_sys_modulo')->where('uri_modulo', 'api-client/')->countAllResults();
        if ($existe === 0) {
            $db->table('tb_sys_modulo')->insert([
                'id_categoria_modulo'   => 3,
                'nome_modulo'           => 'Aplicações API',
                'icon_class_modulo'     => 'bi bi-cpu-fill',
                'uri_modulo'            => 'api-client/',
                'ordem_exibicao_modulo' => 5,
                'status_modulo'         => 1
            ]);

            $idModulo = $db->insertID();

            // Vincula ao perfil SysAdm (id_perfil = 1) caso a tabela de permissões exista
            if ($db->tableExists('tb_sys_perfil_modulo')) {
                $db->table('tb_sys_perfil_modulo')->ignore(true)->insert([
                    'id_perfil' => 1,
                    'id_modulo' => $idModulo
                ]);
            }
        }
    }

    public function down()
    {
        $db = db_connect();
        $db->table('tb_sys_modulo')->where('uri_modulo', 'api-client/')->delete();
    }
}
