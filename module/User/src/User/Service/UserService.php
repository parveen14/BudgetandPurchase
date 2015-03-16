<?php 

namespace User\Service;


use User\Mapper\UserMapperInterface;

class UserService implements UserServiceInterface {
    
    protected $userMapper;
    
    public function __construct( UserMapperInterface $userMapper){
        
        $this->userMapper=$userMapper;
        
    }
    public function getMe(){
        
        $this->userMapper->getMe();
    }
    
    public function addDepartment($data) {
        
        return $this->userMapper->addDepartment($data);
    }
    
    public function addProject($data) {
    
       return $this->userMapper->addProject($data);
    }
    
    public function addBusinessunit($data) {
        
        return $this->userMapper->addBusinessunit($data);
    } 
    
    public function addRole($data) {
    
        return $this->userMapper->addRole($data);
    }
    
    public function addGroup($data) {
    
        return $this->userMapper->addGroup($data);
    }
    
    public function addLocation($data) {
    
        return $this->userMapper->addLocation($data);
    }
    
    public function addLevel($data) {
    
        return $this->userMapper->addLevel($data);
    }
}

?>