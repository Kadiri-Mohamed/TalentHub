<?php
namespace App\Models;
use App\Models\Role;
use App\Models\User;

class Recruiter extends User{

   private string $company_name;

   public function __construct(int $id,string $name,string $email,string $password,Role $role,string $company_name){
        Parent::__construct($id,$name,$email,$password,$role);
        $this->company_name = $company_name;
   }

   public function getCompanyName():string{
    return $this->company_name;
   }

   public function setCompanyName(string $company_name){
    $this->company_name = $company_name;
   }

}