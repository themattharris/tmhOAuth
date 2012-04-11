<?php

/**
 * Very basic streaming API example. In production you would store the
 * received tweets in a queue or database for later processing.
 *
 * Instructions:
 * 1) If you don't have one already, create a Twitter application on
 *      https://dev.twitter.com/apps
 * 2) From the application details page copy the consumer key and consumer
 *      secret into the place in this code marked with (YOUR_CONSUMER_KEY
 *      and YOUR_CONSUMER_SECRET)
 * 3) From the application details page copy the access token and access token
 *      secret into the place in this code marked with (A_USER_TOKEN
 *      and A_USER_SECRET)
 * 4) In a terminal or server type:
 *      php /path/to/here/streaming.php
 * 5) To stop the Streaming API either press CTRL-C or, in the folder the
 *      script is running from type:
 *      touch STOP
 *
 * @author themattharris
 */

function my_streaming_callback($data, $length, $metrics) {
  //$data can be empty when twitter sends a keep alive.
  echo $data .PHP_EOL;
  return file_exists(dirname(__FILE__) . '/STOP');
}

require '../tmhOAuth.php';
require '../tmhUtilities.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'YOUR_CONSUMER_KEY',
  'consumer_secret' => 'YOUR_CONSUMER_SECRET',
  'user_token'      => 'A_USER_TOKEN',
  'user_secret'     => 'A_USER_SECRET',
));

$method = 'https://stream.twitter.com/1/statuses/filter.json';

// show Tweets which contan the word twitter OR have been geo-tagged within
// the bounding box -122.41,37.77,-122.40,37.78 OR are by themattharris

$params = array(
  //matches tweets containing 'twitter' 'Twitter' '#Twitter'
  'track'     => 'twitter',  
  //matches tweets containing 'twitter' or 'love' (no spaces!)
  //'track'   => 'twitter,love'
  //matches tweets containing 'twitter' and 'love'
  //'track'   =>'twitter love'
  //Warning on extra spaces - below matches 'twitter' but not 'love'!
  //'track'   =>'twitter, love'
 
  // Around Twitter HQ. First param is the SW corner of the bounding box
  'locations' => '-122.41,37.77,-122.40,37.78',
  'follow'    => '777925' // themattharris
);

$tmhOAuth->streaming_request('POST', $method, $params, 'my_streaming_callback');

// output any response we get back AFTER the Stream has stopped -- or it errors
tmhUtilities::pr($tmhOAuth);

?>

