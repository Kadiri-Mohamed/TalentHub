<?php

namespace App\Controllers;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Core\Twig;
use App\Core\Session;

class CandidateController
{
    public function __construct()
    {
        // Appliquer les middlewares directement
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('candidate'))->handle();
    }

    public function dashboard()
    {
        Twig::display('dashboard/candidate.twig', [
            'title' => 'Tableau de bord Candidat',
            'user' => [
                'name' => Session::get('user_name') ?? 'Candidat',
                'email' => Session::get('user_email') ?? 'candidat@example.com',
            ],
            'stats' => [
                'applications' => 5,
                'interviews' => 2,
                'offers' => 1,
                'saved_jobs' => 8,
            ],
        ]);
    }

    public function applications()
    {
        Twig::display('candidate/applications.twig', [
            'title' => 'Mes candidatures',
        ]);
    }

    public function profile()
    {
        Twig::display('candidate/profile.twig', [
            'title' => 'Mon profil',
        ]);
    }
}