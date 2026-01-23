<?php

namespace App\Services;

use App\Models\Application;
use App\Repositories\ApplicationRepository;
use App\Repositories\OfferRepository;
use App\Repositories\CandidateRepository;

class ApplicationService
{
    private ApplicationRepository $applicationRepository;
    private OfferRepository $offerRepository;
    private CandidateRepository $candidateRepository;

    public function __construct()
    {
        $this->applicationRepository = new ApplicationRepository();
        $this->offerRepository = new OfferRepository();
        $this->candidateRepository = new CandidateRepository();
    }

    public function apply(int $offerId, int $candidateId, string $cvPath, string $coverLetter): bool
    {
        $offer = $this->offerRepository->getById($offerId);
        if (!$offer) {
            return false;
        }

        $candidate = $this->candidateRepository->getById($candidateId);
        if (!$candidate) {
            return false;
        }

        if ($this->hasApplied($candidateId, $offerId)) {
            return false;
        }

        $application = new Application(
            0,
            $offer,
            $candidate,
            'pending',
            date('Y-m-d H:i:s'),
            $cvPath,
            $coverLetter
        );

        return $this->applicationRepository->create($application);
    }

    public function getAll(int $candidateId): array
    {
        return $this->applicationRepository->findByCandidate($candidateId);
    }

    public function getById(int $applicationId, int $candidateId): ?Application
    {
        $application = $this->applicationRepository->getById($applicationId);
        if ($application && $application->getCandidate()->getId() === $candidateId) {
            return $application;
        }
        return null;
    }

    public function getByOffer(int $offerId, int $candidateId): ?Application
    {
        $applications = $this->getAll($candidateId);
        
        foreach ($applications as $application) {
            if ($application->getOffer()->getId() === $offerId) {
                return $application;
            }
        }
        
        return null;
    }

    public function deleteApplication(int $applicationId, int $candidateId): bool
    {
        $application = $this->applicationRepository->getById($applicationId);
        if ($application && $application->getCandidate()->getId() === $candidateId) {
            return $this->applicationRepository->deleteApplication($applicationId);
        }
        return false;
    }

    public function hasApplied(int $candidateId, int $offerId): bool
    {
        $applications = $this->getAll($candidateId);
        
        foreach ($applications as $application) {
            if ($application->getOffer()->getId() === $offerId) {
                return true;
            }
        }
        
        return false;
    }

    public function getApplicationStatus(int $candidateId, int $offerId): ?string
    {
        $application = $this->getByOffer($offerId, $candidateId);
        return $application ? $application->getStatus() : null;
    }

    public function updateCvPath(int $applicationId, int $candidateId, string $cvPath): bool
    {
        $application = $this->getById($applicationId, $candidateId);
        if (!$application) {
            return false;
        }

        $application->setCvPath($cvPath);
        return $this->applicationRepository->updateApplication($application);
    }

    public function updateCoverLetter(int $applicationId, int $candidateId, string $coverLetter): bool
    {
        $application = $this->getById($applicationId, $candidateId);
        if (!$application) {
            return false;
        }

        $application->setCoverLetter($coverLetter);
        return $this->applicationRepository->updateApplication($application);
    }
}