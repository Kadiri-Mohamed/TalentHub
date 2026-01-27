<?php

namespace App\Services;

use App\Repositories\CandidateRepository;
use App\Repositories\ApplicationRepository;
use App\Models\Candidate;
use App\Core\Session;

class CandidateService
{
    private CandidateRepository $candidateRepository;
    private ApplicationRepository $applicationRepository;

    public function __construct()
    {
        $this->candidateRepository = new CandidateRepository();
        $this->applicationRepository = new ApplicationRepository();
    }

    public function getCurrentCandidate(): ?Candidate
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }
        return $this->candidateRepository->getById($userId);
    }

    public function createCandidate(array $data): bool
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return false;
        }

        if ($this->candidateRepository->findByEmail($data['email'])) {
            return false;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->candidateRepository->createCandidate($data);
    }

    public function updateProfile(Candidate $candidate): bool
    {
        return $this->candidateRepository->updateCandidate($candidate);
    }

    public function updateSalaryExpectations(
        int $candidateId,
        float $min,
        float $max
    ): bool {
        if ($min > $max) {
            return false;
        }
        return $this->candidateRepository->updateSalary($candidateId, $min, $max);
    }

    public function getMyApplications(int $candidateId): array
    {
        return $this->applicationRepository->findByCandidate($candidateId);
    }

    public function getMyStats(int $candidateId): array
    {
        $applications = $this->getMyApplications($candidateId);

        $stats = [
            'total' => count($applications),
            'pending' => 0,
            'accepted' => 0,
            'rejected' => 0,
        ];

        foreach ($applications as $app) {
            $stats[$app->getStatus()]++;
        }

        return $stats;
    }
}
