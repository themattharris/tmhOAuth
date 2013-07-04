<?php

/**
 * Twitter
 *
 * An interface to the Twitter OAuth API
 * found in the libraries directory
 *
 * Exmaple usage:
 *
 * <code>
 * 		// First update the keys in the initialize function, then...
 * 		require_once "thmOAuth.php"; // import the oauth library
 * 		$tweets = Twitter::get(10); // fetches the 10 latest tweets
 *
 * 		// If you need to JSON encode the response to serve a Javascript AJAX request
 * 		header('Content-Type: application/json');
 * 		echo json_encode($tweets);
 * </code>
 *
 * @author adamcbrewer
 * @version 1.0.0
 *
 * 05 June 2013
 *
 */
class Twitter {

	/**
	 * SHould contain an instance of the
	 * Twitter Oauth library
	 *
	 */
	private static $api;


	/**
	 * The twitter username of the account holder
	 *
	 */
	private static $screen_name;


	/**
	 * Pupulate and set-up our class vars
	 *
	 * @return object $api
	 */
	public static function initialize () {

		/*static::$api = new tmhOAuth(array(
			'consumer_key'    		=> '',
			'consumer_secret' 		=> '',
			'user_token'      		=> '',
			'user_secret'     		=> '',
			'curl_ssl_verifypeer'   => FALSE
		));*/
	/*
	 * RZ API implementation -- You can use hard coded values
	 */
		static::$api = new tmhOAuth(array(
			'consumer_key'    		=> rz_setting::get('twitter_consumer_key'),
			'consumer_secret' 		=> rz_setting::get('twitter_consumer_secret'),
			'user_token'      		=> rz_setting::get('twitter_access_token'),
			'user_secret'     		=> rz_setting::get('twitter_access_token_secret'),
			'curl_ssl_verifypeer'   => FALSE
		));

		static::$screen_name = rz_setting::get('twitter_account');

		return static::$api;

	}

	public static function &api()
	{
		if (static::$api === null) {

			static::initialize();
		}
		return static::$api;
	}
	/**
	 * Get a number of specified tweets back from the user's timeline,
	 * optionally JSON formatted
	 *
	 * @param  integer $tweet_count 	The number of tweets to get
	 * @param  boolean $json_encode 	JSON encoded or not
	 * @return object $response
	 */
	public static function get ($tweet_count = 10, $count_replies = false ) {

		static::api()->request('GET', static::api()->url('1.1/statuses/user_timeline'), array(
			'include_entities' => 1,
			'include_rts'      => 1,
			'screen_name'      => static::$screen_name,
			'count'            => $tweet_count,
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
		}

		return false;

	}


	/**
	 * Fetch a particular tweet and all relevant data
	 *
	 * @param  integer $id 	The id of the tweet
	 * @return object $response
	 */
	public static function find ( $id = null ) {

		static::api()->request('GET', static::api()->url('1.1/statuses/show'), array(
			'id' => $id,
			'trim_user' => false,
			'include_entities' => true
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
		}

		return false;

	}


	/**
	 * Return a list of follower IDs, or a list of users
	 * if specified in the parameters
	 *
	 * @param  boolean $list 		to return a list of IDs or user objects?
	 *
	 */
	public static function followers ( $list = false ) {

		static::api()->request('GET', static::api()->url('1.1/followers/ids'), array(
			'screen_name' => static::$screen_name
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			$response = json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
			if ($list === TRUE) {
				return static::users($response->ids);
			} else {
				return count($response->ids);
			}
		}

		return false;

	}



	/**
	 * Fetch a list of user objects by specifying either a
	 * comma-separated list of user_ids or and array of them
	 *
	 * @param  string/array $user_ids An array or comma-separated string of user IDs
	 * @return Twitter user objects
	 */
	public static function users ( $user_ids = '' ) {

		if (is_array($user_ids)) {
			$user_ids = implode(',', $user_ids);
		}

		$response = static::api()->request('GET', static::api()->url('1.1/users/lookup'), array(
			'user_id' => $user_ids,
			'include_entities' => TRUE
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
		}

		return false;

	}



	/**
	 * View the profile of a pecific Twitter user
	 *
	 * @param  string $username The twitter username/screen_name
	 * @return Twitter user object
	 */
	public static function user ( $username = '' ) {

		$response = static::api()->request('GET', static::api()->url('1.1/users/show'), array(
			'screen_name' => $username,
			'include_entities' => true
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
		}

		return false;

	}



	/**
	 * Fetch a list of user-mentions, optionally
	 * specifying a count of returned tweet objects
	 *
	 * @param  integer $count
	 * @return object tweets
	 */
	public static function mentions ( $count = 50 ) {

		static::api()->request('GET', static::api()->url('1.1/statuses/mentions_timeline'), array(
			'count' => $count,
			'include_entities' => true
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
		}

		return false;

	}



	/**
	 * Get replies to a message
	 *
	 * @param  string/int $since_id the ID of the tweet we want replies after
	 * @param  boolean $count_replies Only return a number of the replies
	 * @return mixed Tweets replies or a count
	 */
	public static function replies ( $since_id, $count_replies = false ) {

		$params = array (
			'include_entities' => true,
			'since_id' => $since_id,
			'count' => 100
		);

		// we need to minimise the payload since we only want a number
		if ( $count_replies === true ) $params['include_entities'] = false;

		static::api()->request('GET', static::api()->url('1.1/statuses/mentions_timeline'), $params);

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			$mentions = json_decode($response, false, 512, JSON_BIGINT_AS_STRING);

			// We have all replies since the tweet ID we've specified, but
			// here is where twe pick out only the ones that are specifically
			// replies to our ID
			$replies = array_filter($mentions, function ($mention) use ( $since_id )  {
				if ($mention->in_reply_to_status_id_str == $since_id) return $mention;
			});

			if ( $count_replies === true ) return count($replies);

			return (object) $replies;
		}

		return false;

	}




	/**
	 * The home timeline is central to how most
	 * users interact with the Twitter service. It's basically
	 * the user's homepage on the web version.
	 *
	 * @param  integer $count
	 * @return object tweets
	 */
	public static function home_timeline ( $count = 20 ) {

		static::api()->request('GET', static::api()->url('1.1/statuses/home_timeline'), array(
			'count' => $count,
			'include_entities' => true
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
		}

		return false;

	}



	/**
	 * Favourite a specific tweet
	 *
	 * @param  string  $type create (favourite) or destroy (unfavourite)
	 * @param  string/int $id tweet id to favourite
	 * @return object
	 */
	public static function favourite ( $type = 'create', $id = '' ) {

		if ( ! in_array($type, array('destroy', 'create'))) {
			$type = 'create';
		}

		static::api()->request('POST', static::api()->url('1.1/favorites/' . $type), array(
			'id' => $id,
			'include_entities' => false
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
		}

		return false;

	}




	/**
	 * Sending out a tweet through Twitter's oAuth API
	 *
	 * We can specify an ID of a message we're replying to,
	 * but a reply will only be served if the status body contains
	 * the username of the person/message we're replying to
	 *
	 * @param string $message 		The tweet you want to post
	 * @param sting/int $in_reply_to_status_id The ID of the message we're replying to
	 */
	public static function create ( $tweet = '', $in_reply_to_status_id = false ) {

		$params = array(
			'status' => $tweet
		);
		if ( $in_reply_to_status_id !== false ) {
			$params['in_reply_to_status_id'] = $in_reply_to_status_id;
		}

		static::api()->request('POST', static::api()->url('1.1/statuses/update'), $params);

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false, 512, JSON_BIGINT_AS_STRING);
		}

		return false;

	}


	/**
	 * Delete a tweet
	 *
	 * @param int $id
	 */
	public static function delete ( $id = null ) {

		$destroyed = static::api()->request('POST', static::api()->url('1.1/statuses/destroy/' .$id), array(
			'id' => $id,
			'trim_user' => TRUE
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return true;
		}

		return false;

	}

}
