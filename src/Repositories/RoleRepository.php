<?php

namespace App\Repositories;

use App\Models\Role;
use App\Config\Database;
use PDO;

class RoleRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?Role
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM roles WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            return null;
        }

        return new Role($role['id'], $role['name']);
    }

    public function findByName(string $name): ?Role
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM roles WHERE name = :name"
        );
        $stmt->execute(['name' => $name]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            return null;
        }

        return new Role($role['id'], $role['name']);
    }

    public function findAll(): array{
        $stmt = $this->db->prepare(
            "SELECT * FROM roles"
        );
        $stmt->execute();

        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($role) {
            return new Role($role['id'], $role['name']);
        }, $roles);
    }
}
