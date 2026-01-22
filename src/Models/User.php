<?php

namespace App\Models;
use App\Models\Role;

class User
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private Role $role;

    public function __construct(
        int $id,
        string $name,
        string $email,
        string $password,
        Role $role
    ) {
        $this->id       = $id;
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->role   = $role;
    }

    
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setName(string $name): void{
        $this->name = $name;
    }

    public function setEmail(string $email): void{
        $this->email = $email;
    }   


    public function hasRole(string $roleName, Role $role): bool
    {
        return $role->getName() === $roleName;
    }

    public function setRole(Role $role): void{
        $this->role = $role;
    }
}
