<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\models;

use \li3_oauth\extensions\service\Oauth;

class Consumer extends \lithium\core\StaticObject {

	/**
	 * Holds an instance of the oauth service class
	 *
	 * @see \li3_oauth\extensions\services\Oauth
	 */
	protected static $_service = null;

	/**
	 * Configure the Consumer to access the Oauth service layer
	 * {{{
	 * Consumer::config(array(
	 *    'host' => 'localhost',
	 *    'oauth_consumer_key' => 'key',
	 *    'oauth_consumer_secret' => 'secret',
	 *    'request_token' => 'libraries/oauth_php/example/request_token.php',
	 *    'access_token' => 'libraries/oauth_php/example/access_token.php',
	 * ));
	 * }}}
	 *
	 * @param array $config
	 *              - host: the oauth domain
	 *              - oauth_consumer_key: key from oauth service provider
	 *              - oauth_consumer_secret: secret from oauth service provider
	 *              - oauth_consumer_key: key from oauth service provider
	 *              - authorize: path to authorize  url
	 *              - request_token: path to request token url
	 *              - access_token: path to access token url
	 *
	 * @return void
	 */
	public static function config($config) {
		static::$_service = new Oauth($config);
	}

	/**
	 * Signs and Sends a post request to the request token endpoint with optional params
	 *
	 * @param array $options optional params for the request
	 * @return string
	 */
	public static function request($params = array(), $options = array()) {
		return static::$_service->send('request_token', $params + array(
			'hash' => 'HMAC-SHA1', 'method' => 'POST'
		), $options);
	}

	/**
	 * Signs and Sends request to access token endpoint with the token returned from request method
	 *
	 * @param array $token return value from `Consumer::request()`
	 * @return string
	 */
	public static function access($token, $params = array(), $options = array()) {
		return static::$_service->send('access_token', $params + array(
			'hash' => 'HMAC-SHA1', 'method' => 'POST', 'token' => (array) $token,
		), $options);
	}

	/**
	 * Signs and Sends a post request to the given url
	 *
	 * @param string $url request path that follows host: eg `/statues/update.json`
	 * @param array $token the token from a request
	 * @param array $data data to send as the body of the request
	 * @return string
	 */
	public static function post($url, $token, $data = array(), $params = array(), $options = array()) {
		return static::$_service->send($url, $params + array(
			'hash' => 'HMAC-SHA1', 'method' => 'POST', 'token' => (array) $token, 'data' => $data
		), $options);
	}

	/**
	 * get url from remote authorization endpoint along with request params
	 *
	 * @param mixed $token
	 * @return string
	 */
	public static function authorize($token) {
		$url = static::$_service->url('authorize');
		if (is_array($token)) {
			if (empty($token['oauth_token'])) {
				return $url;
			}
			$token = $token['oauth_token'];
		}
		return "{$url}?oauth_token={$token}";
	}

	/**
	 * get url from remote authenticated endpoint along with token
	 *
	 * @param mixed $token
	 * @return string
	 */
	public static function authenticate($token) {
		$url = static::$_service->url('authenticate');
		if (is_array($token)) {
			if (empty($token['oauth_token'])) {
				return $url;
			}
			$token = $token['oauth_token'];
		}
		return "{$url}?oauth_token={$token}";
	}
	
	/**
	 * undocumented function
	 *
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public static function store($key, $value) {
		return static::$_service->storage->write($key, $value);
	}

	/**
	 * undocumented function
	 *
	 * @param string $key
	 * @return void
	 */
	public static function fetch($key) {
		return static::$_service->storage->read($key);
	}
	
	/**
	 * undocumented function
	 *
	 * @param string $key
	 * @return void
	 */
	public static function delete($key) {
		return static::$_service->storage->remove($key);
	}
}

?>