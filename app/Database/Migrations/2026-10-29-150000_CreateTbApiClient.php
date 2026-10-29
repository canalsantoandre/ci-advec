<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbApiClient extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_api_client' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome_aplicacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'client_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'client_secret' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jwt_secret' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'tempo_expiracao' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 3600,
            ],
            'limite_requisicoes_minuto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 60,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'ultimo_acesso' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'total_requisicoes' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'date_insert' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'date_update' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id_api_client', true);
        $this->forge->createTable('tb_api_client', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_api_client', true);
    }
}
