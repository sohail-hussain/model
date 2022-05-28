<?php
require_once 'ApiHistory.php';

class ApiHistoryHelper {
    private $interval = null;

    public function init() {
        $this->interval = DateInterval::createFromDateString('1 minute');        
    } // init
    
    public function getTheApiHistory(string $action, string $type, string $theDate) {
//         global $logger;
        global $ApiHistory;
        

        $before = DateTime::createFromFormat('Y-m-d H:i:s', $theDate);
        $yearWeek = $before->format("YW");
        $after = $before;
        $before->sub($this->interval);
        $after->add($this->interval);
        $apiHistories = $ApiHistory->getDataByTypeAndDates('_' . $yearWeek, $type, $before->format('Y-m-d H:i:s'), $after->format('Y-m-d H:i:s'));
        foreach ($apiHistories as $thisHistory) {
            $theJson = json_decode($thisHistory['message_received'], true);
            if ($action == 'CALL' && isset($theJson['callExpDateMap']) && strlen(json_encode($theJson['callExpDateMap'])) > 2) {
                return $theJson;
//             }
//                 $hasCall = true;
//             } else {
//                 $hasCall = false;
//             }
            } else if (isset($theJson['putExpDateMap']) && strlen(json_encode($theJson['putExpDateMap'])) > 2) {
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
