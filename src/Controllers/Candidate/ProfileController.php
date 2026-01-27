<?php

namespace App\Controllers\Candidate;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Core\Twig;
use App\Services\CandidateService;

class ProfileController
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

        Twig::display('candidate/profile.twig', [
            'title' => 'Mon profil',
            'candidate' => $candidate
        ]);
    }

    public function edit()
    {
        $candidate = $this->candidateService->getCurrentCandidate();

        Twig::display('candidate/profile_edit.twig', [
            'title' => 'Modifier mon profil',
            'candidate' => $candidate
        ]);
    }

    public function update()
    {
        $candidate = $this->candidateService->getCurrentCandidate();
        if (!$candidate) {
            header('Location: /candidate/profile');
            exit;
        }

        $candidate->setName($_POST['name'] ?? $candidate->getName());
        $candidate->setEmail($_POST['email'] ?? $candidate->getEmail());

        $this->candidateService->updateProfile($candidate);

        header('Location: /candidate/profile');
        exit;
    }

    
}
