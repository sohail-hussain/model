<?php
require_once 'TdaOptionChain.php';
require_once 'ApiHistoryHelper.php';

class BuySellHelper
{

    // private $interval = null;
    private $balance = 2500.0;
    private $percentage = 5;
    private $cutoffPercentage = 50;
    private $threshold = 50;
    private $lastAction = "";
    private $bestLast = "";
    private $bestStrikePrice = "";
    private $bestSymbol = "";
    private $purchaseTimeInEastern;
    private $purchaseTimeInPhoenix;

    public function setBalance(float $value)
    {
        $this->balance = $value;
    }

    public function setPercentage(float $value)
    {
        $this->percentage = $value;
    }

    public function setCutoffPercentage(float $value)
    {
        $this->cutoffPercentage = $value;
    }

    public function setThreshold(float $value)
    {
        $this->threshold = $value;
    }

    public function sellAll(string $phxTime, string $easternTime)
    {
        global $logger;

        $logger->info("sellAll($phxTime, $easternTime) purchaseTime=" . $this->purchaseTimeInEastern);

        $phoenixTime = substr($this->purchaseTimeInPhoenix, 0, 10) . ' ' . $phxTime;
        $easternTime = substr($this->purchaseTimeInEastern, 0, 10) . ' ' . $easternTime;
        $this->sell($this->lastAction, $phoenixTime, $easternTime);
    }

    // sellAll
    public function sell(string $action, string $timeInPhoenix, string $timeInEastern)
    {
        global $TdaOptionChain;
        global $ApiHistoryHelper;
        global $logger;

        if ($this->lastAction == "") {
            return; // nothing to sell
        }
        $logger->info("sell($action, $timeInPhoenix, $timeInEastern)");
        $callChain = $ApiHistoryHelper->getTheApiHistory($action, "getOptionChain", $timeInPhoenix);
        if ($callChain == null) {
            $logger->info("cannout sell $action phx=$timeInPhoenix est=$timeInEastern - no optionChainFound");
            $this->balance += 100 * $this->bestLast;
            print("date|method|action|symbol|last|strike|balance\n");
            print("$timeInEastern|sell|$this->lastAction|$this->bestSymbol|$" . number_format($this->bestStrikePrice, 2));
            print("|$" . number_format($this->bestLast, 2) . "|$" . number_format($this->balance, 2) . "|Cannot found optionChain\n");
            $this->lastAction = "";

            $this->lastAction = ""; // we sold
            return; // cannot buy
        }

        // $logger->info("sell($theDateTimeString)");
        $optionsFound = $TdaOptionChain->findOption($this->lastAction, $callChain, $this->bestSymbol);
        $logger->info('action:' . $optionsFound['action']);
        $logger->info('bestLast:' . $optionsFound['last']);
        $logger->info('bestStrikePrice:' . $optionsFound['strikePrice']);
        $logger->info('bestSymbol:' . $optionsFound['symbol']);

        $this->balance += 100 * $optionsFound['last'];
        print("date|method|action|symbol|last|strike|balance\n");
        print("$timeInEastern|sell|$this->lastAction|$this->bestSymbol|$" . number_format($optionsFound['strikePrice'], 2));
        print("|$" . number_format($optionsFound['last'], 2) . "|$" . number_format($this->balance, 2) . "\n");
        $this->lastAction = ""; // we sold
    }

    // sell
    public function buy(string $action, string $timeInPhoenix, string $timeInEastern)
    {
        global $ApiHistoryHelper;
        global $TdaOptionChain;
        global $logger;

        $logger->info("buy($action, $timeInPhoenix, $timeInEastern)");
        $callChain = $ApiHistoryHelper->getTheApiHistory($action, "getOptionChain", $timeInPhoenix);
        if ($callChain == null) {
            $logger->info("cannout buy $action phx=$timeInPhoenix est=$timeInEastern - no optionChainFound");
            return; // cannot buy
        }
        $optionsFound = $TdaOptionChain->findBestOption($action, $callChain, $this->threshold);
        $this->lastAction = $action;
        $this->bestLast = $optionsFound['last'];
        $this->bestStrikePrice = $optionsFound['strikePrice'];
        $this->bestSymbol = $optionsFound['symbol'];
        $this->purchaseTimeInEastern = $timeInEastern;
        $this->purchaseTimeInPhoenix = $timeInPhoenix;
        // $logger->info('action:' . $optionsFound['action']);
        // $logger->info('bestLast:' . $optionsFound['last']);
        // $logger->info('bestStrikePrice:' . $optionsFound['strikePrice']);
        // $logger->info('bestSymbol:' . $optionsFound['symbol']);
        $this->balance -= 100 * $this->bestLast;
        print("date|method|action|symbol|last|strike|balance|comment\n");
        print("$this->purchaseTimeInEastern|buy|$action|$this->bestSymbol|$" . number_format($this->bestStrikePrice, 2));
        print("|$" . number_format($this->bestLast, 2) . "|$" . number_format($this->balance, 2) . "\n");
    }

    // sell
    public function processMessage(array $thisMessage)
    {
//         global $logger;
//         global $TdaOptionChain;
        // global $ApiHistory;
        
        if (stripos($thisMessage['message_type'], "PUT")) {
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
            $this->sell($otherAction, $thisMessage['created'], $thisMessage['timeInEastern']);
        }
        $this->buy($action, $thisMessage['created'], $thisMessage['timeInEastern']);
        // buy
    } // processMessage
}


// $BuySellHelper->init();
?>
