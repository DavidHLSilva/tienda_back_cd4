<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nombre'          => 'Super',
            'apellido_paterno'=> 'Admin',
            'apellido_materno'=> 'Sistema',
            'email'           => 'admin@davaal.com',
            'password'        => password_hash('admin123', PASSWORD_DEFAULT),
            'perfil'          => 'admin',
            'user_id_created' => null

        ];

        // Se usa Query Builder para insertar el usuario
        $this->db->table('usuarios')->insert($data);
    }
}
