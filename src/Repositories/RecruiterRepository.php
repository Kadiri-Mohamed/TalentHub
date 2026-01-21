<?php

namespace App\Repositories;

use App\Models\Recruiter;
use App\Repositories\BaseRepository;
use PDO;

class RecruiterRepository extends BaseRepository
{
    protected string $table = 'recruiters';
    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct(self::$db);
        $this->userRepository = new UserRepository();
    }

    public function createRecruiter(Recruiter $recruiter): bool
    {
        try {
            self::$db->beginTransaction();

            $this->userRepository->createUser([
                'name' => $recruiter->getName(),
                'email' => $recruiter->getEmail(),
                'password' => $recruiter->getPassword(),
                'role' => $recruiter->getRole()
            ]);

            $userId = self::$db->lastInsertId();

            parent::insert([
                'user_id' => $userId,
                'company_name' => $recruiter->getCompanyName()
            ]);

            self::$db->commit();
            return true;

        } catch (\Exception $e) {
            self::$db->rollBack();
            return false;
        }
    }

    public function getAll(): array
    {
        $sql = "
            SELECT u.id, u.name, u.email, u.password, u.role_id, r.company_name
            FROM users u
            INNER JOIN recruiters r ON u.id = r.user_id
        ";
        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $recruiters = [];
        foreach ($rows as $row) {
            $role = $this->userRepository->getById((int)$row['id'])->getRole();
            $recruiters[] = new Recruiter(
                (int)$row['id'],
                $row['name'],
                $row['email'],
                $row['password'],
                $role,
                $row['company_name']
            );
        }

        return $recruiters;
    }

    public function getById(int $id): ?Recruiter
    {
        $user = $this->userRepository->getById($id);
        if (!$user) return null;

        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Recruiter(
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getRole(),
            $row['company_name']
        );
    }

    public function updateRecruiter(Recruiter $recruiter): bool
    {
        try {
            self::$db->beginTransaction();

            $this->userRepository->updateUser($recruiter);

            parent::update(
                $recruiter->getId(),
                ['company_name' => $recruiter->getCompanyName()]
            );

            self::$db->commit();
            return true;

        } catch (\Exception $e) {
            self::$db->rollBack();
            return false;
        }
    }

    public function deleteRecruiter(int $id): bool
    {
        return $this->userRepository->deleteUser($id);
    }

    
    public function findByEmail(string $email): ?Recruiter
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) return null;

        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['id' => $user->getId()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Recruiter(
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getRole(),
            $row['company_name']
        );
    }
}
