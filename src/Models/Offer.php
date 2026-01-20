<?php
namespace App\Models;
use App\Models\Categorie;
use App\Models\Recruiter;


class Offer
{
    private int $id;
    private string $title;
    private string $description;
    private bool $is_archived;
    private float $salary_min;
    private float $salary_max;
    private string $location;
    private string $job_type;
    private Recruiter $recruiter;
    private Categorie $categorie;

    public function __construct(int $id, string $title, string $description, bool $is_archived, float $salary_min, float $salary_max,  string $location,string $job_type,Recruiter $recruiter, Categorie $categorie
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->is_archived = $is_archived;
        $this->salary_min = $salary_min;
        $this->salary_max = $salary_max;
        $this->location = $location;
        $this->job_type = $job_type;
        $this->recruiter = $recruiter;
        $this->categorie = $categorie;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isArchived(): bool
    {
        return $this->is_archived;
    }

    public function getSalaryMin(): float
    {
        return $this->salary_min;
    }

    public function getSalaryMax(): float
    {
        return $this->salary_max;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getJobType(): string
    {
        return $this->job_type;
    }

    public function getRecruiter(): Recruiter
    {
        return $this->recruiter;
    }

    public function getCategorie(): Categorie
    {
        return $this->categorie;
    }


    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setIsArchived(bool $is_archived): void
    {
        $this->is_archived = $is_archived;
    }

    public function setSalaryMin(float $salary_min): void
    {
        $this->salary_min = $salary_min;
    }

    public function setSalaryMax(float $salary_max): void
    {
        $this->salary_max = $salary_max;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function setJobType(string $job_type): void
    {
        $this->job_type = $job_type;
    }

    public function setRecruiter(Recruiter $recruiter): void
    {
        $this->recruiter = $recruiter;
    }

    public function setCategorie(Categorie $categorie): void
    {
        $this->categorie = $categorie;
    }
}