<?php
date_default_timezone_set('America/Phoenix');
require_once 'constants.php';
require_once 'version.php';
require_once 'log4php/Logger.php';

Logger::configure("config.xml");
$logger = Logger::getLogger('mypredictory');
$auditLogger = Logger::getLogger("audit");

$txId = uniqid();

set_error_handler(
    function ($severity, $message, $file, $line) {
        global $logger;
//         $logger->info("msg=$message, sev=$severity, sev=$severity, file=$file, line=$line\n");
        throw new ErrorException($message, $severity, $severity, $file, $line);
    }
);

function setBackgroundColor() {
    if ($_SERVER['SERVER_ADDR'] == "127.0.0.1") {
        print("<script>");
        print('document.body.style.background="#fafad2";');
        print("</script>");
    }
}
function auditUrlAccess(string $name) {
    global $auditLogger;
    global $theLoggedInUser;
    global $txId;

    $message = "$txId $name";
    if (isset($_SERVER['REMOTE_ADDR'])) {
        $message = "$message ip=" . $_SERVER['REMOTE_ADDR'];
    }
    if (isset($theLoggedInUser)) {
        $message = "$message userid=" . $theLoggedInUser['id'];
    }
    if (isset($_GET)) {
        $message = "$message get=" . json_encode($_GET);
    }
    if (isset($_POST)) {
        $message = "$message post=" . json_encode($_POST);
    }
    $auditLogger->info($message);
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool {
        return $needle === '' || $needle === \substr($haystack, - \strlen($needle));
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}
?>
