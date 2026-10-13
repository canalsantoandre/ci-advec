<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnaliseJustificativaToEscalaVoluntario extends Migration
{
    public function up()
    {
        $fields = [
            'status_analise_justificativa' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
                'default'    => 'PENDENTE',
                'null'       => false,
                'after'      => 'justificativa_recusa'
            ],
            'justificativa_parecer_gestor' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'after'      => 'status_analise_justificativa'
            ],
            'justificativa_analisado_por' => [
                'type'       => 'INT',
                'null'       => true,
                'default'    => null,
                'after'      => 'justificativa_parecer_gestor'
            ],
            'justificativa_analisado_em' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
                'after'      => 'justificativa_analisado_por'
            ]
        ];

        foreach ($fields as $colName => $colDef) {
            if (!$this->db->fieldExists($colName, 'tb_escala_voluntario')) {
                $this->forge->addColumn('tb_escala_voluntario', [$colName => $colDef]);
            }
        }
    }

    public function down()
    {
        $columns = [
            'justificativa_analisado_em',
            'justificativa_analisado_por',
            'justificativa_parecer_gestor',
            'status_analise_justificativa'
        ];

        foreach ($columns as $col) {
            if ($this->db->fieldExists($col, 'tb_escala_voluntario')) {
                $this->forge->dropColumn('tb_escala_voluntario', $col);
            }
        }
    }
}
