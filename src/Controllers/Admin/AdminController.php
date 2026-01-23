<?php

namespace App\Controllers\Admin;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Services\AdminService;
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
    }

    public function dashboard()
    {
        // Récupérer les statistiques de l'administrateur
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