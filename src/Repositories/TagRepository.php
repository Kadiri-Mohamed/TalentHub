<?php

namespace App\Repositories;

use App\Models\Tag;

class TagRepository extends BaseRepository
{
    protected string $table = 'tags';

    public function __construct()
    {
        parent::__construct(self::$db);
    }

    public function create(Tag $tag): bool
    {
        return $this->insert([
            'name' => $tag->getName()
        ]);
    }

    public function getAll(): array
    {
        $rows = parent::findAll();
        $tags = [];

        foreach ($rows as $row) {
            $tags[] = new Tag(
                (int)$row['id'],
                $row['name']
            );
        }

        return $tags;
    }

    public function getById(int $id): ?Tag
    {
        $row = parent::findById($id);

        if (!$row) {
            return null;
        }

        return new Tag(
            (int)$row['id'],
            $row['name']
        );
    }

    public function updateTag(Tag $tag): bool
    {
        return parent::update(
            $tag->getId(),
            ['name' => $tag->getName()]
        );
    }

    public function deleteTag(int $id): bool
    {
        return parent::delete($id);
    }

    public function findByName(string $name): ?Tag
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['name' => $name]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Tag(
            (int)$row['id'],
            $row['name']
        );
    }
}
