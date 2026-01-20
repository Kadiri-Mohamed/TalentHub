<?php
// src/Controllers/LoginController.php

namespace App\Controllers;

use App\Services\AuthService;
use App\Core\Twig;
use App\Core\Middleware\GuestMiddleware;

class LoginController
{
    private AuthService $authService;

    public function __construct()
    {
        (new GuestMiddleware())->handle();
        $this->authService = new AuthService();
    }

    public function index()
    {
        Twig::display('auth/login.twig', [
            'title' => 'Connexion'
        ]);
    }
    
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($this->authService->login($email, $password)) {
            $this->authService->redirectAfterLogin();
        } else {
            $error = "Email ou mot de passe incorrect";
            Twig::display('auth/login.twig', [
                'error' => $error,
                'title' => 'Connexion'
            ]);
        }
    }
}