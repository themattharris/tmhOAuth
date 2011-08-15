<?php

/**
 * Use OAuth Echo to upload a picture to Posterous and then Tweet about it.
 *
 * Although this example uses your user token/secret, you can use
 * the user token/secret of any user who has authorised your application.
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
 * 4) Visit this page using your web browser.
 *
 * @author themattharris
 */

require '../tmhOAuth.php';
require '../tmhUtilities.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'YOUR_CONSUMER_KEY',
  'consumer_secret' => 'YOUR_CONSUMER_SECRET',
  'user_token'      => 'A_USER_TOKEN',
  'user_secret'     => 'A_USER_SECRET',
));
$delegator = 'http://posterous.com/api2/upload.json';

function generate_verify_header($tmhOAuth) {
  // generate the verify crendentials header -- BUT DON'T SEND
  // note the https URL change - this is due to posterous requiring https in the X-Auth-Service-Provider
  $tmhOAuth->config['prevent_request'] = true;
  $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
  $tmhOAuth->config['prevent_request'] = false;
}

function prepare_request($tmhOAuth) {
  // create the headers for the echo
  $headers = array(
    'X-Auth-Service-Provider'            => $tmhOAuth->url('1/account/verify_credentials'),
    'X-Verify-Credentials-Authorization' => $tmhOAuth->auth_header,
  );

  // load the headers for the request
  $tmhOAuth->headers = $headers;
  // prepare the request to posterous
  $params = array(
    'media'   => "@{$_FILES['file']['tmp_name']};type={$_FILES['file']['type']};filename={$_FILES['file']['name']}",
    'message' => 'trying something out'
  );

  return $params;
}

function make_request($tmhOAuth, $url, $params, $auth, $multipart) {
  // make the request, no auth, multipart, custom headers
  $code = $tmhOAuth->request('POST', $url, $params, $auth, $multipart);

  // Posterous liked it or not?
  if ($code == 200)
    return json_decode($tmhOAuth->response['response']);

  return false;
}

if (TRUE || ! empty($_FILES)) {
  // IMPORTANT: Posterous requires the host be https://api.twitter.com
  // versions 0.11+ of tmhOAuth default to SSL so do not need changing

  generate_verify_header($tmhOAuth);
  $params = prepare_request($tmhOAuth);
  // post to OAuth Echo provider
  $resp = make_request($tmhOAuth, $delegator, $params, false, true);

  // post Tweet to Twitter
  if ($resp !== false) {
    $params = array(
      'status' => 'I just OAuth echoed a picture: ' . $resp->url
    );
    $resp = make_request($tmhOAuth, $tmhOAuth->url('1/statuses/update'), $params, true, false);

    if ($resp)
      tmhUtilities::pr(json_decode($tmhOAuth->response['response']));
    else
      echo 'Error: ' . htmlentities($tmhOAuth->response['response']);
  }
}

?>

<form action="" method="POST" enctype="multipart/form-data">
  <div>
    <input type="file" name="file" />
    <input type="submit" value="Submit" />
  </div>
</form>