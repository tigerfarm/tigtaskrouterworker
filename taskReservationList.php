<?php

require __DIR__ . '/twilio-php-master/Twilio/autoload.php';

use Twilio\Rest\Client;

$account_sid = getenv("ACCOUNT_SID");
$auth_token = getenv('AUTH_TOKEN');
$client = new Client($account_sid, $auth_token);
$workspaceSid = getenv("WORKSPACE_SID");
$tasks = $client->taskrouter->v1->workspaces($workspaceSid)
        ->tasks
        ->read();

echo "++ Task Reservation List\xA";
$i = 0;
foreach ($tasks as $task) {
    $i++;
    $taskAttributes = json_decode($task->attributes, true);
    $conferenceSid = $taskAttributes["conference"]["sid"];
    $taskAssignmentStatus = $task->assignmentStatus;
    $doUpdate = "";
    if ($taskAssignmentStatus == "wrapping") {
        $doUpdate = "-*";
    }
    // Optional attribute:
    $selectedProduct = $taskAttributes["selected_product"];
    if ($selectedProduct !== null) {
        $selectedProduct = " selected_product:" . $selectedProduct;
    }
    echo "+ Task SID: " . $task->sid
            . $selectedProduct
            , " assignmentStatus:" . $taskAssignmentStatus . $doUpdate
            . " from:" . $taskAttributes["from"]
            . "\xA";
    if ($conferenceSid !== NULL) {
        echo "++ Task conference: "
            . " SID:" . $conferenceSid
            . " customer:" . $taskAttributes["conference"]["participants"]["customer"]
            . "\xA";
    }
    $reservations = $client->taskrouter->workspaces($workspaceSid)
            ->tasks($task->sid)
            ->reservations
            ->read();
    foreach ($reservations as $reservation) {
        echo "++ Task Reservation:"
        . " " . $reservation->reservationStatus
        . " " . $reservation->workerName
        . " " . $task->assignmentStatus . " " . $doUpdate
        . " " . $task->reason
        . "\xA";
    }
}
if ($i == 0) {
    echo "No task reservations at this time." . "\xA";
}

// Sample task JSON attributes:
// {"from_country":"US","called":"+16505552222","to_country":"US","to_city":"SAN BRUNO",
// "from":"+16508661111","direction":"inbound",
// "to":"+16505552222",
// "selected_product":"support",
// "to_state":"CA","caller_country":"US","call_sid":"CA0e4f74c48a5551810b2f0ac8fb509c14","account_sid":"ACxxxxxxxxx","from_zip":"94030",
// "called_zip":"94030","caller_state":"CA","to_zip":"94030","called_country":"US","from_city":"SAN BRUNO","called_city":"SAN BRUNO","caller_zip":"94030","api_version":"2010-04-01","called_state":"CA","from_state":"CA","caller":"+16508661111","caller_city":"SAN BRUNO",
// "conference":{"sid":"CF7a96054293a6280bd244e9f4fa259e96","participants":{"worker":"CA3e32fa5be1444eaff2685390fb410bec","customer":"CA0e4f74c48a5551810b2f0ac8fb509c14"}}}

?>                    
