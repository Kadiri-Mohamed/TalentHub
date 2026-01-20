<?php
namespace Src\Models;
use App\Models\Role;
use App\Models\User;

class Recruiter extends User{

   private string $companyName;

   public function __construct(int $id,string $name,string $email,string $password,Role $role,string $companyName){
        Parent::__construct($id,$name,$email,$password,$role);
        $this->company_name = $companyName;
   }

   public function getCompanyName():string{
    return $this->companyName;
   }

   public function setCompanyName(string $companyName){
    $this->company_name = $companyName;
   }

}