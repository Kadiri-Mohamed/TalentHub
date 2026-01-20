<?php

namespace App\Controllers;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Core\Twig;
use App\Core\Session;

class RecruiterController
{
    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('recruiter'))->handle();
    }

    public function dashboard()
    {
        Twig::display('dashboard/recruiter.twig', [
            'title' => 'Tableau de bord Recruteur',
            'user' => [
                'name' => Session::get('user_name') ?? 'Recruteur',
                'company' => 'TechRecruit',
                'email' => Session::get('user_email') ?? 'recruteur@example.com',
            ],
            'stats' => [
                'active_jobs' => 12,
                'total_applications' => 156,
                'new_applications' => 24,
                'hired_candidates' => 8,
            ],
        ]);
    }

    public function jobPostings()
    {
        Twig::display('recruiter/job_postings.twig', [
            'title' => 'Mes offres d\'emploi',
        ]);
    }

    public function candidates()
    {
        Twig::display('recruiter/candidates.twig', [
            'title' => 'Candidats',
        ]);
    }
}