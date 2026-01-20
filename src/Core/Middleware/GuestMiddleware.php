<?php

namespace App\Core\Middleware;

use App\Core\Session;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        Session::init();

        if (Session::get('logged_in')) {
            $role = Session::get('role_name');
            header("Location: /{$role}/dashboard");
            exit;
        }
    }
}