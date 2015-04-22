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
    
    public function addCostcenter($data) {
    
        return $this->userMapper->addCostcenter($data);
    }
    
    public function addCostcentergroup($data) {
        return $this->userMapper->addCostcentergroup($data);
    }
    
    public function addUser($data) {
        return $this->userMapper->addUser($data);
    }
    
    public function addUserdetails($data) {
		return $this->userMapper->addUserdetails($data);
	}
	
	public function addWbs($data) {
	    return $this->userMapper->addWbs($data);
	}
	
	public function updateUserprofile($data) {
	    return $this->userMapper->updateUserprofile($data);
	}
	
	public function updateCompanyprofile($data) {
	    return $this->userMapper->updateCompanyprofile($data);
	}
	
	public function deleteWbs($id) {
	    return $this->userMapper->deleteWbs($id);
	}
	
	public function checkWbsParent($id) {
	    return $this->userMapper->checkWbsParent($id);
	}
	public function getWbsProject($id) {
	    return $this->userMapper->getWbsProject($id);
	}
}

?>
