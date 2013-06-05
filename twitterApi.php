<?php

require_once "thmOAuth.php";

/**
 * Twitter
 *
 * An interface to the Twitter OAuth API
 * found in the libraries directory
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

		static::$api = new tmhOAuth(array(
			'consumer_key'    		=> '',
			'consumer_secret' 		=> '',
			'user_token'      		=> '',
			'user_secret'     		=> '',
			'curl_ssl_verifypeer'   => FALSE
		));

		static::$screen_name = '';

		return static::$api;

	}


	/**
	 * Get a number of specified tweets back from the user's timeline,
	 * optionally JSON formatted
	 *
	 * @param  integer $tweet_count 	The number of tweets to get
	 * @param  boolean $json_encode 	JSON encoded or not
	 * @return object response
	 */
	public static function get ($tweet_count = 10) {

		static::api()->request('GET', static::api()->url('1.1/statuses/user_timeline'), array(
			'include_entities' => 0,
			'include_rts'      => '1',
			'screen_name'      => static::$screen_name,
			'count'            => $tweet_count,
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false);
		}

		return $response;

	}


	/**
	 * Fetch a particular tweet and all relevant data
	 *
	 * @param  integer $id 	The id of the tweet
	 * @return object response
	 */
	public static function find ( $id = null ) {

		static::api()->request('GET', static::api()->url('1.1/statuses/show'), array(
			'id' => $id,
			'trim_user' => FALSE
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response, false);
		}

		return false;

	}


	/**
	 * Return a list of follower IDs, or a list of users
	 * if specified in the parameters
	 *
	 * @param  boolean $list 		to return a list of IDs or user objects?
	 * @return mixed (object) response or a (int) count of followers
	 */
	public static function followers ( $list = false ) {

		static::api()->request('GET', static::api()->url('1.1/followers/ids'), array(
			'screen_name' => static::$screen_name
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			$response = json_decode($response);
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
	 * @return object response
	 */
	private static function users ( $user_ids = '' ) {

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
			return json_decode($response);
		}

		return false;

	}



	/**
	 * Sending out a tweet through Twitter's oAuth API
	 *
	 * @param string $message 		The tweet you want to post
	 * @return object response
	 */
	public static function create ( $tweet = '' ) {

		static::api()->request('POST', static::api()->url('1.1/statuses/update'), array(
			'status' => $tweet
		));

		$response = static::api()->response['response'];
		$code = static::api()->response['code'];

		if ($code === 200) {
			return json_decode($response);
		}

		return false;

	}


	/**
	 * Delete a tweet
	 *
	 * @param int $id
	 * @return bool
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


	/**
	 * Catch method to return the api instance, or create
	 * a new one if it doesn't exist
	 *
	 * @return object API instance
	 */
	private static function api () {
		return is_null(static::$api) ? static::initialize() : static::$api;
	}

}
