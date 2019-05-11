# TaskRouter Worker Application Version 3.2

This application is used by Twilio TaskRouter workers to manage their status and reservations.

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

--------------------------------------------------------------------------------

## Steps to Implement a TaskRouter Workflow System

1. Configure your Twilio TaskRouter Workspace.
2. Create a Studio flow to put incoming callers into the TaskRouter queue.
3. Configure your Twilio phone number to use the Studio flow.
4. Deploy the TaskRouter Worker application and set the environment variables.
5. Test.

With Twilio Studio and TaskRouter, in less than two hours, you can set up a call flow, which is the bases of a call center. 
This exercise will walk you through the steps to configure your Twilio phone number to receive calls and put them into a queue.
The callers will listen to music while TaskRouter arranges an agent to take their call.

When a caller is added into the queue, TaskRouter creates a reservation and then asks an agent if they will accept the call.
The agent has the option to Accept, and be connected with the caller; or to Reject the call.
If the call is rejected, TaskRouter will ask the next available agent.

Agents will use their web browser, on their computer, to manage their status: offline, or available to accept calls.
When they accept a call, TaskRouter will dial their TaskRouter worker phone number, to connect them to the caller.

<img src="flowDiagram.jpg" width="500"/>

The instructions are located at this GitHub repository URL:

https://github.com/tigerfarm/tigtaskrouterworker

Click [here](https://www.loom.com/share/f7b6cb45e12a439aaaef05affb714acb) for a video of me walking through the steps.

Implementation requirements:
- You will need a Twilio account. A free Trial account will work.
- You will need an [Heroku account](https://heroku.com/) to host your application.
- For testing, you will need at least 2 phone numbers. You can use two mobile phone numbers, one to be the caller, the other phone number for the worker (agent).
- Developer skills are not required, as the sample application is functional, as is.

### Configure your TaskRouter Workspace

Create a Workspace, Name: writers.
https://www.twilio.com/console/taskrouter/dashboard 

Create a Caller TaskQueue, and set:
- TaskQueue Name to: support.
- Max Reserved Workers: 1.
- Queue expression: skills HAS "support"

Create a Workflow, and set:
- Friendly Name: support.
- Assignment Callback, Task Reservation Timeout to, 10.
- Default queue: support.

Create a Worker, and set:
- Name: charles.
- Attributes to: {"skills":["support"],"contact_uri":"+16505551111"}.

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

### Deploy the TaskRouter Worker Application

This application is ready to run.
To deploy to Heroku, you will need an [Heroku account](https://heroku.com/) to host your application.

Click the Deploy to Heroku link:

[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/tigerfarm/tigtaskrouterworker)

When you deploy to Heroku, you will be prompted for an app name. 
The name needs to be unique. Example, enter your name+tw (example: davidtw). 
Click Deploy app. Once the application is deployed, click Manage app. 
Set Heroku project environment variables by clicking Settings. 
Click Reveal Config Vars.

Add the following key value pairs:
- ACCOUNT_SID : your Twilio account SID (starts with "AC", available from Twilio Console)
- AUTH_TOKEN : your Twilio account auth token (Available from Twilio Console, click view)
- TOKEN_PASSWORD : your token password (Password is required to create tokens. The password can be any string you want to use.)
- WORKSPACE_SID : your TaskRouter workspace SID

Note, if you need to redeploy and keep the same Heroku URL:
- Remove the old app by using the Heroku dashboard: https://dashboard.heroku.com.
- Select the app, click Settings, go to the bottom, click Delete app.
- Then, click Deploy to Heroku button. Note, you will need to re-enter the above Config Vars.

### Test

In your browser, go to your TaskRouter Workers Application.
- WorkSpace name is displayed: writers.
- Enter your worker name: charles.
- Enter your token password.
- Click Get access token. Worker status is displayed: Offline.
- Click Go online. Worker status is displayed: Available.
- Click Go online, and Go offline, to see how you set your availability.
- Click Go online.

<img src="TR_WorkerAr.jpg" width="300"/>

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

## Documentation

### Local host Implementation using the included NodeJS HTTP Webserver

Notes, the Twilio Node.JS helper library is not required.
The server side can run locally on a computer using NodeJS, or run on a website that runs PHP programs.

Download the project zip file.

https://github.com/tigerfarm/tigtaskrouterworker

1. Click Clone or Download. Click Download ZIP.
2. Unzip the file into a work directory.
3. Change into the unzipped directory.

Install the NodeJS "request" module:
    
    $ npm install request

Run the NodeJS HTTP server.

    $ node nodeHttpServer.js
    +++ Start: nodeHttpServer.js
    Static file server running at
      => http://localhost:8000/
    CTRL + C to shutdown
    ...
    
Use a browser to access the application:

    http://localhost:8000/index.html
    
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

Cheers...
