<?php

namespace App\Services;

use App\Models\Categorie;
use App\Repositories\CategorieRepository;

class CategorieService
{
    private CategorieRepository $categorieRepository;

    public function __construct()
    {
        $this->categorieRepository = new CategorieRepository();
    }

   
    public function create(string $name): bool
    {
        $name = trim($name);

        if ($name === '') {
            return false;
        }

        // Prevent duplicates
        if ($this->categorieRepository->findByName($name)) {
            return false;
        }

        $categorie = new Categorie(0, $name);
        return $this->categorieRepository->create($categorie);
    }


    public function getAll(): array
    {
        return $this->categorieRepository->getAll();
    }

    public function getById(int $id): ?Categorie
    {
        return $this->categorieRepository->getById($id);
    }

    public function update(int $id, string $name): bool
    {
        $categorie = $this->categorieRepository->getById($id);

        if (!$categorie) {
            return false;
        }

        $name = trim($name);
        if ($name === '') {
            return false;
        }

        $existing = $this->categorieRepository->findByName($name);
        if ($existing && $existing->getId() !== $id) {
            return false;
        }

        $categorie->setName($name);
        return $this->categorieRepository->updateCategorie($categorie);
    }


    public function delete(int $id): bool
    {
        return $this->categorieRepository->deleteCategorie($id);
    }


    public function getOrCreate(string $name): ?Categorie
    {
        $name = trim($name);

        if ($name === '') {
            return null;
        }

        $categorie = $this->categorieRepository->findByName($name);

        if ($categorie) {
            return $categorie;
        }

        $created = $this->create($name);
        if (!$created) {
            return null;
        }

        return $this->categorieRepository->findByName($name);
    }
}
