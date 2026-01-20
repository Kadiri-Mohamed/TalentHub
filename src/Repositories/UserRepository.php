<?php

namespace App\Repositories;

use App\Models\User;
use App\Config\Database;
use PDO;

class UserRepository
{
    private PDO $db;
    private RoleRepository $roleRepository;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->roleRepository = new RoleRepository(); 
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1"
        );

        $stmt->execute([
            'email' => $email
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        
        $role = $this->roleRepository->findById((int) $data['role_id']);

        return new User(
            $data['id'],
            $data['name'],
            $data['email'],
            $data['password'],
            $role 
        );
    }

    /**
     * Create new user
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role_id)
             VALUES (:name, :email, :password, :role_id)"
        );

        $roleId = $data['role']  
            ? $data['role']->getId() 
            : $data['role_id'];

        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $roleId
        ]);
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE id = :id LIMIT 1"
        );

        $stmt->execute([
            'id' => $id
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $role = $this->roleRepository->findById((int) $data['role_id']);

        return new User(
            $data['id'],
            $data['name'],
            $data['email'],
            $data['password'],
            $role  
        );
    }
}