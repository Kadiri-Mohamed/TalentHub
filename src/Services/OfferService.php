<?php

namespace App\Services;

use App\Repositories\OfferRepository;
use App\Repositories\ApplicationRepository;
use App\Models\Offer;

class OfferService
{
    private OfferRepository $offerRepository;
    private ApplicationRepository $applicationRepository;

    public function __construct()
    {
        $this->offerRepository = new OfferRepository();
        $this->applicationRepository = new ApplicationRepository();
    }


    public function getActiveOffers(): array
    {
        return $this->offerRepository->findActive();
    }

    public function getOfferById(int $id): ?Offer
    {
        return $this->offerRepository->getById($id);
    }

    public function createOffer(Offer $offer): bool
    {
        return $this->offerRepository->createOffer($offer);
    }

    public function updateOffer(Offer $offer): bool
    {
        return $this->offerRepository->updateOffer($offer);
    }

    public function archiveOffer(int $id): bool
    {
        return $this->offerRepository->archive($id);
    }

    public function getOffersByRecruiter(int $recruiterId): array
    {
        return $this->offerRepository->findByRecruiter($recruiterId);
    }

    public function getOfferApplications(int $offerId): array
    {
        return $this->applicationRepository->findByOffer($offerId);
    }


    public function canCandidateApply(int $offerId, int $candidateId): array
    {
        $result = ['can_apply' => false, 'message' => ''];

        $offer = $this->getOfferById($offerId);

        if (!$offer || $offer->isArchived()) {
            $result['message'] = 'Offre non disponible';
            return $result;
        }

        $applications = $this->applicationRepository->findByCandidate($candidateId);

        foreach ($applications as $application) {
            if ($application->getOffer()->getId() === $offerId) {
                $result['message'] = 'Déjà postulé';
                return $result;
            }
        }

        $result['can_apply'] = true;
        $result['message'] = 'Postulation autorisée';
        return $result;
    }


    public function validateOfferData(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) $errors['title'] = 'Titre requis';
        if (empty($data['description'])) $errors['description'] = 'Description requise';
        if (empty($data['location'])) $errors['location'] = 'Localisation requise';
        if (empty($data['job_type'])) $errors['job_type'] = 'Type requis';
        if (empty($data['categorie_id'])) $errors['categorie_id'] = 'Catégorie requise';

        return $errors;
    }
}
