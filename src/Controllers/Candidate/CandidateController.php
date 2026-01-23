<?php

namespace App\Controllers\Candidate;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Services\CandidateService;
use App\Core\Twig;
use App\Core\Session;

class CandidateController
{
    private CandidateService $candidateService;
    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('candidate'))->handle();
        $this->candidateService = new CandidateService();

    }

    public function dashboard()
    {

                $stats = $this->candidateService->getMyStats(Session::get('user_id'));

        Twig::display('dashboard/candidate.twig', [
            'title' => 'Tableau de bord Candidat',
            'user' => [
                'name' => Session::get('user_name') ?? 'Candidat',
                'email' => Session::get('user_email') ?? 'candidat@example.com',
            ],
            'stats' => $stats ,
        ]);
    }
   
}