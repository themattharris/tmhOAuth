<?php

require '../tmhOAuth.php';

if ( ! empty($_FILES)) {
  $tmhOAuth = new tmhOAuth(array(
    'consumer_key'    => '',
    'consumer_secret' => '',
    'user_token'      => '',
    'user_secret'     => '',
  ));

  $tmhOAuth->request('POST', $tmhOAuth->url("account/{$_POST['method']}"), array(
      'image' => "@{$_FILES['image']['tmp_name']};type={$_FILES['image']['type']};filename={$_FILES['image']['name']}",
      'include_entities'  => '1'
    ),
    true, // use auth
    true  // multipart
  );

  if ($tmhOAuth->response['code'] == 200) {
    $tmhOAuth->pr(json_decode($tmhOAuth->response['response']));
  }
  $tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
}

?>

<form action="" method="POST" enctype="multipart/form-data">
  <div>
    <select name="method" id="method" >
      <option value="update_profile_image">update_profile_image</option>
      <option value="update_profile_background_image">update_profile_background_image</option>
    </select>
    <input type="file" name="image" />
    <input type="submit" value="Submit" />
  </div>
</form>