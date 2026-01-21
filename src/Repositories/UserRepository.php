<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Role;
use PDO;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';
    private RoleRepository $roleRepository;

    public function __construct()
    {
        parent::__construct(self::$db);
        $this->roleRepository = new RoleRepository();
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['email' => $email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        
        $role = $this->roleRepository->getById((int) $data['role_id']);

        return new User(
            (int)$data['id'],
            $data['name'],
            $data['email'],
            $data['password'],
            $role
        );
    }

    public function createUser(array $data): bool
    {
        $roleId = isset($data['role']) && $data['role'] instanceof Role
            ? $data['role']->getId()
            : $data['role_id'];

        return $this->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $roleId
        ]);
    }

    public function getById(int $id): ?User
    {
        $data = parent::findById($id);

        if (!$data) {
            return null;
        }

        $role = $this->roleRepository->getById((int) $data['role_id']);

        return new User(
            (int)$data['id'],
            $data['name'],
            $data['email'],
            $data['password'],
            $role
        );
    }


    public function getAll(): array
    {
        $rows = parent::findAll();
        $users = [];

        foreach ($rows as $row) {
            $role = $this->roleRepository->getById((int) $row['role_id']);
            
            $users[] = new User(
                (int)$row['id'],
                $row['name'],
                $row['email'],
                $row['password'],
                $role
            );
        }

        return $users;
    }

    public function updateUser(User $user): bool
    {
        return parent::update(
            $user->getId(),
            [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'role_id' => $user->getRole()->getId()
            ]
        );
    }

    public function deleteUser(int $id): bool
    {
        return parent::delete($id);
    }
   
}