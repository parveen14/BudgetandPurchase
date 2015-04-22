<?php 

namespace User\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use User\Service\UserServiceInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;
use Common\Service\CommonServiceInterface;
use Common\Constants\Constants;
use Zend\Http\PhpEnvironment\Request;


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
        try {
            $userSession = new Container('user');
               
            $this->layout('layout/admin_layout');
            
            $tables=array();
            $tables['table1']='users';
            $tables['table1key']='i_user_id';
            $tables['table2']='user_details';
            $tables['table2key']='i_ref_user_id';
            $tables['table2keyrequired']=array('i_udtl_id','i_ref_company_id','i_ref_bu_id','i_ref_dep_id','i_ref_role_id','i_cu_status'=>'i_status');
            $data=$this->commonService->getDatasetsjoin($tables,'',array('user_details.i_ref_company_id'=>$userSession->data->i_company_id),array('group_by'=>'users.i_user_id'));

        } catch (\Exception $e) {
            $this->flashmessenger()->addErrorMessage($e->getMessage());
        }
       
        $view = new ViewModel(array(
            'userList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/index');
       return $view;
        
    }
    
    public function profileAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
       
        if($userSession->type=='Company') {
            $params=array('i_company_id'=>$userSession->data->i_company_id);
            $details=$this->commonService->getCompanydetails($params);
            $template='user/user/company/profile';
        } else {
             $params=array('i_user_id'=>$userSession->data->i_user_id);
            $details=$this->commonService->getUserdetails($params);
            $template='user/user/profile';
            
        }
        
        $view = new ViewModel(array(
            'details'=>$details,
            'type' => $userSession->type,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
    
        $view->setTemplate($template);
        return $view;
    }
    
    public function editAction() {
        
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');

        
        if($userSession->type=='Company') {
            
            $this->editcompany();
            $params=array('i_company_id'=>$userSession->data->i_company_id);
            $details=$this->commonService->getCompanydetails($params);
            $template='user/user/company/edit';
        } else {
           $this->edituser();
           $params=array('i_user_id'=>$userSession->data->i_user_id);
           $details=$this->commonService->getUserdetails($params);
           $template='user/user/edit';
        }
        
        $countries=$this->commonService->getCountries();
        if($details['i_ref_country_id']!="") {
                $states=$this->commonService->getDatasets('states', array(), array(
                        'country_id' => $details['i_ref_country_id']
                    ));
            
        } else {
            $states="";
        }
        $view = new ViewModel(array(
            'countries'=>$countries,
            'states'=>$states,
            'details'=>$details,
            'type' => $userSession->type,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        
        $view->setTemplate($template);
        return $view;
    }
    
    private function edituser() {
        $userSession = new Container('user');
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
        
            $roleData = array(
                'vc_fname' => $requestQuery->fromPost('vc_fname'),
                'vc_lname' => $requestQuery->fromPost('vc_lname'),
                'vc_phone' => $requestQuery->fromPost('vc_phone'),
                'i_ref_country_id'=> $requestQuery->fromPost('i_ref_country_id'),
                'i_ref_state_id'=> $requestQuery->fromPost('i_ref_state_id'),
                'vc_city'=> $requestQuery->fromPost('vc_city'),
                'vc_zip_code'=> $requestQuery->fromPost('vc_zip_code'),
                'i_user_id'=> $userSession->data->i_user_id,
            );
             
            $request = new Request ();
            $files = $request->getFiles ();
        
            if($files['vc_image']['name']!="") {
                $fileName = $this->commonService->uploadFiles ( array (
                    'path' => 'public/uploads/userImages/',
                    'files' => $files ['vc_image'],
                    'size' => array (
                        'min' => 10,
                    ),
                    'ext' => array (
                        'extension' => array (
                            'jpg','jpeg','png'
                        )
                    ),
                    'deleteImage' => $requestQuery->fromPost('vc_old_image'),
                    // 'thumbnails'=>true,
                    'thumbSizes'=>array('200'=>'200','120'=>'120')
                ) );
                 
                $roleData['vc_image']=$fileName['filename'];
            }
             
            $return=$this->userService->updateUserprofile($roleData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Profile Updated Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while updating profile');
            }
            return $this->redirect()->toUrl('profile');
        }
        
    }
    
    private function editcompany() {
        $userSession = new Container('user');
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
    
            $roleData = array(
                'i_company_id'=> $userSession->data->i_company_id,
                'vc_description' => $requestQuery->fromPost('vc_description'),
                'i_ref_country_id'=> $requestQuery->fromPost('i_ref_country_id'),
                'i_ref_state_id'=> $requestQuery->fromPost('i_ref_state_id'),
                'vc_city'=> $requestQuery->fromPost('vc_city'),
                'vc_zip_code'=> $requestQuery->fromPost('vc_zip_code'),
                'vc_contact_mobile'=> $requestQuery->fromPost('vc_contact_mobile'),
                'vc_contact_skype'=> $requestQuery->fromPost('vc_contact_skype'),
                'vc_contact_landline'=> $requestQuery->fromPost('vc_contact_landline'),
            );
             
            $request = new Request ();
            $files = $request->getFiles ();
    
            if($files['vc_logo']['name']!="") {
                $fileName = $this->commonService->uploadFiles ( array (
                    'path' => 'public/uploads/userImages/',
                    'files' => $files ['vc_logo'],
                    'size' => array (
                        'min' => 10,
                    ),
                    'ext' => array (
                        'extension' => array (
                            'jpg','jpeg','png'
                        )
                    ),
                    'deleteImage' => $requestQuery->fromPost('vc_old_logo'),
                    // 'thumbnails'=>true,
                    'thumbSizes'=>array('200'=>'200','120'=>'120')
                ) );
                 
                $roleData['vc_logo']=$fileName['filename'];
            }
             
            $return=$this->userService->updateCompanyprofile($roleData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Profile Updated Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while updating profile');
            }
            return $this->redirect()->toUrl('profile');
        }
    
    }
    public function adduserAction() {
        try {
            $userSession = new Container('user');
           
            $data=array();
            
            $request = $this->getRequest();
            if($request->isXmlHttpRequest() OR $request->isPost()){
                    $requestQuery = $this->params();
                    $data=$requestQuery->fromPost();
                   
                    $checkExist=$this->commonService->emailExists($data['email']);
                    if(!$checkExist) {
                        $userReturn=$this->userService->addUser(array('vc_email'=>$data['email']));
                        $i_user_id=$userReturn['id'];
                    } else {
                        $i_user_id=$checkExist[0]['i_user_id'];
                    }
                    $data['i_ref_company_id']=$userSession->data->i_company_id;
                    $data['i_ref_user_id']=$i_user_id;
                    
                    $this->userService->addUserdetails($data);
                    if($request->isXmlHttpRequest()) {
                        $result['success'] = TRUE;
                        return new JsonModel($result);
                    } else {
                        $this->flashmessenger()->addMessage("User Added Successfully");
                        return $this->redirect()->toRoute('user');
                    }
            }
           
            $data['businessunit']=$this->commonService->getDatasets('business_units','',array('i_ref_company_id'=>$userSession->data->i_company_id));
            $data['roles']=$this->commonService->getDatasets('roles','',array('i_ref_company_id'=>$userSession->data->i_company_id));
            $this->layout('layout/admin_layout');
            $view = new ViewModel($data);
            $view->setTemplate('user/user/add');
            return $view;
        } catch (\Exception $e) {
            $this->flashmessenger()->addErrorMessage($e->getMessage());
            return $this->redirect()->toRoute('user');
        }
    }
    
    public function departmentAction() {
        
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');

        $data=$this->commonService->getDatasets('departments','',array('i_ref_company_id'=>$userSession->data->i_company_id));

        $view = new ViewModel(array(
            'departmentList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/department/department');
       return $view;
    }
    
    public function departmentdetailsAction() {
    
        $userSession = new Container('user');
        $i_dep_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $this->layout('layout/admin_layout');
        $tables=array();
    
        $departments= $this->commonService->getDatasets('departments','',array('i_ref_company_id'=>$userSession->data->i_company_id,'i_dep_id'=>$i_dep_id))[0];
    
        $tables['table1']='business_departments';
        $tables['table1key']='i_ref_bu_id';
        $tables['table2']='business_units';
        $tables['table2key']='i_bu_id';
    
        $businessunit= $this->commonService->getDatasetsjoin($tables,'',array('i_ref_dep_id'=>$i_dep_id));
             
        $view = new ViewModel(array(
            'businessunit'      => $businessunit,
            'departments'      => $departments,
            'details'      => $userSession,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/department/details');
        return $view;
    }
    
    public function adddepartmentAction() {
        $userSession = new Container('user');

        $i_dep_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $data = array();
        
        $tables=array();
        $tables['table1']='departments';
        $tables['table1key']='i_dep_id';
        $tables['table2']='business_departments';
        $tables['table2key']='i_ref_dep_id';
        $tables['table2requiredColumns']=array('i_ref_bu_id');
     
        if ($i_dep_id) {
            $data = array(
                'i_dep_id' => $i_dep_id,
                'department' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'i_dep_id' => $i_dep_id
                ))[0]
            );
            
        }
        
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
            $departmentData = array(
                'vc_name' => $requestQuery->fromPost('vc_name'),
                'vc_description' => $requestQuery->fromPost('vc_description'),
                'vc_comment' => $requestQuery->fromPost('vc_comment'),
                'i_dep_id' => $requestQuery->fromPost('i_dep_id'),
                'business_units'=> $requestQuery->fromPost('business_units'),
                'i_status' => $requestQuery->fromPost('i_status'),
                'i_ref_company_id'=> $userSession->data->i_company_id,
            );
            $return=$this->userService->addDepartment($departmentData);
           
            if($return['success']) {
                $this->flashmessenger()->addMessage('Department '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding department');
            }
            return $this->redirect()->toRoute('department');
        }
        
        $data['business_units']=$this->commonService->getDatasets('business_units','',array('i_ref_company_id'=>$userSession->data->i_company_id));
        
     
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/department/add');
        return $view;
    }
    
    public function projectAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
    
        $data=$this->commonService->getDatasets('projects','',array('i_ref_company_id'=>$userSession->data->i_company_id));
    
        $view = new ViewModel(array(
            'projectList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/project/project');
        return $view;
    }
    
    public function projectdetailsAction() {
    
        $userSession = new Container('user');
        $i_project_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $this->layout('layout/admin_layout');
        $tables=array();
    
        $tables['table1']='projects';
        $tables['table1key']='i_ref_bu_id';
        $tables['table2']='business_units';
        $tables['table2key']='i_bu_id';
    
        $projects= $this->commonService->getDatasetsjoin($tables,'',array('i_project_id'=>$i_project_id))[0];
         
        $view = new ViewModel(array(
            'projects'      => $projects,
            'details'      => $userSession,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/project/details');
        return $view;
    }
    
    public function addprojectAction() {
        $userSession = new Container('user');
    
        $i_project_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $data = array();
        
        if ($i_project_id) {
            $data = array(
                'i_project_id' => $i_project_id,
                'project' => $this->commonService->getDatasets('projects','',array('i_project_id'=>$i_project_id))[0]
            );
        }
    
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
            $departmentData = array(
                'vc_name' => $requestQuery->fromPost('vc_name'),
                'vc_description' => $requestQuery->fromPost('vc_description'),
                'vc_comment' => $requestQuery->fromPost('vc_comment'),
                'i_project_id' => $requestQuery->fromPost('i_project_id'),
                'i_ref_bu_id' => $requestQuery->fromPost('i_ref_bu_id'),
                'i_status' => $requestQuery->fromPost('i_status'),
                'i_ref_company_id'=> $userSession->data->i_company_id,
            );
            $return=$this->userService->addProject($departmentData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Project '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding project');
            }
            return $this->redirect()->toRoute('project');
        }
    
        $data['business_units']=$this->commonService->getDatasets('business_units','',array('i_ref_company_id'=>$userSession->data->i_company_id));
        
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/project/add');
        return $view;
    }
    
//     public function locationAction() {
    
//         $userSession = new Container('user');
//         $this->layout('layout/admin_layout');
    
//         $data=$this->commonService->getDatasets('location','',array('user_id'=>$userSession->data->id));
    
//         $view = new ViewModel(array(
//             'locationList'      => $data,
//             'messages'  => $this->flashmessenger()->getMessages(),
//             'error_messages' => $this->flashmessenger()->getErrorMessages(),
//         ));
//         $view->setTemplate('user/user/location/location');
//         return $view;
//     }
    
//     public function addlocationAction() {
//         $userSession = new Container('user');
    
//         $location_id = $this->getEvent()
//         ->getRouteMatch()
//         ->getParam('slug');
//         $data = array();
         
//         if ($location_id) {
//             $data = array(
//                 'location_id' => $location_id,
//                 'location' => $this->commonService->getDatasets('location', array(), array(
//                     'id' => $location_id
//                 ))[0]
//             );
    
//         }
    
//         if($this->getRequest()->isPost()) {
//             $requestQuery = $this->params();
//             $departmentData = array(
//                 'title' => $requestQuery->fromPost('title'),
//                 'location_id' => $requestQuery->fromPost('location_id'),
//                 'status' => $requestQuery->fromPost('pStatus'),
//                 'user_id'=> $userSession->data->id,
//             );
//             $return=$this->userService->addLocation($departmentData);
//             if($return['success']) {
//                 $this->flashmessenger()->addMessage('Location '. ucfirst($return['type']) .' Successfully');
//             } else {
//                 $this->flashmessenger()->addErrorMessage('Error while adding location');
//             }
//             return $this->redirect()->toRoute('location');
//         }
    
    
//         $this->layout('layout/admin_layout');
//         $view = new ViewModel($data);
//         $view->setTemplate('user/user/location/add');
//         return $view;
//     }
    
    public function businessunitAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
        $tables=array();
        $tables['table1']='business_units';
        $tables['table1key']='i_bu_id';
        $tables['table2']='business_departments';
        $tables['table2key']='i_ref_bu_id';
        
        $data= $this->commonService->getDatasetsmanyjoin($tables,'',array('i_ref_company_id'=>$userSession->data->i_company_id));
    
        $view = new ViewModel(array(
            'businessunitList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/businessunit/businessunit');
        return $view;
    }
    
    public function bussinessunitdetailsAction() {
    
       $userSession = new Container('user');
       $i_bu_id = $this->getEvent()->getRouteMatch()->getParam('slug');
       $this->layout('layout/admin_layout');
       $tables=array();
       $tables_p=array();

       $businessunit= $this->commonService->getDatasets('business_units','',array('i_ref_company_id'=>$userSession->data->i_company_id,'i_bu_id'=>$i_bu_id))[0];
        
       $tables['table1']='business_departments';
       $tables['table1key']='i_ref_dep_id';
       $tables['table2']='departments';
       $tables['table2key']='i_dep_id';
        
       $departments= $this->commonService->getDatasetsjoin($tables,'',array('i_ref_bu_id'=>$i_bu_id));
       
       
       $projects= $this->commonService->getDatasets('projects','',array('i_ref_bu_id'=>$i_bu_id));
        
       $view = new ViewModel(array(
            'businessunit'      => $businessunit,
            'departments'      => $departments,
            'projects'      => $projects,
            'details'      => $userSession,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
       $view->setTemplate('user/user/businessunit/details');
       return $view;
    }
    
    public function addbusinessunitAction() {
        
        $userSession = new Container('user');
        $i_bu_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $data = array();
        $tables=array();
        $tables['table1']='business_units';
        $tables['table1key']='i_bu_id';
        $tables['table2']='business_departments';
        $tables['table2key']='i_ref_bu_id';
        $tables['table2requiredColumns']=array('i_ref_dep_id');
        if ($i_bu_id) {
            $data = array(
                'i_bu_id' => $i_bu_id,
                'businessunit' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'i_bu_id' => $i_bu_id
                ))[0]
            );
    
        }
        
        $data['departments']= $this->commonService->getDatasets('departments','',array('i_ref_company_id'=>$userSession->data->i_company_id));
        
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
            
            $buData = array(
                'vc_short_name' => $requestQuery->fromPost('vc_short_name'),
                'vc_legal_name' => $requestQuery->fromPost('vc_legal_name'),
                'vc_description' => $requestQuery->fromPost('vc_description'),
                'vc_comments' => $requestQuery->fromPost('vc_comments'),
                'vc_street_address' => $requestQuery->fromPost('vc_street_address'),
                'i_bu_id' => $requestQuery->fromPost('i_bu_id'),
                'i_status' => $requestQuery->fromPost('i_status'),
                'i_ref_company_id'=> $userSession->data->i_company_id,
                
            );
            if($requestQuery->fromPost('departments') AND is_array($requestQuery->fromPost('departments'))) {
                $buData['departments']=$requestQuery->fromPost('departments');
            }
            
            $return=$this->userService->addBusinessunit($buData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Business Unit '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding business unit');
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
        $tables['table1']='roles';
        $tables['table1key']='i_role_id';
        $tables['table2']='roles_permission';
        $tables['table2key']='i_ref_role_id';
    
        $data= $this->commonService->getDatasetsmanyjoin($tables,'',array('i_ref_company_id'=>$userSession->data->i_company_id));
    
        $view = new ViewModel(array(
            'roleList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/role/role');
        return $view;
    }
    
    public function roledetailsAction() {
    
        $userSession = new Container('user');
        $i_role_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $this->layout('layout/admin_layout');
        $tables=array();
        $tables_p=array();
        
        $tables['table1']='roles';
        $tables['table1key']='i_ref_limit_id';
        $tables['table2']='levels';
        $tables['table2key']='i_level_id';
        
        $tables['table2keyrequired']=array('vc_level_name'=>'vc_name','i_start_limit'=>'i_start_limit','i_end_limit'=>'i_end_limit');
        
        $roles= $this->commonService->getDatasetsjoin($tables,'',array('roles.i_ref_company_id'=>$userSession->data->i_company_id,'i_role_id'=>$i_role_id))[0];
    
        $tables_p['table1']='roles_permission';
        $tables_p['table1key']='i_ref_permission_id';
        $tables_p['table2']='permissions';
        $tables_p['table2key']='i_permission_id';
    
        $roles_permission= $this->commonService->getDatasetsjoin($tables_p,'',array('i_ref_role_id'=>$i_role_id));
         
        $view = new ViewModel(array(
            'roles_permission'      => $roles_permission,
            'roles'      => $roles,
            'details'      => $userSession,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/role/details');
        return $view;
    }
    
    public function addroleAction() {
   
        $userSession = new Container('user');
        $i_role_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $data = array();
        $tables=array();
        $parentWhere="i_ref_company_id=".$userSession->data->i_company_id;
        $tables['table1']='roles';
        $tables['table1key']='i_role_id';
        $tables['table2']='roles_permission';
        $tables['table2key']='i_ref_role_id';
        $tables['table2requiredColumns']=array('i_ref_permission_id');
        if ($i_role_id) {
            $data = array(
                'i_role_id' => $i_role_id,
                'role' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'i_role_id' => $i_role_id
                ))[0]
            );
            
            $parentWhere .=" AND i_role_id!=".$i_role_id;
    
        } 
        
        $data['roles']= $this->commonService->getDatasets('roles','',$parentWhere);
        $data['permissions']=$this->commonService->getDatasets('permissions','','');
        $data['levels']=$this->commonService->getDatasets('levels','',array('i_ref_company_id'=>$userSession->data->i_company_id));
         
        if(empty($data['permissions'])) {
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
                'vc_name' => $requestQuery->fromPost('vc_name'),
                'vc_description' => $requestQuery->fromPost('vc_description'),
                'i_ref_limit_id' => $requestQuery->fromPost('i_ref_limit_id'),
                'i_ref_role_id' => $requestQuery->fromPost('i_ref_role_id'),
                'i_status' => $requestQuery->fromPost('i_status'),
                'roles_permission'=>$requestQuery->fromPost('roles_permission'),
                'i_role_id'=> $requestQuery->fromPost('i_role_id'),
                'i_ref_company_id'=> $userSession->data->i_company_id,
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
        $tables['table1']='levels';
        $tables['table1key']='i_level_id';
    
        $data= $this->commonService->getDatasetsmanyjoin($tables,'',array('i_ref_company_id'=>$userSession->data->i_company_id));
    
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
        $i_level_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $data = array();
        $tables=array();
        $tables['table1']='levels';
        $tables['table1key']='i_level_id';

        if ($i_level_id) {
            $data = array(
                'i_level_id' => $i_level_id,
                'level' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'i_level_id' => $i_level_id
                ))[0]
            );
        }
    
         
       
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
    
            $roleData = array(
                'vc_name' => $requestQuery->fromPost('vc_name'),
                'i_start_limit' => $requestQuery->fromPost('i_start_limit'),
                'i_end_limit' => $requestQuery->fromPost('i_end_limit'),
                'i_status' => $requestQuery->fromPost('i_status'),
                'i_level_id'=> $requestQuery->fromPost('i_level_id'),
                'i_ref_company_id'=> $userSession->data->i_company_id,
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
        $tables['table1']='groups';
        $tables['table1key']='i_group_id';
        $tables['table2']='group_role';
        $tables['table2key']='i_ref_group_id';
    
        $data= $this->commonService->getDatasetsmanyjoin($tables,'',array('i_ref_company_id'=>$userSession->data->i_company_id));
    
        $view = new ViewModel(array(
            'groupList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/group/group');
        return $view;
    }
    
    public function groupdetailsAction() {
    
        $userSession = new Container('user');
        $i_group_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $this->layout('layout/admin_layout');
        $tables=array();
        $tables_p=array();
    
        $groups= $this->commonService->getDatasets('groups','',array('i_ref_company_id'=>$userSession->data->i_company_id,'i_group_id'=>$i_group_id))[0];
        
        $tables['table1']='group_permission';
        $tables['table1key']='i_ref_permission_id';
        $tables['table2']='permissions';
        $tables['table2key']='i_permission_id';
        $tables['table2keyrequired']=array('vc_permission_name'=>'vc_name','vc_permission_description'=>'vc_description');
    
        $group_permission= $this->commonService->getDatasetsjoin($tables,'',array('i_ref_group_id'=>$i_group_id));
        
        $tables_p['table1']='group_role';
        $tables_p['table1key']='i_ref_role_id';
        $tables_p['table2']='roles';
        $tables_p['table2key']='i_role_id';
        $tables_p['table2keyrequired']=array('vc_role_name'=>'vc_name','vc_role_description'=>'vc_description','i_role_id'=>'i_role_id');
        
        $group_role= $this->commonService->getDatasetsjoin($tables_p,'',array('i_ref_group_id'=>$i_group_id));
         
        $view = new ViewModel(array(
            'groups'      => $groups,
            'group_permission'      => $group_permission,
            'group_role'      => $group_role,
            'details'      => $userSession,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/group/details');
        return $view;
    }
    
    public function addgroupAction() {
         
        $userSession = new Container('user');
        $i_group_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $data = array();
        $tables=array();
        $tables['table1']='groups';
        $tables['table1key']='i_group_id';
        $tables['table2']='group_role';
        $tables['table2key']='i_ref_group_id';
        $tables['table2requiredColumns']=array('i_ref_role_id');
        $tables['table3']='group_permission';
        $tables['table3key']='i_ref_group_id';
        $tables['table3requiredColumns']=array('i_ref_permission_id');
        if ($i_group_id) {
            $data = array(
                'i_group_id' => $i_group_id,
                'group' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'i_group_id' => $i_group_id
                ))[0]
            );
    
        }
    
        $data['roles']=$this->commonService->getDatasets('roles','',array('i_ref_company_id'=>$userSession->data->i_company_id));
        $data['permissions']=$this->commonService->getDatasets('permissions','','');
        if(empty($data['roles'])) {
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
                'vc_name' => $requestQuery->fromPost('vc_name'),
                'vc_description' => $requestQuery->fromPost('vc_description'),
                'i_status' => $requestQuery->fromPost('i_status'),
                'group_role'=>$requestQuery->fromPost('group_role'),
                'group_permission'=>$requestQuery->fromPost('group_permission'),
                'i_group_id'=> $requestQuery->fromPost('i_group_id'),
                'i_ref_company_id'=> $userSession->data->i_company_id,
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
    
        $data= $this->commonService->getDatasets('cost_center','',array('i_ref_company_id'=>$userSession->data->i_company_id));
        
        $view = new ViewModel(array(
            'costcenterList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/costcenter/costcenter');
        return $view;
    }
    
    public function costcenterdetailsAction() {
    
        $userSession = new Container('user');
        $i_cc_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $this->layout('layout/admin_layout');
        $tables=array();
    
        $cost_center= $this->commonService->getDatasets('cost_center','',array('i_ref_company_id'=>$userSession->data->i_company_id,'i_cc_id'=>$i_cc_id))[0];
    
        $tables['table1']='costcenter_costgroup';
        $tables['table1key']='i_ref_ccgroup_id';
        $tables['table2']='cost_group';
        $tables['table2key']='i_ccgroup_id';
    
        $cc_group= $this->commonService->getDatasetsjoin($tables,'',array('i_ref_cc_id'=>$i_cc_id));
         
        $view = new ViewModel(array(
            'cost_center'      => $cost_center,
            'cc_group'      => $cc_group,
            'details'      => $userSession,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/costcenter/details');
        return $view;
    }
    
    public function addcostcenterAction() {
         
        $userSession = new Container('user');
        $i_cc_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        
        $data = array();
        $tables=array();
        $tables['table1']='cost_center';
        $tables['table1key']='i_cc_id';
        $tables['table2']='costcenter_costgroup';
        $tables['table2key']='i_ref_cc_id';
        $tables['table2requiredColumns']=array('i_ref_ccgroup_id');
        if ($i_cc_id) {
            $data = array(
                'i_cc_id' => $i_cc_id,
                'costcente' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'i_cc_id' => $i_cc_id
                ))[0]
            );
        }

        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
    
            $ccData = array(
                'vc_account_number'=>$requestQuery->fromPost('vc_account_number'),
                'vc_name' => $requestQuery->fromPost('vc_name'),
                'i_cc_id' => $requestQuery->fromPost('i_cc_id'),
                'vc_description' => $requestQuery->fromPost('vc_description'),
                'vc_type' => $requestQuery->fromPost('vc_type'),
                'i_budget'=>$requestQuery->fromPost('i_budget'),
                'i_status'=> $requestQuery->fromPost('i_status'),
                'i_ref_company_id'=> $userSession->data->i_company_id,
            );
            if($requestQuery->fromPost('cost_group') AND is_array($requestQuery->fromPost('cost_group'))) {
                $ccData['cost_group']=$requestQuery->fromPost('cost_group');
            }
            $return=$this->userService->addCostcenter($ccData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Cost Center '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding Cost Center');
            }
            return $this->redirect()->toRoute('costcenter');
        }
    
        $data['cost_group']= $this->commonService->getDatasets('cost_group','',array('i_ref_company_id'=>$userSession->data->i_company_id));
        
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/costcenter/add');
        return $view;
    }
    
    public function costcentergroupAction() {
   
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
    
        $data= $this->commonService->getDatasets('cost_group','',array('i_ref_company_id'=>$userSession->data->i_company_id));
    
        $view = new ViewModel(array(
            'ccgroupList'      => $data,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/costcenter/group');
        return $view;
    }
    
    public function ccgroupdetailsAction() {
    
        $userSession = new Container('user');
        $i_ccgroup_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $this->layout('layout/admin_layout');
        $tables=array();
    
        $cost_group= $this->commonService->getDatasets('cost_group','',array('i_ref_company_id'=>$userSession->data->i_company_id,'i_ccgroup_id'=>$i_ccgroup_id))[0];
    
        $tables['table1']='costcenter_costgroup';
        $tables['table1key']='i_ref_cc_id';
        $tables['table2']='cost_center';
        $tables['table2key']='i_cc_id';
    
        $costcenter_costgroup= $this->commonService->getDatasetsjoin($tables,'',array('i_ref_ccgroup_id'=>$i_ccgroup_id));
         
        $view = new ViewModel(array(
            'cost_group'      => $cost_group,
            'costcenter_costgroup'      => $costcenter_costgroup,
            'details'      => $userSession,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/costcenter/groupdetails');
        return $view;
    }
    
    public function addccgroupAction() {
         
        $userSession = new Container('user');
        $i_ccgroup_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $data = array();
        $tables=array();
        $tables['table1']='cost_group';
        $tables['table1key']='i_ccgroup_id';
        $tables['table2']='costcenter_costgroup';
        $tables['table2key']='i_ref_ccgroup_id';
        $tables['table2requiredColumns']=array('i_ref_cc_id');
        
        if ($i_ccgroup_id) {
            $data = array(
                'i_ccgroup_id' => $i_ccgroup_id,
                'costcentegroup' => $this->commonService->getDatasetsmanyjoin($tables, array(), array(
                    'i_ccgroup_id' => $i_ccgroup_id
                ))[0]
            );
        }
       
        if($this->getRequest()->isPost()) {
            $requestQuery = $this->params();
    
            $roleData = array(
                'vc_account_group' => $requestQuery->fromPost('vc_account_group'),
                'vc_description' => $requestQuery->fromPost('vc_description'),
                'i_ccgroup_id' => $requestQuery->fromPost('i_ccgroup_id'),
                'costcenter_costgroup' => $requestQuery->fromPost('costcenter_costgroup'),
                'i_status'=> $requestQuery->fromPost('i_status'),
                'i_ref_company_id'=> $userSession->data->i_company_id,
            );
            $return=$this->userService->addCostcentergroup($roleData);
            if($return['success']) {
                $this->flashmessenger()->addMessage('Cost Center Group '. ucfirst($return['type']) .' Successfully');
            } else {
                $this->flashmessenger()->addErrorMessage('Error while adding Cost Center Group');
            }
            return $this->redirect()->toRoute('ccgroup');
        }
    
        $data['cost_center']=$this->commonService->getDatasets('cost_center', array(), array('i_ref_company_id' =>$userSession->data->i_company_id));
        
        $this->layout('layout/admin_layout');
        $view = new ViewModel($data);
        $view->setTemplate('user/user/costcenter/addgroup');
        return $view;
    }
    
    
    public function wbsAction() {
    
        $userSession = new Container('user');
        $this->layout('layout/admin_layout');
        $projects=$this->commonService->getDatasets('projects', array(), array('i_ref_company_id' =>$userSession->data->i_company_id));
        $view = new ViewModel(array(
            'projects'  => $projects,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        
        $view->setTemplate('user/user/wbs/wbs');
        return $view;
    }
    
    public function getwbsAction() {
    
        $userSession = new Container('user');
	
        $request = $this->getRequest();
        if($request->isXmlHttpRequest()){
            //$wbs=$this->commonService->getDatasets('wbs', array(), array('i_ref_company_id' =>$userSession->data->i_company_id));
            $requestQuery = $this->params();
			
			$i_ref_project_id=$this->getEvent()->getRouteMatch()->getParam('slug'); 
            $wbs=$this->commonService->getWbs($userSession->data->i_company_id,$i_ref_project_id);
            
            $wbs = array_map(function($wbs){ 
                
                $wbs['Id']=$wbs['i_rec_id'];
                $wbs['Name']=$wbs['vc_name'];
                $wbs['ParentID']=$wbs['i_parent_id'];
                $wbs['Units']=$wbs['vc_unit'];
                
                return $wbs; 
            
            }, $wbs);
              
        }
            $view = new JsonModel($wbs);
            $view->setTerminal(true);
            return $view;
    }
    
    public function addwbsAction() {
			try {
			
			$userSession = new Container('user');

			$request = $this->getRequest();
		
            $request = $this->getRequest();
            if($request->isXmlHttpRequest()){
			
                    $requestQuery = $this->params();
                    $data=$requestQuery->fromPost();
					if($requestQuery->fromPost('rowID') && $this->userService->checkWbsParent($requestQuery->fromPost('rowID'))) {
						  throw new \Exception("You cant edit project details here");
					}

				   $roleData = array(
                       'i_rec_id' => $requestQuery->fromPost('rowID'),
                       'vc_name' => $requestQuery->fromPost('Name'),
                       'vc_unit' => $requestQuery->fromPost('Units'),
				       'vc_plan' => $requestQuery->fromPost('vc_plan'),
				       'vc_forecast' => $requestQuery->fromPost('vc_forecast'),
				       'vc_actual' => $requestQuery->fromPost('vc_actual'),
				       'vc_comment' => $requestQuery->fromPost('vc_comment'),
                       'i_ref_department_id'=> $userSession->data->i_company_id,
				       'i_ref_company_id'=> $userSession->data->i_company_id
                   );
				
					if(!$requestQuery->fromPost('rowID')) {
					  if(!$requestQuery->fromPost('i_type')) {
						  $roleData['i_type']=1;
					   } else {
						  $roleData['i_type']=$requestQuery->fromPost('i_type')+1;
					   }
					}
                   

                   if($requestQuery->fromPost('parentID')!="") {
                       $roleData['i_parent_id']=$requestQuery->fromPost('parentID');
					
					   $wbsProject=$this->userService->getWbsProject($requestQuery->fromPost('parentID'));
					   $roleData['i_ref_project_id']=$wbsProject;
                   } 
                   $return=$this->userService->addWbs($roleData);
				   if(!$requestQuery->fromPost('rowID')) {
						$return['i_type']=$roleData['i_type'];
					}
            }
           } catch (\Exception $e) {
				$return['exception_message']=$e->getMessage();
		   }
            $view = new JsonModel($return);
            $view->setTerminal(true);
            return $view;
    }
    
	public function deletewbsAction() {
        try {
			
			$userSession = new Container('user');

			$request = $this->getRequest();
		
            $request = $this->getRequest();
            if($request->isXmlHttpRequest()){
			
                    $requestQuery = $this->params();
                    $data=$requestQuery->fromPost();
					if($this->userService->checkWbsParent($requestQuery->fromPost('rowID'))) {
						  throw new \Exception("You cant delete project from here");
					}
					
                   
                   $return=$this->userService->deleteWbs($requestQuery->fromPost('rowID'));
                   $return['success']=true;
				   
            }
           } catch (\Exception $e) {
				$return['exception_message']=$e->getMessage();
		   }
            $view = new JsonModel($return);
            $view->setTerminal(true);
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
    	            $where=array($data['field']=>$data['id']);
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
    
    public function companyuserstatusAction() {
        try {
            $request = $this->getRequest();
            if($request->isXmlHttpRequest()){
                $userSession = new Container('user');
                $data = $request->getPost();
                if(isset($data['type']) && !empty($data['type'])){
                    $status= (($data['status']=='1')?'0':'1');
                    $where=array($data['field']=>$data['id'],$data['field1']=>$data['id1']);
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

    public function changecompanyAction() {
        try {
            $userSession = new Container('user');
            $request = $this->getRequest();
            if($request->isXmlHttpRequest()){
                $userSession = new Container('user');
                $data = $request->getPost();
                if(isset($data['id']) && !empty($data['id'])){
                    $userSession->data->i_company_id=$data['id'];
                    $userSession->data->vc_company_name=$data['name'];
                    $result=array('success'=> TRUE);
                }
            }
        } catch (\Exception $e) {
            $result = array(
                'exception_message' => $e->getMessage()
            );
        }
        return new JsonModel($result);
    }
    
    public function selectdataAction() {
    
        try {
            $request = $this->getRequest();
            if($request->isXmlHttpRequest()){
                $data = $request->getPost();
                if(isset($data['id']) && !empty($data['id'])){
                    
                    $tables=array();
                    $tables['table1']='departments';
                    $tables['table1key']='i_dep_id';
                    $tables['table2']='business_departments';
                    $tables['table2key']='i_ref_dep_id';
                    
                        $departments=$this->commonService->getDatasetsjoin($tables, array(), array(
                                'business_departments.i_ref_bu_id' => $data['id']
                            ));
                        
                   if(!is_array($departments)) $departments="";
                    $view = new JsonModel(array('results' => $departments));
                    $view->setTerminal(true);
                } else {
                  throw new \RuntimeException(Constants::SOMTHING_MIGHT_WENT_WRONG);
                }
            } else {
                return $this->redirect()->toRoute('user');
            }

        } catch (\Exception $e) {
            $view = new JsonModel(array('exception_message' => $e->getMessage()));
        }
        return $view;
    }
    
    public function selectstatesAction() {
    
        try {
            $request = $this->getRequest();
            if($request->isXmlHttpRequest()){
                $data = $request->getPost();
                if(isset($data['id']) && !empty($data['id'])){
                    
                    $states=$this->commonService->getDatasets('states', array(), array(
                        'country_id' => $data['id']
                    ));
    
                    if(!is_array($states)) $states="";
                    $view = new JsonModel(array('results' => $states));
                    $view->setTerminal(true);
                } else {
                    throw new \RuntimeException(Constants::SOMTHING_MIGHT_WENT_WRONG);
                }
            } else {
                return $this->redirect()->toRoute('user');
            }
    
        } catch (\Exception $e) {
            $view = new JsonModel(array('exception_message' => $e->getMessage()));
        }
        return $view;
    }
    
    public function addbusinessrowAction() {
        try {
            $userSession = new Container('user');
            $request = $this->getRequest();
            if($request->isXmlHttpRequest()){
                    $data = $request->getPost();
                    $businessunit=$this->commonService->getDatasets('business_units','',array('i_ref_company_id'=>$userSession->data->i_company_id));
                    $roles=$this->commonService->getDatasets('roles','',array('i_ref_company_id'=>$userSession->data->i_company_id));
                    
                    $view = new ViewModel(array('businessunit' => $businessunit,'roles' => $roles));
                    $view->setTemplate('user/user/addrow');
                    $view->setTerminal(true);
            } else {
                return $this->redirect()->toRoute('user');
            }
        
        } catch (\Exception $e) {
            $view = new JsonModel(array('exception_message' => $e->getMessage()));
        }
        return $view;
    }
    
	public function purchaserequestAction() {
    
        $userSession = new Container('user');
        $purchaserequest=$this->commonService->getpurchaserequest($userSession->data->i_company_id);

        $this->layout('layout/admin_layout');
        
        $view = new ViewModel(array(
			'purchaserequest'=>$purchaserequest,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        
        $view->setTemplate('user/user/purchaserequest/purchaserequest');
        return $view;
    }

	
	public function purchaserequestdetailsAction() {
    
        $userSession = new Container('user');
        $i_purchase_id = $this->getEvent()->getRouteMatch()->getParam('slug');
        $this->layout('layout/admin_layout');
    
        $purchaserequest=$this->commonService->getpurchaserequest($userSession->data->i_company_id,$i_purchase_id);
		   if(!$purchaserequest) {
				return $this->redirect()->toRoute('user');
		   }
        $view = new ViewModel(array(
            'purchaserequest'      => $purchaserequest[0],
            'details'      => $userSession,
            'messages'  => $this->flashmessenger()->getMessages(),
            'error_messages' => $this->flashmessenger()->getErrorMessages(),
        ));
        $view->setTemplate('user/user/purchaserequest/details');
        return $view;
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
