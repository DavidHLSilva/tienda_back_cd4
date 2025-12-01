<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;

/**
 * @property \CodeIgniter\HTTP\IncomingRequest $request
 */
class Auth extends ResourceController
{
    public function login()
    {
        if($this->request->getVar('email') && $this->request->getVar('password')){
            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');
            $modelUser = new UserModel();
            //$user = $model->where('email', $email)->first();
            $user = $modelUser->authenticate($email, $password);
            if($user){
                $key = getenv('JWT_SECRET'); // Guardar en .env
                $iat = time(); // Tiempo actual
                $exp = $iat + 3600; // Expira en 1 hora
                $payload = [
                    'iss' => 'TiendaApi',
                    'sub' => $user['id'], // Subject (ID del usuario)
                    'iat' => $iat,
                    'exp' => $exp,
                    'data' => [
                        'perfil' => $user['perfil'],
                        'email'  => $user['email'],
                        'nombre' => $user['nombre'].' '.$user['apellido_paterno'].' '.$user['apellido_materno']
                    ]
                ];
                $accesstoken = JWT::encode($payload, $key, 'HS256');

                // Generar refresh token (valor en claro para el cliente)
                $refreshPlain = bin2hex(random_bytes(64));
                // Guardar sólo hash en BD (ejemplo usando password_hash)
                $refreshHash = password_hash($refreshPlain, PASSWORD_DEFAULT);
                $refreshExpires = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30)); // 30 días
                
                $modelRefresh = new \App\Models\RefreshTokensModel();
                $modelRefresh->createRefreshToken([
                    'user_id' => $user['id'],
                    'token_hash' => $refreshHash,
                    'expires_at' => $refreshExpires,
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getAgentString()
                ]);

                return $this->respond([
                    'status' => 200,
                    'message' => 'Login exitoso',
                    'accesstoken' => $accesstoken,
                    'expires_in' => $exp,
                    'refresh_token' => $refreshPlain,
                    'user_id' => $user['id']
                ]);
            } else {
                return $this->fail('Credenciales inválidas', 401);
            }
        } else {
            return $this->fail('Parametros inválidos', 401);
        }
    }

    public function refreshToken()
    {
        $input = $this->request->getJSON(true);
        $refreshPlain = $input['refresh_token'] ?? null;
        if (empty($refreshPlain)) {
            return $this->fail('Refresh token requerido', 400);
        }

        $db = \Config\Database::connect();
        $row = $db->table('refresh_tokens')
                ->where('revoked', 0)
                ->where('expires_at >=', date('Y-m-d H:i:s'))
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();

        // Buscar fila cuyo hash coincida
        $found = null;
        foreach ($row as $r) {
            if (password_verify($refreshPlain, $r['token_hash'])) {
                $found = $r;
                break;
            }
        }

        if (! $found) {
            return $this->failUnauthorized('Refresh token inválido o expirado');
        }

        // Opcional: revocar token usado (rotación)
        $db->table('refresh_tokens')->where('id', $found['id'])->update(['revoked' => 1]);

        // Generar nuevo access token
        $accessKey = env('JWT_SECRET');
        $iat = time();
        $exp = $iat + 3600;
        $payload = [ /* iss, sub => $found['user_id'], iat, exp, data... */ ];
        $newAccess = \Firebase\JWT\JWT::encode($payload, $accessKey, 'HS256');

        // Opcional: generar nuevo refresh token y guardarlo (rotación)
        $newRefreshPlain = bin2hex(random_bytes(64));
        $newRefreshHash = password_hash($newRefreshPlain, PASSWORD_DEFAULT);
        $db->table('refresh_tokens')->insert([
            'user_id' => $found['user_id'],
            'token_hash' => $newRefreshHash,
            'expires_at' => date('Y-m-d H:i:s', time() + (60*60*24*30)),
            'revoked' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->respond([
            'access_token' => $newAccess,
            'expires_in' => 3600,
            'refresh_token' => $newRefreshPlain,
        ]);
    }

}
