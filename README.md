# TaskRouter Worker Status Application Version 3.2

This application is used by TaskRouter Workers to manage their status and accept reservations.

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

## Steps to Implement

1. Configure your TaskRouter WorkSpace.
2. Create a Studio flow to put incoming callers into the TaskRouter queue.
3. Configure your Twilio phone number to use the Studio flow.
4. Deploy this application and set the environment variables.
5. Test.

## Server side Application Programs

The programs are called from the browser application using Ajax.

getTrActivites.php : When initializing the browser side client, get the WorkSpace friendly name and the TaskRouter activities.

generateTrToken.php?tokenPassword= + tokenPassword + &clientid= + clientId : given a client identity and the password, generate a token.

conferenceEndFn.php?conferenceName= + theConference : given a conference SID, end the conference.

taskReservationTaskFix.php?taskSid= + taskSid : given a task SID, if the status is wrapping, change it to completed.

## Utility Programs

conferenceListInProgress.php : List conferences that are in progress.

taskDeleteAll.php : remove all tasks.

taskReservationList.php : List task information.

taskReservationListFix.php : List task information and, if the status is wrapping, change it to completed.

workerStatus.js : Node.js program to list the status of all the WorkSpace workers.

nodeHttpServer.js : Node.js web server program for testing this application on a local host.

Cheers...
