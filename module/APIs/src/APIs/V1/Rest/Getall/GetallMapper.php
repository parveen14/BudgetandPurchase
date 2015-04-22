<?php

namespace APIs\V1\Rest\Getall;

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

class GetallMapper implements ServiceLocatorAwareInterface {
	
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
	
    public function authenticateUser($data) {
        try {
           if(isset($data->vc_email) AND isset($data->vc_password)) {
            $sql = new Sql($this->adapter);
    		$select = $sql->select(array('u' => 'users'))
    		->where(array('vc_email' => $data->vc_email))
    		->where(array('vc_password' => md5($data->vc_password))); 
    		$statement = $sql->prepareStatementForSqlObject($select);
    		$resultset = $statement->execute()->getResource()->fetch(\PDO::FETCH_ASSOC);
    		
    		if (!$resultset) {
                return new ApiProblem(422,  Constants::INVALID_CREDENTIALS,'Server side validation error',Constants::INVALID_CREDENTIALS);
            }
            else if(!$resultset['i_status'] && $resultset['activate_token']){
            	return new ApiProblem(422,Constants::EMAIL_VERIFICATION_PENDING,'Server side validation error',Constants::EMAIL_VERIFICATION_PENDING);
            }
            else if(!$resultset['i_status']){
            	return new ApiProblem(422,Constants::ACCOUNT_HAS_BEEN_DEACTIVATED,'Server side validation error',Constants::ACCOUNT_HAS_BEEN_DEACTIVATED);
            }
            
            //$data->user_id = $resultset['id'];
           // $data->id = $resultset['id'];
           // $data->username = $resultset['username'];
           // $data->email_id = $resultset['email_id'];
   
            unset($resultset['vc_password']);
            
            return $resultset;
           } else {
               return new ApiProblem(422,  'Email or Password missing','Server side validation error','Email or Password missing');
           }
        } catch (\Exception $e) {
            return new ApiProblem(422,$e,'Server side validation error',$e);
        }
    }
    
    public function updateUser($data) {
        $sql = new Sql ( $this->adapter );
                $exception="";
				    try { 
				        $request = new Request ();
				        $files = $request->getFiles ();
				   
				        if(!$files ['vc_image']['error']){
    				        $oldImage = $this->commonService->getIndividualSettings ( array('i_user_id'=>$data->i_user_id), 'users', array('vc_image') );
                			
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
                					'deleteImage' => $oldImage,
                			        // 'thumbnails'=>true,
                			         'thumbSizes'=>array('200'=>'200','120'=>'120')
                			) );
                			
                			if(!$fileName['success']){
                			    
                			    foreach ($fileName['error_messages'] as $error){
                			        $exception .= $error.',';
                			    }
                			
                			    throw new \RuntimeException ( $exception );
                			} else {
                			    $updateArray =array (
                			        'vc_image' => $fileName['filename'],
                			        'dt_modified' => date ( 'Y-m-d' )
                			    );
                			  
                			    $update = $sql->update ('users')->set($updateArray)->where(array ('i_user_id' => $data->i_user_id));
                			    $statement = $sql->prepareStatementForSqlObject ( $update );
                			    $result = $statement->execute ();
                			    if($result) {
                			        $returnData=$this->commonService->getDatasets('users','',array('i_user_id'=>$data->i_user_id));
                			        if($returnData){
                			            unset($returnData[0]['vc_password']);
                			            return $returnData[0];
                			        } else {
                			             return new ApiProblem ( 422, 'Error while uploading file, Please try again later', 'Server side validation error', 'Image Error' );
                			         }
                			        
                			        //return new ApiProblem ( 200, $fileName['filename'], 'Successfull', 'Image updated' );
                			    } else {
                			        return new ApiProblem ( 422, 'Error while uploading file, Please try again later', 'Server side validation error', 'Image Error' );
                			    }
                			}
				        } else {
				            return new ApiProblem ( 422, 'Please check there is error in file', 'Server side validation error', 'Image Error' );
				        }
            			
				    } catch (\Exception $e) {
    	               return new ApiProblem ( 422, $exception, 'Server side validation error', 'Image error' );
    	             }
    }
    
    
    public function forgotPassword($data) {
         try {
            
              if(isset($data->vc_email)) {
                $emailExists = $this->getForgotpasswordService()->emailExistsPassword($data->vc_email,$this->adapter);
            
                if(!$emailExists)
                {
                    return new ApiProblem(422,  'Email doesnt exist in our database','Server side validation error','Email doesnt exist in our database');
                }
                
                    $results = $this->getForgotpasswordService()->sendFogotPasswordLink($emailExists,$this->adapter);
                    if($results) { 
                        return new ApiProblem ( 200, 'Please check email to reset password', 'Successfull', 'Please check email to reset password' );
                    } else {
                        return new ApiProblem(422,  'Error while sending email , please try again later','Server side validation error','Error while sending email , please try again later');
                    }
                    
            } else {
                return new ApiProblem(422,  'Email missing','Server side validation error','Email missing');
            }
        } catch (\Exception $e) {
    	               return new ApiProblem ( 422, $e, 'Server side validation error', 'Error' );
    	 }
    }
    
    public function getcountries() {
        
        $countries=$this->commonService->getCountries();
		$topCountries['countries']=$countries;
        return $topCountries;
    }
    
    public function getcostcenter($userid) {
    
        
        $costcenters=$this->commonService->getCostcenters($userid);

        if(!$costcenters) {
          return new ApiProblem ( 200, array(), 'Server side validation error', 'Error' );
        } 
		$costcenter['costcenter']=$costcenters;
		$costcenter['method']='itemcostcenter';
        return $costcenter;
    }
    
    public function getwbs($userid) {
        $wbss=$this->commonService->getWbs($userid);
        if(!$wbss) {
            return new ApiProblem ( 200, array(), 'Server side validation error', 'Error' );

        }
        $wbs['wbs']=$wbss;
        return $wbs;
    }
    
    public function getsuppliers($userid) {
    
        $supplierss=$this->commonService->getSuppliers($userid);
        if(!$supplierss) {
            return new ApiProblem ( 200, array(), 'Server side validation error', 'Error' );
        }
		$suppliers['suppliers']=$supplierss;
        return $suppliers;
    }
}
