# TaskRouter Worker Status Application Version 3.2

This application is used by TaskRouter Workers to manage their TaskRouter status and accept reservations.

Requirements:

- For non-developers and developers: you will need a Twilio account. A free Trial account will work.
- For non-developers, you will need an [Heroku account](https://heroku.com/) to host your application.
- For developers, I have included a Node.JS webserver program that you can run locally on your computer.
  Or, you can also run this application on a website that has a PHP runtime environment.

Note, no development required to run this application. It can be completely deployed and tested from a web browser.

[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/tigerfarm/tigtaskrouterworker)

When you deploy to Heroku, you will be prompted for an app name. 
The name needs to be unique. Example, enter your name+tw (example: davidtw). 
Click Deploy app. Once the application is deployed, click Manage app. 
Set Heroku project environment variables by clicking Settings. 
Click Reveal Config Vars. Add the following key value pairs:
````
ACCOUNT_SID : your_account_SID (starts with "AC", available from Twilio Console)
AUTH_TOKEN : your_account_auth_token (Available from Twilio Console)
TOKEN_HOST : your_Twilio_Functions_domain (example: about-time-1235.twil.io)
TOKEN_PASSWORD : your_token_password (Password is required to create tokens. You create the password for your users)
WORKSPACE_SID : your_TaskRouter_workspace_sid (Only required if you are using TaskRouter)
````
To redeploy,
````
To keep the same URL, remove the old app by using the Heroku dashboard:
    https://dashboard.heroku.com,
    Select the app, click Settings, go to the bottom, click Delete app.
Then, from, https://github.com/tigerfarm/tigtaskrouterworker, click Deploy to Heroku button.
Note, you will need to re-enter the above Config Vars.
````

Client Application Screen print:

<img src="Tiger_Agent.jpg"/>

## Steps to Implement

1. Configure your TaskRouter WorkSpace.
2. Create a Twilio Function to generate TaskRouter worker tokens.
3. Configure your account's Twilio Functions settings.
4. Deploy this application.
5. Create a Studio flow to put callers into the TaskRouter queue.
6. Test.

Cheers...
