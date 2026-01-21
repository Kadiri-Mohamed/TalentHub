<?php

namespace App\Repositories;

use App\Models\Categorie;
use App\Repositories\BaseRepository;
use PDO;

class CategorieRepository extends BaseRepository
{
    protected string $table = 'categories';

    public function __construct()
    {
        parent::__construct(self::$db);
    }

    public function create(Categorie $categorie): bool
    {
        return $this->insert([
            'name' => $categorie->getName()
        ]);
    }

    public function getAll(): array
    {
        $rows = parent::findAll();
        $categories = [];

        foreach ($rows as $row) {
            $categories[] = new Categorie(
                (int)$row['id'],
                $row['name']
            );
        }

        return $categories;
    }

    public function getById(int $id): ?Categorie
    {
        $row = parent::findById($id);

        if (!$row) {
            return null;
        }

        return new Categorie(
            (int)$row['id'],
            $row['name']
        );
    }

    public function updateCategorie(Categorie $categorie): bool
    {
        return parent::update(
            $categorie->getId(),
            ['name' => $categorie->getName()]
        );
    }

    public function deleteCategorie(int $id): bool
    {
        return parent::delete($id);
    }

    public function findByName(string $name): ?Categorie
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['name' => $name]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Categorie(
            (int)$row['id'],
            $row['name']
        );
    }
}
