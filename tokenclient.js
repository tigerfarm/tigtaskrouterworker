exports.handler = function(context, event, callback) {
    let tokenPassword = event.tokenPassword || null;
    let contextTokenPassword = context.TOKEN_PASSWORD || null;
    if (tokenPassword === null) {
           console.log("-- Required parameter: tokenPassword.");
           callback(null, "-- Required parameter: tokenPassword.");
           return;
    }
    if (contextTokenPassword === null) {
           console.log("-- In Functions Configure, add: TOKEN_PASSWORD.");
           callback(null, "-- In Functions Configure, add: TOKEN_PASSWORD.");
           return;
    }
    if (tokenPassword !== contextTokenPassword) {
           console.log("-- tokenPassword not valid.");
           callback(null, "-- tokenPassword not valid.");
           return;
    }
    console.log("+ Token password is valid.");
    //
    let clientid = event.clientid || null;
    if (clientid === null) {
           console.log("-- Required parameter: clientid.");
           callback(null, "-- Required parameter: clientid.");
           return;
    }
    console.log("+ Client ID: " + clientid);
    //
    const ClientCapability = require('twilio').jwt.ClientCapability;
    const VoiceResponse = require('twilio').twiml.VoiceResponse;
    const capability = new ClientCapability({
        accountSid: context.ACCOUNT_SID,
        authToken: context.AUTH_TOKEN
    });
    capability.addScope(new ClientCapability.IncomingClientScope(clientid));
    capability.addScope(new ClientCapability.OutgoingClientScope({
        applicationSid: context.VOICE_TWIML_APP_SID_CALL_CLIENT,
        clientName: clientid
    }));
    let token = capability.toJwt();
    console.log(":" + token + ":");
    callback(null, token);
};
