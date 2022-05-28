<?php

require_once 'baseSetup.php';
// require_once 'ApiHistory.php';
// require_once 'Curl.php';

class TdaOptionChain {
 

  ////////////////////////////////
  //
  public function findBestOption(String $action, Array $callChain, Float $priceThreshold) {
    global $logger;

    $logger->trace("findBestOption(act=$action, callChain, priceT=$priceThreshold)");

    $logger->info("callChain: " . strlen(json_encode($callChain)));

    $numberOfContracts = $callChain['numberOfContracts'];
    if ($action == "PUT") {
        $expDateMap = $callChain['putExpDateMap'];
    } else {
        $expDateMap = $callChain['callExpDateMap'];
    }
    $logger->info("expDateMap: " . strlen(json_encode($expDateMap)));
    if (strlen(json_encode($expDateMap)) == 2) {
        $logger->info("callChain: " . json_encode($callChain));
    }

    // ---- inside the expDateMap is one field, but the name contains the date
    foreach($expDateMap as $key => $value) {
        $dateMap = $value;
    }
    //$logger->info("2 dateMap  " . strlen(json_encode($dateMap)));
    // $logger->info("2 dateMap  " . json_encode($dateMap));

    ///////// ---- iside the dateMap there are numberOfContracts entires, all of size ~923
    //////// each is of the form {"200.0", [...]}
    $bestSymbol = 'unknown';
    $bestStrikePrice = 9999.99;
    $bestLast = 9999.99;
    $bestDelta = 9999.99;
    foreach($dateMap as $price => $valueAtPrice) {
      $logger->trace("3 price=" . $price . " valueAtPrice " . json_encode($valueAtPrice));
      // since each element only has 1 value in the array
      $actualValueAtPrice = $valueAtPrice[0];
      $thisSymbol = $actualValueAtPrice['symbol'];
      $thisLast = $actualValueAtPrice['last'];
      $thisStrikePrice = $actualValueAtPrice['strikePrice'];

      // $logger->info("priceThreshold=" . $priceThreshold . " abs=" . abs($priceThreshold - $thisLast));
      if ($thisLast != 0 AND abs($priceThreshold - $thisLast) < $bestDelta) {
          $bestDelta = abs($priceThreshold - $thisLast);
          $bestLast = $thisLast;
          $bestStrikePrice = $thisStrikePrice;
          $bestSymbol = $thisSymbol;
      }


      // $logger->info("3 price=" . $price .
      //   " thisSymbol=" . $thisSymbol . " bestSymbol=" . $bestSymbol .
      //   " thisStrikePrice=" . $thisStrikePrice . " bestStrikePrice=" . $bestStrikePrice .
      //   " thisLast=" . $thisLast . " bestLast=" . $bestLast);
    }


    $returnValue['action'] = $action;
    $returnValue['last'] = $bestLast;
    $returnValue['strikePrice'] = $bestStrikePrice;
    $returnValue['symbol'] = $bestSymbol;

    return $returnValue;
  } // findBestOption

  ////////////////////////////////
  //
  public function findOption(String $action, Array $callChain, string $symbolToSell) {
    global $logger;

    $logger->info("findOption(act=$action, callChain, sym=$symbolToSell)");
    // $logger->info("callChain: " . json_encode($callChain));

    $numberOfContracts = $callChain['numberOfContracts'];
    if ($action == "PUT") {
        $expDateMap = $callChain['putExpDateMap'];
    } else {
        $expDateMap = $callChain['callExpDateMap'];
    }

    // ---- inside the expDateMap is one field, but the name contains the date
    foreach($expDateMap as $key => $value) {
        $dateMap = $value;
    }
    //$logger->info("2 dateMap  " . strlen(json_encode($dateMap)));
    // $logger->info("2 dateMap  " . json_encode($dateMap));
    // $logger->info("dateMap: " . json_encode($dateMap));
    ///////// ---- iside the dateMap there are numberOfContracts entires, all of size ~923
    //////// each is of the form {"200.0", [...]}

    foreach($dateMap as $price => $valueAtPrice) {
      $logger->trace("3 price=" . $price . " valueAtPrice " . json_encode($valueAtPrice));
      // since each element only has 1 value in the array
      $actualValueAtPrice = $valueAtPrice[0];
      if ($symbolToSell == $actualValueAtPrice['symbol']) {
        $returnValue['action'] = $action;
        $returnValue['last'] = $actualValueAtPrice['last'];
        $returnValue['strikePrice'] = $actualValueAtPrice['strikePrice'];
        $returnValue['symbol'] = $symbolToSell;
        return $returnValue;
      } else {
        $logger->trace("symbolToSell=$symbolToSell found=" . $actualValueAtPrice['symbol']);
      }
    }
    return null;
  } // findOption


} // class TdaOptionChain
$TdaOptionChain = new TdaOptionChain();
?>
