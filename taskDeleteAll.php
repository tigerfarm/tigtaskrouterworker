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

echo "+++ Delete tasks.\xA";
foreach ($tasks as $task) {
        $client->taskrouter->v1->workspaces($workspaceSid)->tasks($task->sid)->delete();
        echo "+ Deleted task SID: " . $task->sid . "\xA";
}
echo "+ Completed." . "\xA";
?>                    
