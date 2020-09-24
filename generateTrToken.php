<?php
// -------------------------------------------------------
// clientid : a TaskRouter Worker.
// tokenPassword : text string match to environment variable TOKEN_PASSWORD.
// -------------------------------------------------------
$clientid = htmlspecialchars($_GET["clientid"]);
if ($clientid == "") {
    $clientid = getenv('CLIENT_ID');
    if ($clientid == "") {
        echo '-- CLIENT_ID must be an environment variable or GET parameter.';
        return;
    }
}

$tokenPassword = htmlspecialchars($_GET["tokenPassword"]);
if ($tokenPassword == "") {
    echo '-- tokenPassword must be a GET parameter.';
    return;
}
$token_password = getenv("TOKEN_PASSWORD");
if ($token_password !== $tokenPassword) {
    // echo "0" . " :" . $token_password . ":" . $tokenPassword . ":";
    echo "0";
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
