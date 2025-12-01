<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

//use App\Controllers\BaseController;
//use CodeIgniter\HTTP\ResponseInterface;

class Prueba extends ResourceController
{
    public function index()
    {
        return $this->respond([
            'status'  => 'ok',
            'message' => 'API funcionando desde CodeIgniter 4',
        ]);
    }
}
