<?php
namespace Common\Mapper;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Db\Sql\Expression;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\Db\Adapter\Adapter;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\Filter\File\Rename;
use Zend\File\Transfer\Adapter\Http;
use Common\Constants\Constants;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use AssetManager\Exception\RuntimeException;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Json\Server\Cache;
use Zend\Session\SessionManager;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Assetic\Exception\Exception;

class CommonSqlMapper implements CommonSqlMapperInterface
{

    /**
     *
     * @var \Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     *
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     *
     * @var
     *
     */
    protected $authservice;
    protected $httpadapter;
    protected $cache;
    protected $sessionManger;

    /**
     *
     * @param AdapterInterface $adapter            
     * @param HydratorInterface $hydrator            
     * @param
     *            AuthService
     * @param
     *     
     */
    public function __construct(AdapterInterface $adapter, HydratorInterface $hydrator, $authservice)
    {
        $this->adapter = $adapter;
        $this->hydrator = $hydrator;
        $this->authservice = $authservice;
        $this->httpadapter = new Http();
        $this->sessionManger = new SessionManager();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Common\Mapper\CommonSqlMapperInterface::getEmail()
     */
    public function getEmail($id)
    {
        return 'manu.sharma1222@gmail.com';
    }

    

    public function getFullName($id, $table)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select($table);
        $select->where(array(
            'id = ?' => $id
        ));
        $select->columns(array(
            'firstname',
            'lastname'
        ));
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        
        if ($result->getAffectedRows()) {
            $resultSet = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            return $resultSet['firstname'] . ' ' . $resultSet['lastname'];
        }
    }

   

    public function getUserdetails($params)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('users');
        
        $select->join('countries', 'countries.country_id=users.i_ref_country_id', array('country_name'=>'name'),'left');
        $select->join('states', 'states.states_id=users.i_ref_state_id', array('state_name'=>'name'),'left');
        
        
        $select->where(array(
            'i_user_id' => $params['i_user_id']
        ));
      
        $select->limit(1);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $datasets = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            $usercompanies=$this->getUsercompanies($params['i_user_id']);
            $datasets['user_companies']=$usercompanies;
            return $datasets;
        }
        return false;
    }

    public function getCompanydetails($params)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('company');
        
        $select->join('countries', 'countries.country_id=company.i_ref_country_id', array('country_name'=>'name'),'left');
        $select->join('states', 'states.states_id=company.i_ref_state_id', array('state_name'=>'name'),'left');
        
        
        $select->where(array(
            'i_company_id' => $params['i_company_id']
        ));
      
        $select->limit(1);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $datasets = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            return $datasets;
        }
        return false;
    }

    public function updateAvailablePoints($params)
    {
        $available_points = $this->getAvailablePoints($params);
        
        switch ($params['transaction_type']) {
            case 'credit':
                $pointsLeft = $available_points + $params['points'];
                break;
            case 'debit':
                if ($params['type'] !== SnsPlugin::SNS_REGISTRATION) {
                    if ($available_points >= $params['points'])
                        $pointsLeft = $available_points - $params['points'];
                    else
                        throw new \RuntimeException(Constants::YOU_DONT_HAVE_SUFFICIENT_POINTS);
                } else {
                    $pointsLeft = $available_points - $params['points'];
                }
                break;
        }
        
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('points_history');
        $data = array(
            'points' => $params['points'],
            'available_points' => $pointsLeft,
            'transaction_type' => $params['transaction_type'],
            'user_id' => $params['user_id'],
            'user_type' => $params['user_type'],
            'sns' => $params['sns'],
            'type' => $params['type'],
            'created_at' => date('Y-m-d H:i:s')
        );
        
        if (isset($params['source_type']) && isset($params['source_id'])) {
            $data = array_merge($data, array(
                'source_type' => $params['source_type'],
                'source_id' => $params['source_id']
            ));
        } else {
            $data = array_merge($data, array(
                'source_type' => PointsHistory::CONNEKD,
                'source_id' => 1
            ));
        }
        $insert->values($data);
        
        // echo $sql->getSqlStringForSqlObject($update);die();
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        if ($results->getAffectedRows()) {
            if (in_array($params['type'], array(
                PointsHistory::POINTS_PURCHASED,
                PointsHistory::CAMPAIGN_OFFER,
                PointsHistory::DONATION,
                PointsHistory::POINTS_RETURNED,
                PointsHistory::BOOST_POINTS
            ))) {
                $notificationArray = array(
                    'user_type' => $params['user_type'],
                    'user_id' => $params['user_id'],
                    'message_type' => $params['type'],
                    'message' => $this->getAvailablePointsMessage($params['type'], $data)
                );
                
                if (isset($data['source_type']) && isset($data['source_id'])) {
                    $notificationArray = array_merge($notificationArray, array(
                        'source_type' => $data['source_type'],
                        'source_id' => $data['source_id']
                    ));
                }
                
                $this->pushNotification($notificationArray);
            }
            return $pointsLeft;
        }
        
        throw new \RuntimeException(Constants::SOMTHING_MIGHT_WENT_WRONG);
    }

    private function getAvailablePointsMessage($type, $params)
    {
        switch ($type) {
            case PointsHistory::POINTS_PURCHASED:
                return 'Points purchased and available points are ' . $params['available_points'];
                break;
            case PointsHistory::DONATION:
                if ($params['user_type'] == PointsHistory::SPONSOR) {
                    return 'Sponsorship Request sent and available points are ' . $params['available_points'];
                } else {
                    return 'Sponsorship Request accepted and available points are ' . $params['available_points'];
                }
                break;
            case PointsHistory::CAMPAIGN_OFFER:
                return 'Offer converted and available points are ' . $params['available_points'];
                break;
            case PointsHistory::POINTS_RETURNED:
                return 'User has declined your request and available points are ' . $params['available_points'];
                break;
            case PointsHistory::BOOST_POINTS:
                if ($params['user_type'] == PointsHistory::SPONSOR) {
                    return 'Boost points request sent and available points are ' . $params['available_points'];
                } else {
                    return 'Boost points request accepted and available points are ' . $params['available_points'];
                }
                break;
        }
    }

    private function getNotificationMessageType($params)
    {
        switch ($params['user_type']) {
            case PointsHistory::SPONSOR:
                if ($params['type'] == PointsHistory::POINTS_RETURNED) {
                    return Constants::ACCEPTED_SPONSORSHIP;
                } else {
                    return Constants::POINTS_UPDATED;
                }
                break;
            case PointsHistory::USER:
                return Constants::POINTS_UPDATED;
                break;
            default:
                return Constants::POINTS_UPDATED;
        }
    }

    public function totalPointsFromHistory($params)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(array(
            'ph' => 'points_history'
        ));
        $select->columns(array(
            'totalPoints' => new Expression('COALESCE(sum(ph.points),0)')
        ));
        $where = array(
            'ph.user_type' => $params['user_type'],
            'ph.transaction_type' => $params['transaction_type']
        );
        
        if (isset($params['user_id'])) {
            $where = array_merge($where, array(
                'ph.user_id' => $params['user_id']
            ));
        }
        
        if (isset($params['sns'])) {
            $where = array_merge($where, array(
                'ph.sns' => $params['sns']
            ));
        }
        
        if (isset($params['type'])) {
            $where = array_merge($where, array(
                'ph.type' => $params['type']
            ));
        }
        
        if (isset($params['source_type']) && isset($params['source_id'])) {
            $where = array_merge($where, array(
                'ph.source_type' => $params['source_type'],
                'ph.source_id' => $params['source_id']
            ));
        }
        
        $select->where($where);
        if (isset($params['start_date']) && isset($params['end_date'])) {
            $select->where->between("ph.created_at", date('Y-m-d 00:00:00', strtotime($params['start_date'])), date('Y-m-d 23:59:59', strtotime($params['end_date'])));
            // $select->where ( 'ph.created_at BETWEEN "' . date ( 'Y-m-d 00:00:00', strtotime ( $params ['start_date'] ) ) . '" AND "' . date ( 'Y-m-d 23:59:59', strtotime ( $params ['end_date'] ) ) .'"' );
        }
        // echo $sql->getSqlStringForSqlObject($select);die;
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $dataset = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            return $dataset['totalPoints'];
        }
    }

    function checkUserId($id, $table)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select($table)->where(array(
            'id' => $id
        ));
        $select->columns(array(
            'id'
        ));
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $dataset = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            return true;
        } else {
            return false;
        }
    }

    public function getIndividualSettings($id, $table, $columns = array())
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(array(
            't' => $table
        ));
        if ($columns) {
            $select->columns($columns);
        }
        $select->where($id);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $dataset = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            return sizeof($columns) == 1 ? $dataset[$columns[0]] : $dataset;
        }
        // throw new \RuntimeException ( 'Specified user doesn\'t exists!' );
    }

    public function getCountries()
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('countries')
            ->columns(array(
            'country_id',
            'name'
        ))
            ->order('name ASC');
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function uploadFiles($params)
    {
        $filesize = new Size($params['size']);
        
        $extension = new Extension($params['ext']);
        
        $this->httpadapter->setValidators(array(
            $filesize,
            $extension
        ), $params['files']['name']);
        
        if ($this->httpadapter->isValid()) {
            
            $filter = new Rename(array(
                "target" => $params['path'] . $params['files']['name'],
                "randomize" => true
            ));
            $uploadedFile = $filter->filter($params['files']);
            
            $result = array(
                'success' => true,
                'filename' => basename($uploadedFile['tmp_name'])
            );
            chmod($params['path'] . basename($uploadedFile['tmp_name']), 0755);
            if (! empty($params['deleteImage']) && file_exists($params['path'] . $params['deleteImage'])) {
                unlink($params['path'] . $params['deleteImage']);
                // delete thumbnails
                if (isset($params['thumbSizes'])) {
                    $this->deleteThumbnails($params['path'], $params['deleteImage'], $params['thumbSizes']);
                }
            }
            
            if (isset($params['thumbnails']) && $params['thumbnails'] == true && isset($params['thumbSizes'])) {
                $this->resizeImages($params['path'], $result['filename'], $params['thumbSizes']);
            }
        } else {
            $result = array(
                'success' => false,
                'error_messages' => $this->httpadapter->getMessages()
            );
        }
        
        return $result;
    }

    public function getCountryNameById($id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('countries');
        $select->columns(array(
            'name'
        ));
        $select->where(array(
            'country_id' => $id
        ));
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $dataset = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            return $dataset['name'];
        }
    }

    public function getTotalRecordCount($table = 'users', $where = array())
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(array(
            't' => $table
        ));
        $select->columns(array(
            'totalCount' => new Expression('COALESCE(count(*),0)')
        ));
        
        if (! empty($where)) {
            $select->where($where);
        }
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $dataset = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            return $dataset['totalCount'];
        }
    }

    public function getNotifications($params)
    {
        $this->deleteOldNotifications($params);
        $sql = new Sql($this->adapter);
        $select = $sql->select(array(
            'n' => 'notifications'
        ));
        $where = array(
            'n.user_type' => $params['user_type'],
            'n.user_id' => $params['user_id']
        );
        
        if (isset($params['is_read'])) {
            $where = array_merge($where, array(
                'n.is_read' => $params['is_read']
            ));
        }
        
        if (isset($params['type']) && $params['type'] == "autorefresh") {
            $firstid = $params['firstid'];
            $select->where("`id`>" . $firstid);
        } elseif (isset($params['type']) && $params['type'] == "loadmore") {
            $lastid = $params['lastid'];
            $select->where("`id`<" . $lastid);
            $select->limit(5);
        } else {
            $select->limit(5);
        }
        
        $select->where($where);
        // if ($params ['offset'] != NULL) {
        // $select->limit(10)->offset((int)$params['offset']);
        // }
        
        $select->order('n.id desc');
        // echo $sql->getSqlStringForSqlObject($select);die;
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $datasets = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
            if (! empty($datasets)) {
                foreach ($datasets as $dataset) {
                    if (! $dataset['is_read']) {
                        $this->readNotifications($dataset['id']);
                    }
                }
            }
            return $this->getMessages($datasets);
        }
    }

    private function deleteOldNotifications($params)
    {
        $sql = 'DELETE n FROM notifications AS n JOIN ( SELECT created_at, id FROM notifications ORDER BY created_at DESC, id DESC LIMIT 1 OFFSET 200 ) AS lim ON n.created_at < lim.created_at OR n.created_at = lim.created_at AND n.id < lim.id where n.user_id = ' . $params['user_id'] . ' and n.user_type = "' . $params['user_type'] . '"';
        $result = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        return $result->getAffectedRows();
    }

    private function readNotifications($id)
    {
        $sql = new Sql($this->adapter);
        $notification = $sql->update('notifications')
            ->set(array(
            'is_read' => 1
        ))
            ->where(array(
            'id' => $id
        ));
        $select = $sql->getSqlStringForSqlObject($notification);
        $result = $this->adapter->query($select, Adapter::QUERY_MODE_EXECUTE);
        return $result->getAffectedRows();
    }

    public function getMessages($datasets)
    {
        if (! empty($datasets)) {
            $data = array();
            foreach ($datasets as $dataset) {
                $notification = array();
                $notification['id'] = $dataset['id'];
                $notification['is_read'] = $dataset['is_read'];
                if ($dataset['source_type'] == PointsHistory::USER) {
                    $user = $this->getIndividualSettings($dataset['source_id'], 'users', array(
                        'firstname',
                        'lastname',
                        'image'
                    ));
                    $notification['name'] = $user['firstname'] . ' ' . $user['lastname'];
                    $notification['image'] = $user['image'] && file_exists('public/uploads/userImages/thumbnails/120x120/' . $user['image']) ? '/uploads/userImages/thumbnails/120x120/' . $user['image'] : '/images/userDefImage.png';
                }
                if ($dataset['source_type'] == PointsHistory::SPONSOR) {
                    $sponsor = $this->getIndividualSettings($dataset['source_id'], 'sponsors', array(
                        'company_name',
                        'logo'
                    ));
                    $notification['name'] = $sponsor['company_name'];
                    $notification['image'] = $sponsor['logo'] && file_exists('public/uploads/sponsors/thumbnails/120x120/' . $sponsor['logo']) ? '/uploads/sponsors/thumbnails/120x120/' . $sponsor['logo'] : '/images/default_logo.png';
                }
                if ($dataset['source_type'] == PointsHistory::CAMPAIGN) {
                    $campaignTitle = $this->getIndividualSettings($dataset['source_id'], 'campaign', array(
                        'title'
                    ));
                    
                    $campaignImage = $this->getCampaignDefaultImage($dataset['source_id']);
                    $notification['name'] = $campaignTitle;
                    $notification['image'] = $campaignImage && file_exists('public/uploads/campaign/thumbnails/120x120/' . $campaignImage) ? '/uploads/campaign/thumbnails/120x120/' . $campaignImage : '/images/default_logo.png';
                }
                if ($dataset['source_type'] == PointsHistory::CONNEKD) {
                    $notification['image'] = '/images/default_logo.png';
                }
                $notification['message'] = $dataset['message'];
                
                // switch ($dataset ['message_type']) {
                // case Constants::ACCEPTED_SPONSORSHIP :
                
                // break;
                // case Constants::TOOK_OFFER :
                
                // break;
                // }
                // $format = '{name} has accepted your sponsorship'; $find = array ('{name}');$replace = array ('name' => 'Vipul');
                // $notification ['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
                $data[] = $notification;
            }
            return $data;
        }
    }

    public function getNotificationsCount($params)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(array(
            'n' => 'notifications'
        ));
        $where = array(
            'n.user_type' => $params['user_type'],
            'n.user_id' => $params['user_id']
        );
        
        if (isset($params['is_read'])) {
            $where = array_merge($where, array(
                'n.is_read' => $params['is_read']
            ));
        }
        
        $select->where($where);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        return $resultSet->count();
    }

    public function pushNotification($values)
    {
        $sql = new Sql($this->adapter);
        $data = array(
            'user_type' => $values['user_type'],
            'user_id' => $values['user_id'],
            'message_type' => $values['message_type'],
            'message' => $values['message'],
            'created_at' => date('Y-m-d H:i:s')
        );
        if (isset($values['source_type'])) {
            $data = array_merge($data, array(
                'source_type' => $values['source_type']
            ));
        } else {
            $data = array_merge($data, array(
                'source_type' => PointsHistory::CONNEKD
            ));
        }
        if (isset($values['source_id'])) {
            $data = array_merge($data, array(
                'source_id' => $values['source_id']
            ));
        }
        $notifications = $sql->insert('notifications')->values($data);
        $selectString = $sql->getSqlStringForSqlObject($notifications);
        
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        if ($results->getAffectedRows()) {
            return $this->adapter->getDriver()->getLastGeneratedValue();
        }
    }

    public function getUserid($email = null)
    {
        if (! isset($email)) {
            $email = $this->authservice->getIdentity();
        }
        $sql = new Sql($this->adapter);
        $select = $sql->select('users')->where(array(
            'email_id' => $email
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute()
            ->getResource()
            ->fetch(\PDO::FETCH_ASSOC);
        return (isset($resultSet['id']) ? $resultSet['id'] : null);
    }

    public function getCampaignDefaultImage($campaign_id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select(array(
            'cm' => 'campaign_images'
        ));
        $select->where(array(
            'campaign_id' => $campaign_id
        ));
        $select->columns(array(
            'id',
            'image'
        ));
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $dataset = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            return $dataset['image'];
        }
    }

    public function addOrdinal($number)
    {
        $ends = array(
            'th',
            'st',
            'nd',
            'rd',
            'th',
            'th',
            'th',
            'th',
            'th',
            'th'
        );
        if (($number % 100) >= 11 && ($number % 100) <= 13)
            $abbreviation = $number . 'th';
        else
            $abbreviation = $number . $ends[$number % 10];
        return $abbreviation;
    }

    public function checkIfRecordExists($table, $where)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select($table);
        $select->where($where);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        // echo $sql->getSqlStringForSqlObject($select);die;
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $result->count();
        }
    }

    public function encode($id)
    {
        $id_str = (string) $id;
        $offset = rand(0, 9);
        $encoded = chr(79 + $offset);
        for ($i = 0, $len = strlen($id_str); $i < $len; ++ $i) {
            $encoded .= chr(65 + $id_str[$i] + $offset);
        }
        return strtolower($encoded);
    }

    public function decode($encoded)
    {
        $offset = ord($encoded[0]) - 79;
        $encoded = substr($encoded, 1);
        for ($i = 0, $len = strlen($encoded); $i < $len; ++ $i) {
            $encoded[$i] = ord($encoded[$i]) - $offset - 65;
        }
        return (int) $encoded;
    }

    public function getPages($route = NULL, $type = NULL)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select('pages');
        $select->columns(array(
            'title',
            'content',
            'route'
        ));
        $where = array(
            'status' => 1
        );
        if ($route) {
            $where = array_merge($where, array(
                'route' => $route
            ));
        }
        
        if ($type) {
            // SELECT * FROM `pages` WHERE id NOT IN (SELECT id FROM `pages` WHERE FIND_IN_SET('login',hide_from))
            $select->where('!FIND_IN_SET("' . $type . '",hide_from)');
        }
        $select->where($where);
        $select->order('sort_order');
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            if ($route) {
                return $result->getResource()->fetch(\PDO::FETCH_ASSOC);
            }
            return $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function getDatasets($table, $columns = array(), $where = array(), $params = array())
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select($table);
        if ($columns) {
            $select->columns($columns);
        }
        if ($where) {
            $select->where($where);
        }
        if (isset($params['order_by'])) {
            $select->order($params['order_by']);
        }
        if (isset($params['group_by'])) {
            $select->group($params['group_by']);
        }
        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (isset($params['offset'])) {
            $select->offset($params['offset']);
        }
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function resizeImages($path, $filename, $thumbSizes)
    {
        $imagine = new Imagine();
        if (! empty($thumbSizes)) {
            foreach ($thumbSizes as $width => $height) {
                $image = $imagine->open($path . $filename);
                if (! is_dir($path . 'thumbnails')) {
                    mkdir($path . 'thumbnails');
                }
                if (! is_dir($path . 'thumbnails/' . $width . 'x' . $height)) {
                    mkdir($path . 'thumbnails/' . $width . 'x' . $height);
                }
                $image->resize(new Box($width, $height))->save($path . 'thumbnails/' . $width . 'x' . $height . '/' . $filename);
            }
        }
    }

    public function deleteThumbnails($path, $filename, $thumbSizes)
    {
        if (! empty($thumbSizes)) {
            foreach ($thumbSizes as $width => $height) {
                if (file_exists($path . 'thumbnails/' . $width . 'x' . $height . '/' . $filename)) {
                    unlink($path . 'thumbnails/' . $width . 'x' . $height . '/' . $filename);
                }
            }
        }
    }

    public function sendEmail__($htmlMarkup, $to, $recieverName, $subject, $from = NULL, $senderName = NULL)
    {
        $transport = new Smtp();
        $options = new SmtpOptions(array(
            'host' => 'mail.connekd.com',
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => Constants::SMTP_USERNAME,
                'password' => Constants::SMTP_PASSWORD
            ),
            'port' => '26'
        ));
        $htmlPart = new MimePart($htmlMarkup);
        $htmlPart->type = "text/html";
        
        $body = new MimeMessage();
        $body->setParts(array(
            $htmlPart
        ));
        
        $mail = new Mail\Message();
        $mail->setFrom($from, $senderName);
        $mail->addTo($to, $recieverName);
        $mail->setSubject($subject);
        // $mail->setEncoding("UTF-8");
        $mail->setBody($body);
        $mail->getHeaders()->addHeaderLine('MIME-Version', '1.0');
        $mail->getHeaders()->addHeaderLine('Content-Type', 'text/html; charset=iso-8859-1');
        // $mail->getHeaders()->addHeaderLine('X-Mailer', 'PHP/' . phpversion());
        
        // try {
        $transport->setOptions($options);
        $transport->send($mail);
        // } catch (\Zend_Mail_Transport_Exception $e) {
        // echo "<pre>" . print_r($e->getMessage(), true) . "</pre>";
        // exit();
        // }
        return true;
    }

    public function sendEmail($htmlMarkup, $to, $recieverName, $subject, $from = NULL, $senderName = NULL)
    {

        $html = new MimePart($htmlMarkup);
        $html->type = "text/html";
        
        $body = new MimeMessage();
        $body->setParts(array(
            $html
        ));
        
        $mail = new Mail\Message();
        $mail->setBody($body);
        $mail->getHeaders()->addHeaderLine('MIME-Version', '1.0');
        $mail->getHeaders()->addHeaderLine('X-Mailer', 'PHP/' . phpversion());
        $mail->setFrom($from, $senderName);
        $mail->addTo($to, $recieverName);
        $mail->setSubject($subject);
        
        $transport = new Mail\Transport\Sendmail();
        $transport->send($mail);
    }

    public function createCacheDir($userId)
    {
        if (! file_exists('data/cache/' . $userId . '/' . $this->sessionManger->getId())) {
            mkdir('data/cache/' . $userId . '/' . $this->sessionManger->getId(), 0777, true);
        }
        $this->cache->getOptions()->setCacheDir('data/cache/' . $userId . '/' . $this->sessionManger->getId());
    }

    public function emailExists($email_id)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        
        $select->from('users');
        $select->where(array(
            'vc_email' => $email_id
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultsUsers = $statement->execute();
        
        return (($resultsUsers->getAffectedRows()) ? $resultsUsers->getResource()->fetchAll(\PDO::FETCH_ASSOC) : false);
    }

    public function changeStatusTo($table, $status, $where)
    {
        $sql = new Sql($this->adapter);
        $update = $sql->update($table)->set(array(
            'i_status' => $status,
            'dt_modified' => date('Y-m-d H:i:s')
        ));
        $update->where($where);
        $selectString = $sql->getSqlStringForSqlObject($update);
       
        $result = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        if ($result->getAffectedRows()) {
            return $result->getAffectedRows();
        }
    }
	
	public function getDatasetsmanyjoin($table, $columns = array(), $where = array(), $params = array())
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select($table['table1']);
		//$select->join($table['table2'], $table['table1'].'.'.$table['table1key'] = $table['table2'].'.'.$table['table2key'], array(), 'left');
        if ($columns) {
            $select->columns($columns);
        }
        if ($where) {
            $select->where($where);
        }
        if (isset($params['order_by'])) {
            $select->order($params['order_by']);
        }
        if (isset($params['group_by'])) {
            $select->group($params['group_by']);
        }
        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (isset($params['offset'])) {
            $select->offset($params['offset']);
        }
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
			$returnData= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
			foreach($returnData as $returnDatas){
			    
			  if (isset($table['table2']) && !empty($table['table2'])) {
			    $select = $sql->select($table['table2']);
			    if (isset($table['table2requiredColumns'])) {
			        $select->columns($table['table2requiredColumns']);
			    }
			    $select->where(array($table['table2key']=>$returnDatas[$table['table1key']]));
			    $stmt = $sql->prepareStatementForSqlObject($select);
			    $result = $stmt->execute();
			    if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
			        $returnDatas[$table['table2']]= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
			    } else {
			        $returnDatas[$table['table2']]='';
			    }
			  }
			  
			  if (isset($table['table3']) && !empty($table['table3'])) {
			      $select = $sql->select($table['table3']);
			      if (isset($table['table3requiredColumns'])) {
			          $select->columns($table['table3requiredColumns']);
			      }
			      $select->where(array($table['table3key']=>$returnDatas[$table['table1key']]));
			      $stmt = $sql->prepareStatementForSqlObject($select);
			      $result = $stmt->execute();
			      if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
			          $returnDatas[$table['table3']]= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
			      } else {
			          $returnDatas[$table['table3']]='';
			      }
			  }
			  
			  if (isset($table['table4']) && !empty($table['table4'])) {
			      $select = $sql->select($table['table4']);
			      if (isset($table['table4requiredColumns'])) {
			          $select->columns($table['table4requiredColumns']);
			      }
			      $select->where(array($table['table4key']=>$returnDatas[$table['table1key']]));
			      $stmt = $sql->prepareStatementForSqlObject($select);
			      $result = $stmt->execute();
			      if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
			          $returnDatas[$table['table4']]= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
			      } else {
			          $returnDatas[$table['table4']]='';
			      }
			  }
			  
			    $mergeddata[]=$returnDatas;
			    
			}
			return $mergeddata;
        }
    }
    
    
    
    
    public function getDatasetsjoin($table, $columns = array(), $where = array(), $params = array())
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select($table['table1']);
        if(isset($table['table2keyrequired'])) {
            $requiredFields=$table['table2keyrequired'];
        } else {
            $requiredFields=array('*');
        }
        $select->join($table['table2'], $table['table1'].'.'.$table['table1key'] ."=". $table['table2'].'.'.$table['table2key'], $requiredFields, 'left');
        if ($columns) {
            $select->columns($columns);
        }
        if ($where) {
            $select->where($where);
        }
        if (isset($params['order_by'])) {
            $select->order($params['order_by']);
        }
        if (isset($params['group_by'])) {
            $select->group($params['group_by']);
        }
        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }
        if (isset($params['offset'])) {
            $select->offset($params['offset']);
        }
        //echo $sql->getSqlStringForSqlObject($select);die;
        $stmt = $sql->prepareStatementForSqlObject($select);
      
        $result = $stmt->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            $returnData= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
            return $returnData;
        }
    }
    
    public function getUsercompanies($userId) {
			$sql = new Sql($this->adapter);
			$select = $sql->select('user_details');	
			$select->join('company', 'company.i_company_id=user_details.i_ref_company_id', array('vc_company_name','vc_legal_entity','vc_email','vc_description','vc_logo','vc_street_address','i_ref_country_id','i_ref_state_id','vc_city','vc_contact_mobile','vc_contact_skype','vc_contact_landline'),'left');
			$select->join('business_units', 'business_units.i_bu_id=user_details.i_ref_bu_id', array('business_unit_name'=>'vc_short_name'),'left');
			$select->join('departments', 'departments.i_dep_id=user_details.i_ref_dep_id', array('department_name'=>'vc_name'),'left');
			$select->join('roles', 'roles.i_role_id=user_details.i_ref_role_id', array('role_name'=>'vc_name'),'left');
			
			$select->join('countries', 'countries.country_id=company.i_ref_country_id', array('country_name'=>'name'),'left');
			$select->join('states', 'states.states_id=company.i_ref_state_id', array('state_name'=>'name'),'left');

				
			$select->where(array('i_ref_user_id'=>$userId));
            $select->order('i_udtl_id');
            $select->group('i_ref_company_id');
            $stmt = $sql->prepareStatementForSqlObject($select);
        
			$result = $stmt->execute();
			if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
				$returnData= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
				return $returnData;
			} else {
					return false;
			}
	}
	
	public function getCostcenters($userId) {
			$sql = new Sql($this->adapter);
			$select = $sql->select('cost_center');	
			$select->where(array('i_ref_company_id'=>$userId));
            $select->order('i_cc_id');
            $stmt = $sql->prepareStatementForSqlObject($select);
        
			$result = $stmt->execute();
			if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
				$returnData= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
				return $returnData;
			} 
			return false;
	}
	
	public function getWbs($userId,$i_ref_project_id=NULL) {
		$sql = new Sql($this->adapter);
		if($i_ref_project_id!="") {
			$whr = " AND t1.`i_ref_project_id`=".$i_ref_project_id." AND t1.`i_type`!=1";
		} else {
			$whr =" AND 1=1";
		}
		$select = "select 
t1.`i_rec_id`,t1.`i_parent_id`,t1.`vc_unit`,t1.`i_ref_company_id`,`t1`.`i_ref_project_id`,`t1`.`i_type`,`t3`.`i_ref_department_id`,`t3`.`vc_forecast`,`t3`.`vc_actual`,`t3`.`vc_comment`,
IF (t1.`i_parent_id` IS NULL,    ( SELECT `projects`.`vc_name` AS `vc_name`  FROM projects WHERE projects.i_project_id=t1.`i_ref_project_id`), t1.`vc_name`)  AS vc_name,
(
   CASE
      WHEN t1.`i_type` =1 THEN (SELECT SUM(`vc_plan`) AS `vc_plan`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_ref_project_id=t2.i_ref_project_id))
      WHEN t1.`i_type` =2 THEN (SELECT SUM(`vc_plan`) AS `vc_plan`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id OR i_parent_id in (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id)))
	  WHEN t1.`i_type` =3 THEN (SELECT SUM(`vc_plan`) AS `vc_plan`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id))
      ELSE vc_plan
   END
) AS vc_plan,
(
   CASE
      WHEN t1.`i_type` =1 THEN (SELECT SUM(`vc_forecast`) AS `vc_forecast`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_ref_project_id=t2.i_ref_project_id))
      WHEN t1.`i_type` =2 THEN (SELECT SUM(`vc_forecast`) AS `vc_forecast`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id OR i_parent_id in (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id)))
	  WHEN t1.`i_type` =3 THEN (SELECT SUM(`vc_forecast`) AS `vc_forecast`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id))
      ELSE vc_forecast
   END
) AS vc_forecast,
(
   CASE
      WHEN t1.`i_type` =1 THEN (SELECT SUM(`vc_actual`) AS `vc_actual`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_ref_project_id=t2.i_ref_project_id))
      WHEN t1.`i_type` =2 THEN (SELECT SUM(`vc_actual`) AS `vc_actual`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id OR i_parent_id in (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id)))
	  WHEN t1.`i_type` =3 THEN (SELECT SUM(`vc_actual`) AS `vc_actual`  FROM wbs_department WHERE i_ref_wbs_id IN (SELECT i_rec_id from wbs WHERE i_parent_id=t2.i_parent_id))
      ELSE vc_actual
   END
) AS vc_actual
from wbs t1 
left join wbs t2 on t2.i_parent_id = t1.i_rec_id 
left join wbs_department t3 on t3.i_ref_wbs_id = t2.i_rec_id or t3.i_ref_wbs_id = t1.i_rec_id WHERE `t1`.`i_ref_company_id`=1 AND `t1`.`i_status`=1".$whr." group by t1.i_rec_id" ;
 		
		
		$stmt = $this->adapter->query($select);
// 		print_r($result); die;
		
// 			$select = $sql->select('wbs');	
// 			$select->where(array('i_ref_company_id'=>$userId));
//             $select->order('i_rec_id');
 //           $stmt = $sql->prepareStatementForSqlObject($select);
        
			$result = $stmt->execute();
			if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
				$returnData= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
				return $returnData;
			} 
			return false;
	} 
	public function getSuppliers($userId) {
			$sql = new Sql($this->adapter);
			$select = $sql->select('suppliers');
			$select->join('supplier_details', 'supplier_details.i_ref_supplier_id=suppliers.i_supplier_id', array(),'left');			
			$select->where(array('supplier_details.i_ref_company_id'=>$userId));
            $select->order('i_supplier_id');
            $stmt = $sql->prepareStatementForSqlObject($select);
        
			$result = $stmt->execute();
			if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
				$returnData= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
				return $returnData;
			} 
			return false;
	
	}
	
	public function addpurchaserequest($data) {

	    if(!is_array($data)) {
	        throw new \Exception("There is some error, Please check all fields again");
	    }
	    $sql = new Sql($this->adapter);
	    $items = $data['item'];
	    unset($data['item']);
	    $data['dt_created']=date('Y-m-d');
	    $data['dt_modified']=date('Y-m-d');
	    $data['i_status']=1;
	    $department = $sql->insert('purchase_requests')->values($data);
	    $selectString = $sql->getSqlStringForSqlObject($department);
	    $result = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);

	    $i_ref_purchase_id=$this->adapter->getDriver()->getLastGeneratedValue();
        
		if($result) {
            if(is_array($items)) {
                foreach($items as $item) {
                    unset($item['costcenter']);
                    $item['i_ref_purchase_id']=$i_ref_purchase_id;
                    $department = $sql->insert('requested_items')->values($item);
                    $selectString = $sql->getSqlStringForSqlObject($department);
                    $result = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    $i_id=$this->adapter->getDriver()->getLastGeneratedValue();
                }
            }
                return array(
                    'success' => $result->getAffectedRows(),
                    'id' => $i_ref_purchase_id
                );
        } else {
            
            throw new \Exception("Error while adding purchase request");
        }
	    
	}
	
	public function updatepurchaserequest($data) {

	    if(!is_array($data)) {
	        throw new \Exception("There is some error, Please check all fields again");
	    }
	    $sql = new Sql($this->adapter);
	    $items = $data['item'];
	    unset($data['item']);
		$i_purchase_id=$data['i_purchase_id'];
        unset($data['i_purchase_id']);
	    $data['dt_modified']=date('Y-m-d');
	    $data['i_status']=1;
	    $department = $sql->update('purchase_requests')->set($data)->where(array('i_purchase_id' => $i_purchase_id));
	    $selectString = $sql->getSqlStringForSqlObject($department);
	    $result = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        
		if($result) {
            if(isset($items) AND is_array($items)) {
                    $delete = $sql->delete ( 'requested_items' )->where ( array (
                        'i_ref_purchase_id' => $i_purchase_id
                    ) );
                    $selectString = $sql->getSqlStringForSqlObject($delete);
                    $result =$this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
                    
                   
							foreach($items as $item) {
								unset($item['costcenter']);
								$item['i_ref_purchase_id']=$i_purchase_id;
								$department = $sql->insert('requested_items')->values($item);
								$selectString = $sql->getSqlStringForSqlObject($department);
								$result = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
								$i_id=$this->adapter->getDriver()->getLastGeneratedValue();
							}
                }
        } else {
            
            throw new \Exception("Error while adding purchase request");
        }
	    
	}
	
	public function getpurchaserequest($id,$i_purchase_id=NULL) { 
	    		
		$whr=array('purchase_requests.i_ref_company_id'=>$id,'purchase_requests.i_status'=>1);
		if(!empty($i_purchase_id)) {
			$whr['purchase_requests.i_purchase_id']=$i_purchase_id;
		}
	    $sql = new Sql($this->adapter);
	    $select = $sql->select('purchase_requests');
		$select->join('users', 'users.i_user_id=purchase_requests.i_ref_originator_id', array('vc_originator_fname'=>'vc_fname','vc_originator_lname'=>'vc_lname','vc_originator_email'=>'vc_email'),'left');
	    $select->join('suppliers', 'suppliers.i_supplier_id=purchase_requests.i_ref_supplier_id', array('vc_supplier_fname'=>'vc_fname','vc_supplier_lname'=>'vc_lname'),'left');
		$select->join('wbs', 'wbs.i_rec_id=purchase_requests.i_ref_wbs_id', array('vc_wbs_name'=>'vc_name'),'left');
	    $select->where($whr);
	    $select->order('i_purchase_id');
	    $stmt = $sql->prepareStatementForSqlObject($select);
	    
	    $result = $stmt->execute();
	    if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
	        $returnData= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
	        
	        foreach($returnData as $key=>$returnData_items) {
	            $select = $sql->select('requested_items');
	            $select->join('cost_center', 'cost_center.i_cc_id=requested_items.i_ref_cc_id', array('vc_cc_name'=>'vc_name','vc_cc_account_number'=>'vc_account_number'),'left');
	            $select->where(array('requested_items.i_ref_purchase_id'=>$returnData_items['i_purchase_id']));
	            $stmt = $sql->prepareStatementForSqlObject($select);
	            
	            $result_items = $stmt->execute();
	            if ($result_items instanceof ResultInterface && $result_items->isQueryResult() && $result_items->getAffectedRows()) {
	               
	                $returnData[$key]['item']= $result_items->getResource()->fetchAll(\PDO::FETCH_ASSOC);
	               
	            }
	        }
	        
	        return $returnData;
	    }
	    return false;
	    
	}
	
	public function checkIfLocal()
	{
	    $whitelist = array(
	        '127.0.0.1'
	    );
	    if(!in_array($this->getServiceLocator()->get('request')->getServer('REMOTE_ADDR'), $whitelist)){
	        return false;
	    }
	    return true;
	}
	
	public function getUserpermissions($userId,$companyId) {
	    
	    $sql = new Sql($this->adapter);
	    $select = $sql->select('user_details');
	    $select->columns(array('i_ref_role_id'));
	    $select->where(array('i_ref_user_id'=>$userId,'user_details.i_ref_company_id'=>$companyId));
        $select->group('user_details.i_ref_role_id');
	    $stmt = $sql->prepareStatementForSqlObject($select);
	    
	    $result = $stmt->execute();
	    if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
	        $returnData= $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);	        
	        foreach($returnData as $returnData) { 
	            $select_child = $sql->select('roles_permission');
	            $select_child->columns(array('i_ref_permission_id'));
	            $select_child->group('i_ref_permission_id');
	            $select_child->where(array('i_ref_role_id'=>$returnData['i_ref_role_id']));
	            $stmt_child = $sql->prepareStatementForSqlObject($select_child);
	            $result_child = $stmt_child->execute();
	            $returnData_child[]= $result_child->getResource()->fetchAll(\PDO::FETCH_ASSOC); 
	        }
	        
	        foreach($returnData_child as $returnData_child) {
	            foreach($returnData_child as $returnData_child_child) {
	                $permissions_array[]=$returnData_child_child['i_ref_permission_id'];
	            }
	        }
	        
	        //$permissions=implode(",", $permissions_array);
	        
	        $select_permissions = $sql->select('permissions');
	        $select_permissions->columns(array('vc_actions','vc_description'));
	        $select_permissions->where(array('i_permission_id'=>$permissions_array));
	        $stmt_permissions = $sql->prepareStatementForSqlObject($select_permissions);
	         
	        $result_permissions = $stmt_permissions->execute();
	        $returnData_permissions= $result_permissions->getResource()->fetchAll(\PDO::FETCH_ASSOC);
	       
	        $all_permissions=array();
	        foreach($returnData_permissions as $returnData_permissions) {
	            $all_permissions_old=explode(",",$returnData_permissions['vc_actions']);
	            $all_permissions=array_merge($all_permissions_old,$all_permissions);
	        }
	        return $all_permissions;
	    } else {
	        return false;
	    }
	}
}
