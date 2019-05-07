<?php

// Documentation: https://www.twilio.com/docs/taskrouter/quickstart/php/reservations-create-task-rest
// When a person is put into a conference call in TaskRouter, the conference name is the task SID:
// Task SID: WT5791d819dcc0f953c214311c14b4dd33 selected_product:support assignmentStatus:pending from:+16508661233
// Call SID: CF83401c984035f8d597b688742919de01 Name: WT21dacce356ba47fbba3c0eeaf5686d65
// 
// Other documentation: https://www.twilio.com/docs/taskrouter/api/tasks

require __DIR__ . '/twilio-php-master/Twilio/autoload.php';

use Twilio\Rest\Client;

$account_sid = getenv("ACCOUNT_SID");
$auth_token = getenv('AUTH_TOKEN');
$twilio = new Client($account_sid, $auth_token);
$workspaceSid = getenv("WORKSPACE_SID");
$workflowSid = getenv("WORKFLOW_SUPPORT");

$task = $twilio->taskrouter->workspaces($workspaceSid)->tasks
    ->create(array(
      'attributes' => '{"selected_product": "support","from": "client:stacy"}',
      'workflowSid' => $workflowSid,
    ));
print("+ Task created: " . $task->sid);

?>                    
