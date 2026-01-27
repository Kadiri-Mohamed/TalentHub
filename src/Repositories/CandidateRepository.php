<?php

namespace App\Repositories;

use App\Models\Candidate;
use App\Repositories\BaseRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\Role;
use PDO;

class CandidateRepository extends BaseRepository
{
    protected string $table = 'candidates';
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function getById(int $userId): ?Candidate
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        $candidateData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$candidateData) {
            return null;
        }

        $user = $this->userRepository->getById($userId);

        if (!$user) {
            return null;
        }

        return new Candidate(
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getRole(),
            (float) $candidateData['salary_min'],
            (float) $candidateData['salary_max'],
            $candidateData['cv_path'] ?? '',
        );
    }

    public function createCandidate(array $data): bool
    {
        self::$db->beginTransaction();

        try {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $this->getCandidateRoleId()
            ];

            $userCreated = $this->userRepository->createUser($userData);

            if (!$userCreated) {
                throw new \Exception("Failed to create user");
            }

            $userId = self::$db->lastInsertId();

            $columns = ['user_id', 'salary_min', 'salary_max', 'cv_path'];
            $fields = implode(',', $columns);
            $values = ':' . implode(',:', $columns);
            $sql = "INSERT INTO {$this->table} ($fields) VALUES ($values)";
            $stmt = self::$db->prepare($sql);

            $result = $stmt->execute([
                'user_id' => $userId,
                'salary_min' => $data['salary_min'] ?? 0,
                'salary_max' => $data['salary_max'] ?? 0,
                'cv_path' => $data['cv_path'] ?? ''
            ]);

            self::$db->commit();
            return $result;
        } catch (\Exception $e) {
            self::$db->rollBack();
            return false;
        }
    }

    public function updateCandidate(Candidate $candidate): bool
    {
        self::$db->beginTransaction();

        try {
            $userUpdated = $this->userRepository->updateUser($candidate);

            if (!$userUpdated) {
                throw new \Exception("Failed to update user");
            }

            $data = [
                'salary_min' => $candidate->getSalaryMin(),
                'salary_max' => $candidate->getSalaryMax(),
                'cv_path' => $candidate->getCvPath(),
                'user_id' => $candidate->getId()
            ];

            $fields = [];
            foreach ($data as $column => $value) {
                if ($column !== 'user_id') {
                    $fields[] = "$column = :$column";
                }
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
            $stmt = self::$db->prepare($sql);
            $stmt->execute($data);

            self::$db->commit();
            return true;
        } catch (\Exception $e) {
            self::$db->rollBack();
            return false;
        }
    }

    public function deleteCandidate(int $userId): bool
    {
        self::$db->beginTransaction();

        try {
            $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
            $stmt = self::$db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            $userDeleted = $this->userRepository->deleteUser($userId);

            if (!$userDeleted) {
                throw new \Exception("Failed to delete user");
            }

            self::$db->commit();
            return true;
        } catch (\Exception $e) {
            self::$db->rollBack();
            return false;
        }
    }

    public function getAllCandidates(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = self::$db->query($sql);
        $candidateRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $candidates = [];

        foreach ($candidateRows as $row) {
            $user = $this->userRepository->getById((int) $row['user_id']);

            if ($user) {
                $candidates[] = new Candidate(
                    $user->getId(),
                    $user->getName(),
                    $user->getEmail(),
                    $user->getPassword(),
                    $user->getRole(),
                    (float) $row['salary_min'],
                    (float) $row['salary_max'],
                    $row['cv_path'] ?? ''
                );
            }
        }

        return $candidates;
    }

    public function findByEmail(string $email): ?Candidate
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null;
        }

        return $this->getById($user->getId());
    }

    private function getCandidateRoleId(): int
    {
        $sql = "SELECT id FROM roles WHERE name = 'candidate' LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int) $result['id'] : 2;
    }

    public function updateCvPath(int $candidateId, string $cvPath): bool
    {
        $sql = "UPDATE {$this->table} SET cv_path = :cv_path WHERE user_id = :user_id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'user_id' => $candidateId,
            'cv_path' => $cvPath
        ]);
    }

    public function updateSalary(int $candidateId, float $min, float $max): bool
    {
        $sql = "UPDATE {$this->table} SET salary_min = :salary_min, salary_max = :salary_max 
                WHERE user_id = :user_id";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            'user_id' => $candidateId,
            'salary_min' => $min,
            'salary_max' => $max
        ]);
    }

    public function findBySalaryRange(float $minSalary, float $maxSalary): array
    {
        $sql = "SELECT c.* FROM {$this->table} c 
                WHERE c.salary_min <= :max_salary 
                AND c.salary_max >= :min_salary";

        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            'min_salary' => $minSalary,
            'max_salary' => $maxSalary
        ]);

        $candidateRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $candidates = [];

        foreach ($candidateRows as $row) {
            $user = $this->userRepository->getById((int) $row['user_id']);

            if ($user) {
                $candidates[] = new Candidate(
                    $user->getId(),
                    $user->getName(),
                    $user->getEmail(),
                    $user->getPassword(),
                    $user->getRole(),
                    (float) $row['salary_min'],
                    (float) $row['salary_max'],
                    $row['cv_path'] ?? ''
                );
            }
        }

        return $candidates;
    }

    public function getAllCandidateUsers(): array
    {
        $sql = "SELECT 
                u.id, 
                u.name, 
                u.email, 
                u.password, 
                u.role_id,
                r.name as role_name,
                c.salary_min, 
                c.salary_max, 
                c.cv_path
            FROM users u
            LEFT JOIN candidates c ON u.id = c.user_id
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE r.name = 'candidate' OR u.role_id = :role_id";

        $stmt = self::$db->prepare($sql);
        $stmt->execute(['role_id' => $this->getCandidateRoleId()]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $candidates = [];

        foreach ($rows as $row) {
            $candidates[] = new Candidate(
                (int) $row['id'],
                $row['name'],
                $row['email'],
                $row['password'],
                new Role((int) $row['role_id'], $row['role_name']),
                (float) $row['salary_min'],
                (float) $row['salary_max'],
                $row['cv_path'] ?? ''
            );
        }

        return $candidates;
    }
}