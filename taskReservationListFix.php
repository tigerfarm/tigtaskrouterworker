<?php

// Load Twilio PHP Helper Library.
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';

use Twilio\Rest\Client;

$account_sid = getenv("ACCOUNT_SID");
$auth_token = getenv('AUTH_TOKEN');
$client = new Client($account_sid, $auth_token);
$workspaceSid = getenv("WORKSPACE_SID");
$tasks = $client->taskrouter->v1->workspaces($workspaceSid)
        ->tasks
        ->read();

echo "+++ Fix Task Reservation: If assignment Status wrapping, change to: completed.\xA";
$i = 0;
foreach ($tasks as $task) {
    if ($task->assignmentStatus == "wrapping") {
        $i++;
        echo "+ Assignment stuck in wrapping: " . $task->sid . "\xA";
        $client->taskrouter->v1->workspaces($workspaceSid)->tasks($task->sid)
                ->update(array(
                    'assignmentStatus' => "completed",
                    'reason' => "Was stuck in wrapping"
        ));
        echo "++ Assignment status reset to completed.\xA";
    }
}
if ($i == 0) {
    echo "+ At this time, no task reservation with assignment Status wrapping." . "\xA";
}
// $twilio->taskrouter->v1->workspaces($workspaceSid)->tasks($task->sid)
//         ->update(array('assignmentStatus' => "completed",'reason' => "Stuck in wrapping"));
?>                    
