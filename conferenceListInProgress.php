<?php
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Rest\Client;
$twilio = new Client(getenv("ACCOUNT_SID"), getenv('AUTH_TOKEN'));
echo "++ Conference SIDs" . "\xA";
$conferences = $twilio->conferences->read(
    array(
        // "friendlyName" => "MyRoom",
        "status" => "in-progress"
    ));
foreach ($conferences as $record) {
    echo "+ " . $record->sid . " Name: " . $record->friendlyName . "\xA";
}
echo "++ End of list." . "\xA";
?>
