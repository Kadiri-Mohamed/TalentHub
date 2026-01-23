<?php

namespace App\Controllers;

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
        $stats = $this->adminService->getStats();
        Twig::display('auth/login.twig', [
            'title' => 'Tableau de bord Administrateur',
            'user' => [
                'name' => Session::get('user_name') ?? 'Admin',
                'email' => Session::get('user_email') ?? 'admin@talenthub.com',
            ],
            'stats' => $stats,
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