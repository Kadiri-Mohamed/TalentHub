<?php

namespace App\Services;

use App\Repositories\RecruiterRepository;
use App\Repositories\OfferRepository;
use App\Repositories\ApplicationRepository;
use App\Repositories\CategorieRepository;
use App\Models\Recruiter;
use App\Models\Offer;
use App\Models\Categorie;

class RecruiterService
{
    private RecruiterRepository $recruiterRepository;
    private OfferRepository $offerRepository;
    private ApplicationRepository $applicationRepository;
    private CategorieRepository $categorieRepository;

    public function __construct()
    {
        $this->recruiterRepository = new RecruiterRepository();
        $this->offerRepository = new OfferRepository();
        $this->applicationRepository = new ApplicationRepository();
        $this->categorieRepository = new CategorieRepository();
    }
    public function getAllRecruiters(): array
    {
        return $this->recruiterRepository->getAll();
    }

    public function getRecruiterById(int $id): ?Recruiter
    {
        return $this->recruiterRepository->getById($id);
    }

    public function updateRecruiter(Recruiter $recruiter): bool
    {
        return $this->recruiterRepository->updateRecruiter($recruiter);
    }

    public function getRecruiterOffers(int $recruiterId): array
    {
        return $this->offerRepository->findByRecruiter($recruiterId);
    }

    public function getOfferApplications(int $offerId): array
    {
        return $this->applicationRepository->findByOffer($offerId);
    }

    public function createOffer(array $offerData): bool
    {
        $recruiter = $this->recruiterRepository->getById($offerData['recruiter_id']);
        $category = $this->categorieRepository->getById($offerData['category_id']);

        if (!$recruiter || !$category) {
            return false;
        }

        $offer = new Offer(
            0,
            $offerData['title'],
            $offerData['description'],
            false,
            (float)$offerData['salary_min'],
            (float)$offerData['salary_max'],
            $offerData['location'],
            $offerData['job_type'],
            $recruiter,
            $category
        );

        return $this->offerRepository->createOffer($offer);
    }

    public function updateOffer(int $offerId, array $offerData): bool
    {
        $offer = $this->offerRepository->getById($offerId);

        if (!$offer) {
            return false;
        }

        $offer->setTitle($offerData['title'] ?? $offer->getTitle());
        $offer->setDescription($offerData['description'] ?? $offer->getDescription());
        $offer->setSalaryMin((float)($offerData['salary_min'] ?? $offer->getSalaryMin()));
        $offer->setSalaryMax((float)($offerData['salary_max'] ?? $offer->getSalaryMax()));
        $offer->setLocation($offerData['location'] ?? $offer->getLocation());
        $offer->setJobType($offerData['job_type'] ?? $offer->getJobType());

        if (isset($offerData['category_id'])) {
            $category = $this->categorieRepository->getById($offerData['category_id']);
            if ($category) {
                $offer->setCategorie($category);
            }
        }

        return $this->offerRepository->updateOffer($offer);
    }

    public function deleteOffer(int $offerId): bool
    {
        $offer = $this->offerRepository->getById($offerId);

        if (!$offer) {
            return false;
        }

        return $this->offerRepository->deleteOffer($offerId);
    }

    public function archiveOffer(int $offerId): bool
    {
        $offer = $this->offerRepository->getById($offerId);

        if (!$offer) {
            return false;
        }

        return $this->offerRepository->archive($offerId);
    }

    public function unarchiveOffer(int $offerId): bool
    {
        $offer = $this->offerRepository->getById($offerId);

        if (!$offer) {
            return false;
        }

        return $this->offerRepository->unarchive($offerId);
    }

    public function updateApplicationStatus(int $applicationId, string $status): bool
    {
        $application = $this->applicationRepository->getById($applicationId);

        if (!$application) {
            return false;
        }

        $application->setStatus($status);
        return $this->applicationRepository->updateApplication($application);
    }

    public function getRecruiterStats(int $recruiterId): array
    {
        $offers = $this->offerRepository->findByRecruiter($recruiterId);

        $activeOffers = 0;
        $archivedOffers = 0;
        $totalApplications = 0;
        $newApplications = 0;
        $hiredCandidates = 0;

        $today = new \DateTime();

        foreach ($offers as $offer) {
            if ($offer->isArchived()) {
                $archivedOffers++;
            } else {
                $activeOffers++;
            }

            $applications = $this->applicationRepository->findByOffer($offer->getId());
            $totalApplications += count($applications);

            foreach ($applications as $app) {
                $createdAt = new \DateTime($app->getAppliedAt());
                $interval = $today->diff($createdAt)->days;
                if ($interval <= 7) {
                    $newApplications++;
                }

                if ($app->getStatus() === 'hired') {
                    $hiredCandidates++;
                }
            }
        }

        return [
            'active_offers'       => $activeOffers,
            'archived_offers'     => $archivedOffers,
            'total_offers'        => count($offers),
            'total_applications'  => $totalApplications,
            'new_applications'    => $newApplications,
            'hired_candidates'    => $hiredCandidates
        ];
    }


    public function getRecruiterByEmail(string $email): ?Recruiter
    {
        return $this->recruiterRepository->findByEmail($email);
    }

    public function searchOffers(int $recruiterId, string $keyword): array
    {
        $offers = $this->getRecruiterOffers($recruiterId);

        if (empty($keyword)) {
            return $offers;
        }

        return array_filter($offers, function ($offer) use ($keyword) {
            return stripos($offer->getTitle(), $keyword) !== false ||
                stripos($offer->getDescription(), $keyword) !== false;
        });
    }
}
