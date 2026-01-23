<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use App\Core\Session;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function getCurrentUser(): ?User
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }
        return $this->userRepository->getById($userId);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->getById($id);
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();
    }

    public function createUser(array $data): bool
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return false;
        }

        if ($this->userRepository->findByEmail($data['email'])) {
            return false;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if (!isset($data['role_id'])) {
            $data['role_id'] = 2; 
        }

        return $this->userRepository->createUser($data);
    }

    public function updateUser(User $user): bool
    {
        return $this->userRepository->updateUser($user);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->deleteUser($id);
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        
        if ($user && password_verify($password, $user->getPassword())) {
            return $user;
        }
        
        return null;
    }

    public function updatePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->getById($userId);
        
        if (!$user || !password_verify($currentPassword, $user->getPassword())) {
            return false;
        }

        $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
        return $this->userRepository->updateUser($user);
    }

    public function searchUsers(array $criteria): array
    {
        $users = $this->getAllUsers();
        
        if (empty($criteria)) {
            return $users;
        }

        return array_filter($users, function($user) use ($criteria) {
            foreach ($criteria as $key => $value) {
                $getter = 'get' . ucfirst($key);
                
                if (!method_exists($user, $getter)) {
                    continue;
                }

                $userValue = $user->$getter();
                
                if (is_object($userValue) && method_exists($userValue, 'getId')) {
                    if ($userValue->getId() != $value) {
                        return false;
                    }
                } elseif ($userValue != $value) {
                    return false;
                }
            }
            return true;
        });
    }

    public function getUsersByRole(int $roleId): array
    {
        $allUsers = $this->getAllUsers();
        
        return array_filter($allUsers, function($user) use ($roleId) {
            return $user->getRole()->getId() === $roleId;
        });
    }

    public function getUserStats(): array
    {
        $users = $this->getAllUsers();
        $stats = [
            'total' => count($users),
            'by_role' => [],
            'active' => 0 
        ];

        foreach ($users as $user) {
            $roleName = $user->getRole()->getName();
            
            if (!isset($stats['by_role'][$roleName])) {
                $stats['by_role'][$roleName] = 0;
            }
            $stats['by_role'][$roleName]++;
        }

        return $stats;
    }
}