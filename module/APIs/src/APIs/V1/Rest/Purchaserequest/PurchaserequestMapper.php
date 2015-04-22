<?php

namespace APIs\V1\Rest\Purchaserequest;

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

class PurchaserequestMapper implements ServiceLocatorAwareInterface {
	
    protected $adapter;
	protected $commonService;
	protected $forgotpassword;
	
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
	
    public function getForgotpasswordService(){
        if (! $this->forgotpassword) {
            $this->forgotpassword = $this->getServiceLocator()
            ->get('Auth\Model\Forgotpassword');
        }
        return $this->forgotpassword;
    }
	
	public function fetchAll($filter) {
		
		$sql = new Sql($this->adapter);
        $select = $sql->select('users');
        $select->order('i_user_id desc');
        
        $selectString = $sql->getSqlStringForSqlObject ( $select );
        $result = $this->adapter->query ( $selectString, Adapter::QUERY_MODE_EXECUTE);
		$resultSet = new ResultSet();
		$resultSet->initialize ( $result );
		$Users = $resultSet->toArray ();
		return (! empty ( $Users ) && isset ( $Users ) ? $Users : false);
       
	}
	
    public function addpurchaserequest($data) { 
        $this->commonService->addpurchaserequest($data);
    }
    
    public function updatepurchaserequest($data) {
        $this->commonService->updatepurchaserequest($data);
    }
    
    public function getpurchaserequest($data) {
        $purchaserequests= $this->commonService->getpurchaserequest($data);
        if(!$purchaserequests) {
            $suppliers['purchaserequest']=array();
        } else {
			$suppliers['purchaserequest']=$purchaserequests;
		}
        return $suppliers;
    }
    
	public function deletepurchaserequest($id) {
        $deletepurchaserequest= $this->commonService->changeStatusTo('purchase_requests','0',array('i_purchase_id'=>$id));
        if(!$deletepurchaserequest) {
            return new ApiProblem ( 405, array(), 'Server side validation error', 'Error' );
        }
		return new ApiProblem ( 200, array('Purchase request deleted successfully'), 'Purchase request deleted successfully', 'Success' );
    }
}
