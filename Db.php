<?php
require_once 'configure.php';
require_once 'baseSetup.php';

class DB {
  var $conn;
  var $stmt;

  public function getConnection(string $type, string $fileName, string $lineNumber) {
    global $db_servername, $db_username, $db_password, $db_dbname;
    global $ui_servername, $ui_username, $ui_password, $ui_dbname;
    global $prop_servername, $prop_username, $prop_password, $prop_dbname;
    global $logger;

    //$logger->info("db_servername=$db_servername db_username=$db_username db_password=$db_password db_dbname=$db_dbname");
    // $logger->info("getConnection called from " . debug_backtrace()[1]['function']);
    // Create connection
    if ($type == 'ui') {
      $logger->trace("type=$type db=$ui_dbname user=$ui_username server=$ui_servername");
      $this->conn = new mysqli($ui_servername, $ui_username, $ui_password, $ui_dbname);
    } else if ($type == 'prop') {
      $logger->trace("type=$type db=$prop_dbname user=$prop_username server=$prop_servername");
        $this->conn = new mysqli($prop_servername, $prop_username, $prop_password, $prop_dbname);
    } else {
      // $logger->info("type==$type==" . bin2hex($type) . "==");
      // $logger->info("type=$type db=sohaildb user=sohailUser server=p3plzcpnl489508");
      // $this->conn = new mysqli("p3plzcpnl489508", "sohailuser", "4~;KwMffWf4_", "sohaildb");
      // $this->conn = new mysqli($db_servername, "sohailuser", "4~;KwMffWf4_", $db_dbname);
      $logger->trace("type=$type db=$db_dbname user=$db_username server=$db_servername");
      $this->conn = new mysqli($db_servername, $db_username, $db_password, $db_dbname);

    }

    // Check connection
    if ($this->conn->connect_error) {
      $message = "$fileName:$lineNumber:  Connection failed: " . $this->conn->connect_error;
      $logger.error($message);
      die($message);
    }
//    $logger->info(json_encode($this->conn));

  } // getConnection

  public function prepareStatement(string $sql, string $fileName, string $lineNumber) {
    global $logger;
    $this->stmt = $this->conn->prepare($sql);
    if ($this->stmt === false) {
      $message = "$fileName:$lineNumber: prepare failed: " . json_encode($this->stmt) . " sql=$sql";
      $logger->error($message);
      $logger->error($this->conn->error);
      die($message);
    }
  } // prepareStatement

  public function executeAndGetData(string $fileName, string $lineNumber) {
    $this->stmt->execute();
    $returnData = array();
    $this->stmt->store_result();
    for ($i = 0; $i < $this->stmt->num_rows; $i++)
    {
        $metadata = $this->stmt->result_metadata();
        $params = array();
        while ($field = $metadata->fetch_field())
        {
            $params[] = &$returnData[$i][$field->name];
        }
        call_user_func_array(array($this->stmt, 'bind_result'), $params);
        $this->stmt->fetch();
    }
    $this->conn->close();
    if (isset($returnData)) {
      return $returnData;
    } else {
      return null;
    }
  } // prepareStatement

  
  public function executeInsert(string $fileName, string $lineNumber) {
    global $logger;

    $status = $this->stmt->execute();
    if ($status === false) {
      $message = "$fileName:$lineNumber:  insert failed: " . json_encode($this->stmt);
      $logger->error($message);
      $logger->error($this->conn->error);
      die($message);
    } else {
      $idInserted = $this->stmt->insert_id;
      // $logger->info("idInserted=$idInserted");
    }
    $this->conn->close();
    return $idInserted;
  } // executeInsert

  public function executeUpdate(string $fileName, string $lineNumber) {
    global $logger;

    $status = $this->stmt->execute();
    $this->conn->close();
    if ($status === false) {
      $message = "$fileName:$lineNumber: update error: " . json_encode($this->stmt);
      $logger->error($message);
      die($message);
    }
    return $status;
  } // executeUpdate
}
?>
