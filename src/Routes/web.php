<?php

use App\Routes\Router;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\LogoutController;
use App\Controllers\CandidateController;
use App\Controllers\RecruiterController;
use App\Controllers\AdminController;

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
Router::get('/candidate/applications', [CandidateController::class, 'applications']);
Router::get('/candidate/profile', [CandidateController::class, 'profile']);
Router::get('/candidate/saved-jobs', [CandidateController::class, 'savedJobs']);
Router::get('/candidate/interviews', [CandidateController::class, 'interviews']);

// Routes Recruteur
Router::get('/recruiter/dashboard', [RecruiterController::class, 'dashboard']);
Router::get('/recruiter/job-postings', [RecruiterController::class, 'jobPostings']);
Router::get('/recruiter/candidates', [RecruiterController::class, 'candidates']);
Router::get('/recruiter/create-job', [RecruiterController::class, 'createJob']);
Router::get('/recruiter/analytics', [RecruiterController::class, 'analytics']);

// Routes Admin
Router::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Router::get('/admin/users', [AdminController::class, 'users']);
Router::get('/admin/system', [AdminController::class, 'system']);
Router::get('/admin/reports', [AdminController::class, 'reports']);
Router::get('/admin/settings', [AdminController::class, 'settings']);

/* Dispatch router */
Router::dispatch();