<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Auth;
use App\Models\UserModel;

class AuthController
{

    public function register($data): array
    {
        return UserModel::create($data);
    }

    public function login($data): array
    {
        return Auth::login($data);
    }
}
