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
    public function uploadCv(int $idCandidat, array $fichier): array
    {
        $resultat = [
            'success' => false,
            'message' => ''
        ];

        if (empty($fichier)) {
            $resultat['message'] = 'Aucun fichier fourni';
            return $resultat;
        }

        $repertoireUpload = __DIR__ . '/../../public/uploads/cv/';
        $nomFichier = $idCandidat . '.pdf';
        $cheminFichier = $repertoireUpload . $nomFichier;

        if (!file_exists($repertoireUpload)) {
            mkdir($repertoireUpload, 0777, true);
        }

        if (move_uploaded_file($fichier['tmp_name'], $cheminFichier)) {
            $cheminCv = '/uploads/cv/' . $nomFichier;
            $this->candidateRepository->updateCvPath($idCandidat, $cheminCv);

            $resultat['success'] = true;
            $resultat['message'] = 'CV téléchargé avec succès';
        }

        return $resultat;
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

    public function getProfileCompletion(Candidate $candidate): int
    {
        $fields = [
            $candidate->getName(),
            $candidate->getEmail(),
            $candidate->getCvPath(),
            $candidate->getSalaryMin(),
            $candidate->getSalaryMax()
        ];

        $completed = count(array_filter($fields));
        return (int)(($completed / count($fields)) * 100);
    }
}
