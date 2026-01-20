<?php
// src/Controllers/LogoutController.php

namespace App\Controllers;

use App\Services\AuthService;
use App\Core\Middleware\AuthMiddleware;

class LogoutController
{
    public function logout()
    {
        (new AuthMiddleware())->handle();
        $auth = new AuthService();
        $auth->logout();
    }
}