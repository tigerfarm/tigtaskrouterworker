<?php
// -------------------------------------------------------
$token_password = getenv("TOKEN_PASSWORD");
if ($argc > 1 ) {
    $tokenPassword = $argv[1];
} else {
    $tokenPassword = $_GET['tokenPassword'];
}
if ($token_password !== $tokenPassword) {
    // echo "0" . " :" . $token_password . ":" . $tokenPassword . ":";
    echo "0";
    return;
}
$clientid = "";
if ($argc > 2 ) {
    $clientid = $argv[2];
} else {
    $clientid = $_REQUEST['clientid'];
}
if ($clientid == "") {
    // echo "1" . " :" . $clientid . ":";
    echo "1";
    return;
}
// -------------------------------------------------------
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Jwt\TaskRouter\WorkerCapability;
// -------------------------------------------------------
$account_sid = getenv("ACCOUNT_SID");
$auth_token = getenv('AUTH_TOKEN');
$workspace_sid = getenv("WORKSPACE_SID");
// -------------------------------------------------------
// To do: get the worker SID based on a worker's freindly name paramter.
$workerSid = '';
use Twilio\Rest\Client;
$twilio = new Client(getenv('ACCOUNT_SID'), getenv('AUTH_TOKEN'));
$workers = $twilio->taskrouter->v1->workspaces(getenv("WORKSPACE_SID"))->workers->read();
foreach ($workers as $record) {
    // print('+ Workers SID: ' . $record->sid . " Friendly Name: " . $record->friendlyName . "\xA");
    if ($clientid == $record->friendlyName) {
        $workerSid = $record->sid;
    }
}
if ($workerSid == "") {
    echo "0";
    return;
}
//
$capability = new WorkerCapability($account_sid, $auth_token, $workspace_sid, $workerSid);
$capability->allowFetchSubresources();
$capability->allowActivityUpdates();
$capability->allowReservationUpdates();
$workerToken = $capability->generateToken(28800);  // Expire: 60 * 60 * 8
echo $workerToken;
?>
