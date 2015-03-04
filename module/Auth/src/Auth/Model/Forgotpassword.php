<?php

namespace Auth\Model;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Math\Rand;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Escaper\Escaper;
use Common\Constants\Constants;

class Forgotpassword implements ServiceLocatorAwareInterface
{
	protected $adapter;
	protected $messages;
	protected $commonService;
	protected $commonModuleService;
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->services = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->services;
	}
	
	public function getCommonService() {
		if (! $this->commonService) {
			$this->commonService = $this->getServiceLocator ()->get ( 'Connekd\Model\Common' );
		}
		return $this->commonService;
	}
	public function getCommonModuleService() {
		if (! $this->commonModuleService) {
			$this->commonModuleService = $this->getServiceLocator ()->get ( 'Common\Service\CommonServiceInterface' );
		}
	
		return $this->commonModuleService;
	}
	public function getAdapter()
	{
		if (!isset($this->adapter)) {
			$sm = $this->getServiceLocator();
			$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
		}
		return $this->adapter;
	}
	
	public function sendFogotPasswordLink($params, $adapter)
    {
        
        	$escaper = new Escaper();
        	$randomToken = md5(uniqid(mt_rand() * 1000000, true));
    		$emailData['username'] = $params ['username'];
    		 $emailData['resetLink'] = BASE_URL.'authenticate/resetpassword/'.urlencode($randomToken);
    		$htmlMarkup = $this->getServiceLocator()->get('viewrenderer')->partial('partial/mails/forgot_password', $emailData);
    		
    	   $this->getCommonModuleService()->sendEmail($htmlMarkup, $params ['email_id'], $params ['firstname'],'Reset your password');
    	   $this->adapter = $adapter;
    	   $sql = new Sql($this->adapter);
        	$update = $sql->update ( 'users' )->set ( array (
        	    'password_token' => $randomToken,
        	) )->where ( array ( 'id' => $params['id']	) );
        	
    
        	$selectString = $sql->getSqlStringForSqlObject($update);
        	$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        	return true;
    }
    
    public function emailExistsPassword($email_id, $adapter)
    {
        $this->adapter = $adapter;
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('users');
        $select->where(array('email_id' => $email_id));
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $data = $results->getResource()->fetch(\PDO::FETCH_ASSOC);
        return (!empty($data) && isset($data) ? $data : false);
    }
    
    public function checkToken($password_token, $adapter) {
        
        $this->adapter = $adapter;
        $sql = new Sql($this->adapter);      
        $select = $sql->select()->from('users')->where (array('password_token'=> $password_token ));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $data = $results->getResource()->fetch(\PDO::FETCH_ASSOC);
 
        return (!empty($data) && isset($data) ? true : false);
        
    }
    
    public function resetpassword($params, $adapter) {
    
        $this->adapter = $adapter;
        $sql = new Sql($this->adapter);
        
        $update = $sql->update ( 'users' )->set ( array (
            'password_token' => '',
            'password'=>md5($params['password'])
        ) )->where ( array ( 'password_token' => $params['password_token'],	) );
    
                
        $selectString = $sql->getSqlStringForSqlObject($update);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        return $results;
    
    }
    
}