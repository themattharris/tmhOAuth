<?php

require '../tmhOAuth.php';

$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => '',
  'consumer_secret' => '',
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

  $tmhOAuth->request('GET', $tmhOAuth->url('account/verify_credentials'));
  $tmhOAuth->pr(json_decode($tmhOAuth->response['response']));

// we're being called back by Twitter
} elseif (isset($_REQUEST['oauth_verifier'])) {
  $tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  $tmhOAuth->request('POST', "{$tmhOAuth->config['host']}/oauth/access_token", array(
    'oauth_verifier' => $_REQUEST['oauth_verifier']
  ));
  $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
  unset($_SESSION['oauth']);
  header("Location: {$here}");

// start the OAuth dance
} elseif ( isset($_REQUEST['signin']) || isset($_REQUEST['allow']) ) {
  $callback = isset($_REQUEST['oob']) ? 'oob' : $here;

  $tmhOAuth->request('POST', "{$tmhOAuth->config['host']}/oauth/request_token", array(
    'oauth_callback' => $callback
  ));

  if ($tmhOAuth->response['code'] == 200) {
    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    $method = isset($_REQUEST['signin']) ? 'authenticate' : 'authorize';
    $force  = isset($_REQUEST['force']) ? '&force_login=1' : '';
    header("Location: {$tmhOAuth->config['host']}/oauth/{$method}?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}");

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
  <li><a href="?wipe=1">Start Over</a></li>
</ul>