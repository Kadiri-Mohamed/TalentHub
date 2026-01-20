<?php
namespace App\Models;

class tag{
     private int $id;
    private string $name;

    public function __construct( int $id, string $name ) {
        $this->id = $id;
        $this->name = $name;
    }

      public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

     public function setId(string $id): void{
        $this->id = $id;
    }
     public function setName(string $name): void{
        $this->name = $name;
    }

}