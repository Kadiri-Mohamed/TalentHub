<?php
namespace App\Controllers\Candidate;

use App\Services\ApplicationService;
use App\Core\Session;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Core\Twig;



class ApplicationController
{
    private ApplicationService $applicationService;

    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('candidate'))->handle();
        $this->applicationService = new ApplicationService();
    }

    public function index()
    {
        $applications = $this->applicationService->getAll(Session::get('user_id'));

        Twig::display('candidate/applications.twig', [
            'title' => 'Mes candidatures',
            'applications' => $applications,
        ]);
    }
}
