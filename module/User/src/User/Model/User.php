<?php 

namespace User\Model;

class User implements UserInterface {

    protected $id;
    
    public function setId($id){
        $this->id=$id;
    }
    
    public function getId(){
        return $this->id;
    }
    
}

?>