# TaskRouter Worker Application Version 3.2

This application is used by TaskRouter workers to manage their status and reservations.

## Functionality

- Using their browser, the application allows workers to enter their identity and a password.
- Workers manage their status: available to take a call, busy while on a call, or unavailable.
- Status is displayed in the browser
- Worker can accept or reject a call reservation.
- If a reservation times out, the worker status is changed to unavailable.
- A worker can end a reservation.
- A worker can end a call which disconnects all participants from the reservation conference call.
- If a task is set to wrapping, it is automatically reset to completed. This avoids a worker not being able to reset their status.

Worker application screen:

<img src="Tiger_Agent.jpg" width="300"/>

Implementation requirements:

- For non-developers and developers: you will need a Twilio account. A free Trial account will work.
- For non-developers, you will need an [Heroku account](https://heroku.com/) to host your application.
- For developers, I have included a Node.JS webserver program that you can run locally on your computer.
  Or, you can also run this application on a website that has a PHP runtime environment.

Note, further development is not required to run this application.
It can be deployed to Heroku and tested from a web browser.

[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/tigerfarm/tigtaskrouterworker)

When you deploy to Heroku, you will be prompted for an app name. 
The name needs to be unique. Example, enter your name+tw (example: davidtw). 
Click Deploy app. Once the application is deployed, click Manage app. 
Set Heroku project environment variables by clicking Settings. 
Click Reveal Config Vars. Add the following key value pairs:
````
ACCOUNT_SID : your_account_SID (starts with "AC", available from Twilio Console)
AUTH_TOKEN : your_account_auth_token (Available from Twilio Console)
TOKEN_PASSWORD : your_token_password (Password is required to create tokens. The password can be any string you want to use.)
WORKSPACE_SID : your_TaskRouter_workspace_sid
````
To redeploy,
````
To keep the same URL, remove the old app by using the Heroku dashboard:
    https://dashboard.heroku.com,
    Select the app, click Settings, go to the bottom, click Delete app.
Then, from, https://github.com/tigerfarm/tigtaskrouterworker, click Deploy to Heroku button.
Note, you will need to re-enter the above Config Vars.
````

### Server side Application Programs

The programs are called from the browser application using Ajax.

getTrActivites.php : When initializing the browser side client, get the WorkSpace friendly name and the TaskRouter activities.

generateTrToken.php?tokenPassword= + tokenPassword + &clientid= + clientId : given a client identity and the password, generate a token.

conferenceEndFn.php?conferenceName= + theConference : given a conference SID, end the conference.

taskReservationTaskFix.php?taskSid= + taskSid : given a task SID, if the status is wrapping, change it to completed.

### Utility Programs

conferenceListInProgress.php : List conferences that are in progress.

taskDeleteAll.php : remove all tasks.

taskReservationList.php : List task information.

taskReservationListFix.php : List task information and, if the status is wrapping, change it to completed.

workerStatus.js : Node.js program to list the status of all the WorkSpace workers.

nodeHttpServer.js : Node.js web server program for testing this application on a local host.

--------------------------------------------------------------------------------

## Steps to Implement

1. Configure your TaskRouter WorkSpace.
2. Create a Studio flow to put incoming callers into the TaskRouter queue.
3. Configure your Twilio phone number to use the Studio flow.
4. Deploy this application and set the environment variables.
5. Test.

The instructions are located at the following GitHub repository URL:

https://github.com/tigerfarm/tigtaskrouterworker

### TaskRouter WorkSpace Configurations

Create a Workspace, Name: writers.
https://www.twilio.com/console/taskrouter/dashboard 

Create a Caller TaskQueue
- TaskQueue Name to: support.
- Queue expression: skills HAS "support"

Create a Workflow, Friendly Name: support.
- Set the Assignment Callback, Task Reservation Timeout to, 10.
- Set Default queue: support.

Create a Worker, Name, to, charles.
- Set the Attributes to, {"skills":["support"],"contact_uri":"+16505551111"}.

View Your TaskRouter Activities: Offline, Available, and Unavailable

### Create an IVR Studio Flow to Manage Incoming Calls

Create a new flow, Friendly name: Writers IVR.
https://www.twilio.com/console/studio

Add, Gather Input On Call widget.
- Set the Text to Say to, "Welcome to Support. I will put you on hold while I find you an agent."
- Set “Stop gathering after” 1 digits.

Drag an Enqueue Call widget onto the flow panel.
- Set the widget name to: enqueue_to_Support.
- Set, TaskRouter Workspace, to: writers.
- Set, TaskRouter Workflow, to: support.

### Configure your Twilio phone number to use the Studio flow.

In the Console, buy a phone number.
https://www.twilio.com/console/phone-numbers/search

In the phone number’s configuration page,
- Set Voice & Fax, A Call Comes In, to: Studio Flow / Writers IVR

Test, by calling your IVR Twilio phone number.
- You will hear your Say welcome message.
- You will be put into the TaskRouter queue and hear the wait music.
- Disconnect/hangup, your IVR works.

### Deploy the TaskRouter Workers Application

Click the Deploy to Heroku link:

[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/tigerfarm/tigtaskrouterworker)

Create Heroku environment variables.
- ACCOUNT_SID : your Twilio account SID
- AUTH_TOKEN : your Twilio account auth token
- TOKEN_PASSWORD : your token password
- WORKSPACE_SID : your TaskRouter workspace SID

### Test

In your browser, go to your TaskRouter Workers Application.
- WorkSpace name is displayed: writers.
- Enter your worker name: charles.
- Enter your token password.
- Click Get access token. Worker status is displayed: Offline.
- Click Go online. Worker status is displayed: Available.
- Click Go online, and Go offline, to see how you set your availability.
- Click Go online.

Call your IVR Twilio phone number and be put into your TaskRouter queue.
- In your TaskRouter Workers Application, Accept and Reject options are highlighted.
- Click Accept. End reservation is highlighted. Your phone will ring, and End conference is now highlighted.
- Answer you phone, and you are connected to the caller.
- Click End conference, and both you (the TaskRouter worker) and the caller are disconnected from the conference.

You now have a working and tested TaskRouter implementation.

Next steps:
- Add workers.
- Add voicemail, for the case where no workers are available.
This requires setting up voicemail, then linking your Workflow timeout to use voicemail.
- Add business hours to your IVR. If not business hours, go to straight to voicemail.
- Add a sales TaskRouter queue and Workflow.
- Add sales workers.

--------------------------------------------------------------------------------

Cheers...
