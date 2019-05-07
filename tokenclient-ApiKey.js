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
    // -------------------
    const AccessToken = require('twilio').jwt.AccessToken;
    const VoiceGrant = AccessToken.VoiceGrant;
    const voiceGrant = new VoiceGrant({
        outgoingApplicationSid: context.VOICE_TWIML_APP_SID_CALL_CLIENT,
        incomingAllow: true
    });
    const token = new AccessToken(context.ACCOUNT_SID, context.VOICE_API_KEY, context.VOICE_API_SECRET);
    token.addGrant(voiceGrant);
    token.identity = clientid;
    let tokenJwt = token.toJwt();
    callback(null, tokenJwt);
};
