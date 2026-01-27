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

}