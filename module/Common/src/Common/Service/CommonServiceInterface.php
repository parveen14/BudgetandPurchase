<?php

namespace Common\Service;

interface CommonServiceInterface {
	/**
	 * Get Email by id!
	 *
	 * @author Vipul
	 * @return SponsorsInterface
	 */
	public function getEmail($id);
	
	/**
	 * Get full name of user or sponsor!
	 *
	 * @author Vipul
	 * @param unknown $id        	
	 * @param unknown $table        	
	 */
	public function getFullName($id, $table);
	/**
	 * Get Available Points for users / sponsors!
	 *
	 * @author Vipul
	 * @param array $params        	
	 */
	public function getAvailablePoints($params);
	/**
	 * Get Earned Points for users / sponsors!
	 *
	 * @author Parveen
	 * @param array $params        	
	 */
	public function getEarnedPoints($params);
	/**
	 * Update Available Points Credit/Debit for users/sponsors!
	 *
	 * @author Vipul
	 * @param array $params
	 *        	=> points|available_points|transaction_type|user_id|user_type|sns|type|source_type|source_id
	 */
	public function updateAvailablePoints($params);
	/**
	 * Calculate total points by passing params!
	 *
	 * @author Vipul
	 * @param array $params        	
	 */
	public function totalPointsFromHistory($params);
	/**
	 * Check if user exist!
	 *
	 * @author Vipul
	 * @param array $params        	
	 */
	public function checkUserId($id, $table);
	
	/**
	 * Get the record of user/sponsor!
	 *
	 * @author Vipul
	 * @param int $id        	
	 * @param array $columns        	
	 * @param string $table        	
	 */
	public function getIndividualSettings($id, $table, $columns = array());
	
	/**
	 * Get all the countries!
	 */
	public function getCountries();
	
	/**
	 * Upload Image on server!
	 *
	 * @author Vipul
	 * @param array $params
	 *        	=> path|files|size|ext|deleteImage
	 */
	public function uploadFiles($params);
	
	/**
	 * Get Country name using id!
	 *
	 * @author Vipul
	 * @param unknown $id        	
	 */
	public function getCountryNameById($id);
	
	/**
	 * Get total count from particular table!
	 *
	 * @param unknown $table        	
	 */
	public function getTotalRecordCount($table = 'users', $where = array());
	
	/**
	 * Get users/sponsors notifications!
	 *
	 * @param array $params        	
	 */
	public function getNotifications($params);
	
	/**
	 *
	 * @param array $params        	
	 */
	public function getNotificationsCount($params);
	/**
	 * Send notification to user/sponsor!
	 *
	 * @param array $values
	 *        	=> user_type|user_id|source_type|source_id|message_type|message
	 */
	public function pushNotification($values);
	/**
	 * Get user Id from email
	 *
	 * @param array $values
	 *        	=> user_email
	 */
	public function getUserid($email = NULL);
	
	/**
	 * Get Campaign Default Image!
	 *
	 * @author Vipul
	 * @param int $campaign_id        	
	 */
	public function getCampaignDefaultImage($campaign_id);
	/**
	 * Add ordinal to the number!
	 *
	 * @author Vipul
	 * @param int $number        	
	 */
	public function addOrdinal($number);
	/**
	 * Check if record exists or not!
	 *
	 * @author Vipul
	 * @param string $table        	
	 * @param array $where        	
	 */
	public function checkIfRecordExists($table, $where);
	/**
	 * Encode integer value!
	 *
	 * @author Vipul
	 * @param integer $id        	
	 */
	public function encode($id);
	/**
	 * Decode integer value!
	 *
	 * @author Vipul
	 * @param string $encoded        	
	 */
	public function decode($encoded);
	/**
	 * Get Pages data!
	 *
	 * @author Vipul
	 * @param string $route        	
	 */
	public function getPages($route = NULL, $type = NULL);
	
	/**
	 * Get all the records of specified table!
	 *
	 * @author Vipul Sharma
	 * @param string $table        	
	 * @param array $where        	
	 * @param array $columns        	
	 * @param array $params        	
	 */
	public function getDatasets($table, $columns = array(), $where = array(), $params = array());
	
	/**
	 * It will resize images as per passed parameters!
	 *
	 * @author Vipul Sharma
	 * @param string $path        	
	 * @param string $filename        	
	 * @param array $thumbSizes
	 *        	=> array(array('width' => 'height'), array('width' => 'height'), array('width' => 'height'))
	 */
	public function resizeImages($path, $filename, $thumbSizes);
	/**
	 * It will delete existing thumbnails as per passed parameters!!
	 *
	 * @author Vipul Sharma
	 * @param string $path        	
	 * @param string $filename        	
	 * @param array $thumbSizes
	 *        	=> array(array('width' => 'height'), array('width' => 'height'), array('width' => 'height'))
	 */
	public function deleteThumbnails($path, $filename, $thumbSizes);
	public function sendEmail($htmlMarkup, $to, $recieverName, $subject, $from = NULL, $senderName = NULL);
	public function createCacheDir($userId);
	public function emailExists($email_id);
	public function changeStatusTo($table, $status, $where);
	public function getAds($status = NULL);
	public function getCrowns($userpoints);
}