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
    		$emailData['username'] = $params ['vc_fname'];
    		 $emailData['resetLink'] = BASE_URL.'authenticate/resetpassword/'.urlencode($randomToken);
    		$htmlMarkup = $this->getServiceLocator()->get('viewrenderer')->partial('partial/mails/forgot_password', $emailData);
    		
    	   $this->getCommonModuleService()->sendEmail($htmlMarkup, $params ['vc_email'], $params ['vc_fname'],'Reset your password');
    	   $this->adapter = $adapter;
    	   $sql = new Sql($this->adapter);
        	$update = $sql->update ( 'users' )->set ( array (
        	    'vc_password_token' => $randomToken,
        	) )->where ( array ( 'i_user_id' => $params['i_user_id']	) );
        	
    
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
        $select->where(array('vc_email' => $email_id));
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $data = $results->getResource()->fetch(\PDO::FETCH_ASSOC);
        return (!empty($data) && isset($data) ? $data : false);
    }
    
    public function checkToken($password_token, $adapter) {
        
        $this->adapter = $adapter;
        $sql = new Sql($this->adapter);      
        $select = $sql->select()->from('users')->where (array('vc_password_token'=> $password_token ));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $data = $results->getResource()->fetch(\PDO::FETCH_ASSOC);
 
        return (!empty($data) && isset($data) ? true : false);
        
    }
    
    public function resetpassword($params, $adapter) {
    
        $this->adapter = $adapter;
        $sql = new Sql($this->adapter);
        
        $update = $sql->update ( 'users' )->set ( array (
            'vc_password_token' => '',
            'vc_password'=>md5($params['vc_password'])
        ) )->where ( array ( 'vc_password_token' => $params['vc_password_token'],	) );
    
                
        $selectString = $sql->getSqlStringForSqlObject($update);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        return $results;
    
    }
    
}
