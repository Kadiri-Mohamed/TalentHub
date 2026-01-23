<?php
// Fichier: App/Controllers/Recruiter/OfferController.php

namespace App\Controllers\Recruiter;

use App\Core\Twig;
use App\Services\OfferService;
use App\Services\CategorieService;
use App\Core\Session;
use App\Core\Middleware\AuthMiddleware;
use App\Models\Offer;
use App\Models\Recruiter;
use App\Models\Role;
use App\Models\Categorie;

class OfferController
{
    private OfferService $offerService;
    private CategorieService $categorieService;

    public function __construct()
    {
        (new AuthMiddleware())->handle();
        $this->offerService = new OfferService();
        $this->categorieService = new CategorieService();
    }

    public function store(): void
    {
      
        
        $recruiterId = Session::get('user_id');
        
        $role = new Role(2, 'recruiter');
        
        $recruiter = new Recruiter(
            $recruiterId,
            Session::get('user_name') ?? 'Recruteur',
            Session::get('user_email') ?? 'recruteur@example.com',
            '',
            $role,
            Session::get('user_company') ?? 'Entreprise'
        );
        
        $categoryRepository = new \App\Repositories\CategorieRepository();
        $category = $categoryRepository->getById((int)($_POST['category_id'] ?? 0));
        
        if (!$category) {
            Session::set('error', 'Catégorie invalide');
            header('Location: /recruiter/create-job');
            exit;
        }
        
        $salaryMin = 0.0;
        $salaryMax = 0.0;
        
        if (isset($_POST['salary_min']) && $_POST['salary_min'] !== '') {
            $salaryMin = floatval($_POST['salary_min']);
        }
        
        if (isset($_POST['salary_max']) && $_POST['salary_max'] !== '') {
            $salaryMax = floatval($_POST['salary_max']);
        }
        
        $offer = new Offer(
            0,
            trim($_POST['title'] ?? ""),
            trim($_POST['description'] ?? ""),
            false,
            $salaryMin,
            $salaryMax,
            trim($_POST['location'] ?? ""),
            $_POST['job_type'] ?? "",
            $recruiter,
            $category
        );
        
        $data = [
            'title' => $offer->getTitle(),
            'description' => $offer->getDescription(),
            'location' => $offer->getLocation(),
            'job_type' => $offer->getJobType(),
            'categorie_id' => $category->getId()
        ];
        
        $errors = $this->offerService->validateOfferData($data);
        
        if (!empty($errors)) {
            Session::set('errors', $errors);
            Session::set('old', $_POST);
            header('Location: /recruiter/create-job');
            exit;
        }
        
        $success = $this->offerService->createOffer($offer);
        
        if ($success) {
            Session::set('success', 'Offre créée avec succès !');
            header('Location: /recruiter/dashboard');
        } else {
            Session::set('error', 'Erreur lors de la création de l\'offre');
            header('Location: /recruiter/create-job');
        }
        exit;
    }
    public function jobPostings(): void
{
    $recruiterId = Session::get('user_id');

    if (!$recruiterId) {
        header('Location: /login');
        exit;
    }

    $offers = $this->offerService->getOffersByRecruiter($recruiterId);

    Twig::display('recruiter/job-postings.twig', [
        'title' => 'Mes offres d\'emploi',
        'offers' => $offers,
        'user' => [
            'name' => Session::get('user_name'),
            'email' => Session::get('user_email'),
            'company' => Session::get('user_company')
        ]
    ]);
}

}
