<?php

namespace ZakharovAndrew\user\models;

use Yii;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthJwt
{    
    public static function generateToken($userId)
    {
        $tokenId    = base64_encode(random_bytes(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt;
        $expire     = $issuedAt + ( Yii::$app->getModule('user')->jwtExpiresTime);
        $serverName = Yii::$app->request->hostInfo;

        $token = [
            'iat'  => $issuedAt,         
            'jti'  => $tokenId,          
            'iss'  => $serverName,       
            'nbf'  => $notBefore,       
            'exp'  => $expire,          
            'data' => [
                'userId' => $userId,    
            ]
        ];

        return JWT::encode($token, Yii::$app->getModule('user')->jwtSecretKey, 'HS256');
    }
    
    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(Yii::$app->getModule('user')->jwtSecretKey, 'HS256'));
            return $decoded->data->userId; // return user ID
        } catch (\Exception $e) {
            return null; // token invalid
        }
    }
}

