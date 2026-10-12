<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWhatsappGroupAndNotificacaoAgendaToDepartamento extends Migration
{
    public function up()
    {
        $fields = [
            'whatsapp_group_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'after'      => 'notificar_whatsapp',
                'comment'    => 'ID ou JID do grupo do WhatsApp de destino das notificações deste departamento'
            ],
            'receber_notificacao_agenda' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'whatsapp_group_id',
                'comment'    => '1 = Sim (recebe notificações de agendamentos/artes), 0 = Não'
            ]
        ];

        if (!$this->db->fieldExists('whatsapp_group_id', 'tb_departamento')) {
            $this->forge->addColumn('tb_departamento', [
                'whatsapp_group_id' => $fields['whatsapp_group_id']
            ]);
        }

        if (!$this->db->fieldExists('receber_notificacao_agenda', 'tb_departamento')) {
            $this->forge->addColumn('tb_departamento', [
                'receber_notificacao_agenda' => $fields['receber_notificacao_agenda']
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('whatsapp_group_id', 'tb_departamento')) {
            $this->forge->dropColumn('tb_departamento', 'whatsapp_group_id');
        }
        if ($this->db->fieldExists('receber_notificacao_agenda', 'tb_departamento')) {
            $this->forge->dropColumn('tb_departamento', 'receber_notificacao_agenda');
        }
    }
}
