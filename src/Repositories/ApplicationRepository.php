<?php

namespace App\Repositories;

use App\Models\Application;
use App\Repositories\BaseRepository;
use App\Repositories\OfferRepository;
use App\Repositories\CandidateRepository;
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

    public function deleteApplication(int $id): bool
    {
        return parent::delete($id);
    }

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
