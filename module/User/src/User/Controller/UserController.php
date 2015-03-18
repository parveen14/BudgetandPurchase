<?php 

namespace User\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use User\Service\UserServiceInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;
use Common\Service\CommonServiceInterface;
use Common\Constants\Constants;


class UserController extends AbstractActionController 
{
    
    protected $userService;
    protected $authservice;
    protected $commonService;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        if (! $this->getAuthService ()->hasIdentity ()) {
            return $this->redirect ()->toRoute ( 'login' );
        }
    
        return parent::onDispatch ( $e );
    }
    
    public function __construct(UserServiceInterface $userService,CommonServiceInterface $commonService){
        
        $this->userService=$userService;
        $this->commonService = $commonService;
        
    }
    
    public function getAuthService() {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator ()->get ( 'AuthService' );
        }
    
        return $this->authservice;
    }
    
    public function indexAction()
    {  
        //echo APPLICATION_ROOT; die;
        $totalRole = $this->commonService->getTotalRecordCount('role');
        $totalGroup = $this->commonService->getTotalRecordCount('group');
        $totalBusinessunit = $this->commonService->getTotalRecordCount('businessunit');
        $totalDepartment = $this->commonService->getTotalRecordCount('department');
        
        $this->layout('layout/admin_layout');
        return new ViewModel(array(
            'totalRole' => $totalRole,
            'totalGroup' => $totalGroup,
            'totalBusinessunit' => $totalBusinessunit,
            'totalDepartment' => $totalDepartment
        ));
        
    }
    
    public function departmentAction() {
        
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');

        $data=$this->commonService->getDatasets('department','',array('user_id'=>$userSession->data->id));

        $view = new ViewModel(array(
            'departmentList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/department/department');
       return $view;
    }
    
    public function adddepartmentAction() {
        $userSession = new Container('user');

        $department_id = $this->getEvent()
        ->getRouteMatch()
        ->getParam('slug');
        $data = array();
     
        if ($department_id) {
            $data = array(
                'department_id' => $department_id,
                'department' => $this->commonService->getDatasets('department', array(), array(
                    'id' => $department_id
                ))[0]
            );
            
        }
        
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
            $departmentData = array(
                'title' => $requestQuery->fromPost('title'),
                'description' => $requestQuery->fromPost('description'),
                'department_id' => $requestQuery->fromPost('department_id'),
                'status' => $requestQuery->fromPost('pStatus'),
                'user_id'=> $userSession->data->id,
            );
            $return=$this->userService->addDepartment($departmentData);
           
            if($return['success']) {
                $this->flashmessenger()->addMessage('Department '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding department');
            }
            return $this->redirect()->toRoute('department');
        }
        
        
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/department/add');
        return $view;
    }
    
    public function projectAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
    
        $data=$this->commonService->getDatasets('project','',array('user_id'=>$userSession->data->id));
    
        $view = new ViewModel(array(
            'projectList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/project/project');
        return $view;
    }
    
    public function addprojectAction() {
        $userSession = new Container('user');
    
        $project_id = $this->getEvent()
        ->getRouteMatch()
        ->getParam('slug');
        $data = array();
        
        
        if ($project_id) {
            $tables['table1']='project';
            $tables['table1key']='id';
            $tables['table2']='business_project';
            $tables['table2key']='project_id';
            
            $data = array(
                'project_id' => $project_id,
                'project' => $this->commonService->getDatasetsmanyjoin($tables,'',array('id'=>$project_id))[0]
            );
    
        }
    
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
            $departmentData = array(
                'title' => $requestQuery->fromPost('title'),
                'description' => $requestQuery->fromPost('description'),
                'project_id' => $requestQuery->fromPost('project_id'),
                'business_unit' => $requestQuery->fromPost('business_unit'),
                'status' => $requestQuery->fromPost('pStatus'),
                'user_id'=> $userSession->data->id,
            );
            $return=$this->userService->addProject($departmentData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Project '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding project');
            }
            return $this->redirect()->toRoute('project');
        }
    
        $data['businessunit']=$this->commonService->getDatasets('businessunit','',array('user_id'=>$userSession->data->id));
        
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/project/add');
        return $view;
    }
    
    public function locationAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
    
        $data=$this->commonService->getDatasets('location','',array('user_id'=>$userSession->data->id));
    
        $view = new ViewModel(array(
            'locationList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/location/location');
        return $view;
    }
    
    public function addlocationAction() {
        $userSession = new Container('user');
    
        $location_id = $this->getEvent()
        ->getRouteMatch()
        ->getParam('slug');
        $data = array();
         
        if ($location_id) {
            $data = array(
                'location_id' => $location_id,
                'location' => $this->commonService->getDatasets('location', array(), array(
                    'id' => $location_id
                ))[0]
            );
    
        }
    
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
            $departmentData = array(
                'title' => $requestQuery->fromPost('title'),
                'location_id' => $requestQuery->fromPost('location_id'),
                'status' => $requestQuery->fromPost('pStatus'),
                'user_id'=> $userSession->data->id,
            );
            $return=$this->userService->addLocation($departmentData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Location '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding location');
            }
            return $this->redirect()->toRoute('location');
        }
    
    
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/location/add');
        return $view;
    }
    
    public function businessunitAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
        $tables=array();
        $tables['table1']='businessunit';
        $tables['table1key']='id';
        $tables['table2']='business_department';
        $tables['table2key']='businessunit_id';
        
        $data= $this->commonService->getDatasetsmanyjoin($tables,'',array('user_id'=>$userSession->data->id));
    
        $view = new ViewModel(array(
            'businessunitList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/businessunit/businessunit');
        return $view;
    }
    
    public function addbusinessunitAction() {
        
        $userSession = new Container('user');
        $businessunit_id = $this->getEvent()
        ->getRouteMatch()
        ->getParam('slug');
        $data = array();
        $tables=array();
        $tables['table1']='businessunit';
        $tables['table1key']='id';
        $tables['table2']='business_department';
        $tables['table2key']='businessunit_id';
        $tables['table2requiredColumns']=array('department_id');
        $tables['table3']='business_project';
        $tables['table3key']='businessunit_id';
        $tables['table3requiredColumns']=array('project_id');
        $tables['table4']='business_location';
        $tables['table4key']='businessunit_id';
        $tables['table4requiredColumns']=array('location_id');
        if ($businessunit_id) {
            $data = array(
                'businessunit_id' => $businessunit_id,
                'businessunit' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'id' => $businessunit_id
                ))[0]
            );
    
        }
        
        $data['department']=$this->commonService->getDatasets('department','',array('user_id'=>$userSession->data->id,'status'=>'1'));
      //  $data['project']=$this->commonService->getDatasets('project','',array('user_id'=>$userSession->data->id,'status'=>'1'));
        $data['location']=$this->commonService->getDatasets('location','',array('user_id'=>$userSession->data->id,'status'=>'1'));
         
        if(empty($data['department'])) {
               $error=array();
               $error['errormessage']='Please add/activate Departments & Projects before adding / editing Business Unit';
               $error['okbutton']='Add Department';
               $error['okbuttonlink']=$this->getRequest()->getBaseUrl().'/user/department/adddepartment';
               $error['cancelbutton']='Add Project';
               $error['cancelbuttonlink']=$this->getRequest()->getBaseUrl().'/user/project/addproject';
            return $this->showerror($error);
        }
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
            $departmentData = array(
                'title' => $requestQuery->fromPost('title'),
                'description' => $requestQuery->fromPost('description'),
                'businessunit_id' => $requestQuery->fromPost('businessunit_id'),
                'status' => $requestQuery->fromPost('pStatus'),
                'department'=>$requestQuery->fromPost('department'),
              //  'project'=>$requestQuery->fromPost('project'),
                'location'=>$requestQuery->fromPost('location'),
                'user_id'=> $userSession->data->id,
            );
            $return=$this->userService->addBusinessunit($departmentData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Department '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding department');
            }
            return $this->redirect()->toRoute('businessunit');
        }
    
    
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/businessunit/add');
        return $view;
    }
    
    
    public function roleAction() {
        
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
        $tables=array();
        $tables['table1']='role';
        $tables['table1key']='id';
        $tables['table2']='role_permission';
        $tables['table2key']='role_id';
    
        $data= $this->commonService->getDatasetsmanyjoin($tables,'',array('user_id'=>$userSession->data->id));
    
        $view = new ViewModel(array(
            'roleList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/role/role');
        return $view;
    }
    
    public function addroleAction() {
   
        $userSession = new Container('user');
        $role_id = $this->getEvent()
        ->getRouteMatch()
        ->getParam('slug');
        $data = array();
        $tables=array();
        $parentWhere="user_id=".$userSession->data->id;
        $tables['table1']='role';
        $tables['table1key']='id';
        $tables['table2']='role_permission';
        $tables['table2key']='role_id';
        $tables['table2requiredColumns']=array('permission_id');
        if ($role_id) {
            $data = array(
                'role_id' => $role_id,
                'role' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'id' => $role_id
                ))[0]
            );
            
            $parentWhere .=" AND id!=".$role_id;
    
        } 
        
        $data['parentroles']= $this->commonService->getDatasets('role','',$parentWhere);
        $data['permission']=$this->commonService->getDatasets('permission','','');
        $data['levels']=$this->commonService->getDatasets('level','','');
         
        if(empty($data['permission'])) {
            $error=array();
            $error['errormessage']='Error, please try again later';
            $error['okbutton']='Ok';
            $error['okbuttonlink']=$this->getRequest()->getBaseUrl().'/user/';
            $error['cancelbutton']='Cancel';
            $error['cancelbuttonlink']=$this->getRequest()->getBaseUrl().'/user/';
            return $this->showerror($error);
        }
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
    
            $roleData = array(
                'title' => $requestQuery->fromPost('title'),
                'level_id' => $requestQuery->fromPost('level_id'),
                'parent_role' => $requestQuery->fromPost('parent_role'),
                'status' => $requestQuery->fromPost('pStatus'),
                'permission'=>$requestQuery->fromPost('permission'),
                'role_id'=> $requestQuery->fromPost('role_id'),
                'user_id'=> $userSession->data->id,
            );
            $return=$this->userService->addRole($roleData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Role '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding role');
            }
            return $this->redirect()->toRoute('role');
        }
    
    
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/role/add');
        return $view;
    }
    
    public function levelAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
        $tables=array();
        $tables['table1']='level';
        $tables['table1key']='id';
    
        $data= $this->commonService->getDatasetsmanyjoin($tables,'',array('user_id'=>$userSession->data->id));
    
        $view = new ViewModel(array(
            'levelList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/level/level');
        return $view;
    }
    
    
    public function addlevelAction() {
        
        $userSession = new Container('user');
        $level_id = $this->getEvent()
        ->getRouteMatch()
        ->getParam('slug');
        $data = array();
        $tables=array();
        $parentWhere="user_id=".$userSession->data->id;
        $tables['table1']='level';
        $tables['table1key']='id';

        if ($level_id) {
            $data = array(
                'level_id' => $level_id,
                'level' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'id' => $level_id
                ))[0]
            );
        }
    
         
       
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
    
            $roleData = array(
                'title' => $requestQuery->fromPost('title'),
                'budget_min' => $requestQuery->fromPost('budget_min'),
                'budget_max' => $requestQuery->fromPost('budget_max'),
                'status' => $requestQuery->fromPost('pStatus'),
                'level_id'=> $requestQuery->fromPost('level_id'),
                'user_id'=> $userSession->data->id,
            );
            $return=$this->userService->addLevel($roleData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Level '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding role');
            }
            return $this->redirect()->toRoute('level');
        }
    
    
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/level/add');
        return $view;
    }
    
    public function groupAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
        $tables=array();
        $tables['table1']='group';
        $tables['table1key']='id';
        $tables['table2']='group_role';
        $tables['table2key']='group_id';
    
        $data= $this->commonService->getDatasetsmanyjoin($tables,'',array('user_id'=>$userSession->data->id));
    
        $view = new ViewModel(array(
            'groupList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/group/group');
        return $view;
    }
    
    public function addgroupAction() {
         
        $userSession = new Container('user');
        $group_id = $this->getEvent()
        ->getRouteMatch()
        ->getParam('slug');
        $data = array();
        $tables=array();
        $tables['table1']='group';
        $tables['table1key']='id';
        $tables['table2']='group_role';
        $tables['table2key']='group_id';
        $tables['table2requiredColumns']=array('role_id');
        if ($group_id) {
            $data = array(
                'group_id' => $group_id,
                'group' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'id' => $group_id
                ))[0]
            );
    
        }
    
        $data['role']=$this->commonService->getDatasets('role','',array('user_id'=>$userSession->data->id,'status'=>1));
         
        if(empty($data['role'])) {
            $error=array();
            $error['errormessage']='Please add Roles before adding / editing Group.';
            $error['okbutton']='Add Role';
            $error['okbuttonlink']=$this->getRequest()->getBaseUrl().'/user/role/addrole';
            $error['cancelbutton']='Cancel';
            $error['cancelbuttonlink']=$this->getRequest()->getBaseUrl().'/user/role';
            return $this->showerror($error);
        }
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
    
            $groupData = array(
                'title' => $requestQuery->fromPost('title'),
                'description' => $requestQuery->fromPost('description'),
                'status' => $requestQuery->fromPost('pStatus'),
                'role'=>$requestQuery->fromPost('role'),
                'group_id'=> $requestQuery->fromPost('group_id'),
                'user_id'=> $userSession->data->id,
            );
            $return=$this->userService->addGroup($groupData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Group '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding group');
            }
            return $this->redirect()->toRoute('group');
        }
    
    
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/group/add');
        return $view;
    }
    
    public function costcenterAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
    
        $data= $this->commonService->getDatasets('cost_center','',array('user_id'=>$userSession->data->id));
    
        $view = new ViewModel(array(
            'costcenterList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/costcenter/costcenter');
        return $view;
    }
    
    public function addcostcenterAction() {
         
        $userSession = new Container('user');
        $costcenter_id = $this->getEvent()
        ->getRouteMatch()
        ->getParam('slug');
        $data = array();
        $parentWhere="user_id=".$userSession->data->id;
        if ($costcenter_id) {
            $data = array(
                'costcenter_id' => $costcenter_id,
                'costcente' => $this->commonService->getDatasets('cost_center', array(), array(
                    'id' => $costcenter_id
                ))[0]
            );
            $parentWhere .=" AND id!=".$costcenter_id;
        }
    
        $data['parentcostcenter']= $this->commonService->getDatasets('cost_center','',$parentWhere);
       
         
       
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
    
            $roleData = array(
                'user_id'=> $userSession->data->id,
                'code'=>$requestQuery->fromPost('code'),
                'title' => $requestQuery->fromPost('title'),
                'costcenter_id' => $requestQuery->fromPost('costcenter_id'),
                'parent_id' => $requestQuery->fromPost('parent_id'),
                'description' => $requestQuery->fromPost('description'),
                'budget'=>$requestQuery->fromPost('budget'),
                'status'=> $requestQuery->fromPost('status'), 
            );
            $return=$this->userService->addCostcenter($roleData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Cost Center '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding Cost Center');
            }
            return $this->redirect()->toRoute('costcenter');
        }
    
    
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/costcenter/add');
        return $view;
    }
    
    public function changestatusAction() {
        try {
            $request = $this->getRequest();
            if($request->isXmlHttpRequest()){
                $userSession = new Container('user');
    	        $data = $request->getPost();
    	        if(isset($data['type']) && !empty($data['type'])){
    	            $status= (($data['status']=='1')?'0':'1');
    	            $where=array('id'=>$data['id'],'user_id'=>$userSession->data->id);
                	   if ($this->commonService->changeStatusTo($data['type'], $status, $where)) {
                            $result['success'] = TRUE;
                            $result['status'] = $status;
                        } else {
                            new \Exception('Something might went wrong');
                        }
    	        }
    	    }
        } catch (\Exception $e) {
            $result = array(
                'exception_message' => $e->getMessage()
            );
        }
	    return new JsonModel($result);
    }

    private function showerror($errorMessage=array()) {
        if(!isset($errorMessage['errormessage'])) {
            $errorMessage['errormessage']=Constants::SOMTHING_MIGHT_WENT_WRONG;
        }
        if(!isset($errorMessage['okbutton'])) {
            $errorMessage['okbutton']='OK';
        }
        if(!isset($errorMessage['okbuttonlink'])) {
            $errorMessage['okbuttonlink']= $this->basePath('user');
        }
        if(!isset($errorMessage['cancelbutton'])) {
            $errorMessage['okbutton']='Cancel';
        }
        if(!isset($errorMessage['cancelbuttonlink'])) {
            $errorMessage['okbuttonlink']= $this->basePath('user');
        }
        $this->layout('layout/admin_layout');

        $view = new ViewModel($errorMessage);
        $view->setTemplate('error/error');
        return $view;
    }
}

?>