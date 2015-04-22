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
use Common\Service\CommonServiceInterface;

class UserMapper implements UserMapperInterface{
    
    protected $dbAdapter;
    protected $hydrator;
    protected $userPrototype;
    protected $commonservice;
    
    public function __construct(AdapterInterface $dbAdapter,HydratorInterface $hydrator,UserInterface $userPrototype,CommonServiceInterface $commonservice ) {
        
        $this->dbAdapter=$dbAdapter;    
        $this->hydrator=$hydrator;
        $this->userPrototype=$userPrototype;
        $this->commonservice=$commonservice;
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
            $business_units=$data['business_units'];
            unset($data['business_units']);
            if ((! $data['i_dep_id']) OR empty($data['i_dep_id'])) {
                unset($data['i_dep_id']);
                $data['dt_created']=date('Y-m-d');
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->insert('departments')->values($data);
                $type = "insert";
                
            } else {
                $i_dep_id=$data['i_dep_id'];
                unset($data['i_dep_id']);
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->update('departments')->set($data)->where(array('i_dep_id' => $i_dep_id));
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            
             if ((! $i_dep_id) OR empty($i_dep_id)) {
                $i_dep_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }
            
            if ($result) {

                if(isset($business_units) AND is_array($business_units)) {
                    $delete = $sql->delete ( 'business_departments' )->where ( array (
                        'i_ref_dep_id' => $i_dep_id
                    ) );
                    $selectString = $sql->getSqlStringForSqlObject($delete);
                    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    
                    foreach($business_units as $business_units) {
                        $business_department=$sql->insert('business_departments')->values(array(
                            'i_ref_bu_id' => $business_units,
                            'i_ref_dep_id'=> $i_dep_id
                        ));
                        $selectString = $sql->getSqlStringForSqlObject($business_department);
                        $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    } 
                }
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $i_dep_id,
                    'type' => $type
                );
            }
        }
        
        public function addProject($data)
        {
        
            $sql = new Sql($this->dbAdapter);
             
            if ((! $data['i_project_id']) OR empty($data['i_project_id'])) {
                unset($data['i_project_id']);
                $data['dt_created']=date('Y-m-d');
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->insert('projects')->values($data);
                $type = "insert";
				
			} else {
                
                $i_project_id=$data['i_project_id'];
                unset($data['i_project_id']);
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->update('projects')->set($data)->where(array('i_project_id' => $i_project_id));
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            
            if ((!isset($i_project_id)) OR empty($i_project_id)) {
                $i_project_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
                
                unset($data['vc_description']);	unset($data['vc_comment']);	unset($data['i_ref_bu_id']);
                $data['i_parent_id']=NULL;
                $data['i_type']=1;
                $data['i_ref_project_id']=$i_project_id;
                $department = $sql->insert('wbs')->values($data);
                $selectString = $sql->getSqlStringForSqlObject($department);
                $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            }
            
            if ($result) {
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $i_project_id,
                    'type' => $type
                );
            }
        }
        
        public function addBusinessunit($data)
        {
        
            $sql = new Sql($this->dbAdapter);
            
            if(isset($data['departments'])) {
                $departments=$data['departments'];
                unset($data['departments']);
            }
            
            if ((! $data['i_bu_id']) OR empty($data['i_bu_id'])) {
                    unset($data['i_bu_id']);
                    $data['dt_created']=date('Y-m-d');
                    $data['dt_modified']=date('Y-m-d');
                    $department = $sql->insert('business_units')->values($data);
                    $type = "insert";
            } else {
                $i_bu_id=$data['i_bu_id'];
                unset($data['i_bu_id']);
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->update('business_units')->set($data)->where(array('i_bu_id' => $i_bu_id));
                $type = "update";
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            if ((!$i_bu_id) OR empty($i_bu_id)) {
                $i_bu_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }
         
            if ($result) {
                
                $delete = $sql->delete ( 'business_departments' )->where ( array (
                    'i_ref_bu_id' => $i_bu_id
                ) );
                $selectString = $sql->getSqlStringForSqlObject($delete);
                $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);

                if(isset($departments) AND is_array($departments)) {
                
                    foreach($departments as $departments) {
                        $business_department=$sql->insert('business_departments')->values(array(
                            'i_ref_bu_id' => $i_bu_id,
                            'i_ref_dep_id'=> $departments
                        ));
                        $selectString = $sql->getSqlStringForSqlObject($business_department);
                        $result_child = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    }
                }
                
                return array(
                   // 'success' => $result->getAffectedRows(),
                   'success' => 1,
                    'id' => $i_bu_id,
                    'type' => $type
                );
            }
        }
        
        public function addRole($data)
        {
           
            $sql = new Sql($this->dbAdapter);
            $roles_permission=$data['roles_permission'];
            unset($data['roles_permission']);
            if ((! $data['i_role_id']) OR empty($data['i_role_id'])) {
                
                unset($data['i_role_id']);
                $data['dt_created']=date('Y-m-d');
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->insert('roles')->values($data);
                $type = "insert";
                
            } else {
                
                $i_role_id=$data['i_role_id'];
                unset($data['i_role_id']);
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->update('roles')->set($data)->where(array('i_role_id' => $i_role_id));
                $type = "update";
                
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            if ((! $i_role_id) OR empty($i_role_id)) {
                $i_role_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }

            if ($result) {
                
                if(isset($roles_permission) AND is_array($roles_permission)) {
                    $delete = $sql->delete ( 'roles_permission' )->where ( array (
                        'i_ref_role_id' => $i_role_id
                    ) );
                    $selectString = $sql->getSqlStringForSqlObject($delete);
                    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                
                    foreach($roles_permission as $roles_permission) {
                        $business_department=$sql->insert('roles_permission')->values(array(
                            'i_ref_role_id' => $i_role_id,
                            'i_ref_permission_id'=> $roles_permission
                        ));
                        $selectString = $sql->getSqlStringForSqlObject($business_department);
                        $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    }
                }
                
                
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $i_role_id,
                    'type' => $type
                );
            }
        }
        
        public function addGroup($data)
        {
        
            $sql = new Sql($this->dbAdapter);
            $group_role=$data['group_role'];
            $group_permission=$data['group_permission'];
            unset($data['group_role']);
            unset($data['group_permission']);
            
            if ((! $data['i_group_id']) OR empty($data['i_group_id'])) {
                
                unset($data['i_group_id']);
                $data['dt_created']=date('Y-m-d');
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->insert('groups')->values($data);
                $type = "insert";
                
            } else {
                
                $i_group_id=$data['i_group_id'];
                unset($data['i_project_id']);
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->update('groups')->set($data)->where(array('i_group_id' => $i_group_id));
                $type = "update";
                
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            if ((! $i_group_id) OR empty($i_group_id)) {
                $i_group_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }
        
            if ($result) {
                
                if(isset($group_role) AND is_array($group_role)) {
                    $delete = $sql->delete ( 'group_role' )->where ( array (
                        'i_ref_group_id' => $i_group_id
                    ) );
                    $selectString = $sql->getSqlStringForSqlObject($delete);
                    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                
                    foreach($group_role as $group_role) {
                        $business_department=$sql->insert('group_role')->values(array(
                            'i_ref_group_id' => $i_group_id,
                            'i_ref_role_id'=> $group_role
                        ));
                        $selectString = $sql->getSqlStringForSqlObject($business_department);
                        $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    }
                }
                
                if(isset($group_permission) AND is_array($group_permission)) {
                    $delete = $sql->delete ( 'group_permission' )->where ( array (
                        'i_ref_group_id' => $i_group_id
                    ) );
                    $selectString = $sql->getSqlStringForSqlObject($delete);
                    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                
                    foreach($group_permission as $group_permission) {
                        $business_department=$sql->insert('group_permission')->values(array(
                            'i_ref_group_id' => $i_group_id,
                            'i_ref_permission_id'=> $group_permission
                        ));
                        $selectString = $sql->getSqlStringForSqlObject($business_department);
                        $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    }
                }
                
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $i_group_id,
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
             
            if ((! $data['i_level_id']) OR empty($data['i_level_id'])) {
                unset($data['i_level_id']);
                $data['dt_created']=date('Y-m-d');
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->insert('levels')->values($data);
                $type = "insert";
                
            } else {
                $i_level_id=$data['i_level_id'];
                unset($data['i_level_id']);
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->update('levels')->set($data)->where(array('i_level_id' => $i_level_id));
                $type = "update";
                
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
           
            if ((! $i_level_id) OR empty($i_level_id)) {
                $i_level_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }
            
            if ($result) {
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $i_level_id,
                    'type' => $type
                );
            }
        }
        
        public function addCostcenter($data)
        {

            $sql = new Sql($this->dbAdapter); 
            
            if(isset($data['cost_group'])) {
                $cost_group=$data['cost_group'];
                unset($data['cost_group']);
            }
            
            if ((! $data['i_cc_id']) OR empty($data['i_cc_id'])) {
                unset($data['i_cc_id']);
                $data['dt_created']=date('Y-m-d');
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->insert('cost_center')->values($data);
                $type = "insert";
                
            } else {
                $i_cc_id=$data['i_cc_id'];
                unset($data['i_cc_id']);
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->update('cost_center')->set($data)->where(array('i_cc_id' => $i_cc_id));
                $type = "update";
            }
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            
            if ((! $i_cc_id) OR empty($i_cc_id)) {
                $i_cc_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }
            
            if ($result) {
                
                $delete = $sql->delete ( 'costcenter_costgroup' )->where ( array (
                    'i_ref_cc_id' => $i_cc_id
                ) );
                $selectString = $sql->getSqlStringForSqlObject($delete);
                $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                
                if(isset($cost_group) AND is_array($cost_group)) {
                
                    foreach($cost_group as $cost_group) {
                        $business_department=$sql->insert('costcenter_costgroup')->values(array(
                            'i_ref_cc_id' => $i_cc_id,
                            'i_ref_ccgroup_id'=> $cost_group
                        ));
                        $selectString = $sql->getSqlStringForSqlObject($business_department);
                        $result_child = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    }
                }
                
                return array(
                    'success' => 1,
                    'id' => $i_cc_id,
                    'type' => $type
                );
            }
        }
        
        public function addCostcentergroup($data)
        {
        
            $sql = new Sql($this->dbAdapter);
            $costcenter_costgroup=$data['costcenter_costgroup'];
            unset($data['costcenter_costgroup']);
            if ((! $data['i_ccgroup_id']) OR empty($data['i_ccgroup_id'])) {

                unset($data['i_ccgroup_id']);
                $data['dt_created']=date('Y-m-d');
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->insert('cost_group')->values($data);
                $type = "insert";
                
            } else {
                
                $i_ccgroup_id=$data['i_ccgroup_id'];
                unset($data['i_ccgroup_id']);
                $data['dt_modified']=date('Y-m-d');
                $department = $sql->update('cost_group')->set($data)->where(array('i_ccgroup_id' => $i_ccgroup_id));
                $type = "update";
                
            }
        
            $selectString = $sql->getSqlStringForSqlObject($department);
            $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            if ((! $i_ccgroup_id) OR empty($i_ccgroup_id)) {
                $i_ccgroup_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            }
        
            if ($result) {
                 
                if(isset($costcenter_costgroup) AND is_array($costcenter_costgroup)) {
                    
                    $delete = $sql->delete ( 'costcenter_costgroup' )->where ( array (
                     'i_ref_ccgroup_id' => $i_ccgroup_id
                     ) );
                 $selectString = $sql->getSqlStringForSqlObject($delete);
                 $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                
                    foreach($costcenter_costgroup as $costcenter_costgroup) {
                    
                        $group_role=$sql->insert('costcenter_costgroup')->values(array(
                            'i_ref_cc_id' => $costcenter_costgroup,
                            'i_ref_ccgroup_id'=> $i_ccgroup_id
                        ));
                        $selectString = $sql->getSqlStringForSqlObject($group_role);
                        $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    }
                }
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $i_ccgroup_id,
                    'type' => $type
                );
            }
        }
        
        
        public function addUser($data)
        {
            $sql = new Sql($this->dbAdapter);
            if ((! $data['vc_email']) OR empty($data['vc_email'])) {
               return false;
            } else {
                $randomToken = md5 ( uniqid ( mt_rand () * 1000000, true ) );
               
                 $data['activate_token']=$randomToken;
                 $data['dt_created']=date('Y-m-d');
                 $data['dt_modified']=date('Y-m-d');
                 $department = $sql->insert('users')->values($data);
        
                 $selectString = $sql->getSqlStringForSqlObject($department);
                 $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                 $i_user_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
            
                if ($result) {
                    
                    
                    if (! $this->commonservice->checkIfLocal()) {
                        $emailData ['confirmationLink'] = BASE_URL . 'authenticate/activate/user/' . urlencode ( $randomToken );
                        $htmlMarkup = $this->getServiceLocator ()->get ( 'viewrenderer' )->partial ( 'partial/mails/account_activation', $emailData );
                        $this->getCommonModuleService ()->sendEmail ( $htmlMarkup, $data['vc_email'], $data['vc_email'], 'Activate your account' );
                    }
                    
                    return array(
                        'success' => $result->getAffectedRows(),
                        'id' => $i_user_id,
                    );
                  } else {
                      return false;
                  }
               }
        }
        
        public function addUserdetails($data) { 
             $sql = new Sql($this->dbAdapter);
			if(is_array($data)) {
				if(isset($data['business_unit']) AND is_array($data['business_unit'])) {
					foreach($data['business_unit'] as $key=>$business_unit) {
						
							$group_role=$sql->insert('user_details')->values(array(
								'i_ref_company_id' => $data['i_ref_company_id'],
								'i_ref_user_id'=> $data['i_ref_user_id'],
								'i_ref_bu_id'=> $business_unit,
								'i_ref_dep_id'=> $data['department'][$key],
								'i_ref_role_id'=> $data['role'][$key],
							));
							
							$selectString = $sql->getSqlStringForSqlObject($group_role);
							$result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
					}
			}
		}
		
		if($result) {
				return true;
		} else {
				return false;
		}
	}
	
	public function addWbs($data)
	{
	
	    $sql = new Sql($this->dbAdapter);

	    $wbs_department['i_ref_department_id']=$data['i_ref_department_id'];
	    $wbs_department['vc_plan']=$data['vc_plan'];
	    $wbs_department['vc_forecast']=$data['vc_forecast'];
	    $wbs_department['vc_actual']=$data['vc_actual'];
	    $wbs_department['vc_comment']=$data['vc_comment'];
	    
	    if ((! $data['i_rec_id']) OR empty($data['i_rec_id'])) {
	        unset($data['i_rec_id'], $data['i_ref_department_id'], $data['vc_plan'], $data['vc_forecast'], $data['vc_actual'], $data['vc_comment']);
	        $data['dt_created']=date('Y-m-d');
	        $data['dt_modified']=date('Y-m-d');
	        $data['i_status']=1;
	        $department = $sql->insert('wbs')->values($data);
	        $type = "insert";
	
	    } else {
	        $i_rec_id=$data['i_rec_id'];
	        unset($data['i_rec_id'], $data['i_ref_department_id'], $data['vc_plan'], $data['vc_forecast'], $data['vc_actual'], $data['vc_comment']);
	        $data['dt_modified']=date('Y-m-d');
	        $department = $sql->update('wbs')->set($data)->where(array('i_rec_id' => $i_rec_id));
	        $type = "update";
	
	    }
	
	    $selectString = $sql->getSqlStringForSqlObject($department);
	    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	     
	    if ((!isset($i_rec_id)) OR empty($i_rec_id)) {
	           $i_rec_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
	           $wbs_department['i_ref_wbs_id']=$i_rec_id;
	           $department = $sql->insert('wbs_department')->values($wbs_department);
	           $selectString = $sql->getSqlStringForSqlObject($department);
	           $result_child = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	           
	    } else {
	        $department = $sql->update('wbs_department')->set($wbs_department)->where(array('i_ref_wbs_id' => $i_rec_id));
	        $selectString = $sql->getSqlStringForSqlObject($department);
	        $result_child = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	    }
	
	    if ($result) {
	        
	       
	        return array(
	            'success' => $result->getAffectedRows(),
	            'id' => $i_rec_id,
	            'type' => $type
	        );
	    }
	}
	
	public function updateUserprofile($data) {
	    $sql = new Sql($this->dbAdapter);
	    $i_user_id=$data['i_user_id'];
	    unset($data['i_user_id']);
	    $data['dt_modified']=date('Y-m-d');
	    $department = $sql->update('users')->set($data)->where(array('i_user_id' => $i_user_id));
	    $type = "update";
	    
	    $selectString = $sql->getSqlStringForSqlObject($department);
	    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	    
	    if ((!isset($i_user_id)) OR empty($i_user_id)) {
	        $i_rec_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
	    }
	    
	    if ($result) {
	        return array(
	            'success' => $result->getAffectedRows(),
	            'id' => $i_user_id,
	            'type' => $type
	        );
	    }
	}
	
	public function updateCompanyprofile($data) {
	    $sql = new Sql($this->dbAdapter);
	    $i_company_id=$data['i_company_id'];
	    unset($data['i_company_id']);
	    $data['dt_modified']=date('Y-m-d');
	    $department = $sql->update('company')->set($data)->where(array('i_company_id' => $i_company_id));
	    $type = "update";
	     
	    $selectString = $sql->getSqlStringForSqlObject($department);
	    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	     
	    if ((!isset($i_company_id)) OR empty($i_company_id)) {
	        $i_rec_id=$this->dbAdapter->getDriver()->getLastGeneratedValue();
	    }
	     
	    if ($result) {
	        return array(
	            'success' => $result->getAffectedRows(),
	            'id' => $i_company_id,
	            'type' => $type
	        );
	    }
	}
	
	public function deleteWbs($id) {
	    $sql = new Sql($this->dbAdapter);
	    $dataset="";
	    $data['dt_modified']=date('Y-m-d');
	    $data['i_status']=0;
	    $department = $sql->update('wbs')->set($data)->where(array('i_rec_id' => $id));
	    $type = "update";
	    $selectString = $sql->getSqlStringForSqlObject($department);
	    $result = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	     	     
	    if ($result) {
	        $select = $sql->select('wbs')->where(array('i_parent_id' => $id))->limit(100);
	        $stmt = $sql->prepareStatementForSqlObject($select);
	        $result = $stmt->execute();
	        
	        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
	            $dataset = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
	
	            if(is_array($dataset)) {
	                
	                foreach($dataset as $dataset_child) {
	                    
	                    $this->deleteWbs($dataset_child['i_rec_id']);
	                }
	            } 
	        }
	    }
	
	}
	
	public function checkWbsParent($id) {
	    $sql = new Sql($this->dbAdapter);
	       $select = $sql->select('wbs')->columns(array('i_parent_id'))->where(array('i_rec_id' => $id));
	       $stmt = $sql->prepareStatementForSqlObject($select);
	       $result = $stmt->execute();
	       
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $dataset = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            if(empty($dataset['i_parent_id'])) {
                return true;
            }
            return false;
        } else {
            return true;
        }
	}
	public function getWbsProject($id) {
			$sql = new Sql($this->dbAdapter);
	       $select = $sql->select('wbs')->columns(array('i_ref_project_id'))->where(array('i_rec_id' => $id));
	       $stmt = $sql->prepareStatementForSqlObject($select);
	       $result = $stmt->execute();
	       
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $dataset = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            if(!empty($dataset['i_ref_project_id'])) {
                return $dataset['i_ref_project_id'];
            } 
        }
		return 0;
	}
}

?>
