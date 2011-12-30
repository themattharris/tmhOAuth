<?php

require '../tmhOAuth.php';
require '../tmhUtilities.php';

// since version 0.6 tmhOAuth automatically sets the SSL parameters to true.
// we do it here for readability for this test.

$tmhOAuth = new tmhOAuth(array(
  'curl_ssl_verifypeer' => true,
  'curl_ssl_verifyhost' => true,
));


// Make an SSL request to the Twitter API help/test endpoint
$code = $tmhOAuth->request(
  'GET',
  $tmhOAuth->url('1/help/test'),
  array(),
  false
);

// Verify the SSL worked as expected
if ($code == 200 && $tmhOAuth->response['info']['ssl_verify_result'] === 0) {
  echo 'A verified SSL connection was successfully made to ' . $tmhOAuth->response['info']['url'] . PHP_EOL;
} elseif ($code == 200 && $tmhOAuth->response['info']['ssl_verify_result'] !== 0) {
  echo 'ERROR: A verified SSL connection could not be successfully made to ' . $tmhOAuth->response['info']['url'] . PHP_EOL;
  echo 'The error was: ' . $tmhOAuth->response['error'];
} elseif ($code !== 200) {
  echo 'ERROR: There was a problem making the request' . PHP_EOL;
  echo 'The error was: ' . $tmhOAuth->response['error'] . PHP_EOL;
}

?>