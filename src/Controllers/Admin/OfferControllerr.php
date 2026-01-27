<?php

namespace App\Controllers\Admin;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Services\AdminService;
use App\Services\OfferService;
use App\Services\RecruiterService;
use App\Services\CategorieService;
use App\Models\Offer;
use App\Models\Recruiter;
use App\Models\Categorie;
use App\Core\Twig;
use App\Core\Session;

class OfferControllerr
{
    private AdminService $adminService;
    private OfferService $offerService;
    private RecruiterService $recruiterService;
    private CategorieService $categorieService;
    
    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('admin'))->handle();
        $this->adminService = new AdminService();
        $this->offerService = new OfferService();
        $this->recruiterService = new RecruiterService();
        $this->categorieService = new CategorieService();
    }

    public function index()
    {
        $offers = $this->adminService->getAllOffers();
        $stats = $this->adminService->getStats();
        
        Twig::display('admin/offers/index.twig', [
            'title' => 'Gestion des offers d\'emploi',
            'offers' => $offers,
            'stats' => $stats,
            'current_user' => [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name') ?? 'Admin',
                'email' => Session::get('user_email') ?? 'admin@talenthub.com',
            ]
        ]);
    }

    public function show($id)
    {
        $offer = $this->adminService->getOfferById((int) $id);
        
        if (!$offer) {
            Session::set('error', 'Offre non trouvée');
            header('Location: /admin/offers');
            exit;
        }
        
        $applications = $this->offerService->getOfferApplications($offer->getId());
        
        Twig::display('admin/offers/show.twig', [
            'title' => 'Détails de l\'offre',
            'offer' => $offer,
            'applications' => $applications,
            'current_user' => [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name') ?? 'Admin',
            ]
        ]);
    }

    public function create()
    {
        $recruiters = $this->recruiterService->getAllRecruiters();
        $categories = $this->categorieService->getAll();
        
        Twig::display('admin/offers/create.twig', [
            'title' => 'Créer une nouvelle offre',
            'recruiters' => $recruiters,
            'categories' => $categories,
            'job_types' => ['CDI', 'CDD', 'Stage', 'Alternance', 'Freelance', 'Temps partiel'],
            'current_user' => [
                'name' => Session::get('user_name') ?? 'Admin',
            ]
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/offers/create');
            exit;
        }
        
        $recruiter = $this->recruiterService->getRecruiterById((int) ($_POST['recruiter_id'] ?? 0));
        $categorie = $this->categorieService->getById((int) ($_POST['categorie_id'] ?? 0));
        
        if (!$recruiter || !$categorie) {
            Session::set('error', 'Recruteur ou catégorie invalide');
            header('Location: /admin/offers/create');
            exit;
        }
        
        $validationErrors = $this->offerService->validateOfferData($_POST);
        if (!empty($validationErrors)) {
            Session::set('error', implode(', ', array_values($validationErrors)));
            header('Location: /admin/offers/create');
            exit;
        }
        
        $offer = new Offer(
            0, 
            trim($_POST['title'] ?? ''),
            trim($_POST['description'] ?? ''),
            false, // is_archived
            (float) ($_POST['salary_min'] ?? 0),
            (float) ($_POST['salary_max'] ?? 0),
            trim($_POST['location'] ?? ''),
            trim($_POST['job_type'] ?? ''),
            $recruiter,
            $categorie
        );
        
        if ($this->offerService->createOffer($offer)) {
            Session::set('success', 'Offre créée avec succès');
            header('Location: /admin/offers');
        } else {
            Session::set('error', 'Erreur lors de la création de l\'offre');
            header('Location: /admin/offers/create');
        }
        exit;
    }

    public function edit($id)
    {
        $offer = $this->adminService->getOfferById((int) $id);
        
        if (!$offer) {
            Session::set('error', 'Offre non trouvée');
            header('Location: /admin/offers');
            exit;
        }
        
        $recruiters = $this->recruiterService->getAllRecruiters();
        $categories = $this->categorieService->getAll();
        
        Twig::display('admin/offers/edit.twig', [
            'title' => 'Modifier l\'offre',
            'offer' => $offer,
            'recruiters' => $recruiters,
            'categories' => $categories,
            'job_types' => ['CDI', 'CDD', 'Stage', 'Alternance', 'Freelance', 'Temps partiel'],
            'current_user' => [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name') ?? 'Admin',
            ]
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/offers/' . $id . '/edit');
            exit;
        }
        
        $offer = $this->adminService->getOfferById((int) $id);
        
        if (!$offer) {
            Session::set('error', 'Offre non trouvée');
            header('Location: /admin/offers');
            exit;
        }
        
        $recruiter = $this->recruiterService->getRecruiterById((int) ($_POST['recruiter_id'] ?? $offer->getRecruiter()->getId()));
        $categorie = $this->categorieService->getById((int) ($_POST['categorie_id'] ?? $offer->getCategorie()->getId()));
        
        if (!$recruiter || !$categorie) {
            Session::set('error', 'Recruteur ou catégorie invalide');
            header('Location: /admin/offers/' . $id . '/edit');
            exit;
        }
        
        $validationErrors = $this->offerService->validateOfferData($_POST);
        if (!empty($validationErrors)) {
            Session::set('error', implode(', ', array_values($validationErrors)));
            header('Location: /admin/offers/' . $id . '/edit');
            exit;
        }
        
        $offer->setTitle(trim($_POST['title'] ?? $offer->getTitle()));
        $offer->setDescription(trim($_POST['description'] ?? $offer->getDescription()));
        $offer->setSalaryMin((float) ($_POST['salary_min'] ?? $offer->getSalaryMin()));
        $offer->setSalaryMax((float) ($_POST['salary_max'] ?? $offer->getSalaryMax()));
        $offer->setLocation(trim($_POST['location'] ?? $offer->getLocation()));
        $offer->setJobType(trim($_POST['job_type'] ?? $offer->getJobType()));
        $offer->setRecruiter($recruiter);
        $offer->setCategorie($categorie);
        
        if ($this->offerService->updateOffer($offer)) {
            Session::set('success', 'Offre mise à jour avec succès');
            header('Location: /admin/offers');
        } else {
            Session::set('error', 'Erreur lors de la mise à jour de l\'offre');
            header('Location: /admin/offers/' . $id . '/edit');
        }
        exit;
    }

    public function delete($id)
    {
        $offer = $this->adminService->getOfferById((int) $id);
        
        if (!$offer) {
            Session::set('error', 'Offre non trouvée');
            header('Location: /admin/offers');
            exit;
        }
        
        if ($this->adminService->deleteOffer((int) $id)) {
            Session::set('success', 'Offre supprimée avec succès');
        } else {
            Session::set('error', 'Erreur lors de la suppression de l\'offre');
        }
        
        header('Location: /admin/offers');
        exit;
    }

    public function toggleArchive($id)
    {
        $offer = $this->adminService->getOfferById((int) $id);
        
        if (!$offer) {
            Session::set('error', 'Offre non trouvée');
            header('Location: /admin/offers');
            exit;
        }
        
        if ($offer->isArchived()) {
            $this->adminService->unarchiveOffer((int) $id);
            Session::set('success', 'Offre désarchivée avec succès');
        } else {
            $this->adminService->archiveOffer((int) $id);
            Session::set('success', 'Offre archivée avec succès');
        }
        
        header('Location: /admin/offers');
        exit;
    }

    public function applications($id)
    {
        $offer = $this->adminService->getOfferById((int) $id);
        
        if (!$offer) {
            Session::set('error', 'Offre non trouvée');
            header('Location: /admin/offers');
            exit;
        }
        
        $applications = $this->offerService->getOfferApplications($offer->getId());
        
        Twig::display('admin/offers/applications.twig', [
            'title' => 'Candidatures pour l\'offre : ' . $offer->getTitle(),
            'offer' => $offer,
            'applications' => $applications,
            'current_user' => [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name') ?? 'Admin',
            ]
        ]);
    }
}