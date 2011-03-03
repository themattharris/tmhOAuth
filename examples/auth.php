<?php

/**
 * Demonstration of the various OAuth flows. You would typically do this
 * when an unknown user is first using your application. Instead of storing
 * the token and secret in the session you would probably store them in a
 * secure database with their logon details for your website.
 *
 * When the user next visits the site, or you wish to act on their behalf,
 * you would use those tokens and skip this entire process.
 *
 * Instructions:
 * 1) If you don't have one already, create a Twitter application on
 *      http://dev.twitter.com/apps
 * 2) From the application details page copy the consumer key and consumer
 *      secret into the place in this code marked with (YOUR_CONSUMER_KEY
 * 3) Visit this page using your web browser.
 *
 * @author themattharris
 */

require '../tmhOAuth.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'YOUR_CONSUMER_KEY',
  'consumer_secret' => 'YOUR_CONSUMER_SECRET',
));

$here = $tmhOAuth->php_self();
session_start();

// reset?
if ( isset($_REQUEST['wipe'])) {
  session_destroy();
  header("Location: {$here}");

// already got some credentials stored?
} elseif ( isset($_SESSION['access_token']) ) {
  $tmhOAuth->config['user_token']  = $_SESSION['access_token']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

  $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
  $tmhOAuth->pr(json_decode($tmhOAuth->response['response']));

// we're being called back by Twitter
} elseif (isset($_REQUEST['oauth_verifier'])) {
  $tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
    'oauth_verifier' => $_REQUEST['oauth_verifier']
  ));
  $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
  unset($_SESSION['oauth']);
  header("Location: {$here}");

// start the OAuth dance
} elseif ( isset($_REQUEST['signin']) || isset($_REQUEST['allow']) ) {
  $callback = isset($_REQUEST['oob']) ? 'oob' : $here;

  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), array(
    'oauth_callback' => $callback
  ));

  if ($code == 200) {
    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    $method = isset($_REQUEST['signin']) ? 'authenticate' : 'authorize';
    $force  = isset($_REQUEST['force']) ? '&force_login=1' : '';
    $forcewrite  = isset($_REQUEST['force_write']) ? '&oauth_access_type=write' : '';
    $forceread  = isset($_REQUEST['force_read']) ? '&oauth_access_type=read' : '';
    header("Location: " . $tmhOAuth->url("oauth/{$method}", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}{$forcewrite}{$forceread}");

  } else {
    // error
    $tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
  }
}

?>
<ul>
  <li><a href="?signin=1">Sign in with Twitter</a></li>
  <li><a href="?signin=1&amp;force=1">Sign in with Twitter (force)</a></li>
  <li><a href="?allow=1">Allow Application (callback)</a></li>
  <li><a href="?allow=1&amp;oob=1">Allow Application (oob)</a></li>
  <li><a href="?allow=1&amp;force_read=1">Allow Application (callback) (read)</a></li>
  <li><a href="?allow=1&amp;force_write=1">Allow Application (callback) (write)</a></li>
  <li><a href="?wipe=1">Start Over</a></li>
</ul>