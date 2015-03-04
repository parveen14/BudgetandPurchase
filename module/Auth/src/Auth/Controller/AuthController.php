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

class AuthController extends AbstractActionController
{
    protected $form;
    protected $storage;
    protected $storage_from_sns;
    protected $authservice;
	protected $register;    
	protected $adapter;
	protected $commonService;
	protected $forgotpassword;
	protected $sessionManger;
	
	public function __construct()
	{
	    
		$this->storage_from_sns = new SessionStorage();
		$this->httpadapter = new Http();
		$this->sessionManger = new SessionManager ();
	}
	
	
	/**
	 *
	 *
	 * @param DataStorageInterface $storage
	 *
	 * @return $this
	 */
	public function setStorage(DataStorageInterface $storage)
	{
		$this->storage_from_sns = $storage;
	
		return $this;
	}
	
	/**
	 *
	 * @return DataStorageInterface
	 */
	public function getStorage()
	{
		return $this->storage_from_sns;
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
       // $plugin = $this->SnsPlugin ();
    	$this->layout('layout/login');
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('dashboard');
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
       
        if ($request->isPost() && $request->getPost('email_id') && $request->getPost('password')) {
            $form->setData($request->getPost());
         
            if ($form->isValid()) {
               
                $this->getAuthService()->getAdapter()
                                       ->setIdentity($request->getPost('email_id'))
                                       ->setCredential($request->getPost('password'));

                $result = $this->getAuthService()->authenticate();

                $isActivated = $this->getRegisterService()->checkIfActivated($this->getAuthService()->getAdapter()->getIdentity());
                if ($result->isValid() && (isset($isActivated['status']) && $isActivated['status'])) {
                    
                    $user = $this->getAuthService()->getAdapter()->getResultRowObject();
                    
                    
                	$redirect = 'dashboard';

                    if ($request->getPost('rememberme') == 1 ) {
                        $this->getSessionStorage()->setRememberMe(1);
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->setStorage($this->getSessionStorage());
                    $this->getAuthService()->getStorage()->write($request->getPost('email_id'));
                    $roles = new Container('roles');
                    $roles->role = 'users';
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
                        	$this->getAuthService()->clearIdentity();
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
        return $this->redirect()->toRoute('forgotpassword');
    }
    
    
    public function activateAction()
    {
    	$activate_token = $this->getEvent()->getRouteMatch()->getParam('activate_token');
    	$results = $this->getRegisterService()->activateUser($activate_token);
		if($results){
    		$this->flashmessenger()->addMessage(Constants::ACCOUNT_ACTIVATED_MESSAGE);
		}
		else{
			$this->flashmessenger()->addErrorMessage(Constants::ACTIVATION_LINK_EXPIRED);
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
            ->get('Connekd\Model\Common');
        }
        return $this->commonService;
    }


}