<?php

namespace App\Models;

class Categorie {

    private int $id;    
    private string $name;

    public function __construct(int $id,string $name){
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

    public function setId(int $id ){
        $this->id = $id;
    }
    
    public function setName(string $name ){
        $this->name = $name;
    }

    }
