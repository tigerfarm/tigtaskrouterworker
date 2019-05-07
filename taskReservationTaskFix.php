<?php

if ($argc === 2) {
    $taskSid = $argv[1];
} else {
    $taskSid = $_REQUEST['taskSid'];
}
// echo "+ taskSid = " . $taskSid . "\xA";
if ($taskSid === null) {
    echo "0";
    return;
}
// Load Twilio PHP Helper Library.
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';

use Twilio\Rest\Client;

$account_sid = getenv("ACCOUNT_SID");
$auth_token = getenv('AUTH_TOKEN');
$client = new Client($account_sid, $auth_token);

$workspaceSid = getenv("WORKSPACE_SID");
$task = $client->taskrouter->v1->workspaces($workspaceSid)->tasks($taskSid);
$taskAssignmentStatus = $task->fetch()->assignmentStatus;
echo "+ Task assignmentStatus: " . $taskAssignmentStatus . "\xA";
if ($taskAssignmentStatus === "wrapping") {
    $task->update(array(
            'assignmentStatus' => "completed",
            'reason' => "Was stuck in wrapping"
        ));
    echo "++ Task assignmentStatus set to: completed.\xA";
}

?>                    
