<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDataEspecificaToCultoPadrao extends Migration
{
    public function up()
    {
        $fields = [
            'data_especifica' => [
                'type'       => 'DATE',
                'null'       => true,
                'default'    => null,
                'after'      => 'tipo_recorrencia'
            ]
        ];

        if (!$this->db->fieldExists('data_especifica', 'tb_culto_padrao')) {
            $this->forge->addColumn('tb_culto_padrao', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('data_especifica', 'tb_culto_padrao')) {
            $this->forge->dropColumn('tb_culto_padrao', 'data_especifica');
        }
    }
}
