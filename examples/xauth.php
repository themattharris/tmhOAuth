<?php

require '../tmhOAuth.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => '',
  'consumer_secret' => '',
  'user_token'      => '',
  'user_secret'     => '',
));

$tmhOAuth->request('POST', "https://api.twitter.com/oauth/access_token", array(
  'x_auth_username' => '',
  'x_auth_password' => '',
  'x_auth_mode'     => 'client_auth'
));

if ($tmhOAuth->response['code'] == 200) {
  $tokens = $tmhOAuth->extract_params($tmhOAuth->response['response']);
  $tmhOAuth->pr($tokens);
} else {
  $tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
}

?>