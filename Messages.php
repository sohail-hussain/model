<?php
require_once 'Db.php';

class Messages extends Db {

    public function getNextMessage(string $lastDate) {
        global $logger;
        
        $logger->trace("getNextMessage($lastDate)");
        
        // Create connection
        parent::getConnection('ui', __FILE__, __LINE__);
        $sql = "SELECT * FROM mp_messages where created > ? order by created asc limit 1";
        parent::prepareStatement($sql, __FILE__, __LINE__);
        $this->stmt->bind_param("s", $lastDate);
        $results = parent::executeAndGetData(__FILE__, __LINE__);
        return $results;
    } // getNextMessage

  public function getData(int $offset, int $rowCount) {
    global $logger;

    $logger->info("getData($offset, $rowCount)");

    // Create connection
    parent::getConnection('ui', __FILE__, __LINE__);
    $sql = "SELECT * FROM mp_messages order by created asc limit ?, ?";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("ii", $offset, $rowCount);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    return $results;
  } // getData

  public function getMessagesByDate(string $theDate) {
    global $logger;

    //$logger->info("getMessagesByDate()");

    // Create connection
    parent::getConnection('ui', __FILE__, __LINE__);

    $sql = "SELECT * FROM mp_messages where created >= ? and created <=? order by created";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $start = $theDate . " 00:00:00";
    $end = $theDate . " 23:00:00";
    $this->stmt->bind_param("ss", $start ,$end);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    return $results;
  } // getMessagesByDate
 

  public function getTodaysMessages() {
    global $logger;

    // Create connection
    parent::getConnection('ui', __FILE__, __LINE__);
    $sql = "SELECT * FROM mp_messages where Date(created) = current_date() order by id asc";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    return $results;
  } // getTodaysMessages

  public function clearTodaysMessages() {
    global $logger;
    global $txId;

    $logger->trace("clearTodaysMessages()");
    // Create connection
    parent::getConnection('ui', __FILE__, __LINE__);
    $sql = "update `mp_messages` set cleared = 1, tx_update = ?, updated=now() where Date(created) = current_date()";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("s", $txId);
    $status = parent::executeUpdate(__FILE__, __LINE__);
    return $status;
  } // clearTodaysMessages

  public function getCountOfMessageToday() {
    global $logger;

    $logger->trace("getCountOfMessageToday()");
    // Create connection
    parent::getConnection('ui', __FILE__, __LINE__);
    $sql = "SELECT count(*) as count FROM `mp_messages` where Date(created) = current_date() and cleared = 0";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    if (isset($results) && count($results) > 0) {
      return $results[0]['count'];
    }
    $logger.error("no messages found");
    return 0;
  } // getCountOfMessageToday

  public function getCountOfMessageTodayAfter10() {
    global $logger;

    $logger->trace("getCountOfMessageTodayAfter10()");
    // Create connection
    parent::getConnection('ui', __FILE__, __LINE__);
    $sql = "SELECT count(*) as count FROM `mp_messages` where Date(created) = current_date() and cleared = 0 and after_10 = 1";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    if (isset($results) && count($results) > 0) {
      return $results[0]['count'];
    }
    $logger.error("no messages found");
    return 0;
  } // getCountOfMessageTodayAfter10

  public function saveTheMessageData(string $actionTaken, string $messageBody, string $messageFrom, string $messageType, int $after10) {
    global $txId;
    // Create connection
    parent::getConnection('ui', __FILE__, __LINE__);

    $sql = "insert mp_messages(action_taken, message_body, message_from, message_type, after_10, tx, tx_update) values (?, ?, ?, ?, ?, ?, ?)";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("ssssiss", $actionTaken, $messageBody, $messageFrom, $messageType, $after10, $txId, $txId);
    $idInserted = parent::executeInsert(__FILE__, __LINE__);
    return $idInserted;
  } // saveTheMessageData

} // Messages

$Messages = new Messages();
?>
