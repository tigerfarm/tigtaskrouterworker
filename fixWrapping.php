<?php

require __DIR__ . '/twilio-php-master/Twilio/autoload.php';

use Twilio\Rest\Client;

$account_sid = getenv("ACCOUNT_SID");
$auth_token = getenv('AUTH_TOKEN');
$client = new Client($account_sid, $auth_token);
$workspaceSid = getenv("WORKSPACE_SID");



$tasks1 = $client->taskrouter->v1->workspaces($workspaceSid)
        ->tasks()   // {assignmentStatus: "wrapping"}
        ->read();
        
$tasks = $client->taskrouter->v1->workspaces($workspaceSid)
        ->tasks()   // {assignmentStatus: "wrapping"}
        ->read();

echo "++ Task Reservation List\xA";
$i = 0;
foreach ($tasks as $task) {
    $i++;
    $taskAssignmentStatus = $task->assignmentStatus;
    echo "+ Task SID: " . $task->sid
            , " assignmentStatus:" . $taskAssignmentStatus
            . "\xA";
}
if ($i == 0) {
    echo "No task reservations at this time." . "\xA";
}

?>                    
