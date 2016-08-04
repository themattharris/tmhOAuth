<?php

/**
 * Demonstration of the OAuth authenticate flow. 
 * We use `authenticate` instead of `authorize` so that users having already authorized the app don't have to do it again.
 * 
 * You can use this as an example of a simple AJAX authentication API "library" that you can drop in to your Web app. 
 * You must define APP_URL as your Web app's root URL (the URL users will be redirected to after authentication).
 * A gist of the Javascript to use in your Web app can be found here: https://gist.github.com/3215194
 * 
 * Instructions:
 * 1) If you don't have one already, create a Twitter application on
 *      https://dev.twitter.com/apps
 * 2) From the application details page copy the consumer key and consumer
 *      secret into the place in this code marked with (YOUR_CONSUMER_KEY
 *      and YOUR_CONSUMER_SECRET)
 * 3) Visit this page using your web browser.
 * 
 */

require '../tmhOAuth.php';
require '../tmhUtilities.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'YOUR_CONSUMER_KEY',
  'consumer_secret' => 'YOUR_CONSUMER_SECRET',
));


define('APP_URL', 'http://example.com');


header('Content-type: application/json');

session_start();

function outputError($tmhOAuth) {
  header('HTTP/1.1 500 Internal Server Error');
  echo json_encode($tmhOAuth->response['response']);
}

function wipe() {
  session_destroy();
  echo json_encode(array('wiped' => "success"));
}


// Step 1: Request a temporary token
function request_token($tmhOAuth) {
  $code = $tmhOAuth->request(
    'POST',
    $tmhOAuth->url('oauth/request_token', ''),
    array(
      'oauth_callback' => tmhUtilities::php_self()
    )
  );

  if ($code == 200) {
    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    authorize($tmhOAuth);
  } else {
    outputError($tmhOAuth);
  }
}


// Step 2: Direct the user to the authenticate web page
function authorize($tmhOAuth) {
  $authurl = $tmhOAuth->url("oauth/authenticate", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}";
  
  echo json_encode(array('authurl' => $authurl));
}


// Step 3: This is the code that runs when Twitter redirects the user to the callback. Exchange the temporary token for a permanent access token
function access_token($tmhOAuth) {
  $tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  $code = $tmhOAuth->request(
    'POST',
    $tmhOAuth->url('oauth/access_token', ''),
    array(
      'oauth_verifier' => $_REQUEST['oauth_verifier']
    )
  );

  if ($code == 200) {
    $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    unset($_SESSION['oauth']);
    header('Location: ' . APP_URL);
  } else {
    outputError($tmhOAuth);
  }
}


// Step 4: Now the user has authenticated, do something with the permanent token and secret we received
function verify_credentials($tmhOAuth) {
  $tmhOAuth->config['user_token']  = $_SESSION['access_token']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

  $code = $tmhOAuth->request(
    'GET',
    $tmhOAuth->url('1/account/verify_credentials')
  );

  if ($code == 200) {
    echo $tmhOAuth->response['response'];
  }
  else {
    outputError($tmhOAuth);
  }
}



/* Auth Flow */

if (isset($_REQUEST['wipe'])) {
  // Logging out
  wipe();
  return;
}

if (isset($_REQUEST['start'])) {
  // Let's start the OAuth dance
  request_token($tmhOAuth);
}
elseif (isset($_REQUEST['oauth_verifier'])) {
  access_token($tmhOAuth);
}
elseif (isset($_SESSION['access_token'])) {
  // Some credentials already stored in this browser session.
  verify_credentials($tmhOAuth);
}
else {
  // User's not logged in.
  echo json_encode(array('loggedin' => false));
}

