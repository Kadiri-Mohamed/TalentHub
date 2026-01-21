<?php

namespace App\Repositories;

use App\Models\Application;
use App\Models\Offer;
use App\Models\Candidate;

class ApplicationRepository extends BaseRepository
{
    protected string $table = 'applications';

    private OfferRepository $offerRepository;
    private CandidateRepository $candidateRepository;

    public function __construct()
    {
        $this->offerRepository = new OfferRepository();
        $this->candidateRepository = new CandidateRepository();
    }

    // Create a new application
    public function create(Application $application): bool
    {
    return $this->insert([
        'offer_id' => $application->getOffer()->getId(),
        'candidate_id' => $application->getCandidate()->getId(),
        'status' => $application->getStatus(),
        'cv_path' => $application->getCvPath(),
        'cover_letter' => $application->getCoverLetter()
    ]);
    }


    // Get all applications
    public function getAll(): array
    {
        $rows = parent::findAll();
        $applications = [];

        foreach ($rows as $row) {
            $offer = $this->offerRepository->getById((int)$row['offer_id']);
            $candidate = $this->candidateRepository->getById((int)$row['candidate_id']);

            $applications[] = new Application(
                (int)$row['id'],
                $offer,
                $candidate,
                $row['status'],
                $row['applied_at'],
                $row['cv_path'],
                $row['cover_letter']
            );
        }

        return $applications;
    }

    // Get application by ID
    public function getById(int $id): ?Application
    {
        $row = parent::findById($id);
        if (!$row) return null;

        $offer = $this->offerRepository->getById((int)$row['offer_id']);
        $candidate = $this->candidateRepository->getById((int)$row['candidate_id']);

        return new Application(
            (int)$row['id'],
            $offer,
            $candidate,
            $row['status'],
            $row['applied_at'],
            $row['cv_path'],
            $row['cover_letter']
        );
    }

    // Update an application
    public function updateApplication(Application $application): bool
    {
        return parent::update(
            $application->getId(),
            [
                'offer_id' => $application->getOffer()->getId(),
                'candidate_id' => $application->getCandidate()->getId(),
                'status' => $application->getStatus(),
                'applied_at' => $application->getAppliedAt(),
                'cv_path' => $application->getCvPath(),
                'cover_letter' => $application->getCoverLetter()
            ]
        );
    }

    // Delete an application
    public function deleteApplication(int $id): bool
    {
        return parent::delete($id);
    }

    // Find all applications for a specific candidate
    public function findByCandidate(int $candidateId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE candidate_id = :cid";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['cid' => $candidateId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $applications = [];
        foreach ($rows as $row) {
            $offer = $this->offerRepository->getById((int)$row['offer_id']);
            $candidate = $this->candidateRepository->getById((int)$row['candidate_id']);

            $applications[] = new Application(
                (int)$row['id'],
                $offer,
                $candidate,
                $row['status'],
                $row['applied_at'],
                $row['cv_path'],
                $row['cover_letter']
            );
        }

        return $applications;
    }

    // Find all applications for a specific offer
    public function findByOffer(int $offerId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE offer_id = :oid";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['oid' => $offerId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $applications = [];
        foreach ($rows as $row) {
            $offer = $this->offerRepository->getById((int)$row['offer_id']);
            $candidate = $this->candidateRepository->getById((int)$row['candidate_id']);

            $applications[] = new Application(
                (int)$row['id'],
                $offer,
                $candidate,
                $row['status'],
                $row['applied_at'],
                $row['cv_path'],
                $row['cover_letter']
            );
        }

        return $applications;
    }
}
