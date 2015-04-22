<?php

namespace Auth\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Escaper\Escaper;

class Register implements ServiceLocatorAwareInterface {
	protected $adapter;
	protected $messages;
	protected $commonService;
	protected $commonModuleService;
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->services = $serviceLocator;
	}
	public function getServiceLocator() {
		return $this->services;
	}
	
	public function getAdapter() {
		if (! isset ( $this->adapter )) {
			$sm = $this->getServiceLocator ();
			$this->adapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
		}
		return $this->adapter;
	}
	
	public function registerUser($params, $adapter) {
		$escaper = new Escaper ();
		// $randomToken = base64_encode(Rand::getBytes(32, true));
		$randomToken = md5 ( uniqid ( mt_rand () * 1000000, true ) );
		if (! $this->getCommonService ()->checkIfLocal ()) {
			$emailData ['firstname'] = $params ['firstname'];
			$emailData ['confirmationLink'] = BASE_URL . 'authenticate/activate/' . urlencode ( $randomToken );
			$emailData ['pages'] = $this->getCommonModuleService ()->getPages ();
			$htmlMarkup = $this->getServiceLocator ()->get ( 'viewrenderer' )->partial ( 'partial/mails/email_confirmation', $emailData );
			$this->getCommonModuleService ()->sendEmail ( $htmlMarkup, $params ['email_id'], $params ['firstname'] . ' ' . $params ['lastname'], 'Complete your connekd registration' );
		}
		$dob = $params ['dob'] ['month'] . '/' . $params ['dob'] ['day'] . '/' . $params ['dob'] ['year'];
		$this->adapter = $adapter;
		
		$sql = new Sql ( $this->adapter );
		$insert = $sql->insert ( 'users' );
		$newData = array (
				'firstname' => $params ['firstname'],
				'lastname' => $params ['lastname'],
				'email_id' => $params ['email_id'],
				'password' => md5 ( $params ['password'] ),
				'date_of_birth' => date ( 'Y-m-d', strtotime ( $dob ) ),
				'created_at' => date ( 'Y-m-d' ),
				'modified_at' => date ( 'Y-m-d' ),
				'identifier' => $params ['REMOTE_ADDR'],
				'user_agent' => $params ['HTTP_USER_AGENT'],
				'next_valid_date' => date ( 'Y-m-d', strtotime ( '+1 Years', strtotime ( date ( 'Y-m-d' ) ) ) ),
				'activate_token' => $randomToken,
				'image' => $params ['profile_pic'],
				'country_id' => $params ['country'],
				'gender' => $params ['gender'] 
		);
		$insert->values ( $newData );
		$selectString = $sql->getSqlStringForSqlObject ( $insert );
		$results = $this->adapter->query ( $selectString, Adapter::QUERY_MODE_EXECUTE );
		$lastId = $this->adapter->getDriver ()->getLastGeneratedValue ();
		return $lastId;
	}
	public function registersocialUser($params, $adapter, $socialsite) {
		$escaper = new Escaper ();
		$this->adapter = $adapter;
		
		$sql = new Sql ( $this->adapter );
		$insert = $sql->insert ( 'users' );
		$newData = array (
				'firstname' => $params ['firstname'],
				'lastname' => $params ['lastname'],
				'email_id' => $params ['email_id'],
				'password' => md5 ( $params ['email_id'] ),
				'date_of_birth' => date ( 'Y-m-d', strtotime ( $params ['birthday'] ) ),
				'created_at' => date ( 'Y-m-d' ),
				'modified_at' => date ( 'Y-m-d' ),
				'identifier' => $params ['REMOTE_ADDR'],
				'user_agent' => $params ['HTTP_USER_AGENT'],
				'activate_token' => '',
				'country_id' => '1',
				'gender' => $params ['gender'],
				'status' => '1',
				'social_login' => $socialsite 
		);
		
		(isset ( $params ['username'] ) ? $newData ['username'] = $params ['username'] : '');
		(isset ( $params ['profile_pic'] ) ? $newData ['image'] = $params ['profile_pic'] : '');
		
		$insert->values ( $newData );
		$selectString = $sql->getSqlStringForSqlObject ( $insert );
		$results = $this->adapter->query ( $selectString, Adapter::QUERY_MODE_EXECUTE );
		$lastId = $this->adapter->getDriver ()->getLastGeneratedValue ();
		return $lastId;
	}
	public function emailExists($email_id, $adapter) {
		$this->adapter = $adapter;
		$sql = new Sql ( $this->adapter );
		$select = $sql->select ();
		$select->from ( 'sponsors' );
		$select->where ( array (
				'email_id' => $email_id 
		) );
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$resultsSponsors = $statement->execute ();
		
		$select->from ( 'users' );
		$select->where ( array (
				'email_id' => $email_id 
		) );
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$resultsUsers = $statement->execute ();
		
		return (($resultsSponsors->getAffectedRows () || $resultsUsers->getAffectedRows ()) ? true : false);
	}
	public function activateUser($activate_token,$table='users') {
		$sql = new Sql ( $this->getAdapter () );
		$update = $sql->update ( $table )->set ( array (
				'i_status' => 1,
				'activate_token' => '' 
		) )->where ( array (
				'activate_token' => $activate_token 
		) );
		
		$selectString = $sql->getSqlStringForSqlObject ( $update );
		$results = $this->getAdapter ()->query ( $selectString, Adapter::QUERY_MODE_EXECUTE );
		
		return $results->getAffectedRows ();
	}
	
	public function activateCompanyuser($data) {
	    $sql = new Sql ( $this->getAdapter () );
	    $update = $sql->update ( 'users' )->set ( array (
	        'vc_fname' => $data['vc_fname'],
	        'vc_lname' => $data['vc_lname'],
	        'i_status' => 1,
	        'activate_token' => ''
	    ) )->where ( array (
	        'activate_token' => $data['activate_token'],
	        'vc_email' => $data['vc_email']
	    ) );
	
	    $selectString = $sql->getSqlStringForSqlObject ( $update );
	    $results = $this->getAdapter ()->query ( $selectString, Adapter::QUERY_MODE_EXECUTE );
	
	    return $results->getAffectedRows ();
	}
	
	public function checkIfActivated($email,$type='Employee') {
	    if($type=='Company') {
	        $table='company';
	    } else {
	        $table='users';
	    }
		$sql = new Sql ( $this->getAdapter () );
		$select = $sql->select ( $table )->where ( array (
				'vc_email' => $email 
		) );
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$resultSet = $statement->execute ()->getResource ()->fetch ( \PDO::FETCH_ASSOC );
		return (isset ( $resultSet ) ? $resultSet : array ());
	}
	
}