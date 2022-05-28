<?php
require_once 'Db.php';


class ApiHistory extends Db {

    
    public function getDataByTypeAndDates(string $suffix, string $type, string $before, string $after) {
        global $logger;
  
        // Create connection
        parent::getConnection('data', __FILE__, __LINE__);
        
        $sql = "SELECT * FROM mp_apihistory$suffix where message_type=? and created >= ? and created <= ? order by created ";
        parent::prepareStatement($sql, __FILE__, __LINE__);
        $this->stmt->bind_param("sss", $type, $before, $after);
        $results = parent::executeAndGetData(__FILE__, __LINE__);
        return $results;
    } // getDataByTypeAndDates
    
 
  public function getDataByUserAndDate(int $user, string $lookingForCreated) {
    global $logger;

    $time = DateTime::createFromFormat("Y-m-d H:i:s", $lookingForCreated);
    $endTime = $time->add(new DateInterval('PT' . 5 . 'S'))->format('Y-m-d H:i:s');
    $logger->info("user=" . $user . " start=" . $lookingForCreated . " end=" . $endTime);
 
    // Create connection
    parent::getConnection('data', __FILE__, __LINE__);

    $sql = "SELECT created, id, message_received, message_sent, message_type, updated, user FROM mp_apihistory where user=? and created >= ? and created <= ? order by created ";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("iss", $user, $lookingForCreated, $endTime);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    return $results;
  } // getData

   
  public function getDataByUserAndId(int $lookingForUser, int $lookingForId) {
    global $logger;

    $logger->info("user=$lookingForUser id=$lookingForId");

    // Create connection
    parent::getConnection('data', __FILE__, __LINE__);

    $sql = "SELECT created, id, message_received, message_sent, message_type, updated, user FROM mp_apiHistory where user=? and id >= ?";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("ii", $lookingForUser, $lookingForId);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    return $results[0];
  } // getDataByUserAndId

  public function getDataByUserAndType(int $lookingForUser, string $lookingForType, string $suffix = "" ) {
    global $logger;

    $logger->trace("getDataByUserAndType($lookingForUser, $lookingForType)");

    // Create connection
    parent::getConnection('data', __FILE__, __LINE__);

    $sql = "SELECT * FROM mp_apihistory$suffix where user=? and message_type = ? order by created";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("is", $lookingForUser, $lookingForType);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    return $results;
  } // getDataByUserAndType

   
  public function getDataByTxId(string $yearWeek, string $lookingForTx) {
    global $logger;

    $logger->info("getDataByTxId($yearWeek, $lookingForTx)");

    // Create connection
    parent::getConnection('data', __FILE__, __LINE__);

    $sql = "SELECT * FROM mp_apiHistory_$yearWeek where tx = ?";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("s", $lookingForId);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    return $results;
  } // getDataByUserAndId


  public function saveTheApiData(string $messagereceived, string $messageSent, string $messageStatus, string $messageType, int $userId) {
    global $logger;
    global $txId;

    $logger->trace("ApiHistory:saveTheApiData messageType=" . $messageType);

    // Create connection
    parent::getConnection('data', __FILE__, __LINE__);

    $sql = "insert mp_apihistory(message_received, message_sent, message_status, message_type, user, tx) values (?, ?, ?, ?, ?, ?)";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("ssssis", $messagereceived, $messageSent, $messageStatus,  $messageType,  $userId, $txId);
    $idInserted = parent::executeInsert(__FILE__, __LINE__);
    return $idInserted;
  } // saveTheApiData

}

$ApiHistory = new ApiHistory();
?>
