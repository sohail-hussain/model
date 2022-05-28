<?php
require_once 'ApiHistory.php';

class ApiHistoryHelper {
    private $interval = null;

    public function init() {
        $this->interval = DateInterval::createFromDateString('1 minute');        
    } // init
    
    public function getTheApiHistory(string $suffix, string $type, string $theDate) {
        global $logger;
        global $ApiHistory;
        
        $before = DateTime::createFromFormat('Y-m-d H:i:s', $theDate);
        $after = $before;
        $before->sub($this->interval);
        $after->add($this->interval);
        $apiHistories = $ApiHistory->getDataByTypeAndDates('_' . $suffix, $type, $before->format('Y-m-d H:i:s'), $after->format('Y-m-d H:i:s'));
        foreach ($apiHistories as $thisHistory) {
            $theJson = json_decode($thisHistory['message_received'], true);
            $hasCall = isset($theJson['callExpDateMap']);
            $hasPut = isset($theJson['putExpDateMap']);
//             $logger->info("hasPut=$hasPut and hasCall=$hasCall");
            if ($hasCall && $hasPut) {
//                 exit("have chains");
                return $theJson;
            }
        }
        return null;
    } // getTheApiHistory
}

$ApiHistoryHelper = new ApiHistoryHelper();
$ApiHistoryHelper->init();
?>
