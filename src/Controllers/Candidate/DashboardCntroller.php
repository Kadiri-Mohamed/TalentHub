<?php

namespace App\Controllers\Candidate;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Core\Twig;
use App\Services\CandidateService;

class DashboardController
{
    private CandidateService $candidateService;

    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('candidate'))->handle();

        $this->candidateService = new CandidateService();
    }

    public function index()
    {
        $candidate = $this->candidateService->getCurrentCandidate();

        if (!$candidate) {
            header('Location: /login');
            exit;
        }

        $stats = $this->candidateService->getMyStats($candidate->getId());
        $completion = $this->candidateService->getProfileCompletion($candidate);

        Twig::display('dashboard/candidate.twig', [
            'title'       => 'Tableau de bord Candidat',
            'candidate'   => $candidate,
            'stats'       => $stats,
            'completion'  => $completion,
        ]);
    }
}
