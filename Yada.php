<?php

require_once 'MessagesHelper.php';
$MessagesHelper = new MessagesHelper();
$MessagesHelper->init();

require_once 'ApiHistoryHelper.php';
require_once 'BuySellHelper.php';
$BuySellHelper = new BuySellHelper();

$logger = Logger::getLogger('mypredictory');

print("hello\n");
$endOfData = false;
$numberOfAlertsSeenSince10Am = 0;

$BuySellHelper->setBalance(2500.0);
$BuySellHelper->setCutoffPercentage(50.0);
$BuySellHelper->setPercentage(5.0);
$BuySellHelper->setThreshold(1.25);
$skipFirst30Min = true;
$skipFirstMessage = true;
$doNotBuyAtEnd = true;
$sellAtEndOfDay = true;

$nProcessed = 0;
while (true) {
    $thisMessage = $MessagesHelper->getNextMessage();
    $nProcessed++;
    if ($nProcessed > 2500) {
        break;
    }
    
    
    
    if ($thisMessage == null) {
        $BuySellHelper->sellAll("12:45:00", "15:45:00");
        exit("end of data");
    }
//     if (isset($thisMessage['tx'])) {
//         $tx = $thisMessage['tx'];
//     } else {
//         $tx = null;
//     }
    $created = DateTime::createFromFormat('Y-m-d H:i:s', $thisMessage['created']);
    $timeInEastern = DateTime::createFromFormat('Y-m-d H:i:s', $thisMessage['timeInEastern']);
    $yearWeek = $created->format("YW");
//     $logger->info("type yearWeek " . gettype($yearWeek));
    $logger->info("$nProcessed: nAlertsAfter10: $numberOfAlertsSeenSince10Am type={$thisMessage['message_type']} yearWeek=$yearWeek timeInEastern=" . $timeInEastern->format('Y-m-d H:i:s'));

    
    if ($MessagesHelper->isNewDay() === true) {
        $logger->info("found new day");
        if ($sellAtEndOfDay) {
            $BuySellHelper->sellAll("12:45:00", "15:45:00");
        } else {
            exit("do not know what to do with sellAtEndOfDay=$sellAtEndOfDay");
        }
        $numberOfAlertsSeenSince10Am = 0;
    }
    
//     $thisHistory = $ApiHistoryHelper->getTheApiHistory($yearWeek, "getOptionChain", $thisMessage['created']);
//     if ($thisHistory == null) {
// //         $logger->info("skipping.no chain... created={$thisMessage['created']}");
//         continue;
//     }
    if ($MessagesHelper->isMarketOpen($timeInEastern) === false) {
        $logger->info("market is closed");
//         $logger->info("skipping first 30 min =" . $MessagesHelper->isInFirst30Min($timeInEastern) . "=");
        continue;
    }
    if ($MessagesHelper->isInFirst30Min($timeInEastern) === true && $skipFirst30Min) {
        $logger->info("skipping first 30 min");
        $logger->info("skipping first 30 min =" . $MessagesHelper->isInFirst30Min($timeInEastern) . "=");
        continue;
    }
    if ($MessagesHelper->isLast15Min($timeInEastern) === true && $doNotBuyAtEnd) {
        $logger->info("skipping last 15 mins");
        continue;
    }
    $numberOfAlertsSeenSince10Am++;
    if ($numberOfAlertsSeenSince10Am == 1 && $skipFirstMessage) {
        $logger->info("skipping first alert after 10 AM");
        continue;
    }
//     $logger->info("would buy now {$thisMessage['message_type']}");
    $BuySellHelper->processMessage($thisMessage);
//     if (count($messages) != $size || count($messages) == 0 || $nProcessed > 0) {
//         break;
//     } else {
//         $start += $size;
//     }
} // while true
print("world\n");
?>