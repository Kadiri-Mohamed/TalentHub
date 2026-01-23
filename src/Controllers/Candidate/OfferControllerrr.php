<?php

namespace App\Controllers\Candidate;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Core\Twig;
use App\Core\Session;
use App\Services\OfferService;
use App\Services\ApplicationService;

class OfferControllerrr
{
    private OfferService $offerService;
    private ApplicationService $applicationService;

    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('candidate'))->handle();

        $this->offerService = new OfferService();
        $this->applicationService = new ApplicationService();
    }

    public function index()
    {
        $offers = $this->offerService->getActiveOffers();

        Twig::display('candidate/offers.twig', [
            'title' => 'Offres disponibles',
            'offers' => $offers
        ]);
    }

    public function show(int $id)
    {
        $offer = $this->offerService->getOfferById($id);

        if (!$offer) {
            header('Location: /candidate/offers');
            exit;
        }

        $hasApplied = $this->applicationService->hasApplied(
            Session::get('user_id'),
            $id
        );

        Twig::display('candidate/offer_show.twig', [
            'title' => $offer->getTitle(),
            'offer' => $offer,
            'hasApplied' => $hasApplied
        ]);
    }

    public function apply(int $id)
    {
        $candidateId = Session::get('user_id');

        $canApply = $this->offerService->canCandidateApply($id, $candidateId);

        if (!$canApply['can_apply']) {
            header('Location: /candidate/offers/' . $id);
            exit;
        }

        $this->applicationService->apply(
            $id,
            $candidateId,
            '/uploads/cv.pdf',     
            'Lettre de motivation'
        );

        header('Location: /candidate/applications');
        exit;
    }
}
