<?php
namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends BaseRepository
{
    protected string $table = 'roles';

    public function __construct()
    {
        parent::__construct(self::$db);
    }

    public function create(Role $role): bool
    {
        return $this->insert([
            'name' => $role->getName()
        ]);
    }

    public function getAll(): array
    {
        $rows = parent::findAll();
        $roles = [];

        foreach ($rows as $row) {
            $roles[] = new Role(
                (int)$row['id'],
                $row['name']
            );
        }

        return $roles;
    }

    public function getById(int $id): ?Role
    {
        $row = parent::findById($id);

        if (!$row) {
            return null;
        }

        return new Role(
            (int)$row['id'],
            $row['name']
        );
    }

    public function updateRole(Role $role): bool
    {
        return parent::update(
            $role->getId(),
            ['name' => $role->getName()]
        );
    }

    public function deleteRole(int $id): bool
    {
        return parent::delete($id);
    }

    public function findByName(string $name): ?Role
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['name' => $name]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Role(
            (int)$row['id'],
            $row['name']
        );
    }
}
