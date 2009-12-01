<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\models;

use \li3_oauth\extensions\service\Oauth;

class Provider extends \lithium\core\StaticObject {

	/**
	 * Holds an instance of the oauth service class
	 *
	 * @see \li3_oauth\extensions\services\Oauth
	 */
	protected static $_service = null;

	/**
	 * Configure the Consumer to access the Oauth service layer
	 * {{{
	 * Provider::config(array(
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
	 * generate a consumer or token
	 *
	 * @param string $type
	 *               - consumer: creates object with oauth_consumer_key and oauth_consumer_secret
	 *               - token: creates query string with aoauth_token and oauth_token_secret
	 * @param string $key
	 * @param string $secret
	 * @return void
	 */
	public static function create($type, $key = null, $secret = null) {
		$key = $key ?: sha1(mt_rand());
		$secret = $secret ?: sha1(mt_rand());
		switch($type) {
			case 'token':
				return (object) array(
					'oauth_token' => $key, 'oauth_token_secret' => $secret
				);
			break;
			default:
			case 'consumer':
				return (object) array(
					'oauth_consumer_key' => $key, 'oauth_consumer_secret' => $secret
				);
			break;
		}
	}

	/**
	 * undocumented function
	 *
	 * @param string $query
	 * @return void
	 */
	public static function verify($request) {
		if (!empty($request['params']['oauth_signature'])) {
			$sig = $request['params']['oauth_signature'];
			unset($request['params']['oauth_signature']);
			$test = static::$_service->sign($request);

			if ($sig == $test['oauth_signature']) {
				return true;
			}
		}
		return false;
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