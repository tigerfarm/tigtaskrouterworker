<?php
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Rest\Client;
// -------------------------------------------------------
$account_sid = getenv("ACCOUNT_SID");
$auth_token = getenv('AUTH_TOKEN');
$client = new Client($account_sid, $auth_token);
// -------------------------------------------------------
$workspace_sid = getenv("WORKSPACE_SID");
$workspace = $client->taskrouter->v1->workspaces($workspace_sid)
                                    ->fetch();
// echo "+ Workspace friendlyName: " . $workspace->friendlyName . "\xA";
$separator = ":";
// Add the Workspace friendlyName to the list.
$sList = $workspace->friendlyName . $separator . "workspacefriendlyname" . $separator;

$activities = $client->taskrouter->v1->workspaces($workspace_sid)->activities->read();
foreach ($activities as $item) {
    // echo $item->sid . $separator . $item->friendlyName . $separator . "\xA";
    $sList = $sList . $item->sid . $separator . $item->friendlyName . $separator;
}
echo substr($sList,0,strlen($sList)-1);
?>
