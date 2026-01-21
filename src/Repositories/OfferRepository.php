<?php

namespace App\Repositories;

use App\Models\Offer;
use App\Repositories\BaseRepository;
use App\Repositories\CategorieRepository;
use App\Repositories\RecruiterRepository;
use App\Models\Recruiter;
use App\Models\Categorie;

class OfferRepository extends BaseRepository
{
    protected string $table = 'offers';
    private RecruiterRepository $recruiterRepository;
    private CategorieRepository $categoryRepository;

    public function __construct()
    {
        parent::__construct(self::$db);
        $this->recruiterRepository = new RecruiterRepository();
        $this->categoryRepository = new CategorieRepository();
    }

    /**
     * Create new offer
     */
    public function createOffer(Offer $offer): bool
    {
        return $this->insert([
            'title' => $offer->getTitle(),
            'description' => $offer->getDescription(),
            'is_archived' => $offer->isArchived() ? 1 : 0,
            'salary_min' => $offer->getSalaryMin(),
            'salary_max' => $offer->getSalaryMax(),
            'location' => $offer->getLocation(),
            'job_type' => $offer->getJobType(),
            'recruiter_id' => $offer->getRecruiter()->getId(),
            'categorie_id' => $offer->getCategorie()->getId()
        ]);
    }

    /**
     * Get all offers as Offer objects
     */
    public function getAll(): array
    {
        $rows = parent::findAll();
        $offers = [];

        foreach ($rows as $row) {
            $recruiter = $this->recruiterRepository->getById((int) $row['recruiter_id']);
            $category = $this->categoryRepository->getById((int) $row['categorie_id']);
            
            $offers[] = new Offer(
                (int)$row['id'],
                $row['title'],
                $row['description'],
                (bool)$row['is_archived'],
                (float)$row['salary_min'],
                (float)$row['salary_max'],
                $row['location'],
                $row['job_type'],
                $recruiter,
                $category
            );
        }

        return $offers;
    }

    /**
     * Get offer by ID as Offer object
     */
    public function getById(int $id): ?Offer
    {
        $row = parent::findById($id);

        if (!$row) {
            return null;
        }

        $recruiter = $this->recruiterRepository->getById((int) $row['recruiter_id']);
        $category = $this->categoryRepository->getById((int) $row['categorie_id']);

        return new Offer(
            (int)$row['id'],
            $row['title'],
            $row['description'],
            (bool)$row['is_archived'],
            (float)$row['salary_min'],
            (float)$row['salary_max'],
            $row['location'],
            $row['job_type'],
            $recruiter,
            $category
        );
    }

    /**
     * Update offer
     */
    public function updateOffer(Offer $offer): bool
    {
        return parent::update(
            $offer->getId(),
            [
                'title' => $offer->getTitle(),
                'description' => $offer->getDescription(),
                'is_archived' => $offer->isArchived() ? 1 : 0,
                'salary_min' => $offer->getSalaryMin(),
                'salary_max' => $offer->getSalaryMax(),
                'location' => $offer->getLocation(),
                'job_type' => $offer->getJobType(),
                'recruiter_id' => $offer->getRecruiter()->getId(),
                'categorie_id' => $offer->getCategorie()->getId()
            ]
        );
    }

    /**
     * Delete offer
     */
    public function deleteOffer(int $id): bool
    {
        return parent::delete($id);
    }

    /**
     * Find offers by recruiter
     */
    public function findByRecruiter(int $recruiterId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE recruiter_id = :recruiter_id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['recruiter_id' => $recruiterId]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $offers = [];

        foreach ($rows as $row) {
            $recruiter = $this->recruiterRepository->getById((int) $row['recruiter_id']);
            $category = $this->categoryRepository->getById((int) $row['categorie_id']);
            
            $offers[] = new Offer(
                (int)$row['id'],
                $row['title'],
                $row['description'],
                (bool)$row['is_archived'],
                (float)$row['salary_min'],
                (float)$row['salary_max'],
                $row['location'],
                $row['job_type'],
                $recruiter,
                $category
            );
        }

        return $offers;
    }

    /**
     * Find offers by category
     */
    public function findByCategory(int $categoryId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE categorie_id = :categorie_id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['categorie_id' => $categoryId]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $offers = [];

        foreach ($rows as $row) {
            $recruiter = $this->recruiterRepository->getById((int) $row['recruiter_id']);
            $category = $this->categoryRepository->getById((int) $row['categorie_id']);
            
            $offers[] = new Offer(
                (int)$row['id'],
                $row['title'],
                $row['description'],
                (bool)$row['is_archived'],
                (float)$row['salary_min'],
                (float)$row['salary_max'],
                $row['location'],
                $row['job_type'],
                $recruiter,
                $category
            );
        }

        return $offers;
    }

    /**
     * Find active offers (not archived)
     */
    public function findActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_archived = 0";
        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $offers = [];

        foreach ($rows as $row) {
            $recruiter = $this->recruiterRepository->getById((int) $row['recruiter_id']);
            $category = $this->categoryRepository->getById((int) $row['categorie_id']);
            
            $offers[] = new Offer(
                (int)$row['id'],
                $row['title'],
                $row['description'],
                (bool)$row['is_archived'],
                (float)$row['salary_min'],
                (float)$row['salary_max'],
                $row['location'],
                $row['job_type'],
                $recruiter,
                $category
            );
        }

        return $offers;
    }

    /**
     * Archive an offer
     */
    public function archive(int $id): bool
    {
        return parent::update($id, ['is_archived' => 1]);
    }

    /**
     * Unarchive an offer
     */
    public function unarchive(int $id): bool
    {
        return parent::update($id, ['is_archived' => 0]);
    }

    /**
     * Search offers by title or description
     */
    public function search(string $keyword): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE (title LIKE :keyword OR description LIKE :keyword) AND is_archived = 0";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['keyword' => "%$keyword%"]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $offers = [];

        foreach ($rows as $row) {
            $recruiter = $this->recruiterRepository->getById((int) $row['recruiter_id']);
            $category = $this->categoryRepository->getById((int) $row['categorie_id']);
            
            $offers[] = new Offer(
                (int)$row['id'],
                $row['title'],
                $row['description'],
                (bool)$row['is_archived'],
                (float)$row['salary_min'],
                (float)$row['salary_max'],
                $row['location'],
                $row['job_type'],
                $recruiter,
                $category
            );
        }

        return $offers;
    }

    /**
     * Find offers by location
     */
    public function findByLocation(string $location): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE location = :location AND is_archived = 0";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['location' => $location]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $offers = [];

        foreach ($rows as $row) {
            $recruiter = $this->recruiterRepository->getById((int) $row['recruiter_id']);
            $category = $this->categoryRepository->getById((int) $row['categorie_id']);
            
            $offers[] = new Offer(
                (int)$row['id'],
                $row['title'],
                $row['description'],
                (bool)$row['is_archived'],
                (float)$row['salary_min'],
                (float)$row['salary_max'],
                $row['location'],
                $row['job_type'],
                $recruiter,
                $category
            );
        }

        return $offers;
    }
}