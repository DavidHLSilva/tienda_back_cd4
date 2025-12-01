<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;

/**
 * @property \CodeIgniter\HTTP\IncomingRequest $request
 */

class Users extends ResourceController
{
    public function prueba()
    {
        // ... validaciones ...
    
        // Para obtener el ID del creador, lo ideal es decodificar el token nuevamente 
        // o pasarlo desde el Filtro (si modificaste el request).
        // Aquí un ejemplo rápido decodificando de nuevo:
        $adminId = service('request')->id;
        $data = service('request')->user;
        /*$newUser = [
            // ... campos ...
            'user_id_created' => $adminId,
        ];*/
    
        return $this->respond([
            'status' => 200,
            'message' => 'PRUEBA EXITOSA TOKEN',
            'data' => $data,
            'user_id' => $adminId,
            'exp' => service('request')->exp
        ]);
        
        // ... insert ...
    }

    public function create()
    {
        // ... validaciones ...
    
        // Para obtener el ID del creador, lo ideal es decodificar el token nuevamente 
        // o pasarlo desde el Filtro (si modificaste el request).
        // Aquí un ejemplo rápido decodificando de nuevo:
        $adminId = service('request')->id;
        $newUser = [
            // ... campos ...
            'user_id_created' => $adminId,
        ];
        
        // ... insert ...
    }
}
