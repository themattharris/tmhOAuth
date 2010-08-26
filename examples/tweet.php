<?php

require '../tmhOAuth.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => '',
  'consumer_secret' => '',
  'user_token'      => '',
  'user_secret'     => '',
));

$tmhOAuth->request('POST', $tmhOAuth->url('statuses/update'), array(
  'status' => 'Hanging out at the big eye café then heading -->> to meet some friends.'
));

if ($tmhOAuth->response['code'] == 200) {
  $tmhOAuth->pr(json_decode($tmhOAuth->response['response']));
} else {
  $tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
}

?>