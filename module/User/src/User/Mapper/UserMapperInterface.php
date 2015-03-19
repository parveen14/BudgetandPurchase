<?php 

namespace User\Mapper;

interface UserMapperInterface 
{
    
    public function getMe();
    
    public function addDepartment($data);
    
    public function addProject($data);
    
    public function addBusinessunit($data);
    
    public function addRole($data);
    
    public function addGroup($data);
    
    public function addLocation($data);
    
    public function addLevel($data);
    
    public function addCostcenter($data);
    
    public function addCostcentergroup($data);
}

?>