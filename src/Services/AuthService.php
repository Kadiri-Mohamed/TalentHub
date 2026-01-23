<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Core\Session;

class AuthService
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct()
    {
        Session::init();

        $this->userRepository = new UserRepository();
        $this->roleRepository = new RoleRepository();
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user->getPassword())) {
            return false;
        }

        $role = $this->roleRepository->getById($user->getRole()->getId());

        Session::regenerate();

        Session::set('user_id', $user->getId());
        Session::set('user_email', $user->getEmail());
        Session::set('user_name', $user->getName());
        Session::set('role_id', $role->getId());
        Session::set('role_name', $role->getName());
        Session::set('logged_in', true);

        return true;
    }

    public function logout(): void
    {
        Session::destroy();
        
        header('Location: /login');
        exit;
    }

    public function isAuthenticated(): bool
    {
        return Session::get('logged_in') === true;
    }

    public function getRole(): ?string
    {
        return Session::get('role_name');
    }

    public function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }


    public function requireRole(string $role): void
    {
        $this->requireAuth();

        if ($this->getRole() !== $role) {
            http_response_code(403);
            echo "Accès interdit : Rôle requis: {$role}";
            exit;
        }
    }


    public function redirectAfterLogin(): void
{
    error_log("Redirection après login, session: " . print_r($_SESSION, true));
    
    $role = Session::get('role_name');
    error_log("Rôle récupéré: " . $role);

        if (!$role) {
            header('Location: /login');
            exit;
        }

        switch ($role) {
            case 'candidate':
                header('Location: /candidate/dashboard');
                break;

            case 'recruiter':
                header('Location: /recruiter/dashboard');
                break;

            case 'admin':
                header('Location: /admin/dashboard');
                break;

            default:
                header('Location: /login');
        }

        exit;
    }
}