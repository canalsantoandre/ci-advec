<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificarWhatsappToDepartamento extends Migration
{
    public function up()
    {
        $fields = [
            'notificar_whatsapp' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'null'       => false,
                'after'      => 'cor_identificacao',
                'comment'    => '1 = Sim (notificar voluntarios ao escalar), 0 = Nao'
            ]
        ];

        if (!$this->db->fieldExists('notificar_whatsapp', 'tb_departamento')) {
            $this->forge->addColumn('tb_departamento', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('notificar_whatsapp', 'tb_departamento')) {
            $this->forge->dropColumn('tb_departamento', 'notificar_whatsapp');
        }
    }
}
