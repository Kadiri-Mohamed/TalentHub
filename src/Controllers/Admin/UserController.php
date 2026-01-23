<?php

namespace App\Controllers\Admin;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;
use App\Services\AdminService;
use App\Services\UserService;
use App\Core\Twig;
use App\Core\Session;

class UserController
{
    private AdminService $adminService;
    private UserService $userService;
    
    public function __construct()
    {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware('admin'))->handle();
        $this->adminService = new AdminService();
        $this->userService = new UserService();
    }

    public function index()
    {
        $users = $this->adminService->getAllUsers();
        $stats = $this->adminService->getStats();
        
        Twig::display('admin/users/index.twig', [
            'title' => 'Gestion des utilisateurs',
            'users' => $users,
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
        $user = $this->adminService->getUserById((int) $id);
        
        if (!$user) {
            Session::set('error', 'Utilisateur non trouvé');
            header('Location: /admin/users');
            exit;
        }
        
        Twig::display('admin/users/show.twig', [
            'title' => 'Détails de l\'utilisateur',
            'user' => $user,
            'current_user' => [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name') ?? 'Admin',
            ]
        ]);
    }

    public function create()
    {
        Twig::display('admin/users/create.twig', [
            'title' => 'Créer un nouvel utilisateur',
            'current_user' => [
                'name' => Session::get('user_name') ?? 'Admin',
            ]
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users/create');
            exit;
        }
        
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role_id' => (int) ($_POST['role_id'] ?? 2),
        ];
        
        
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            Session::set('error', 'Tous les champs obligatoires doivent être remplis');
            header('Location: /admin/users/create');
            exit;
        }
        
        if ($this->userService->createUser($data)) {
            Session::set('success', 'Utilisateur créé avec succès');
            header('Location: /admin/users');
        } else {
            Session::set('error', 'Erreur lors de la création de l\'utilisateur. L\'email existe peut-être déjà.');
            header('Location: /admin/users/create');
        }
        exit;
    }

    public function edit($id)
    {
        $user = $this->adminService->getUserById((int) $id);
        
        if (!$user) {
            Session::set('error', 'Utilisateur non trouvé');
            header('Location: /admin/users');
            exit;
        }
        
        Twig::display('admin/users/edit.twig', [
            'title' => 'Modifier l\'utilisateur',
            'user' => $user,
            'current_user' => [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name') ?? 'Admin',
            ]
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users/' . $id . '/edit');
            exit;
        }
        
        $user = $this->adminService->getUserById((int) $id);
        
        if (!$user) {
            Session::set('error', 'Utilisateur non trouvé');
            header('Location: /admin/users');
            exit;
        }
        
        
        $user->setName(trim($_POST['name'] ?? $user->getName()));
        $user->setEmail(trim($_POST['email'] ?? $user->getEmail()));
        
        
        if (isset($_POST['role_id'])) {
            $roleId = (int) $_POST['role_id'];
            $this->adminService->updateUserRole($user->getId(), $roleId);
        }
        
        
        if (!empty($_POST['password'])) {
            $user->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
        }
        
        if ($this->userService->updateUser($user)) {
            Session::set('success', 'Utilisateur mis à jour avec succès');
            header('Location: /admin/users');
        } else {
            Session::set('error', 'Erreur lors de la mise à jour de l\'utilisateur');
            header('Location: /admin/users/' . $id . '/edit');
        }
        exit;
    }

    public function delete($id)
    {
        $user = $this->adminService->getUserById((int) $id);
        
        if (!$user) {
            Session::set('error', 'Utilisateur non trouvé');
            header('Location: /admin/users');
            exit;
        }
        
        
        $currentUserId = (int) Session::get('user_id');
        if ($user->getId() === $currentUserId) {
            Session::set('error', 'Vous ne pouvez pas supprimer votre propre compte');
            header('Location: /admin/users');
            exit;
        }
        
        if ($this->adminService->deleteUser((int) $id)) {
            Session::set('success', 'Utilisateur supprimé avec succès');
        } else {
            Session::set('error', 'Erreur lors de la suppression de l\'utilisateur');
        }
        
        header('Location: /admin/users');
        exit;
    }

    public function toggleStatus($id)
    {
        
        Session::set('info', 'La fonctionnalité de changement de statut sera bientôt disponible');
        header('Location: /admin/users/' . $id . '/edit');
        exit;
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Location: /admin/users');
            exit;
        }
        
        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? '');
        
        $users = $this->adminService->getAllUsers();
        
        if (!empty($search) || !empty($role)) {
            $filteredUsers = array_filter($users, function($user) use ($search, $role) {
                $matchesSearch = empty($search) || 
                               stripos($user->getName(), $search) !== false || 
                               stripos($user->getEmail(), $search) !== false;
                
                $matchesRole = empty($role) || 
                              (strtolower($user->getRole()->getName()) === strtolower($role));
                
                return $matchesSearch && $matchesRole;
            });
            
            $users = array_values($filteredUsers);
        }
        
        $stats = $this->adminService->getStats();
        
        Twig::display('admin/users/index.twig', [
            'title' => 'Résultats de recherche',
            'users' => $users,
            'stats' => $stats,
            'search' => $search,
            'selected_role' => $role,
            'current_user' => [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name') ?? 'Admin',
            ]
        ]);
    }
}