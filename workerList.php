<?php

// https://www.twilio.com/docs/taskrouter/api/workers
if ($argc > 1) {
    $workerFn = $argv[1];
} else {
    $workerFn = $_REQUEST['workerFn'];
}
if ($workerFn === null) {
    echo "0";
    return;
}
echo "+++ List Conference participants on conference SID: " . $conferenceSid . "\xA";
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Rest\Client;
$twilio = new Client(getenv('ACCOUNT_SID'), getenv('AUTH_TOKEN'));
echo "++ Worker List\xA";
$workers = $twilio->taskrouter->v1->workspaces(getenv("WORKSPACE_SID"))->workers->read();
foreach ($workers as $record) {
    print('+ Workers SID: ' . $record->sid . " Friendly Name: " . $record->friendlyName . "\xA");
}
echo "+++ Exit.\xA";
?>
