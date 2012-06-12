<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth_pecl\extensions\service;

use \OAuth;

/**
 * Oauth service class for handling requests/response to consumers and from providers
 *
 *
 */
class Oauth extends \OAuth {

	protected $_autoConfig = array('classes' => 'merge');

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
		$defaults = array(
			'scheme'				=> 'http',
			'host'					=> 'localhost',
			'proxy'					=> false,
			'authorize'				=> '/oauth/authorize',
			'request_token'			=> '/oauth/request_token',
			'access_token'			=> '/oauth/access_token',
			'oauth_consumer_key'	=> 'key',
			'oauth_consumer_secret'	=> 'secret',
			'oauth_redirect'		=> false,
			'oauth_debug'			=> false,
			'oauth_auth_type'		=> 'authorization',
			'oauth_sign_method'		=> 'HMAC-SHA1',
			'oauth_version'			=> '1.0',
			'request_engine'		=> 'streams',
			'disable_ssl_checks'	=> false
		);
		$config += $defaults;
		
		extract($config);
		
		parent::__construct($config['oauth_consumer_key'], $config['oauth_consumer_secret'], null, $this->_getSignMethod($oauth_sign_method));

		$this->_setAuthType($oauth_auth_type);		
		$this->setVersion($oauth_version);
		$this->setTimestamp(time());
		
		if ($disable_ssl_checks) {
			$this->disableSSLChecks();
		}
		if ($oauth_debug) {
			$this->enableDebug();
		}
	}
	
	protected _setRequestEngine($engine = 'streams') {
		if($engine == 'streams') {
			return $this->setRequestEngine(OAUTH_REQENGINE_STREAMS);
		}
		if($engine == 'curl') {
			return $this->setRequestEngine(OAUTH_REQENGINE_CURL);
		}
		return $this->setRequestEngine(OAUTH_REQENGINE_STREAMS);
	}
	
	protected _getSignMethod($method = 'HMAC-SHA1') {
		if($method === 'HMAC-SHA1') {
			return OAUTH_SIG_METHOD_HMACSHA1;
		}
		if($method === 'RSA-SHA1') {
			return OAUTH_SIG_METHOD_RSASHA1;
		}
		if($method === 'HMAC-SHA256') {
			return OAUTH_SIG_METHOD_HMACSHA256;
		}
		return OAUTH_SIG_METHOD_HMACSHA1;
	}
	
	protected _setAuthType($type = 'authorization') {
		if($type === 'authorization') {
			$this->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);
		}
		if($type === 'form') {
			$this->setAuthType(OAUTH_AUTH_TYPE_FORM);
		}
		if($type === 'uri') {
			$this->setAuthType(OAUTH_AUTH_TYPE_URI);
		}
		return $this->setAuthType(OAUTH_AUTH_TYPE_NONE);
	}
	
	protected _getHttpType($method = 'get') {
		if($type === 'get') {
			return OAUTH_HTTP_METHOD_GET;
		}
		if($type === 'post') {
			return OAUTH_HTTP_METHOD_POST;
		}
		if($type === 'delete') {
			return OAUTH_HTTP_METHOD_DELETE;
		}
		if($type === 'put') {
			return OAUTH_HTTP_METHOD_PUT;
		}
		if($type === 'head') {
			return OAUTH_HTTP_METHOD_HEAD;
		}
		return OAUTH_HTTP_METHOD_GET;
	}

	/**
	 * Send request with the given options and data. The token should be part of the options.
	 *
	 * @param string $method
	 * @param string $path
	 * @param array $data encoded for the request
	 *
	 */
	public function send($method = 'POST', $path = null, $data = array(), array $options = array()) {
		$method = $this->_getHttpType($method);
		$options['headers'] += array();
		return $this->fetch($this->url($path), $data, $method, $options['headers']);
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
		$defaults = array('token' => array('oauth_token' => false), 'usePort' => false);
		$options += $defaults + $this->_config;
		$url = !empty($options[$url]) ? $options['url'] : $url;

		if (!empty($options['token']['oauth_token'])) {
			$url = "{$url}?oauth_token={$options['token']['oauth_token']}";
		}
		$base = $this->_config['host'];
		$base .= ($options['usePort']) ? ":{$this->_config['port']}" : null;
		return "{$this->_config['scheme']}://" . str_replace('//', '/', "{$base}/{$url}");
	}
}

?>