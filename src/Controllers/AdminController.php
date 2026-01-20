<?php

namespace App\Controllers;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Core\Twig;
use App\Core\Session;

class AdminController
{
    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('admin'))->handle();
    }

    public function dashboard()
    {
        Twig::display('dashboard/admin.twig', [
            'title' => 'Tableau de bord Administrateur',
            'user' => [
                'name' => Session::get('user_name') ?? 'Admin',
                'email' => Session::get('user_email') ?? 'admin@talenthub.com',
            ],
            'stats' => [
                'total_users' => 1250,
                'new_users_today' => 42,
                'active_jobs' => 189,
                'total_applications' => 2547,
            ],
        ]);
    }

    public function users()
    {
        Twig::display('admin/users.twig', [
            'title' => 'Gestion des utilisateurs',
        ]);
    }

    public function system()
    {
        Twig::display('admin/system.twig', [
            'title' => 'Configuration système',
        ]);
    }
}