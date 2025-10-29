<?php

namespace App\Http\Middleware;

use App\Models\UserModel;
use DateTime;

class Auth
{

    public static function generateToken(string $email, string $password_hash): string
    {
        $randomBytes = $email . $password_hash . random_bytes(32);
        $token = bin2hex($randomBytes);

        $hashedToken = hash('sha256', $token);

        return $hashedToken;
    }

    public static function login($data): array
    {
        $email = $data['email'];
        $password = $data['password'];

        // password_hash gets in the token hash
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $userData = UserModel::findByEmail($email);
        if (isset($userData) && !empty($userData)) {
            if (password_verify($password, $userData->password_hash)) {
                $token = self::generateToken($email, $password_hash);
            }
        } else {
            return ['error' => 'Could not find the user'];
        }

        $expire_minutes = 5;

        $token_expire_date = new DateTime()->modify('+'.$expire_minutes.' minute')->format('Y-m-d H:i:s');

        $setToken = UserModel::setToken($token, $token_expire_date, $userData->id);

        if ($setToken !== true) {
            return ['error' => 'Token could not be set'];
        }
        return [
            'message' => 'The user is logged in',
            'token' => $token,
        ];
    }
}
