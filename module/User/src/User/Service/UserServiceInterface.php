<?php 

namespace User\Service;


interface UserServiceInterface {
    
    public function getMe();
    
    public function addDepartment($data);
    
    public function addProject($data);
    
    public function addBusinessunit($data);
    
    public function addRole($data);
    
    public function addGroup($data);
    
    public function addLocation($data);
    
    public function addLevel($data);
}

?>