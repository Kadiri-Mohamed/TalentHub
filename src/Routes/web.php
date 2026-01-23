<?php

use App\Routes\Router;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\Candidate\CandidateController;
use App\Controllers\Candidate\ApplicationController;
use App\Controllers\Recruiter\RecruiterController;
use App\Controllers\Recruiter\OfferController;
use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\UserController;
use App\Controllers\Candidate\OfferControllerrr;
use App\Controllers\Candidate\ProfileController;
use App\Controllers\Admin\OfferControllerr;


// Routes publiques (invités seulement)
Router::get('/login', [LoginController::class, 'index']);
Router::post('/login', [LoginController::class, 'login']);
Router::get('/register', [RegisterController::class, 'index']);
Router::post('/register', [RegisterController::class, 'register']);

// Route de déconnexion (connectés seulement)
Router::get('/logout', [LogoutController::class, 'logout']);

// Page d'accueil - redirige selon l'état de connexion
Router::get('/', function() {
    session_start();
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $role = $_SESSION['role_name'] ?? 'candidate';
        header("Location: /{$role}/dashboard");
    } else {
        header('Location: /login');
    }
    exit;
});

// Routes Candidat
Router::get('/candidate/dashboard', [CandidateController::class, 'dashboard']);
Router::get('/candidate/applications', [ApplicationController::class,'index']);
Router::get('/candidate/saved-jobs', [CandidateController::class, 'savedJobs']);
Router::get('/candidate/interviews', [CandidateController::class, 'interviews']);
Router::get('/candidate/offers', [OfferControllerrr::class, 'index']);
Router::get('/candidate/offers/{id}', [OfferControllerrr::class, 'show']);
Router::post('/candidate/offers/{id}/apply', [OfferControllerrr::class, 'apply']);
Router::get('/candidate/profile', [ProfileController::class, 'index']);
Router::get('/candidate/profile/edit', [ProfileController::class, 'edit']);
Router::post('/candidate/profile/update', [ProfileController::class, 'update']);
Router::post('/candidate/profile/upload-cv', [ProfileController::class, 'uploadCv']);

// Routes Recruteur
Router::get('/recruiter/dashboard', [RecruiterController::class, 'dashboard']);
Router::get('/recruiter/job-postings', [RecruiterController::class, 'jobPostings']);
Router::get('/recruiter/candidates', [RecruiterController::class, 'candidates']);
Router::get('/recruiter/create-job', [RecruiterController::class, 'createOfferForm']);
Router::get('/recruiter/analytics', [RecruiterController::class, 'analytics']);



// Routes Admin
// Dans src/Routes/web.php ou votre fichier de routes
Router::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Router::get('/admin/system', [AdminController::class, 'system']);
Router::get('/admin/reports', [AdminController::class, 'reports']);
Router::get('/admin/settings', [AdminController::class, 'settings']);

// Routes pour la gestion des utilisateurs
Router::get('/admin/users', [UserController::class, 'index']);
Router::get('/admin/users/create', [UserController::class, 'create']);
Router::post('/admin/users/store', [UserController::class, 'store']);
Router::get('/admin/users/{id}', [UserController::class, 'show']);
Router::get('/admin/users/{id}/edit', [UserController::class, 'edit']);
Router::post('/admin/users/{id}/update', [UserController::class, 'update']);
Router::get('/admin/users/{id}/delete', [UserController::class, 'delete']);
Router::get('/admin/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
Router::get('/admin/users/search', [UserController::class, 'search']);

// Routes pour la gestion des offres
Router::get('/admin/offers', [OfferControllerr::class, 'index']);
Router::get('/admin/offers/create', [OfferControllerr::class, 'create']);
Router::post('/admin/offers/store', [OfferControllerr::class, 'store']);
Router::get('/admin/offers/{id}/applications', [OfferControllerr::class, 'applications']);
Router::get('/admin/offers/{id}', [OfferControllerr::class, 'show']);
Router::get('/admin/offers/{id}/edit', [OfferControllerr::class, 'edit']);
Router::post('/admin/offers/{id}/update', [OfferControllerr::class, 'update']);
Router::get('/admin/offers/{id}/delete', [OfferControllerr::class, 'delete']);
Router::get('/admin/offers/{id}/toggle-archive', [OfferControllerr::class, 'toggleArchive']);
Router::get('/admin/offers/search', [OfferControllerr::class, 'search']);

/* Dispatch router */
Router::dispatch();