// -----------------------------------------------------------------
// TaskRouter JS code
// -----------------------------------------------------------------
//
let worker;                 // Worker object: worker.activityName
let taskSid = "";
let ReservationObject;
var trTokenValid = false;

// Workspace activity SIDs
var ActivitySid_Available = "";
var ActivitySid_Offline = "";
var ActivitySid_Unavailable = "";
// 
// -----------------------------------------------------------------
// let worker = new Twilio.TaskRouter.Worker("<?= $workerToken ?>");
function registerTaskRouterCallbacks() {
    logger("registerTaskRouterCallbacks().");
    worker.on('ready', function (worker) {
        logger("Worker registered: " + worker.friendlyName + ".");
        if (worker.attributes.skills) {
            logger("Skills: " + worker.attributes.skills.join(', '));
        }
        if (worker.attributes.languages) {
            logger("Languages: " + worker.attributes.languages.join(', '));
        }
        logger("Current activity is: " + worker.activityName);
        if (worker.activityName === "Unavailable") {
            goOffline();
        }
        logger("---------");
        setTrButtons(worker.activityName);
        setActivityStatus(worker.activityName);
        $('#btn-trtoken').prop('disabled', true);
    });
    worker.on('activity.update', function (worker) {
        logger("Worker activity updated to: " + worker.activityName);
        if (taskSid !== "") {
            logger("taskSid = " + taskSid);
        }
        setTrButtons(worker.activityName);
        setActivityStatus(worker.activityName);
        if (taskSid !== "" && worker.activityName === "Offline") {
            // Insure the agent is not hanging in assignment status of wrapping.
            $.get("taskReservationTaskFix.php?taskSid=" + taskSid, function (theResponse) {
                logger("Task check: " + theResponse);
            })
                    .fail(function () {
                        logger("- Error running Task Reservation Fix for status: wrapping.");
                        logger("-- The response: " + theResponse);
                        return;
                    });
            taskSid = "";
        }
    });

    // -----------------------------------------------------------------
    worker.on('reservation.created', function (reservation) {
        // reservation.task.attributes can be passed when the task is created.
        logger("---------");
        logger("reservation.created: You are reserved to handle a call from: " + reservation.task.attributes.from);
        if (reservation.task.attributes.selected_language) {
            logger("Caller selected language: " + reservation.task.attributes.selected_language);
        }
        if (reservation.task.attributes.selected_product) {
            logger("Customer request, task.attributes.selected_product: " + reservation.task.attributes.selected_product);
        }
        logger("Reservation SID: " + reservation.sid);
        setTrButtons("Incoming Reservation");
        ReservationObject = reservation;
        taskSid = reservation.task.sid;
        logger("reservation.task.sid: " + taskSid);
    });
    worker.on('reservation.accepted', function (reservation) {
        logger("Reservation accepted, SID: " + reservation.sid);
        logger("---------");
        ReservationObject = reservation;
        setTrButtons('reservation.accepted');
        theConference = ReservationObject.task.attributes.conference.sid;
        logger("Conference SID: " + theConference);
        setButtonEndConference(false);
        worker.update("ActivitySid", ActivitySid_Unavailable, function (error, worker) {
            if (error) {
                logger("--- acceptReservation, goUnavailable, Error:");
                logger(error.code);
                logger(error.message);
                // Example error message: The conference instruction can only be issued on a task that was created using the <Enqueue> TwiML verb.
                $('#btn-online').prop('disabled', true);
                $('#btn-offline').prop('disabled', true);
                $('#btn-trtoken').prop('disabled', false);
                $("div.msgTokenPassword").html("Refresh TaskRouter token.");
            }
        });

    });
    worker.on('reservation.rejected', function (reservation) {
        taskSid = "";
        logger("Reservation rejected, SID: " + reservation.sid + " by worker.sid: " + worker.sid);
        setTrButtons("canceled");
    });
    worker.on('reservation.timeout', function (reservation) {
        taskSid = "";
        logger("Reservation timed out: " + reservation.sid);
        setTrButtons("Offline");
    });
    worker.on('reservation.canceled', function (reservation) {
        taskSid = "";
        logger("Reservation canceled: " + reservation.sid);
        setTrButtons("canceled");
    });
    // -----------------------------------------------------------------
}

// -----------------------------------------------------------------
function setActivityStatus(workerActivity) {
    $("div.trStatus").html(workerActivity);
}

function taskReservationTaskFix() {
    // Insure the agent is not hanging in assignment status of wrapping.
    logger("taskReservationTaskFix() taskSid=" + taskSid);
    $.get("taskReservationTaskFix.php?taskSid=" + taskSid, function (thisResponse) {
        logger("Task check: " + thisResponse);
    })
            .fail(function () {
                logger("- Error running Task Reservation, possible cause, status: wrapping.");
                logger("-- The response: " + thisResponse);
                return;
            });
    taskSid = "";
}

// -----------------------------------------------------------------
function goAvailable() {
    logger("goAvailable(): update worker's activity to: Available.");
    worker.update("ActivitySid", ActivitySid_Available, function (error, worker) {
        if (error) {
            logger("--- goAvailable, Error:");
            logger(error.code);
            logger(error.message);
            $('#btn-online').prop('disabled', true);
            $('#btn-offline').prop('disabled', true);
            $('#btn-trtoken').prop('disabled', false);
            $("div.msgTokenPassword").html("Refresh TaskRouter token.");
        }
        ReservationObject.task.complete();
    });
}
function goOffline() {
    logger("goOffline(): update worker's activity to: Offline.");
    worker.update("ActivitySid", ActivitySid_Offline, function (error, worker) {
        if (error) {
            logger("--- goOffline, Error:");
            logger(error.code);
            logger(error.message);
        }
    });
}
function trHangup() {
    logger("trHangup(), set ReservationObject.task.complete().");
    ReservationObject.task.complete();
    worker.update("ActivitySid", ActivitySid_Offline, function (error, worker) {
        logger("Worker ended the call: " + worker.friendlyName);
        hangup();   // Call client hangup to take care of: Twilio.Device.disconnectAll();
        if (error) {
            logger("--- trHangup, Error:");
            logger(error.code);
            logger(error.message);
        } else {
            logger(worker.activityName);
        }
        logger("---------");
    });
    logger("---------");
}
// -----------------------------------------------------------------
function rejectReservation() {
    logger("rejectReservation(): reject the reservation.");
    ReservationObject.reject();
}
function acceptReservation() {
    logger("acceptReservation(): start a conference call, and connect caller and agent.");
    // Set Agent Conference on:
    //     https://www.twilio.com/console/voice/conferences/settings
    //
    // Conference call options:
    //     https://www.twilio.com/docs/taskrouter/js-sdk/workspace/worker
    //  Note, Timeout and Record doesn't work.
    // Tried:
    //  https://twilio.github.io/twilio-taskrouter.js/Reservation.html#.ConferenceOptions
    // With:
    //  https://www.twilio.com/docs/voice/twiml/conference#attributes
    //  
    // Now using:
    // https://www.twilio.com/docs/voice/api/conference-participant#create-a-participant-agent-conference-only
    var options = {
        "PostWorkActivitySid": ActivitySid_Offline,
        "Timeout": 5 // Timeout is the time allowed for the phone to ring, once the reservation is accepted.
        // , "record": "true" // true or false (default)
        // , "record": "record-from-start" // record-from-start or do-not-record (default)
    };
    logger("Conference call attribute, Record: " + options.Record);
    logger("Conference call attribute, Timeout: " + options.Timeout);
    logger("TaskRouter post activity SID: " + options.PostWorkActivitySid);
    //
    // https://www.twilio.com/docs/taskrouter/api/reservations
    // https://www.twilio.com/docs/taskrouter/js-sdk/worker#reservation-conference
    // Note: The conference instruction can only be issued on a task that was created using the <Enqueue> TwiML verb.
    // https://www.twilio.com/docs/taskrouter/js-sdk/workspace/worker
    // https://www.twilio.com/docs/taskrouter/js-sdk/workspace/worker#reservation-conference
    ReservationObject.conference(null, null, null, null, null, options);
    logger("Conference initiated.");
    setTrButtons("In a Call");
}

// -----------------------------------------------------------------------------
// Get TaskRouter activities.
function getTrActivies() {
    logger("Refresh TaskRouter workspace activities.");
    $.get("getTrActivites.php", function (theActivites) {
        logger("+ theActivites = " + theActivites);
        arrayValues = theActivites.split(":");
        $("div.trWorkSpace").html(arrayValues[0]);  // Display the Workspace friendly name
        var i;
        for (i = 1; i < arrayValues.length; i++) {
            // logger("+ i value = " + i + ":" + arrayValues[i]);
            if (arrayValues[i] === "Available") {
                ActivitySid_Available = arrayValues[i - 1];
                logger("+ ActivitySid_Available = " + ActivitySid_Available);
            }
            if (arrayValues[i] === "Offline") {
                ActivitySid_Offline = arrayValues[i - 1];
                logger("+ ActivitySid_Offline = " + ActivitySid_Offline);
            }
            if (arrayValues[i] === "Unavailable") {
                ActivitySid_Unavailable = arrayValues[i - 1];
                logger("+ ActivitySid_Unavailable = " + ActivitySid_Unavailable);
            }
        }
    })
            .fail(function () {
                logger("- Error refreshing the TaskRouter workspace activities.");
                return;
            });
}
// -----------------------------------------------------------------
function setWorkSpace(workerActivity) {
    $("div.trWorkSpace").html(arrayValues[0]);
}

// -----------------------------------------------------------------------------
// Get a TaskRouter Worker token.
function trToken() {
    if (trTokenValid) {
        $("div.msgTokenPassword").html("TaskRouter token already valid.");
        return;
    }
    clearMessages();
    clientId = $("#clientid").val();
    if (clientId === "") {
        $("div.msgClientid").html("<b>Required</b>");
        logger("- Required: Client id.");
        return;
    }
    tokenPassword = $("#tokenPassword").val();
    if (tokenPassword === "") {
        $("div.msgTokenPassword").html("<b>Required</b>");
        logger("- Required: Token password.");
        return;
    }
    // Since, programs cannot make an Ajax call to a remote resource,
    // Need to do an Ajax call to a local program that goes and gets the token.
    // logger("Refresh the TaskRouter token using client id: " + clientId + "&tokenPassword=" + tokenPassword);
    logger("Refresh the TaskRouter token using client id: " + clientId);
    $("div.trMessages").html("Refreshing token, please wait.");
    $.get("generateTrToken.php?tokenPassword=" + tokenPassword + "&clientid=" + clientId, function (theToken) {
        if (theToken.startsWith('0')) {
            $("div.trMessages").html("Invalid password.");
            return;
        }
        if (theToken.startsWith('1')) {
            $("div.trMessages").html("Missing client identity.");
            return;
        }
        $("div.trMessages").html("TaskRouter token received.");
        worker = new Twilio.TaskRouter.Worker(theToken);
        registerTaskRouterCallbacks();
        $("div.msgClientid").html("TaskRouter Token id: " + clientId);
        trTokenValid = true;
        // logger("TaskRouter token refreshed :" + theToken.trim() + ":");
        logger("TaskRouter token refreshed.");
        tokenClientId = clientId;
        $("div.msgTokenPassword").html("TaskRouter Token refreshed");
    })
            .fail(function () {
                logger("- Error refreshing the TaskRouter token.");
                return;
            });
}

// -----------------------------------------------------------------
function setTrButtons(workerActivity) {
    // logger("setTrButtons, Worker activity: " + workerActivity);
    $("div.trMessages").html("Current TaskRouter status: " + workerActivity);
    switch (workerActivity) {
        case "init":
            $('#btn-online').prop('disabled', true);
            $('#btn-offline').prop('disabled', true);
            $('#btn-acceptTR').prop('disabled', true);
            $('#btn-rejectTR').prop('disabled', true);
            $('#btn-trHangup').prop('disabled', true);
            getTrActivies();
            break;
        case "Available":
            $('#btn-online').prop('disabled', true);
            $('#btn-offline').prop('disabled', false);
            $('#btn-acceptTR').prop('disabled', true);
            $('#btn-rejectTR').prop('disabled', true);
            $('#btn-trHangup').prop('disabled', true);
            break;
        case "Offline":
            $('#btn-online').prop('disabled', false);
            $('#btn-offline').prop('disabled', true);
            $('#btn-acceptTR').prop('disabled', true);
            $('#btn-rejectTR').prop('disabled', true);
            $('#btn-trHangup').prop('disabled', true);
            break;
        case "Incoming Reservation":
            $('#btn-online').prop('disabled', true);
            $('#btn-offline').prop('disabled', true);
            $('#btn-acceptTR').prop('disabled', false);
            $('#btn-rejectTR').prop('disabled', false);
            $('#btn-trHangup').prop('disabled', true);
            break;
        case 'reservation.accepted':
            $('#btn-online').prop('disabled', true);
            $('#btn-offline').prop('disabled', true);
            $('#btn-acceptTR').prop('disabled', true);
            $('#btn-rejectTR').prop('disabled', true);
            $('#btn-trHangup').prop('disabled', false);
            setButtonEndConference(false);
            break;
        case "In a Call":
            $('#btn-online').prop('disabled', true);
            $('#btn-offline').prop('disabled', true);
            $('#btn-acceptTR').prop('disabled', true);
            $('#btn-rejectTR').prop('disabled', true);
            $('#btn-trHangup').prop('disabled', false);
            break;
        case "canceled":
            $('#btn-online').prop('disabled', true);
            $('#btn-offline').prop('disabled', false);
            $('#btn-acceptTR').prop('disabled', true);
            $('#btn-rejectTR').prop('disabled', true);
            $('#btn-trHangup').prop('disabled', true);
            break;
    }
}

// -----------------------------------------------------------------
// eof