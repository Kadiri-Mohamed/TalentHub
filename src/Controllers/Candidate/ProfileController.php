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

    // Show profile page
    public function index()
    {
        $candidate = $this->candidateService->getCurrentCandidate();

        Twig::display('candidate/profile.twig', [
            'title' => 'Mon profil',
            'candidate' => $candidate
        ]);
    }

    // Edit profile page
    public function edit()
    {
        $candidate = $this->candidateService->getCurrentCandidate();

        Twig::display('candidate/profile_edit.twig', [
            'title' => 'Modifier mon profil',
            'candidate' => $candidate
        ]);
    }

    // Update profile data
    public function update()
    {
        $candidate = $this->candidateService->getCurrentCandidate();
        if (!$candidate) {
            header('Location: /candidate/profile');
            exit;
        }

        // Update candidate fields from POST
        $candidate->setName($_POST['name'] ?? $candidate->getName());
        $candidate->setEmail($_POST['email'] ?? $candidate->getEmail());

        $this->candidateService->updateProfile($candidate);

        header('Location: /candidate/profile');
        exit;
    }

    // Upload CV
    public function uploadCv()
    {
        $candidate = $this->candidateService->getCurrentCandidate();
        if (!$candidate) {
            header('Location: /candidate/profile');
            exit;
        }

        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $this->candidateService->uploadCv($candidate->getId(), $_FILES['cv']);
        }

        header('Location: /candidate/profile');
        exit;
    }
}
