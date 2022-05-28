<?php
require_once 'Db.php';

class Property extends Db {

  public function setProperty(string $name, string $value) {
    global $logger;

    $logger->info("setProperty(" . $name . ", " . $value . ")");

    // Create connection
    parent::getConnection('prop', __FILE__, __LINE__);

    $sql = "INSERT INTO mp_properties (name, value) VALUES(?, ?) ON DUPLICATE KEY UPDATE   
    name=?, value=?, updated=now()";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("ssss", $name, $value, $name, $value);
    $idInserted = parent::executeInsert(__FILE__, __LINE__);
    return $idInserted;
  } // setProperty

  public function getProperty(string $name) {
    // Create connection
    parent::getConnection('prop', __FILE__, __LINE__);

    $sql = "SELECT value FROM mp_properties WHERE name=?";
    parent::prepareStatement($sql, __FILE__, __LINE__);
    $this->stmt->bind_param("s", $name);
    $results = parent::executeAndGetData(__FILE__, __LINE__);
    if (isset($results) && count($results) > 0) {
      return $results[0]['value'];
    }
    return null;
  } // getProperty

}

$Property = new Property();
?>
