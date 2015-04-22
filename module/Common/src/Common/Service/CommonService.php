<?php
namespace Common\Service;

use Common\Mapper\CommonSqlMapperInterface;
use Common\Constants\Constants;
use Zend\Filter\Null;

class CommonService implements CommonServiceInterface
{

    protected $mapper;

    public function __construct(CommonSqlMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getEarnedpoints() {
        
    }
    /**
     * {@inheritDoc}
     */
    public function getEmail($id)
    {
        return $this->mapper->getEmail($id);
    }

   
    public function getFullName($id, $table)
    {
        return $this->mapper->getFullName($id, $table);
    }

    public function getUserdetails($params)
    {
        return $this->mapper->getUserdetails($params);
    }

    public function getCompanydetails($params)
    {
        return $this->mapper->getCompanydetails($params);
    }

    public function updateAvailablePoints($params)
    {
        return $this->mapper->updateAvailablePoints($params);
    }

    public function totalPointsFromHistory($params)
    {
        return $this->mapper->totalPointsFromHistory($params);
    }

    public function checkUserId($id, $table)
    {
        return $this->mapper->checkUserId($id, $table);
    }

    public function getIndividualSettings($id, $table, $columns = array())
    {
        return $this->mapper->getIndividualSettings($id, $table, $columns);
    }

    public function getCountries()
    {
        return $this->mapper->getCountries();
    }

    public function uploadFiles($params)
    {
        return $this->mapper->uploadFiles($params);
    }

    public function getCountryNameById($id)
    {
        return $this->mapper->getCountryNameById($id);
    }

    public function getTotalRecordCount($table = 'users', $where = array())
    {
        return $this->mapper->getTotalRecordCount($table, $where);
    }

    public function getNotifications($params)
    {
        return $this->mapper->getNotifications($params);
    }

    public function getNotificationsCount($params)
    {
        return $this->mapper->getNotificationsCount($params);
    }

    public function pushNotification($values)
    {
        return $this->mapper->pushNotification($values);
    }

    public function getUserid($email = NULL)
    {
        return $this->mapper->getUserid($email);
    }

    public function getCampaignDefaultImage($campaign_id)
    {
        return $this->mapper->getCampaignDefaultImage($campaign_id);
    }

    public function addOrdinal($number)
    {
        return $this->mapper->addOrdinal($number);
    }

    public function checkIfRecordExists($table, $where)
    {
        return $this->mapper->checkIfRecordExists($table, $where);
    }

    public function encode($id)
    {
        return $this->mapper->encode($id);
    }

    public function decode($encoded)
    {
        return $this->mapper->decode($encoded);
    }

    public function getPages($route = NULL, $type = NULL)
    {
        return $this->mapper->getPages($route, $type);
    }

    public function getDatasets($table, $columns = array(), $where = array(), $params = array())
    {
        return $this->mapper->getDatasets($table, $columns, $where, $params);
    }

    public function resizeImages($path, $filename, $thumbSizes)
    {
        return $this->mapper->resizeImages($path, $filename, $thumbSizes);
    }

    public function deleteThumbnails($path, $filename, $thumbSizes)
    {
        return $this->mapper->deleteThumbnails($path, $filename, $thumbSizes);
    }

    public function sendEmail($htmlMarkup, $to, $recieverName, $subject, $from = Constants::ADMIN_EMAIL, $senderName = Constants::SENDER_NAME)
    {
        return $this->mapper->sendEmail($htmlMarkup, $to, $recieverName, $subject, $from, $senderName);
    }

    public function createCacheDir($userId)
    {
        return $this->mapper->createCacheDir($userId);
    }

    public function emailExists($email_id)
    {
        return $this->mapper->emailExists($email_id);
    }

    public function changeStatusTo($table, $status, $where = array())
    {
        return $this->mapper->changeStatusTo($table, $status, $where);
    }

	public function getDatasetsmanyjoin($table, $columns = array(), $where = array(), $params = array())
	{
        return $this->mapper->getDatasetsmanyjoin($table, $columns, $where, $params);
    }
    
    public function getDatasetsjoin($table, $columns = array(), $where = array(), $params = array())
    {
        return $this->mapper->getDatasetsjoin($table, $columns, $where, $params);
    }
    
    public function getUsercompanies($userId) {
			return $this->mapper->getUsercompanies($userId);
	}
	
	public function getCostcenters($userId) {
			return $this->mapper->getCostcenters($userId);
	}
	
	public function getWbs($userId,$i_ref_project_id=NULL) {
			return $this->mapper->getWbs($userId,$i_ref_project_id);
	}
	
	public function getSuppliers($userId) {
			return $this->mapper->getSuppliers($userId);
	}
	
	public function addpurchaserequest($data) {
	    return $this->mapper->addpurchaserequest($data);
	}
	
	public function updatepurchaserequest($data) {
	    return $this->mapper->updatepurchaserequest($data);
	}
	
	public function getpurchaserequest($id,$i_purchase_id=NULL) {
	    return $this->mapper->getpurchaserequest($id,$i_purchase_id);
	}
	
	public function checkIfLocal()
	{
	    return $this->mapper->checkIfLocal();
	}
	
	public function getUserpermissions($userId,$companyId)
	{
	    return $this->mapper->getUserpermissions($userId,$companyId);
	}
}
