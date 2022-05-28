<?php
require_once 'Messages.php';
require_once 'Property.php';

class MessagesHelper {
    private $lastCreatedRead = null;
    private $lastDateProcessed = null;
    private $allowedIps = null;
    private $newDay = false;
    private $interval = null;

    public function init() {
        global $Property;
        
        $this->allowedIps = $Property->getProperty(PROP_ALLOWED_IPS);
        $this->lastCreatedRead = "2022-03-18 05:25:01";
        $this->lastDateProcessed = $this->lastCreatedRead;
        $this->interval = DateInterval::createFromDateString('3 hour');
        
    } // init
    
    public function isNewDay() : bool {
        return $this->newDay;
    }
    
    public function isInFirst30Min(object $theDate) : bool {
        global $logger;
        
//         $logger->info("isInFirst30min(" . $theDate->format('Y-m-d H:i:s') . " hour=" . $theDate->format('H'));
        if ($theDate->format('H') == 9) {
//             $logger->info("returning true");
            return true;
        }
//         $logger->info("returning false");
        return false;
    }

    public function isLast15Min(object $theDate) : bool {
        global $logger;
//         $logger->info("isLast15Min(" . $theDate->format('Y-m-d H:i:s') . " hour=" . $theDate->format('H') . " min=" . $theDate->format('i'));
        if ($theDate->format('H') == 15 && $theDate->format('i') >= 45) {
//             $logger->info("returning true");
            return true;
        }
//         $logger->info("returning false");
        return false;
    }
    
    public function getMessageFromDb() {
//         global $logger;
        global $Messages;
        
        while (true) {
            // keep getting the message from db - until we have a valid ip
//             $logger->info("getNextMessage() lastCreatedRead=" . $this->lastCreatedRead);
            $theMessages = $Messages->getNextMessage($this->lastCreatedRead);
            //         $logger->info("type " . gettype($thisMessage));
            //         $logger->info("read " . json_encode($thisMessage));
//             $logger->info("thisIp {$theMessages[0]['message_from']} allowed=$this->allowedIps");
            $this->lastCreatedRead = $theMessages[0]['created'];
            if (str_contains($this->allowedIps, $theMessages[0]['message_from'])) {
                return $theMessages[0];
            }
        }
    } // getMessageFromDb
    
    public function getTimeInEastern(string $theTime) {
        $timeInEastern = DateTime::createFromFormat('Y-m-d H:i:s', $theTime);
        $timeInEastern->add($this->interval);
        return $timeInEastern->format('Y-m-d H:i:s');
    }
    
    public function getNextMessage() {
        global $logger;
    
        $thisMessage = $this->getMessageFromDb();
        
        $timeInEastern = DateTime::createFromFormat('Y-m-d H:i:s', $thisMessage['created']);
        $timeInEastern->add($this->interval);
        $thisMessage['timeInEastern'] = $timeInEastern->format('Y-m-d H:i:s');
        
        
        $thisDate = substr($thisMessage['timeInEastern'], 0, 10);
        $lastDate = substr($this->lastDateProcessed, 0, 10);
        if ($thisDate == $lastDate) {
            $this->newDay = false;
        } else {
            $this->newDay = true;
        }
//         $logger->info("this=$thisDate last=$lastDate newDay=$this->newDay");
        $this->lastDateProcessed = $thisMessage['timeInEastern'];
        return $thisMessage;
    } // getNextMessage

}

$MessagesHelper = new MessagesHelper();
$MessagesHelper->init();
?>
