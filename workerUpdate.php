<?php
// https://www.twilio.com/docs/taskrouter/api/workers

// Not tested.

if ($argc > 1) {
    $workerSid = $argv[1];
} else {
    echo "+++ Requires a worker SID.\xA";
    exit;
}echo "+++ List Conference participants on conference SID: " . $conferenceSid . "\xA";
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Rest\Client;
$twilio = new Client(getenv('ACCOUNT_SID'), getenv('AUTH_TOKEN'));
echo "++ Worker List\xA";
$workers = $twilio->taskrouter->v1->workspaces(getenv("WORKSPACE_SID"))
        ->workers($workerSid)
        ->update(array(
            "attributes" => array(
                "ActivitySid" => "Offline"
                )
        ));
echo "+++ Exit.\xA";
?>
