<?php

namespace App\Models;
use App\Models\User;

class Candidate extends User
{

    private float $salary_min;
    private float $salary_max;
    private string $cv_path;

    public function __construct(
        int $id,
        string $name,
        string $email,
        string $password,
        Role $role,
        float $salary_min,
        float $salary_max,
        string $cv_path,
    ) {
        parent::__construct($id, $name, $email, $password, $role);
        $this->salary_min = $salary_min;
        $this->salary_max = $salary_max;
        $this->cv_path = $cv_path;
    }

    public function getSalaryMin(): float{
        return $this->salary_min;
    }

    public function getSalaryMax(): float{
        return $this->salary_max;
    }

    public function getCvPath(): string{
        return $this->cv_path;
    }

    public function setSalaryMin(float $salary_min): void{
        $this->salary_min = $salary_min;
    }

    public function setSalaryMax(float $salary_max): void{
        $this->salary_max = $salary_max;
    }

    public function setCvPath(string $cv_path): void{
        $this->cv_path = $cv_path;
    }
}
