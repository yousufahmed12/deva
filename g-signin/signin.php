<?php

/*
    Used code from Official Google + PHP Quickstart Github
    https://github.com/googleplus/gplus-quickstart-php
*/
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Replace this with the client ID you got from the Google APIs console.
 */
const CLIENT_ID = '947841634540-qqgs81kk3dg3iu3ku5h8f07p601t5bsp.apps.googleusercontent.com';

/**
 * Replace this with the client secret you got from the Google APIs console.
 */
const CLIENT_SECRET = 'FBWma49ifAsoeJ21Sdc2gLIH';

/**
 * Optionally replace this with your application's name.
 */
const APPLICATION_NAME = "Rowan Parking Solution";

$client = new Google_Client();
$client->setApplicationName(APPLICATION_NAME);
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri('postmessage');

$plus = new Google_Service_Plus($client);

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__,
));
$app->register(new Silex\Provider\SessionServiceProvider());

// Initialize a session for the current user, and render index.html.
$app->get('/', function () use ($app) {
    $state = md5(rand());
    $app['session']->set('state', $state);
    return $app['twig']->render('signin.html', array(
        'CLIENT_ID' => CLIENT_ID,
        'STATE' => $state,
        'APPLICATION_NAME' => APPLICATION_NAME
    ));
});

// Upgrade given auth code to token, and store it in the session.
// POST body of request should be the authorization code.
// Example URI: /connect?state=...&gplus_id=...
$app->post('/connect', function (Request $request) use ($app, $client) {
    $token = $app['session']->get('token');

    if (empty($token)) {
        // Ensure that this is no request forgery going on, and that the user
        // sending us this connect request is the user that was supposed to.
        if ($request->get('state') != ($app['session']->get('state'))) {
            return new Response('Invalid state parameter', 401);
        }

        // Normally the state would be a one-time use token, however in our
        // simple case, we want a user to be able to connect and disconnect
        // without reloading the page.  Thus, for demonstration, we don't
        // implement this best practice.
        //$app['session']->set('state', '');

        $code = $request->getContent();
        // Exchange the OAuth 2.0 authorization code for user credentials.
        $client->authenticate($code);
        $token = json_decode($client->getAccessToken());

        // You can read the Google user ID in the ID token.
        // "sub" represents the ID token subscriber which in our case
        // is the user ID. This sample does not use the user ID.
        $attributes = $client->verifyIdToken($token->id_token, CLIENT_ID)
            ->getAttributes();
        $gplus_id = $attributes["payload"]["sub"];

        // Store the token in the session for later use.
        $app['session']->set('token', json_encode($token));
        $response = 'Successfully connected with token: ' . print_r($token, true);
    } else {
        $response = 'Already connected';
    }

    return new Response($response, 200);
});

// Get list of activities visible to this app.
$app->get('/activities', function () use ($app, $client, $plus) {
    $token = $app['session']->get('token');

    if (empty($token)) {
        return new Response('Unauthorized request', 401);
    }

    $client->setAccessToken($token);
    $activities = $plus->activities->listActivities('me', 'public', array());

    /*
     * Note (Gerwin Sturm):
     * $app->json($activities) ignores the $activities->items not returning this array
     * Probably needs to be fixed in the Client Library
     * Using ->toSimpleObject for now to get a JSON-convertible object
     */
    return $app->json($activities->toSimpleObject());
});

// Revoke current user's token and reset their session.
$app->post('/disconnect', function () use ($app, $client) {
    $token = json_decode($app['session']->get('token'))->access_token;
    $client->revokeToken($token);
    // Remove the credentials from the user's session.
    $app['session']->set('token', '');
    return new Response('Successfully disconnected', 200);
});

$app->run();
?>