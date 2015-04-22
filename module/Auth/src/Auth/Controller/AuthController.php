<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\Result;
use Auth\Form\AuthenticateForm;
use Auth\Form\RegisterForm;
use Auth\Form\ForgotpasswordForm;
use Auth\Form\ResetpasswordForm;
use Zend\Session\Container;

use Zend\Http\PhpEnvironment\Request;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\Rename;
use Zend\Session\SessionManager;
use Zend\Session\Storage\SessionStorage;
use Common\Constants\Constants;
use Auth\Form\ActivateuserForm;

class AuthController extends AbstractActionController
{
    protected $form;
    protected $storage;
    protected $authservice;
	protected $register;    
	protected $adapter;
	protected $commonService;
	protected $forgotpassword;
	protected $sessionManger;
	
	public function __construct()
	{
	   
		$this->httpadapter = new Http();
		$this->sessionManger = new SessionManager ();
	}
	
	
	
	public function getAdapter()
	{
		if (!isset($this->adapter)) {
			$sm = $this->getServiceLocator();
			$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
		}
		return $this->adapter;
	}
	public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()
                                      ->get('AuthService');
        }

        return $this->authservice;
    }
    
    public function getAdminAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()
                                      ->get('AdminAuthService');
        }

        return $this->authservice;
    }
    
	public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()
                                  ->get('Auth\Model\MyAuthStorage');
        }
        return $this->storage;
    }
    
    
	
    public function getRegisterService(){
    	if (! $this->register) {
    		$this->register = $this->getServiceLocator()
    		->get('Auth\Model\Register');
    	}
    	return $this->register;
    }
    
    public function getForgotpasswordService(){
        if (! $this->forgotpassword) {
            $this->forgotpassword = $this->getServiceLocator()
            ->get('Auth\Model\Forgotpassword');
        }
        return $this->forgotpassword;
    }
    
    
	public function loginAction()
    {

    	$this->layout('layout/login');
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('user');
        }

        $form = new AuthenticateForm();
    	$form->get('submit')->setValue('Login');

        return array(
            'form'      => $form,
            'messages'  => $this->flashmessenger()->getMessages(),
        	'error_messages' => $this->flashmessenger()->getErrorMessages(),
        );
    }

    public function authenticateAction()
    {
        $form = new AuthenticateForm();
    	$form->get('submit')->setValue('Login');
        $redirect = 'login';

        $request = $this->getRequest();
       
        if ($request->isPost() && $request->getPost('vc_email') && $request->getPost('vc_password')) {
            $form->setData($request->getPost());
         
            if ($form->isValid()) {
               
               if($request->getPost('type')=='Company') {
				   $checkauth = $this->getAdminAuthService();
				} else {
					$checkauth = $this->getAuthService();
				}
				
                $checkauth->getAdapter()
                                       ->setIdentity($request->getPost('vc_email'))
                                       ->setCredential($request->getPost('vc_password'));
                
                
                $result = $checkauth->authenticate();
              
                $isActivated = $this->getRegisterService()->checkIfActivated($checkauth->getAdapter()->getIdentity(),$request->getPost('type'));
              
                if ($result->isValid() && (isset($isActivated['i_status']) && $isActivated['i_status'])) {
                    
                   $columnsToOmit = array('vc_password');
                   $user = $checkauth->getAdapter()->getResultRowObject(null, $columnsToOmit);

                	$redirect = 'dashboard';

                    if ($request->getPost('rememberme') == 1 ) {
                        $this->getSessionStorage()->setRememberMe(1);
                        $checkauth->setStorage($this->getSessionStorage());
                    }
                   
                    
                    $checkauth->setStorage($this->getSessionStorage());
                    $checkauth->getStorage()->write($request->getPost('vc_email'));

                    $userSession = new Container('user');
                    $userSession->type = $request->getPost('type');
                    $userSession->role = 'user';
                    if($request->getPost('type')=='Company') {
						$user->vc_fname=$user->vc_company_name;
						$user->vc_lname="";
					} else {
						
						$usercompanies=$this->getCommonService()->getUsercompanies($user->i_user_id);
						if($usercompanies) {
						    $userSession->usercompanies=$usercompanies;
						    $user->i_company_id=$usercompanies[0]['i_ref_company_id'];
						    $user->vc_company_name=$usercompanies[0]['vc_company_name'];
						    $user->vc_logo=$usercompanies[0]['vc_logo'];
						} else {
						   $checkauth->clearIdentity();
						   $redirect = 'login';
						   $this->flashmessenger()->addErrorMessage('You are not assigned to any company');
						}
						
						$userpermisions=$this->getCommonService()->getUserpermissions($user->i_user_id, $user->i_company_id);
					   
					   if(!$userpermisions) {
					       $checkauth->clearIdentity();
						   $redirect = 'login';
						   $this->flashmessenger()->addErrorMessage('No permissions assigned to you');				
					   } else {
					       $userSession->userpermissions=$userpermisions;
					   }
					}
					
                    $userSession->data = $user; 
                }
                else{
                    switch ($result->getCode()) {
                        case Result::FAILURE_IDENTITY_NOT_FOUND:
                            $this->flashmessenger()->addErrorMessage('Email Not Found');
                        break;

                        case Result::FAILURE_CREDENTIAL_INVALID:
                            $this->flashmessenger()->addErrorMessage('Invalid Password');
                        break;
                        default:
                        	$redirect = 'login';
                        	$checkauth->clearIdentity();
                        	if(!$isActivated['activate_token']){
                        		$this->flashmessenger()->addErrorMessage('Account Deactivated');
                        	}
                        	else{
                        		$this->flashmessenger()->addErrorMessage('Email varification Pending');
                        	}
                        break;
                    }
                }
            }
        }else{
         $this->flashmessenger()->addErrorMessage('Email and password required');
       }

        return $this->redirect()->toRoute($redirect);
    }

    public function logoutAction()
    {
		
        if ($this->getAuthService()->hasIdentity()) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();
            $this->flashmessenger()->addMessage('Successfully Logged out');
        }
        
        return $this->redirect()->toRoute('login');
    }
    
    public function registerAction()
    {
    	$this->layout('layout/login');
    	$commonService = $this->getServiceLocator()->get('Connekd\Model\Common');
    	
    	$form = new RegisterForm($commonService);
    	$form->get('submit')->setValue('Register');
    	
    	return array(
    			'form'      => $form,
    			'messages'  => $this->flashmessenger()->getErrorMessages()
    	);
    }
    
    public function forgotpasswordAction()
    {
        $this->layout('layout/login');
        $form = new ForgotpasswordForm();
        return array(
            'form'      => $form,
            'messages'  => $this->flashmessenger()->getMessages(),
        	'error_messages' => $this->flashmessenger()->getErrorMessages(),
        );
    }
    
   
    
    public function createUserAction()
    {
        $request  = new Request();
    	$params = $this->getRequest()->getPost()->toArray();
    	$params['REMOTE_ADDR'] = $this->getRequest()->getServer('REMOTE_ADDR');
    	$params['HTTP_USER_AGENT'] = $this->getRequest()->getServer('HTTP_USER_AGENT');
    	$params['profile_pic']='';
    	$emailExists = $this->getRegisterService()->emailExists($params['email_id'],$this->getAdapter());
    	$isValid = true;
    	if($emailExists)
    	{
    		$this->flashmessenger()->addErrorMessage(Constants::EMAIL_ALREADY_EXISTS);
    		$isValid = false;
    	}
    	
    	if($params['password'] != $params['confirm_password']){
    		$this->flashmessenger()->addErrorMessage(Constants::PASSWORDS_DONT_MATCH);
    		$isValid = false;
    	}
    	
    	if($isValid){
    	    $files =  $request->getFiles();
    	
    	    if(!empty($files['profile_pic']['name'])) {
    	        $commonService = $this->getServiceLocator ()->get ( 'Common\Service\CommonServiceInterface' );
    	        
    	        $fileName = $commonService->uploadFiles ( array (
    	            'path' => 'public/uploads/userImages/',
    	            'files' => $files['profile_pic'],
    	            'size' => array (
    	                'min' => 10
    	            ),
    	            'ext' => array (
    	                'extension' => array (
    	                    'jpg',
    	                    'jpeg',
    	                    'png'
    	                )
    	            ),
    	            'thumbnails' => true,
    	            'thumbSizes' => array (
    	                120 => 120,
    	                200 => 200
    	            )
    	        ) );
    	         
    	        $params['profile_pic']= $fileName ['filename'];
    	    }
    	    
    		$results = $this->getRegisterService()->registerUser($params, $this->getAdapter());
    		$this->flashmessenger()->addMessage(Constants::REGISTRATION_SUCCESSFUL);
    		return $this->redirect()->toRoute('login');
    	}
    	return $this->redirect()->toRoute('register');
    }
    
   
    
    public function confirmforgotAction()
    {
        try{
        $params = $this->getRequest()->getPost()->toArray();
        
        $emailExists = $this->getForgotpasswordService()->emailExistsPassword($params['email_id'],$this->getAdapter());

        $isValid = true;
        
        if(!$emailExists)
        {
            $this->flashmessenger()->addErrorMessage('Email doesnt exist');
            $isValid = false;
        }
       
        if($isValid){
            $results = $this->getForgotpasswordService()->sendFogotPasswordLink($emailExists, $this->getAdapter());
            $this->flashmessenger()->addMessage('Check email id to reset password');
        }
        } catch (\Exception $e){
            $this->flashmessenger()->addErrorMessage($e->getMessage());
        }
        return $this->redirect()->toRoute('forgotpassword');
    }
    
    
    public function activatecompanyAction()
    {
    	$activate_token = $this->getEvent()->getRouteMatch()->getParam('activate_token');
    	$results = $this->getRegisterService()->activateUser($activate_token,'company');
		if($results){
    		$this->flashmessenger()->addMessage(Constants::ACCOUNT_ACTIVATED_MESSAGE);
		}
		else{
			$this->flashmessenger()->addErrorMessage(Constants::ACTIVATION_LINK_EXPIRED);
		}
    	return $this->redirect()->toRoute('login');
    }
    
    public function activateuserAction()
    {  
        try {
        if($this->getRequest()->isPost()) {
         
            $requestQuery = $this->params();

            $userData = array(
                'vc_email' => $requestQuery->fromPost('vc_email'),
                'vc_fname' => $requestQuery->fromPost('vc_fname'),
                'vc_lname' => $requestQuery->fromPost('vc_lname'),
                'vc_password' => $requestQuery->fromPost('vc_password'),
                'activate_token'=> $requestQuery->fromPost('activate_token'),
            );
            $return=$this->getRegisterService()->activateCompanyuser($userData);
            if($return){
                $this->flashmessenger()->addMessage(Constants::ACCOUNT_ACTIVATED_MESSAGE);
            }
            else{
                $this->flashmessenger()->addErrorMessage(Constants::ACTIVATION_LINK_EXPIRED);
            }
        } else {
            $activate_token = $this->getEvent()->getRouteMatch()->getParam('activate_token');
            $this->layout('layout/login');
            if(!empty($activate_token)) {
                $data=$this->getCommonService()->getDatasets('users',array('vc_email'),array('activate_token'=>$activate_token));
               if($data) {
                   $form = new ActivateuserForm();
          
                $form->get('submit')->setValue('Activate');
                $form->get('vc_email')->setValue($data[0]['vc_email']);
                $form->get('activate_token')->setValue($activate_token);
                
                return array(
                    'form'      => $form,
                    'messages'  => $this->flashmessenger()->getMessages(),
                    'error_messages' => $this->flashmessenger()->getErrorMessages(),
                );
               } else {
                   $this->flashmessenger()->addErrorMessage(Constants::SOMTHING_MIGHT_WENT_WRONG);
               }
            } else {
                $this->flashmessenger()->addErrorMessage(Constants::ACTIVATION_LINK_EXPIRED);
            }
        }
        } catch (\Exception $e) {
            $this->flashmessenger()->addErrorMessage($e->getMessage());
        }
        return $this->redirect()->toRoute('login');
    }
    
    public function resetpasswordAction()
    {
       
        if($this->getRequest()->getPost('submit')) {
            $isValid=true;
            $params = $this->getRequest()->getPost()->toArray();
                if(empty($params['password_token'])) {
                    $isValid = false;
                }
            
                if($params['password'] != $params['confirm_password']){
                    $this->flashmessenger()->addErrorMessage(Constants::PASSWORDS_DONT_MATCH);
                    $isValid = false;
                }
            if($isValid) {
                    $results = $this->getForgotpasswordService()->resetpassword($params,$this->getAdapter());
                    if($results) {
                         $this->flashmessenger()->addMessage(Constants::PASSWORD_RESET_MESSAGE);
                         return $this->redirect()->toRoute('login');
                    } else { 
                        $this->flashmessenger()->addErrorMessage(Constants::SOMTHING_MIGHT_WENT_WRONG);
                    }
                    $password_token='';
            } else {
               
    
              return  $this->redirect()->toUrl(BASE_URL.'authenticate/resetpassword/'.urldecode($params['password_token']));
            }
        } else {
           
            $password_token = urldecode($this->getEvent()->getRouteMatch()->getParam('password_token'));

            if(empty($password_token)) {
               return $this->redirect()->toRoute('login');
                
            }
            $results = $this->getForgotpasswordService()->checkToken($password_token,$this->getAdapter());

            if($results){
               
                $this->flashmessenger()->addMessage(Constants::ADD_NEW_PASSWORD);
            } else {
                $this->flashmessenger()->addErrorMessage(Constants::ACTIVATION_LINK_EXPIRED);
                return $this->redirect()->toRoute('login');
            }
        }
       $this->layout('layout/login');
       $form = new ResetpasswordForm();
               
        return array(
            'form'      => $form,
            'messages'  => $this->flashmessenger()->getMessages(),
        	'error_messages' => $this->flashmessenger()->getErrorMessages(),
            'password_token' =>$password_token
        );
    }
    
    public function getCommonService(){
        if (!$this->commonService) {
            $this->commonService = $this->getServiceLocator()
            ->get('Common\Service\CommonServiceInterface');
        }
        return $this->commonService;
    }


}
