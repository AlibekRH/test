<?php
ini_set("display_errors", 0);
error_reporting(0);
require_once './vendor/autoload.php';
$callerId = 'client:quick_start';
$to = (isset($_POST['to']) && !empty($_POST['to'])) ? $_POST['to'] : '';
$callerNumber = '+17472290773';
$response = new Twilio\Twiml();
//$response = new Twilio\Twiml();

if (!isset($to) || empty($to)) {
    $response->say('Congratulations! You have just made your first call! Good bye.');
} else if (is_numeric($to)) {
    $dial = $response->dial(
            array(
                'callerId' => $callerNumber
    ));
    $dial->number($to);
} else {
    $dial = $response->dial(
            array(
                'callerId' => $callerNumber
    ));
    $dial->client($to);
}
print $response;
?>

