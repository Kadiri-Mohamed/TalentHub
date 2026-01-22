<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\OfferRepository;
use App\Repositories\ApplicationRepository;
use App\Repositories\RecruiterRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TagRepository;
use App\Repositories\CategorieRepository;

use App\Models\User;
use App\Models\Candidate;
use App\Models\Offer;
use App\Models\Application;
use App\Models\Recruiter;
use App\Models\Role;
use App\Models\Tag;
use App\Models\Categorie;

class AdminService
{
    private UserRepository $userRepository;
    private CandidateRepository $CandidateRepository;
    private OfferRepository $offerRepository;
    private ApplicationRepository $applicationRepository;
    private RecruiterRepository $recruiterRepository;
    private RoleRepository $roleRepository;
    private TagRepository $tagRepository;
    private CategorieRepository $categorieRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->CandidateRepository = new CandidateRepository();
        $this->offerRepository = new OfferRepository();
        $this->applicationRepository = new ApplicationRepository();
        $this->recruiterRepository = new RecruiterRepository();
        $this->roleRepository = new RoleRepository();
        $this->tagRepository = new TagRepository();
        $this->categorieRepository = new CategorieRepository();
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();
    }

    public function getUserById(int $id): ?User
    {
        $user = $this->userRepository->getById($id);
        if ($user) {
            return $user;
        }
        return null;
    }

    public function updateUserRole(int $userId, int $roleId): bool
    {
        $user = $this->userRepository->getById($userId);
        if ($user) {
            $role = $this->roleRepository->getById($roleId);
            $user->setRole($role);
            return $this->userRepository->updateUser($user);
        }
        return false;
    }
    public function deleteUser(int $userId): bool
    {
        $user = $this->userRepository->getById($userId);
        if ($user) {
            return $this->userRepository->deleteUser($userId);
        }
        return false;
    }
    public function getAllCandidates(): array
    {
        return $this->CandidateRepository->getAllCandidateUsers();
    }
    public function getAllOffers(): array
    {
        return $this->offerRepository->getAll();
    }
    public function getAllApplications(): array
    {
        return $this->applicationRepository->getAll();
    }
    public function getAllRecruiters(): array
    {
        return $this->recruiterRepository->getAll();
    }
    public function getRecruiterById(int $id): ?Recruiter
    {
        $recruiter = $this->recruiterRepository->getById($id);
        if ($recruiter) {
            return $recruiter;
        }
        return null;
    }
    public function deleteRecruiter(int $id): bool
    {
        $recruiter = $this->recruiterRepository->getById($id);
        if ($recruiter) {
            return $this->recruiterRepository->deleteRecruiter($id);
        }
        return false;
    }
    public function archiveOffer(int $id): bool
    {
        $offer = $this->offerRepository->getById($id);
        if ($offer) {
            $offer->setIsArchived(true);
            return $this->offerRepository->archive($id);
        }
        return false;
    }
    public function unarchiveOffer(int $id): bool
    {
        $offer = $this->offerRepository->getById($id);
        if ($offer) {
            $offer->setIsArchived(false);
            return $this->offerRepository->unarchive($id);
        }
        return false;
    }
    public function deleteOffer(int $id): bool
    {
        $offer = $this->offerRepository->getById($id);
        if ($offer) {
            return $this->offerRepository->deleteOffer($id);
        }
        return false;
    }
    public function deleteApplication(int $id): bool
    {
        $application = $this->applicationRepository->getById($id);
        if ($application) {
            return $this->applicationRepository->deleteApplication($id);
        }
        return false;
    }
    public function deleteCandidate(int $id): bool
    {
        $candidate = $this->CandidateRepository->getById($id);
        if ($candidate) {
            return $this->CandidateRepository->deleteCandidate($id);
        }
        return false;
    }
    public function getCandidateById(int $id): ?Candidate
    {
        $candidate = $this->CandidateRepository->getById($id);
        if ($candidate) {
            return $candidate;
        }
        return null;
    }

    public function updateApplicationStatus(int $id, string $status): bool
    {
        $application = $this->applicationRepository->getById($id);
        if ($application) {
            $application->setStatus($status);
            return $this->applicationRepository->updateApplication($application);
        }
        return false;
    }

    public function getOfferById(int $id): ?Offer
    {
        $offer = $this->offerRepository->getById($id);
        if ($offer) {
            return $offer;
        }
        return null;
    }

    public function createTag(string $name): bool
    {
        $tag = new Tag(1, $name);
        return $this->tagRepository->create($tag);
    }

    public function createCategorie(string $name): bool
    {
        $categorie = new Categorie(1, $name);
        return $this->categorieRepository->create($categorie);
    }

    public function deleteTag(int $id): bool
    {
        $tag = $this->tagRepository->getById($id);
        if ($tag) {
            return $this->tagRepository->delete($id);
        }
        return false;
    }

    public function deleteCategorie(int $id): bool
    {
        $categorie = $this->categorieRepository->getById($id);
        if ($categorie) {
            return $this->categorieRepository->delete($id);
        }
        return false;
    }

     public function getStats(): array
    {
        return [
            'users' => count($this->getAllUsers()),
            'candidates' => count($this->getAllCandidates()),
            'recruiters' => count($this->getAllRecruiters()),
            'offers' => count($this->getAllOffers()),
            'applications' => count($this->getAllApplications()),
        ];
    }


}