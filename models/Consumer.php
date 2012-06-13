<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_pecl_oauth\models;

class Consumer extends \lithium\core\StaticObject {

	/**
	 * Holds an instance of the oauth service class
	 *
	 * @see \li3_pecl_oauth\extensions\services\Oauth
	 */
	protected static $_service = null;

	protected static $_classes = array(
		'oauth' => '\li3_pecl_oauth\extensions\service\Oauth'
	);

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
		static::$_service = new static::$_classes['oauth']($config);
	}

	/**
	 * Magic method to pass through HTTP methods. i.e.`Consumer::post()`
	 *
	 * @param string $method
	 * @param string $params
	 * @return mixed
	 */
	public static function __callStatic($method, $params) {
		return static::$_service->invokeMethod($method, $params);
	}

	/**
	 * Signs and Sends a post request to the request token endpoint with optional params
	 *
	 * @param string $type the type of token to get. request|access
	 * @param array $options optional params for the request
	 *              - `method`: POST
	 *              - `oauth_signature_method`: HMAC-SHA1
	 * @return string
	 */
	public static function token($type, array $options = array()) {
		return static::$_service->token($type, $options);
	}

	/**
	 * get url from remote authorization endpoint along with request params
	 *
	 * @param array $token
	 * @param array $options
	 * @return string
	 */
	public static function authorize(array $token, array $options = array()) {
		return static::$_service->url('authorize', compact('token') + $options);
	}

	/**
	 * get url from remote authenticated endpoint along with token
	 *
	 * @param array $token
	 * @param array $options
	 * @return string
	 */
	public static function authenticate(array $token, array $options = array()) {
		return static::$_service->url('authenticate', compact('token') + $options);
	}
}

?>