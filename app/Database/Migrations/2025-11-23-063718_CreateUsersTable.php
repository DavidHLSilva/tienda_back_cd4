<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'apellido_paterno'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'apellido_materno'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'email'       => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'unique'     => true,
            ],
            'password'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'perfil'      => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'almacen', 'ventas', 'supervisor'],
                'default'    => 'almacen',
            ],
            'user_id_created' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'activo' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        // Opcional: Agregar llave foránea para user_id_created si se desea integridad referencial estricta
        // $this->forge->addForeignKey('user_id_created', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('usuarios');
    }

    public function down()
    {
        $this->forge->dropTable('usuarios');
    }
}
