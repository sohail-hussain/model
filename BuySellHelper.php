<?php
require_once 'TdaOptionChain.php';
require_once 'ApiHistoryHelper.php';

class BuySellHelper {
//     private $interval = null;
    private $balance = 2500.0;
    private $percentage = 5;
    private $cutoffPercentage = 50;
    private $threshold = 50;
    private $lastAction = "";
    private $bestLast = "";
    private $bestStrikePrice = "";
    private $bestSymbol = "";
    private $purchaseTime;
    
    public function setBalance(float $value) {
        $this->balance = $value;
    }
    public function setPercentage(float $value) {
        $this->percentage = $value;
    }
    public function setCutoffPercentage(float $value) {
        $this->cutoffPercentage = $value;
    }
    public function setThreshold(float $value) {
        $this->threshold = $value;
    }
    
    public function sellAll(string $theTime) {
        global $ApiHistoryHelper;
        global $logger;
        
        $logger->info("sellAll($theTime) purchaseTime=" . $this->purchaseTime);
        
        if ($this->bestLast == "") {
            return;
        }
        $sellTime = substr($this->purchaseTime, 0, 10) . ' ' . $theTime;
        $created = DateTime::createFromFormat('Y-m-d H:i:s', $sellTime);
        $yearWeek = $created->format("YW");
        $thisHistory = $ApiHistoryHelper->getTheApiHistory($yearWeek, "getOptionChain", $sellTime);
        
        if ($thisHistory == null) {
            $this->balance += 100 * $this->bestLast;
            print("date|method|action|symbol|last|strike|balance\n");
            print("$this->purchaseTime|sell|$this->lastAction|$this->bestSymbol|$" . number_format($this->bestStrikePrice,2));
            print("|$" . number_format($this->bestLast, 2) . "|$" . number_format($this->balance, 2) . "|Cannot found optionChain\n");
            $this->lastAction = "";
        } else {
            $this->sell(getTimeInEastern($sellTime->format('Y-m-d H:i:s')), $thisHistory);
            exit("sellAll");
        }
    } // sellAll
    
    public function sell(string $theDateTimeString, array $optionChain) {
        global $TdaOptionChain;
        global $logger;
        
        $logger->info("sell($theDateTimeString)");
        $optionsFound = $TdaOptionChain->findOption($this->lastAction, $optionChain, $this->bestSymbol);    
        $logger->info('action:' . $optionsFound['action']);
        $logger->info('bestLast:' . $optionsFound['last']);
        $logger->info('bestStrikePrice:' . $optionsFound['strikePrice']);
        $logger->info('bestSymbol:' . $optionsFound['symbol']);
        
        $this->balance += 100 * $optionsFound['last'];
        print("date|method|action|symbol|last|strike|balance\n");
        print("$this->purchaseTime|sell|$this->lastAction|$this->bestSymbol|$" . number_format($optionsFound['strikePrice'],2));
        print("|$" . number_format($optionsFound['last'], 2) . "|$" . number_format($this->balance, 2) . "\n");
        $this->lastAction = ""; // we sold
    } // sell
    
    public function processMessage(array $thisMessage, array $callChain) {
        global $logger;
        global $TdaOptionChain;
//         global $ApiHistory;
        if ($thisMessage['message_type'] == "alertFromTradingView PUT") {
            $action = "PUT";
            $otherAction = "CALL";
        } else {
            $action = "CALL";
            $otherAction = "PUT";
        }
        if ($this->lastAction == $action) {
            exit("we have a disaster");
        }
        if ($this->lastAction == $otherAction) {
            // sell
            $this->sell($thisMessage['timeInEastern'], $callChain);
        }
        // buy
        $optionsFound = $TdaOptionChain->findBestOption($action, $callChain, $this->threshold);
        $this->lastAction = $action;
        $this->bestLast = $optionsFound['last'];
        $this->bestStrikePrice = $optionsFound['strikePrice'];
        $this->bestSymbol = $optionsFound['symbol'];
        $this->purchaseTime = $thisMessage['timeInEastern'];
//         $logger->info('action:' . $optionsFound['action']);
//         $logger->info('bestLast:' . $optionsFound['last']);
//         $logger->info('bestStrikePrice:' . $optionsFound['strikePrice']);
//         $logger->info('bestSymbol:' . $optionsFound['symbol']);
        $this->balance -= 100 * $this->bestLast;
        print("date|method|action|symbol|last|strike|balance|comment\n");
        print("$this->purchaseTime|buy|$action|$this->bestSymbol|$" . number_format($this->bestStrikePrice,2));
        print("|$" . number_format($this->bestLast, 2) . "|$" . number_format($this->balance, 2) . "\n");
    } // processMessage
}

$BuySellHelper = new BuySellHelper();
// $BuySellHelper->init();
?>
