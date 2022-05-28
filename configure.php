<?php
require_once 'configureData.php';
$debugConfigure = false;
function setDataDb(string $dbString) {
    global $debugConfigure;
    global $logger;
    global $db_username, $db_password, $db_dbname, $db_servername;
    
    if ($debugConfigure) print("setDataDb dbString=$dbString<br>");
    $json = json_decode($dbString, true);
    // $logger->info("json=" . gettype($json));
    if ($debugConfigure) print("setDataDb json=" . json_encode($json) . '<br>');

    $db_servername = $json['server'];
    $db_username = $json['user'];
    $db_password = $json['password'];
    $db_dbname = $json['db'];
}


function setUiDb(string $dbString) {
    global $debugConfigure;
    global $logger;
    global $ui_username, $ui_password, $ui_dbname, $ui_servername;
    
    if ($debugConfigure) print("setUiDb dbString=$dbString<br>");
    $json = json_decode($dbString, true);
    // $logger->info("json=" . gettype($json));
    if ($debugConfigure) print("setUiDb json=" . json_encode($json) . '<br>');

    $ui_servername = $json['server'];
    $ui_username = $json['user'];
    $ui_password = $json['password'];
    $ui_dbname = $json['db'];
}

function setPropDb(string $dbString) {
    global $debugConfigure;
    global $logger;
    global $prop_username, $prop_password, $prop_dbname, $prop_servername;
    
    if ($debugConfigure) print("setPropDb dbString=$dbString<br>");
    $json = json_decode($dbString, true);
    // $logger->info("json=" . gettype($json));
    if ($debugConfigure) print("setPropDb json=" . json_encode($json) . '<br>');

    $prop_servername = $json['server'];
    $prop_username = $json['user'];
    $prop_password = $json['password'];
    $prop_dbname = $json['db'];
}

if (isset($_GET['debugConfigure']) && $_GET['debugConfigure'] == 'xyZZy') {
    $debugConfigure = true;
}

setDataDb($localData);
setUiDb($localData);
setPropDb($localData);
// // constant values not stored in source code control
// $cwd = getcwd();
// if ($debugConfigure) print("*********entered comfigure cwd=$cwd " . gethostname() . "<br>");
// if (gethostname() == "sohail" || gethostname() == 'mahmood') {
// 	// print("a " . __LINE__ . "<br>");
//     // $db_servername = "localhost";
//     if ($cwd == "C:\\xampp\\htdocs\\www") {
//         // print('*********configure found sohail and www<br>');
//         setDataDb($localUi);
//         setUiDb($localUi);
//         setPropDb($localUi);
//     } else if ($cwd == "C:\\xampp\\htdocs\\www\\data") {
//         // print('*********configure found sohail and testing<br>');
//         setDataDb($localData);
//         setUiDb($localUi);
//     } else if ($cwd == "C:\\xampp\\htdocs\\www\\tx") {
//         // print('*********configure found sohail and testing<br>');
//         setDataDb($localUi);
//         setUiDb($localUi);
//     } else if ($cwd == "C:\\xampp\\htdocs\\testing") {
//         // print('*********configure found sohail and testing<br>');
//         setDataDb($localTestUi);
//         setUiDb($localTestUi);
//     } else if ($cwd == "C:\\xampp\\htdocs\\testing\\data") {
//         // print('*********configure found sohail and testing<br>');
//         setDataDb($localTestData);
//         setUiDb($localTestData);
//     } else if ($cwd == "C:\\xampp\\htdocs\\testing\\tx") {
//         // print('*********configure found sohail and testing<br>');
//         setDataDb($localTestTx);
//         setUiDb($localTestTx);
//     }
// } else {
//     if ($debugConfigure) print("b " . __LINE__ . "<br>");
//     $db_servername = "p3plzcpnl489508.prod.phx3.secureserver.net";
//     if ($cwd == "/home/jlad6eh1efe9/public_htmlx") {
//     //     // print('*********configure found remote and www<br>');
//     //     setDataDb($godaddyUi);
//     //     setUiDb($godaddyUi);
//     // } else if ($cwd == "/home/jlad6eh1efe9/public_html/data") {
//     //     // print('*********configure found remote and data<br>');
//     //     setDataDb($godaddyData);
//     //     setUiDb($godaddyData);
//     // } else if ($cwd == "/home/jlad6eh1efe9/public_html/ui") {
//     //     if ($debugConfigure) print('*********configure found remote and ui<br>');
//     //     setDataDb($godaddyTx);
//     //     setUiDb($godaddyTx);
//     // } else if ($cwd == "/home/jlad6eh1efe9/public_html/tx") {
//     //     // print('*********configure found remote and tx<br>');
//     //     setDataDb($godaddyTx);
//     //     setUiDb($godaddyTx);
//     } else if ($cwd == "/home/jlad6eh1efe9/public_html/testing.mypredictory.com") {
//         if ($debugConfigure) print('*********configure found remote test and www<br>');
//         setDataDb($godaddyTestUi);
//         setUiDb($godaddyTestUi);
//         setPropDb($godaddyTestUi);
//     } else if ($cwd == "/home/jlad6eh1efe9/public_html/testing.mypredictory.com/data") {
//         if ($debugConfigure) print('*********configure found remote test and data<br>');
//         setDataDb($godaddyTestData);
//         setUiDb($godaddyTestUi);
//         setPropDb($godaddyTestUi);
//     } else if ($cwd == "/home/jlad6eh1efe9/public_html/testing.mypredictory.com/tx") {
//         if ($debugConfigure) print('*********configure found remote test and tx<br>');
//         setDataDb($godaddyTestUi);
//         setUiDb($godaddyTestUi);
//         setPropDb($godaddyTestUi);
//     }
// }
// if ($debugConfigure) print("c " . __LINE__ . "<br>");
// if ($debugConfigure) print("user = $db_username<br>");
?>
