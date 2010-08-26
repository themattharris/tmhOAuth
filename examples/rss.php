<?php

require '../tmhOAuth.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => '',
  'consumer_secret' => '',
  'user_token'      => '',
  'user_secret'     => '',
));

$tmhOAuth->request('GET', $tmhOAuth->url('statuses/home_timeline', 'rss'));

if ($tmhOAuth->response['code'] == 200) {
  header('Content-Type: application/rss+xml; charset=utf-8');
  echo $tmhOAuth->response['response'];
} else {
  $tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
}

?>