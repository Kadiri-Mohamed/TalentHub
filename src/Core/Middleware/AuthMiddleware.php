<?php

namespace App\Core\Middleware;

use App\Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        Session::init();
        if (!Session::get('logged_in')) {
            header('Location: /login');
            exit;
        }
    }
}