<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;

class TagService
{
    private TagRepository $tagRepository;

    public function __construct()
    {
        $this->tagRepository = new TagRepository();
    }

    
    public function create(string $name): bool
    {
        $name = trim($name);

        if ($name === '') {
            return false;
        }

        if ($this->tagRepository->findByName($name)) {
            return false;
        }

        $tag = new Tag(0, $name);
        return $this->tagRepository->create($tag);
    }

    
    public function getAll(): array
    {
        return $this->tagRepository->getAll();
    }

    
    public function getById(int $id): ?Tag
    {
        return $this->tagRepository->getById($id);
    }

    public function update(int $id, string $name): bool
    {
        $tag = $this->tagRepository->getById($id);

        if (!$tag) {
            return false;
        }

        $name = trim($name);
        if ($name === '') {
            return false;
        }

        $existing = $this->tagRepository->findByName($name);
        if ($existing && $existing->getId() !== $id) {
            return false;
        }

        $tag->setName($name);
        return $this->tagRepository->updateTag($tag);
    }

    
    public function delete(int $id): bool
    {
        return $this->tagRepository->deleteTag($id);
    }

   
    public function getOrCreate(string $name): ?Tag
    {
        $name = trim($name);

        if ($name === '') {
            return null;
        }

        $tag = $this->tagRepository->findByName($name);

        if ($tag) {
            return $tag;
        }

        $created = $this->create($name);
        if (!$created) {
            return null;
        }

        return $this->tagRepository->findByName($name);
    }
}
