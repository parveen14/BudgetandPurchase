<?php

namespace APIs\V1\Rest\User;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;
use ZF\ApiProblem\ApiProblem;
use Zend\Http\PhpEnvironment\Request;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\Rename;
use Common\Service\CommonServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Common\Constants\Constants;

class UserMapper implements ServiceLocatorAwareInterface {
	protected $adapter;
	protected $commonService;
	public function __construct(AdapterInterface $adapter, CommonServiceInterface $commonService) {
		$this->adapter = $adapter;
		$this->httpadapter = new Http ();
		$this->commonService = $commonService;
	}
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->services = $serviceLocator;
	}
	public function getServiceLocator() {
		return $this->services;
	}
	public function fetchAll($filter) {
		
		$sql = new Sql($this->adapter);
        $select = $sql->select('users');
        $select->order('id desc');
        
        $selectString = $sql->getSqlStringForSqlObject ( $select );
        $result = $this->adapter->query ( $selectString, Adapter::QUERY_MODE_EXECUTE);
		$resultSet = new ResultSet();
		$resultSet->initialize ( $result );
		$Users = $resultSet->toArray ();
		return (! empty ( $Users ) && isset ( $Users ) ? $Users : false);
       
	}
	
    public function authenticateUser($data) {
        try {
           if(isset($data->email_id) AND isset($data->password)) {
            $sql = new Sql($this->adapter);
    		$select = $sql->select(array('u' => 'users'))
    		->where(array('email_id' => $data->email_id))
    		->where(array('password' => md5($data->password))); 
    		$statement = $sql->prepareStatementForSqlObject($select);
    		$resultset = $statement->execute()->getResource()->fetch(\PDO::FETCH_ASSOC);
    		
    		if (!$resultset) {
                return new ApiProblem(422,  Constants::INVALID_CREDENTIALS,'Server side validation error',Constants::INVALID_CREDENTIALS);
            }
            else if(!$resultset['status'] && $resultset['activate_token']){
            	return new ApiProblem(422,Constants::EMAIL_VERIFICATION_PENDING,'Server side validation error',Constants::EMAIL_VERIFICATION_PENDING);
            }
            else if(!$resultset['status']){
            	return new ApiProblem(422,Constants::ACCOUNT_HAS_BEEN_DEACTIVATED,'Server side validation error',Constants::ACCOUNT_HAS_BEEN_DEACTIVATED);
            }
            
            $data->user_id = $resultset['id'];
            $data->id = $resultset['id'];
            $data->username = $resultset['username'];
            $data->email_id = $resultset['email_id'];
   
            unset($resultset['password']);
            
            return $resultset;
           } else {
               return new ApiProblem(422,  'Email or Password missing','Server side validation error','Email or Password missing');
           }
        } catch (\Exception $e) {
            return new ApiProblem(422,$e,'Server side validation error',$e);
        }
    }
}
