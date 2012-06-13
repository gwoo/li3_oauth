<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth_pecl\extensions\service;

use \OAuth as PeclOauth;
use \OAuthException;

/**
 * Oauth service class for handling requests/response to consumers and from providers
 *
 *
 */
class Oauth extends \lithium\net\http\Service {

	protected $_autoConfig = array('classes' => 'merge');
	protected $_defaults = array(
		'scheme'					=> 'http',
		'host'						=> 'localhost',
		'proxy'						=> false,
		'authorize'					=> '/oauth/authorize',
		'request_token'				=> '/oauth/request_token',
		'access_token'				=> '/oauth/access_token',
		'oauth_consumer_key'		=> 'OAUTH_CONSUMER_KEY',
		'oauth_consumer_secret'		=> 'OAUTH_CONSUMER_SECRET',
		'oauth_callback'			=> null,
		'oauth_auth_type'			=> 'authorization',
		'oauth_signature_method'	=> 'HMAC-SHA1',
		'oauth_version'				=> '1.0',
		'oauth_debug'				=> false,
		'request_engine'			=> 'streams',
		'enable_ssl_checks'			=> false
	);
	protected static $_OAuth;
	
	/**
	 * If a key is set returns the value of that key
	 * Without a key it will return config array
	 *
	 * @param string $key eg `oauth_consumer_key`
	 * @return void
	 */
	public function config($key = null) {
		if (isset($this->_config[$key])) {
			return $this->_config[$key];
		}
		if ($key !== null) {
			return $key;
		}
		return $this->_config;
	}
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 *              - host: the oauth domain
	 *              - oauth_consumer_key: key from oauth service provider
	 *              - oauth_consumer_secret: secret from oauth service provider
	 *              - oauth_consumer_key: key from oauth service provider
	 *              - authorize: path to authorize  url
	 *              - request_token: path to request token url
	 *              - access_token: path to access token url
	 */
	public function __construct($config = array()) {
		return $this->_configure($config);
	}
	
	protected function _configure(array $config) {
		$this->_config += $config + $this->_defaults;
		extract($this->_config);
		
		static::$_OAuth = new PeclOauth(
			$oauth_consumer_key,
			$oauth_consumer_secret,
			$this->_getSignMethod($oauth_signature_method),
			(int) $this->_getAuthType($oauth_auth_type)
		);
		
		if (is_object(static::$_OAuth)) {
			return false;
		}
		if (!$this->setVersion($oauth_version)) {
			return false;
		}
		if (!$this->setNonce(sha1(time() . mt_rand()))) {
			return false;
		}
		if (!$this->setTimestamp(time())) {
			return false;
		}
		if ($disable_ssl_checks) {
			$this->disableSSLChecks();
		}		
		if ($oauth_debug) {
			$this->enableDebug();
		}
		if (!$this->setRequestEngine($engine = 'streams')) {
			return false;
		}
		
		return true;
	}
	
	public function setRequestEngine($engine = 'streams') {
		switch($engine) {
			case 'streams':
				return static::$_OAuth->setRequestEngine(OAUTH_REQENGINE_STREAMS);
			break;
			
			case 'curl':
				return static::$_OAuth->setRequestEngine(OAUTH_REQENGINE_CURL);
			break;	
		}
		return static::$_OAuth->setRequestEngine(OAUTH_REQENGINE_STREAMS);
	}
	
	protected function _getSignMethod($method = 'HMAC-SHA1') {
		switch($method) {
			case 'HMAC-SHA1':
				return OAUTH_SIG_METHOD_HMACSHA1;
			break;
			
			case 'RSA-SHA1':
				return OAUTH_SIG_METHOD_RSASHA1;
			break;
			
			case  'HMAC-SHA256':
				return OAUTH_SIG_METHOD_HMACSHA256;
			break;
		}
		return OAUTH_SIG_METHOD_HMACSHA1;
	}
	
	protected function setAuthType($type = 'authorization') {
		return static::$_OAuth->setAuthType($this->_getAuthType($type));
	}
	
	protected function _getAuthType($type = 'authorization') {
		switch ($type) {
			case 'none':
				return OAUTH_AUTH_TYPE_NONE;
			break;
			
			case 'authorization':
				return OAUTH_AUTH_TYPE_AUTHORIZATION;
			break;
			
			case 'form':
				return OAUTH_AUTH_TYPE_FORM;
			break;
			
			case 'uri':
				return OAUTH_AUTH_TYPE_URI;
			break;
		}
		return OAUTH_AUTH_TYPE_NONE;
	}
	
	protected function _getMethodType($type = 'GET') {
		$type = strtoupper($type);
		switch ($type) {
			case 'GET':
				return OAUTH_HTTP_METHOD_GET;
			break;
			
			case 'POST':
				return OAUTH_HTTP_METHOD_POST;
			break;
			
			case 'PUT':
				return OAUTH_HTTP_METHOD_PUT;
			break;
			
			case 'HEAD':
				return OAUTH_HTTP_METHOD_HEAD;
			break;
			
			case 'DELETE':
				return OAUTH_HTTP_METHOD_DELETE;
			break;
		}
		return OAUTH_AUTH_TYPE_GET;
	}
	
	protected function _setToken(array $token = array()) {
		if(!empty($token['oauth_token'])) {
			if(!empty($token['oauth_token_secret'])) {
				extract($token);
				return static::$_OAuth->setToken($oauth_token, $oauth_token_secret);
			}
		}
		return false;
	}
	
	public function token($type, array $options = array()) {
		$defaults = array(
			'token' => array(),
			'oauth_callback'		=> $this->_config['oauth_callback'],
			'oauth_session_handle'	=> null
		);
		$options += $defaults;
		$options['token'] += array('oauth_verifier' => null);
		
		$url = $this->url("{$type}_token", $options);

		extract($options);
		
		if($type === 'request') {
			return static::$_OAuth->getRequestToken($url, $oauth_callback);
		}
		if($type === 'access') {
			return static::$_OAuth->getAccessToken($url, $oauth_session_handle, $token['oauth_verifier']);
		}
		
		return false;
	}

	/**
	 * Send request with the given options and data. The token should be part of the options.
	 *
	 * @param string $method
	 * @param string $path
	 * @param array $data encoded for the request
	 *
	 */
 	/**
 	 * Send request with the given options and data. The token should be part of the options.
 	 *
 	 * @param string $method
 	 * @param string $path
 	 * @param array $data encoded for the request
 	 * @param array $options oauth parameters
 	 *              - headers : send parameters in the header. (default: true)
 	 *              - realm : the realm to authenticate. (default: app directory name)
 	 * @return void
 	 */
 	public function send($method, $path = null, $data = array(), array $options = array()) {
 		$defaults = array('headers' => array());
 		$options += $defaults + $this->_config;
 		$url = $this->config($path);
 		$options['host'] = $options['proxy'] ? $options['proxy'] : $options['host'];
		$url = $this->url($url, $options);
		
		if(!empty($options['token']['oauth_token_secret'])) {
			if(!$this->_setToken($options['token'])) {
				return false;
			}
		}
		
		$fetch = static::$_OAuth->fetch($url, $data, $this->_getMethodType($method), $options['headers']);
		return $fetch ? static::$_OAuth->getLastResponse() : false;
 	}

	/**
	 * A utility method to return a authorize or authenticate url for redirect
	 *
	 * @param string $url
	 * @param array $options
	 *              - `token`: (array) adds the oauth_token to the query params
	 *              - `usePort`: (boolean) use the port in the signature base string
	 * @return void
	 */
	public function url($url = null, array $options = array()) {
		$defaults = array('token' => array('oauth_token' => null), 'usePort' => false);
		$options += $defaults + $this->_config;
		
		$args = '';
		if (!empty($options['token']['oauth_token'])) {
			if($url === 'authorize') {
				$args = "?oauth_token={$options['token']['oauth_token']}";
			}
			if(!empty($options['token']['oauth_token_secret'])) {
				$this->_setToken($options['token']);
			}
		}
		$url = $url ? $this->config($url) : null;
		
		$base = $options['host'];
		$base .= ($options['usePort']) ? ":{$options['port']}" : null;
		return "{$this->_config['scheme']}://" . str_replace('//', '/', "{$base}/{$url}{$args}");
	}
}

?>