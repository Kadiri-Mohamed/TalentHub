<?php
// src/Core/Middleware/RoleMiddleware.php

namespace App\Core\Middleware;
use App\Core\Twig;
use App\Core\Session;

class RoleMiddleware implements MiddlewareInterface
{
    private string $requiredRole;

    public function __construct(string $role)
    {
        Session::init();
        $this->requiredRole = $role;
    }

    public function handle(): void
    {
        if (Session::get('role_name') !== $this->requiredRole) {
            twig::display('errors/403.twig');
            exit;
        }
    }
}