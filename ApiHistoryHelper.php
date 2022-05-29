<?php
require_once 'ApiHistory.php';

class ApiHistoryHelper {
    private $interval = null;

    public function init() {
        $this->interval = DateInterval::createFromDateString('1 minute');        
    } // init
    
    public function getTheApiHistory(string $action, string $type, string $theDate) {
        global $logger;
        global $ApiHistory;

        $logger->info("getTheApiHistory($action, $type, $theDate)");

        $before = DateTime::createFromFormat('Y-m-d H:i:s', $theDate);
        $before->sub($this->interval);
        
        $after = DateTime::createFromFormat('Y-m-d H:i:s', $theDate);
        $after->add($this->interval);
        
        $yearWeek = $before->format("YW");

        $apiHistories = $ApiHistory->getDataByTypeAndDates('_' . $yearWeek, $type, $before->format('Y-m-d H:i:s'), $after->format('Y-m-d H:i:s'));
        $logger->info("n histories found " . count($apiHistories));
        
        foreach ($apiHistories as $thisHistory) {
            $theJson = json_decode($thisHistory['message_received'], true);
//             $logger->info("type ofthe json " . gettype($theJson));
            $logger->info("just read id={$thisHistory['id']} time={$thisHistory['created']} len=" . strlen($thisHistory['message_received']) . " call=" . isset($theJson['callExpDateMap']) . " put=" . isset($theJson['putExpDateMap']));
            if ($action == 'CALL' && isset($theJson['callExpDateMap']) && strlen(json_encode($theJson['callExpDateMap'])) > 2) {
//                 $logger->info("id= call len=" .  strlen(json_encode($theJson['callExpDateMap'])));
                return $theJson;
//             }
//                 $hasCall = true;
//             } else {
//                 $hasCall = false;
//             }
            } else if ($action == 'PUT' && isset($theJson['putExpDateMap']) && strlen(json_encode($theJson['putExpDateMap'])) > 2) {
//                 $logger->info("id= put-$action- len=" .  strlen(json_encode($theJson['putExpDateMap'])));
                return $theJson;
//             } else {
//                 $hasPut = false;
//             }
//             $hasCall = isset($theJson['callExpDateMap']);
//             $hasPut = isset($theJson['putExpDateMap']);
//             if ($hasCall) $logger->info("callExpDateMap: " . strlen(json_encode($theJson['callExpDateMap'])));
//             if ($hasPut) $logger->info("putExpDateMap: " . strlen(json_encode($theJson['putExpDateMap'])));
//             $logger->info("hasPut=$hasPut and hasCall=$hasCall");
//             if ($hasCall && $hasPut) {
// //                 exit("have chains");
//                 return $theJson;
//             }
            }
        }
        return null;
    } // getTheApiHistory
}

$ApiHistoryHelper = new ApiHistoryHelper();
$ApiHistoryHelper->init();
?>
