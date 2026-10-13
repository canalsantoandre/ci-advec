<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthorAndGenreToResources extends Migration
{
    public function up()
    {
        $fields = [
            'author' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'title'
            ],
            'genre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'author'
            ]
        ];

        if (!$this->db->fieldExists('author', 'resources')) {
            $this->forge->addColumn('resources', [
                'author' => $fields['author']
            ]);
        }

        if (!$this->db->fieldExists('genre', 'resources')) {
            $this->forge->addColumn('resources', [
                'genre' => $fields['genre']
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('genre', 'resources')) {
            $this->forge->dropColumn('resources', 'genre');
        }

        if ($this->db->fieldExists('author', 'resources')) {
            $this->forge->dropColumn('resources', 'author');
        }
    }
}
