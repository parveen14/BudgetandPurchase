<?php 

namespace User\Mapper;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Stdlib\Hydrator\HydratorInterface;
use User\Model\UserInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;


class UserMapper implements UserMapperInterface{
    
    protected $dbAdapter;
    protected $hydrator;
    protected $userPrototype;
    
    public function __construct(AdapterInterface $dbAdapter,HydratorInterface $hydrator,UserInterface $userPrototype ) {
        
        $this->dbAdapter=$dbAdapter;    
        $this->hydrator=$hydrator;
        $this->userPrototype=$userPrototype;
        
    }
    
    public function getMe(){
        
        $sql    = new Sql($this->dbAdapter);
         $select = $sql->select('department');

         $stmt   = $sql->prepareStatementForSqlObject($select);
         $result = $stmt->execute();

         if ($result instanceof ResultInterface && $result->isQueryResult()  && $result->getAffectedRows()) {
             
             $resultSet = new ResultSet ();
             $resultSet->initialize ( $result );
             $allData=$resultSet->toArray();
            // print_r($allData); die;
             return $allData;
//              $resultSet = new HydratingResultSet($this->hydrator, $this->userPrototype);
//              return $resultSet->initialize($result);
         }
         
         return array();
    }
    
    public function addDepartment($data)
    {
        
            $sql = new Sql($this->dbAdapter);
           
            if ((! $data['department_id']) OR empty($data['department_id'])) {
                $department = $sql->insert('department')->values(array(
                    'title' => $data['title'],
                    'user_id'=> $data['user_id'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d'),
                    'modified_at' => date('Y-m-d')
                ));
                
                //$department_id = $this->dbAdapter->getDriver()->getLastGeneratedValue();
                $type = "insert";
            } else {
                $values = array(
                    'title' => $data['title'],
                    'user_id' => $data['user_id'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'modified_at' => date('Y-m-d')
                    
                );
               
                $department = $sql->update('department')
                ->set($values)
                ->where(array(
                    'id' => $data['department_id']
                ));
                $department_id = $data['department_id'];
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
       
            if ($result) {  
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $department_id,
                    'type' => $type
                );
            }
        }
        
        public function addProject($data)
        {
        
            $sql = new Sql($this->dbAdapter);
             
            if ((! $data['project_id']) OR empty($data['project_id'])) {
                $department = $sql->insert('project')->values(array(
                    'title' => $data['title'],
                    'user_id'=> $data['user_id'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d'),
                    'modified_at' => date('Y-m-d')
                ));
        
                //$department_id = $this->dbAdapter->getDriver()->getLastGeneratedValue();
                $type = "insert";
            } else {
                $values = array(
                    'title' => $data['title'],
                    'user_id' => $data['user_id'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'modified_at' => date('Y-m-d')
        
                );
                 
                $department = $sql->update('project')
                ->set($values)
                ->where(array(
                    'id' => $data['project_id']
                ));
                $project_id = $data['project_id'];
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            
            if ((! $data['project_id']) OR empty($data['project_id'])) {
                $project_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }

            /****  Assign Business Unit To Project ***/
            $select = $sql->select('business_project');
            $select->where(array('project_id'=>$project_id));
            $stmt = $sql->prepareStatementForSqlObject($select);
            $result1 = $stmt->execute();
            if ($result1 instanceof ResultInterface && $result1->isQueryResult() && $result1->getAffectedRows()) {
                $assignBusinessUnit = $sql->update('business_project')
                ->set(array('businessunit_id'=>$data['business_unit']))
                ->where(array(
                    'project_id' => $project_id
                ));
            } else {
                $assignBusinessUnit = $sql->insert('business_project')
                ->values(array('businessunit_id'=>$data['business_unit'],'project_id' => $project_id));
            }
            
            $selectString = $sql->getSqlStringForSqlObject($assignBusinessUnit);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            
            /****  End Assign Business Unit To Project ***/
            
            if ($result) {
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $project_id,
                    'type' => $type
                );
            }
        }
        
        public function addBusinessunit($data)
        {
        
            $sql = new Sql($this->dbAdapter);
             
            if ((! $data['businessunit_id']) OR empty($data['businessunit_id'])) {
                $department = $sql->insert('businessunit')->values(array(
                    'title' => $data['title'],
                    'user_id'=> $data['user_id'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d'),
                    'modified_at' => date('Y-m-d')
                ));
        
                $type = "insert";
            } else {
                $values = array(
                    'title' => $data['title'],
                    'user_id' => $data['user_id'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'modified_at' => date('Y-m-d')
        
                );
                 
                $department = $sql->update('businessunit')
                ->set($values)
                ->where(array(
                    'id' => $data['businessunit_id']
                ));
                $businessunit_id = $data['businessunit_id'];
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            if ((! $data['businessunit_id']) OR empty($data['businessunit_id'])) {
                $businessunit_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }
         
            if ($result) {
                
                     $delete = $sql->delete ( 'business_department' )->where ( array (
                            'businessunit_id' => $businessunit_id
                        ) );
                     $selectString = $sql->getSqlStringForSqlObject($delete);
                     $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                     
                     
                   //  $delete = $sql->delete ( 'business_project' )->where ( array (
                   //      'businessunit_id' => $businessunit_id
                  //   ) );
                  //   $selectString = $sql->getSqlStringForSqlObject($delete);
                  //   $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                     
                     $delete = $sql->delete ( 'business_location' )->where ( array (
                         'businessunit_id' => $businessunit_id
                     ) );
                     $selectString = $sql->getSqlStringForSqlObject($delete);
                     $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                  
                foreach($data['department'] as $department) {
                    $business_department=$sql->insert('business_department')->values(array(
                        'businessunit_id' => $businessunit_id,
                        'department_id'=> $department
                    ));
                    $selectString = $sql->getSqlStringForSqlObject($business_department);
                    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                }
                
//                 foreach($data['project'] as $project) {
//                     $business_department=$sql->insert('business_project')->values(array(
//                         'businessunit_id' => $businessunit_id,
//                         'project_id'=> $project
//                     ));
//                     $selectString = $sql->getSqlStringForSqlObject($business_department);
//                     $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
//                 }
                
                foreach($data['location'] as $location) {
                    $business_location=$sql->insert('business_location')->values(array(
                        'businessunit_id' => $businessunit_id,
                        'location_id'=> $location
                    ));
                    $selectString = $sql->getSqlStringForSqlObject($business_location);
                    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                }
                
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $businessunit_id,
                    'type' => $type
                );
            }
        }
        
        public function addRole($data)
        {
        
            $sql = new Sql($this->dbAdapter);
             
            if ((! $data['role_id']) OR empty($data['role_id'])) {
                $department = $sql->insert('role')->values(array(
                    'title' => $data['title'],
                    'user_id'=> $data['user_id'],
                    'level_id' => $data['level_id'],
                    'parent_role' => $data['parent_role'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d'),
                    'modified_at' => date('Y-m-d')
                ));
        
                $type = "insert";
            } else {
                $values = array(
                    'title' => $data['title'],
                    'user_id' => $data['user_id'],
                    'level_id' => $data['level_id'],
                    'parent_role' => $data['parent_role'],
                    'status' => $data['status'],
                    'modified_at' => date('Y-m-d')
        
                );
                 
                $department = $sql->update('role')
                ->set($values)
                ->where(array(
                    'id' => $data['role_id']
                ));
                $role_id = $data['role_id'];
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            if ((! $data['role_id']) OR empty($data['role_id'])) {
                $role_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }

            if ($result) {
                $delete = $sql->delete ( 'role_permission' )->where ( array (
                    'role_id' => $role_id
                ) );
                    $selectString = $sql->getSqlStringForSqlObject($delete);
                     $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        
                foreach($data['permission'] as $permission) {
                    $role_permission=$sql->insert('role_permission')->values(array(
                        'role_id' => $role_id,
                        'permission_id'=> $permission
                    ));
                    $selectString = $sql->getSqlStringForSqlObject($role_permission);
                    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                }
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $role_id,
                    'type' => $type
                );
            }
        }
        
        public function addGroup($data)
        {
        
            $sql = new Sql($this->dbAdapter);
             
            if ((! $data['group_id']) OR empty($data['group_id'])) {
                $department = $sql->insert('group')->values(array(
                    'title' => $data['title'],
                    'user_id'=> $data['user_id'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d'),
                    'modified_at' => date('Y-m-d')
                ));
        
                $type = "insert";
            } else {
                $values = array(
                    'title' => $data['title'],
                    'user_id' => $data['user_id'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'modified_at' => date('Y-m-d')
        
                );
                 
                $department = $sql->update('group')
                ->set($values)
                ->where(array(
                    'id' => $data['group_id']
                ));
                $group_id = $data['group_id'];
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            if ((! $data['group_id']) OR empty($data['group_id'])) {
                $group_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }
        
            if ($result) {
                $delete = $sql->delete ( 'group_role' )->where ( array (
                    'group_id' => $group_id
                ) );
                $selectString = $sql->getSqlStringForSqlObject($delete);
                $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        
                foreach($data['role'] as $roles) {
                  
                    $group_role=$sql->insert('group_role')->values(array(
                        'group_id' => $group_id,
                        'role_id'=> $roles
                    ));
                    $selectString = $sql->getSqlStringForSqlObject($group_role);
                    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                }
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $group_id,
                    'type' => $type
                );
            }
        }
        
        
        public function addLocation($data)
        {
        
            $sql = new Sql($this->dbAdapter);
             
            if ((! $data['location_id']) OR empty($data['location_id'])) {
                $location = $sql->insert('location')->values(array(
                    'title' => $data['title'],
                    'user_id'=> $data['user_id'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d'),
                    'modified_at' => date('Y-m-d')
                ));
        
                //$department_id = $this->dbAdapter->getDriver()->getLastGeneratedValue();
                $type = "insert";
            } else {
                $values = array(
                    'title' => $data['title'],
                    'user_id' => $data['user_id'],
                    'status' => $data['status'],
                    'modified_at' => date('Y-m-d')
        
                );
                 
                $location = $sql->update('location')
                ->set($values)
                ->where(array(
                    'id' => $data['location_id']
                ));
                $location_id = $data['location_id'];
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($location);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        
            if ($result) {
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $location_id,
                    'type' => $type
                );
            }
        }
        
        
        public function addLevel($data)
        {
        
            $sql = new Sql($this->dbAdapter);
             
            if ((! $data['level_id']) OR empty($data['level_id'])) {
                $department = $sql->insert('role')->values(array(
                    'title' => $data['title'],
                    'user_id'=> $data['user_id'],
                    'budget_min' => $data['budget_min'],
                    'budget_max' => $data['budget_max'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d'),
                    'modified_at' => date('Y-m-d')
                ));
        
                $type = "insert";
            } else {
                $values = array(
                    'title' => $data['title'],
                    'user_id' => $data['user_id'],
                    'budget_min' => $data['budget_min'],
                    'budget_max' => $data['budget_max'],
                    'status' => $data['status'],
                    'modified_at' => date('Y-m-d')
        
                );
                 
                $department = $sql->update('level')
                ->set($values)
                ->where(array(
                    'id' => $data['level_id']
                ));
                $level_id = $data['level_id'];
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
           
        
            if ($result) {
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $level_id,
                    'type' => $type
                );
            }
        }
}

?>