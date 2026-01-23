<?php

namespace App\Controllers\Recruiter;

use App\Core\Twig;
use App\Services\RecruiterService;
use App\Services\CategorieService;
use App\Services\ApplicationService;
use App\Core\Session;
use App\Core\Middleware\RoleMiddleware;

use App\Core\Middleware\AuthMiddleware;

class RecruiterController
{
    private $recruiterService;
    private $categorieService;

    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('recruiter'))->handle();
        $this->recruiterService = new RecruiterService();
        $this->categorieService = new CategorieService();

    }

   public function dashboard()
{
    $recruiterId = Session::get('user_id');

    $stats = $this->recruiterService->getRecruiterStats($recruiterId);

    Twig::display('dashboard/recruiter.twig', [
    'title' => 'Tableau de bord Recruteur',
    'user' => [
        'name' => Session::get('user_name') ?? 'Recruteur',
        'company' => 'TechRecruit',
        'email' => Session::get('user_email') ?? 'recruteur@example.com',
    ],
    'stats' => [
        'active_jobs'         => $stats['active_offers'],
        'total_applications'  => $stats['total_applications'],
        'new_applications'    => $stats['new_applications'],
        'hired_candidates'    => $stats['hired_candidates'],
    ],
]);

}


    public function listOffers(): void
    {
        $recruiterId = $_SESSION['recruiter_id'];
        $keyword = $_GET['search'] ?? '';
        
        if (!empty($keyword)) {
            $offers = $this->recruiterService->searchOffers($recruiterId, $keyword);
        } else {
            $offers = $this->recruiterService->getRecruiterOffers($recruiterId);
        }
        
        Twig::display('recruiter/offers/list.twig', [
            'offers' => $offers,
            'keyword' => $keyword
        ]);
    }

   
    public function createOfferForm(): void
    {
        $categories = $this->categorieService->getAll();
        
        Twig::display('recruiter/offers/create.twig', [
            'categories' => $categories
        ]);
    }

   
    public function createOffer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recruiter/offers/create');
            exit;
        }
        
        $recruiterId = $_SESSION['recruiter_id'];
        
        $offerData = [
            'recruiter_id' => $recruiterId,
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'salary_min' => floatval($_POST['salary_min'] ?? 0),
            'salary_max' => floatval($_POST['salary_max'] ?? 0),
            'location' => trim($_POST['location'] ?? ''),
            'job_type' => trim($_POST['job_type'] ?? ''),
            'category_id' => intval($_POST['category_id'] ?? 0)
        ];
        
        // Validation
        $errors = $this->validateOfferData($offerData);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $offerData;
            header('Location: /recruiter/offers/create');
            exit;
        }
        
        $created = $this->recruiterService->createOffer($offerData);
        
        if ($created) {
            $_SESSION['success'] = 'Offre créée avec succès';
            header('Location: /recruiter/offers');
        } else {
            $_SESSION['error'] = 'Erreur lors de la création de l\'offre';
            header('Location: /recruiter/offers/create');
        }
        exit;
    }

   
    public function editOfferForm(int $offerId): void
    {
        $recruiterId = $_SESSION['recruiter_id'];
        $offers = $this->recruiterService->getRecruiterOffers($recruiterId);
        
        $offer = null;
        foreach ($offers as $o) {
            if ($o->getId() === $offerId) {
                $offer = $o;
                break;
            }
        }
        
        if (!$offer) {
            $_SESSION['error'] = 'Offre non trouvée';
            header('Location: /recruiter/offers');
            exit;
        }
        
        $categories = $this->categorieService->getAll();
        
        Twig::display('recruiter/offers/edit.twig', [
            'offer' => $offer,
            'categories' => $categories
        ]);
    }

   
    public function updateOffer(int $offerId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recruiter/offers/' . $offerId . '/edit');
            exit;
        }
        
        $offerData = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'salary_min' => floatval($_POST['salary_min'] ?? 0),
            'salary_max' => floatval($_POST['salary_max'] ?? 0),
            'location' => trim($_POST['location'] ?? ''),
            'job_type' => trim($_POST['job_type'] ?? ''),
            'category_id' => intval($_POST['category_id'] ?? 0)
        ];
        
        
        $errors = $this->validateOfferData($offerData);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $offerData;
            header('Location: /recruiter/offers/' . $offerId . '/edit');
            exit;
        }
        
        $updated = $this->recruiterService->updateOffer($offerId, $offerData);
        
        if ($updated) {
            $_SESSION['success'] = 'Offre modifiée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la modification';
        }
        
        header('Location: /recruiter/offers/' . $offerId . '/edit');
        exit;
    }

   
    public function archiveOffer(int $offerId): void
    {
        $archived = $this->recruiterService->archiveOffer($offerId);
        
        if ($archived) {
            $_SESSION['success'] = 'Offre archivée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'archivage';
        }
        
        header('Location: /recruiter/offers');
        exit;
    }

 
    public function unarchiveOffer(int $offerId): void
    {
        $unarchived = $this->recruiterService->unarchiveOffer($offerId);
        
        if ($unarchived) {
            $_SESSION['success'] = 'Offre réactivée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la réactivation';
        }
        
        header('Location: /recruiter/offers');
        exit;
    }

 
    public function deleteOffer(int $offerId): void
    {
        $deleted = $this->recruiterService->deleteOffer($offerId);
        
        if ($deleted) {
            $_SESSION['success'] = 'Offre supprimée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression';
        }
        
        header('Location: /recruiter/offers');
        exit;
    }

  
    public function viewApplications(int $offerId): void
    {
        $recruiterId = $_SESSION['recruiter_id'];
        $offers = $this->recruiterService->getRecruiterOffers($recruiterId);
        
        $offer = null;
        foreach ($offers as $o) {
            if ($o->getId() === $offerId) {
                $offer = $o;
                break;
            }
        }
        
        if (!$offer) {
            $_SESSION['error'] = 'Offre non trouvée';
            header('Location: /recruiter/offers');
            exit;
        }
        
        $applications = $this->recruiterService->getOfferApplications($offerId);
        
        Twig::display('recruiter/applications/list.twig', [
            'offer' => $offer,
            'applications' => $applications
        ]);
    }


    public function updateApplicationStatus(int $applicationId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recruiter/offers');
            exit;
        }
        
        $status = $_POST['status'] ?? '';
        $offerId = intval($_POST['offer_id'] ?? 0);
        
        if (!in_array($status, ['pending', 'accepted', 'rejected'])) {
            $_SESSION['error'] = 'Statut invalide';
            header('Location: /recruiter/offers/' . $offerId . '/applications');
            exit;
        }
        
        $updated = $this->recruiterService->updateApplicationStatus($applicationId, $status);
        
        if ($updated) {
            $_SESSION['success'] = 'Statut mis à jour avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
        }
        
        header('Location: /recruiter/offers/' . $offerId . '/applications');
        exit;
    }

  
    public function profile(): void
    {
        $recruiterId = $_SESSION['recruiter_id'];
        $recruiter = $this->recruiterService->getRecruiterById($recruiterId);
        
        Twig::display('recruiter/profile.twig', [
            'recruiter' => $recruiter
        ]);
    }


    public function updateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recruiter/profile');
            exit;
        }
        
        $recruiterId = $_SESSION['recruiter_id'];
        $recruiter = $this->recruiterService->getRecruiterById($recruiterId);
        
        if (!$recruiter) {
            $_SESSION['error'] = 'Recruteur non trouvé';
            header('Location: /recruiter/profile');
            exit;
        }
        
        $updates = [
            'nom' => 'setNom',
            'prenom' => 'setPrenom',
            'tel' => 'setTel',
            'entreprise' => 'setEntreprise'
        ];
        
        foreach ($updates as $field => $setter) {
            if (method_exists($recruiter, $setter) && isset($_POST[$field])) {
                $getter = 'get' . substr($setter, 3);
                $value = trim($_POST[$field]);
                if (!empty($value) || method_exists($recruiter, $getter)) {
                    $recruiter->$setter($value);
                }
            }
        }
        
        $updated = $this->recruiterService->updateRecruiter($recruiter);
        
        if ($updated) {
            $_SESSION['success'] = 'Profil mis à jour avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
        }
        
        header('Location: /recruiter/profile');
        exit;
    }


    private function validateOfferData(array $data): array
    {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors['title'] = 'Le titre est requis';
        }
        
        if (empty($data['description'])) {
            $errors['description'] = 'La description est requise';
        }
        
        if (empty($data['location'])) {
            $errors['location'] = 'La localisation est requise';
        }
        
        if (empty($data['job_type'])) {
            $errors['job_type'] = 'Le type de contrat est requis';
        }
        
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'La catégorie est requise';
        }
        
        if ($data['salary_min'] < 0) {
            $errors['salary_min'] = 'Le salaire minimum doit être positif';
        }
        
        if ($data['salary_max'] < 0) {
            $errors['salary_max'] = 'Le salaire maximum doit être positif';
        }
        
        if ($data['salary_max'] > 0 && $data['salary_max'] < $data['salary_min']) {
            $errors['salary_max'] = 'Le salaire maximum doit être supérieur au minimum';
        }
        
        return $errors;
    }


    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}