<?php

namespace App\Controllers\Admin;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Services\AdminService;
// use App\Services\;
use App\Core\Twig;
use App\Core\Session;

class AdminController
{

 private AdminService $adminService;

    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('admin'))->handle();
        $this->adminService = new AdminService();
        // $this->authService = new AuthService();
    }

}