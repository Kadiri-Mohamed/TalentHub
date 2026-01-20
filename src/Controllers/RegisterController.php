<?php
// src/Controllers/RegisterController.php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Utils\Validator;
use App\Core\Twig;
use App\Core\Middleware\GuestMiddleware;

class RegisterController
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct()
    {
        (new GuestMiddleware())->handle();
        $this->userRepository = new UserRepository();
        $this->roleRepository = new RoleRepository();
    }

    public function index(): void
    {
        Twig::display('auth/register.twig', [
            'title' => 'Inscription',
            'post' => $_POST ?? []
        ]);
    }

    public function register(): void
    {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleName = $_POST['role'] ?? '';

        if (!Validator::required($name) || !Validator::alpha($name)) {
            $error = 'Nom invalide.';
        } elseif (!Validator::email($email)) {
            $error = 'Email invalide.';
        } elseif (!Validator::minLength($password, 6)) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
        } elseif (!Validator::inArray($roleName, ['candidate', 'recruiter'])) {
            $error = 'Rôle invalide.';
        } elseif ($this->userRepository->findByEmail($email)) {
            $error = 'Cet email existe déjà.';
        }

        if (isset($error)) {
            Twig::display('auth/register.twig', [
                'error' => $error,
                'title' => 'Inscription',
                'post' => $_POST
            ]);
            return;
        }

        $role = $this->roleRepository->findByName($roleName);

        if (!$role) {
            $error = 'Rôle non trouvé.';
            Twig::display('auth/register.twig', [
                'error' => $error,
                'title' => 'Inscription',
                'post' => $_POST
            ]);
            return;
        }

        $this->userRepository->create([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id'  => $role->getId()
        ]);

        header('Location: /login');
        exit;
    }
}