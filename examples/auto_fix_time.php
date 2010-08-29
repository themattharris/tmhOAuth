<?php

require '../tmhOAuth.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => '',
  'consumer_secret' => '',
  'user_token'      => '',
  'user_secret'     => '',
));

// for the demo set the timestamp to yesterday
$tmhOAuth->config['force_timestamp'] = true;
$tmhOAuth->config['timestamp'] = strtotime('yesterday');

$tmhOAuth->auto_fix_time_request('GET', $tmhOAuth->url('account/verify_credentials'));

if ($tmhOAuth->response['code'] == 200) {
  if ($tmhOAuth->auto_fixed_time)
    echo 'Had to auto adjust the time. Please check the date and time is correct on your device/server';

  $tmhOAuth->pr(json_decode($tmhOAuth->response['response']));
} else {
  $tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
}

?>