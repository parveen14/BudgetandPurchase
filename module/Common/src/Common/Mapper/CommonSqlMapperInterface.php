<?php
namespace Common\Mapper;

interface CommonSqlMapperInterface
{

    /**
     *
     * @param unknown $id            
     */
    public function getEmail($id);

    public function getFullName($id, $table);

    public function getAvailablePoints($params);

    public function getEarnedPoints($params);

    public function updateAvailablePoints($params);

    public function totalPointsFromHistory($params);

    public function checkUserId($id, $table);

    public function getIndividualSettings($id, $table, $columns = array());

    public function getCountries();

    public function uploadFiles($params);

    public function getCountryNameById($id);

    public function getTotalRecordCount($table = 'users', $status = NULL);

    public function getNotifications($params);

    public function getNotificationsCount($params);

    public function pushNotification($values);

    public function getUserid($email = NULL);

    public function getCampaignDefaultImage($campaign_id);
    // public function getNotificationMessageType($params);
    public function addOrdinal($number);

    public function checkIfRecordExists($table, $where);

    public function encode($id);

    public function decode($encoded);

    public function getPages($route = NULL, $type = NULL);

    public function getDatasets($table, $columns = array(), $where = array(), $params = array());

    public function resizeImages($path, $filename, $thumbSizes);

    public function deleteThumbnails($path, $filename, $thumbSizes);

    public function sendEmail($htmlMarkup, $to, $recieverName, $subject, $from = NULL, $senderName = NULL);

    public function createCacheDir($userId);

    public function emailExists($email_id);

    public function changeStatusTo($table, $status, $where);

    public function getAds($status = NULL);
	
	 public function getCrowns($userpoints);
}