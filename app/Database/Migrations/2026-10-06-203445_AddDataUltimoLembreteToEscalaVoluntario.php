<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDataUltimoLembreteToEscalaVoluntario extends Migration
{
    public function up()
    {
        $fields = [
            'data_ultimo_lembrete' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
                'after'      => 'data_resposta'
            ]
        ];

        if (!$this->db->fieldExists('data_ultimo_lembrete', 'tb_escala_voluntario')) {
            $this->forge->addColumn('tb_escala_voluntario', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('data_ultimo_lembrete', 'tb_escala_voluntario')) {
            $this->forge->dropColumn('tb_escala_voluntario', 'data_ultimo_lembrete');
        }
    }
}
