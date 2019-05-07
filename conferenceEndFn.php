<?php

// echo "+ argc = " . $argc . "\xA";
if ($argc === 2) {
    $conferenceName = $argv[1];
} else {
    $conferenceName = $_REQUEST['conferenceName'];
}
// echo "+ conferenceName = " . $conferenceName . "\xA";
if ($conferenceName === null) {
    echo "0";
    return;
}

require __DIR__ . '/twilio-php-master/Twilio/autoload.php';

use Twilio\Rest\Client;

$twilio = new Client(getenv("ACCOUNT_SID"), getenv('AUTH_TOKEN'));

if (strncmp($conferenceName, "CF", 2) === 0) {
    echo "++ End Conference, SID: " . $conferenceName . "\xA";
    try {
        $twilio->conferences($conferenceName)->update(array("status" => "completed"));
    } catch (Exception $ex) {
        echo "+ Conference not found.\xA";
    }

    echo "++ Ended.\xA";
    return;
}

echo "++ End Conference, name: " . $conferenceName . "\xA";
$conferences = $twilio->conferences->read(
        array(
            "friendlyName" => $conferenceName,
            "status" => "in-progress"
        ));
$counter = 0;
foreach ($conferences as $record) {
    $counter++;
    echo "+ " . $record->sid . " Name: " . $record->friendlyName . "\xA";
    $theConference = $record->sid;
}
echo "+ counter" + $counter + "\xA";
if ($counter === 0) {
    echo "+ Conference not found.\xA";
    return;
}
$twilio->conferences($theConference)->update(array("status" => "completed"));
echo "++ Ended.\xA";
?>
