<!DOCTYPE html>
<head>
  <meta charset="UTF-8" />
</head>
<body>
<?php

/**
 * Render a very rough timeline with entities included.
 *
 * Instructions:
 * 1) If you don't have one already, create a Twitter application on
 *      http://dev.twitter.com/apps
 * 2) From the application details page copy the consumer key and consumer
 *      secret into the place in this code marked with (YOUR_CONSUMER_KEY
 *      and YOUR_CONSUMER_SECRET)
 * 4) Visit the 'My Access Token' screen linked to from your application
 *      details page
 * 5) Copy the user token and user secret into the place in this code marked
 *      with (A_USER_TOKEN and A_USER_SECRET)
 * 6) Visit this page using your web browser.
 *
 * @author themattharris
 */

require '../tmhOAuth.php';
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'YOUR_CONSUMER_KEY',
  'consumer_secret' => 'YOUR_CONSUMER_SECRET',
  'user_token'      => 'A_USER_TOKEN',
  'user_secret'     => 'A_USER_SECRET',
));

$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/user_timeline'), array(
  'include_entities' => '1',
  'include_rts'      => '1',
  'screen_name'      => 'themattharris',
  'count'            => '1'
));

if ($code == 200) {
  $timeline = json_decode($tmhOAuth->response['response'], true);
  foreach ($timeline as $tweet) :
    $keys = array();
    $replacements = array();
    $is_retweet = false;

    if (isset($tweet['retweeted_status'])) {
      $tweet = $tweet['retweeted_status'];
      $is_retweet = true;
    }

    // prepare the entities
    foreach ($tweet['entities'] as $type => $things) {
      foreach ($things as $entity => $value) {
        $tweet_link = "<a href=\"http://twitter.com/{$value['screen_name']}/statuses/{$tweet['id']}\">{$tweet['created_at']}</a>";

        switch ($type) {
          case 'hashtags':
            $href = "<a href=\"http://search.twitter.com/search?q=%23{$value['text']}\">#{$value['text']}</a>";
            break;
          case 'user_mentions':
            $href = "@<a href=\"http://twitter.com/{$value['screen_name']}\" title=\"{$value['name']}\">{$value['screen_name']}</a>";
            break;
          case 'urls':
            $url = empty($value['expanded_url']) ? $value['url'] : $value['expanded_url'];
            $display = isset($value['display_url']) ? $value['display_url'] : str_replace('http://', '', $url);
            // Not all pages are served in UTF-8 so you may need to do this ...
            $display = urldecode(str_replace('%E2%80%A6', '&hellip;', urlencode($display)));
            $href = "<a href=\"{$value['url']}\">{$display}</a>";
            break;
        }
        $keys[$value['indices']['0']] = substr(
          $tweet['text'],
          $value['indices']['0'],
          $value['indices']['1'] - $value['indices']['0']
        );
        $replacements[$value['indices']['0']] = $href;
      }
    }

    ksort($replacements);
    $replacements = array_reverse($replacements, true);
    $entified_tweet = $tweet['text'];
    foreach ($replacements as $k => $v) {
      $entified_tweet = substr_replace($entified_tweet, $v, $k, strlen($keys[$k]));
    }
  ?>
  <div id="<?php echo $tweet['id_str']; ?>" style="margin-bottom: 1em">
    <span>Orig: <?php echo $tweet['text']; ?></span><br>
    <span>Entitied: <?php echo $entified_tweet ?></span>
    <small><?php echo $tweet_link ?><?php if ($is_retweet) : ?>is retweet<?php endif; ?></small>
  </div>
<?php
  endforeach;
} else {
  $tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
}

?>
</body>
</html>