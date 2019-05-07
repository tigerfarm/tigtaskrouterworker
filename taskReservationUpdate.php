<?php
// Documentation: https://www.twilio.com/docs/taskrouter/api/worker-reservations#reject
if ($argc > 1) {
    $workerSid = $argv[1];
} else {
    echo "+++ Requires a worker SID.\xA";
    exit;
}
if ($argc > 2) {
    $reservationSid = $argv[2];
} else {
    echo "+++ Requires a reservation SID.\xA";
    exit;
}
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Rest\Client;

$account_sid = getenv("ACCOUNT_SID");
$auth_token = getenv('AUTH_TOKEN');
$client = new Client($account_sid, $auth_token);
$workspaceSid = getenv("WORKSPACE_SID");
$workflowSid = getenv("WORKFLOW_SUPPORT");
$activityOfflineSid = getenv("ACTIVITY_OFFLINE");
$reservation = $client->taskrouter->workspaces($workspaceSid)
    ->workers($workerSid)
    ->reservations($reservationSid)
    ->update(
        array(
            'reservationStatus' => 'rejected',
            'WorkerActivitySid' => $activityOfflineSid
        ));
print("+ Task updated: " . $task->sid . ", Activity set to Offline.");

?>                    
