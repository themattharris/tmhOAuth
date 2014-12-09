# Twitter API Extension

## Usage

Import the `tmhOAuth.php` library:

```
require_once "tmhOAuth.php";
```

Create a new instance:
```
 $twitter = new Twitter($config = array(
    'consumer_key'          => '',
    'consumer_secret'       => '',
    'user_token'            => '',
    'user_secret'           => '',
    'screen_name'           => ''
));
```

Get your tweets (consult the `twitterApi.php` file for more methods).
```
// fetches the 10 latest tweets from your timeline
$tweets = $twitter->get(10);
```

If you need to JSON encode the response to serve a Javascript AJAX request:
```
header('Content-Type: application/json');
echo json_encode($tweets);
```

## Attribution
The `tmhOAuth.php` script was implemented by [themattharris](https://github.com/themattharris/tmhOAuth). Please consult those docs regarding Twitter OAuth stuff.

## Changelog

+ 2014/12/09: Added search method
+ 2013/06/05: Created
