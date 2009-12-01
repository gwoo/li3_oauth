<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\extensions\service;

/**
 * Oauth service class for handling requests/response to consumers and from providers
 *
 *
 */
class Oauth extends \lithium\core\Object {

	protected $_autoConfig = array('classes' => 'merge');

	/**
	 * Fully-namespaced class references
	 *
	 * @var array
	 */
	protected $_classes = array(
		'service'   => '\lithium\http\Service',
		'storage'  => '\li3_oauth\extensions\storage\File'
	);

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
			'host' => null,
			'authorize' => '/oauth/authorize',
			'request_token' => '/oauth/request_token',
			'access_token' => '/oauth/access_token',
			'oauth_consumer_key' => 'key',
			'oauth_consumer_secret' => 'secret'
		);
		$config += $defaults;

		parent::__construct($config);
	}

	/**
	 * Initialize classes to be used
	 *
	 * @return void
	 */
	public function _init() {
		parent::_init();
		$config = $this->_config;
		if (!empty($config['proxy'])) {
			$config['host'] = $config['proxy'];
		}
		$this->service = new $this->_classes['service']($config);
		$this->storage = new $this->_classes['storage']($config);
	}

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
	 * Send request
	 *
	 * @param string $method
	 * @param string $path
	 * @param string $data
	 * @param string $options
	 * @return void
	 */
	public function send($path = null, $data = null, $options = array()) {
		$url = $this->config($path);
		$method = !empty($options['method']) ? $options['method'] : 'post';
		$data = $this->sign($data + compact('url'));
		$response = $this->service->send($method, $url, $data, $options);
		if (strpos($response, 'oauth_') === 0) {
			return $this->_decode($response);
		}
		return $response;
	}

	/**
	 * undocumented function
	 *
	 * @param string $url
	 * @return void
	 */
	public function url($url) {
		$url = $this->config($url);
		return "http://{$this->_config['host']}{$url}";
	}

	/**
	 * undocumented function
	 *
	 * @param string $options
	 *               - hash: HMAC-SHA1
	 *               - secret: config['oauth_consumer_secret']
	 *               - params: extra params for to sign request
	 *               - url: url of request
	 *               - data: post data for request
	 *               - token: array with keys oauth_token, oauth_token_secret
	 * @return void
	 */
	public function sign($options = array()) {
		$defaults = array(
			'url' => '', 'method' => 'POST', 'hash' => 'HMAC-SHA1',
			'oauth_consumer_secret' => $this->_config['oauth_consumer_secret'],
			'params' => array(), 'data' => array(),
			'token' => array('oauth_token' => null, 'oauth_token_secret' => null),
		);
		$options += $defaults;
		$params = $this->_build($options['params'] + (array)$options['token']) + $options['data'];
		$base = $this->_base($options['method'], $options['url'], $params);
		$key = join("&", array(
			rawurlencode($options['oauth_consumer_secret']),
			rawurlencode($options['token']['oauth_token_secret'])
		));
		switch ($options['hash']) {
			case 'HMAC-SHA1':
				$signature = base64_encode(hash_hmac('sha1', $base, $key, true));
			break;
			default:
				return $options['token']['oauth_token'];
			break;
		}
		$params['oauth_signature'] = $signature;
		return $params;
	}

	/**
	 * undocumented function
	 *
	 * @param string $method
	 * @param string $url
	 * @param string $params
	 * @return void
	 */
	protected function _base($method, $url, $params) {
		$path = $this->url($url);
		$base = join("&", array(
			$method, rawurlencode($path),
			rawurlencode(http_build_query($params))
		));
		return $base;
	}

	/**
	 * undocumented function
	 *
	 * @param string $params
	 * @return void
	 */
	protected function _build($params = array()) {
		$defaults =  array(
			'oauth_consumer_key' => 'key',
			'oauth_nonce' => sha1(time() . mt_rand()),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_token' => '',
			'oauth_version' => '1.0'
		);
		$result = array();
		foreach ($defaults as $key => $value) {
			if (isset($params[$key])) {
				$result[$key] = $params[$key];
				continue;
			}
			if (isset($this->_config[$key])) {
				$result[$key] = $this->_config[$key];
				continue;
			}
			if ($value) {
				$result[$key] = $value;
			}
		}
		ksort($result);
		return $result;
	}

	/**
	 * undocumented function
	 *
	 * @param string $path
	 * @return void
	 */
	protected function _decode($query = null) {
		$token = array();
		$result = array_filter(explode('&', $query), function ($value) use (&$token) {
			$parts = explode("=", $value);
			if (count($parts) > 1) {
				$token[rawurldecode($parts[0])] = rawurldecode($parts[1]);
			}
			return false;
		});
		return $token;
	}
}
?>