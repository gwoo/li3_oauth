<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\tests\cases\extensions\service;

use \li3_oauth\tests\mocks\extensions\service\MockOauth;

class OauthTest extends \lithium\test\Unit {

	protected $_testConfig = array(
		'classes' => array(
			'socket' => '\li3_oauth\tests\mocks\extensions\service\MockSocket',
		),
		'persistent' => false,
		'protocol' => 'http',
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'port' => 80,
		'timeout' => 1,
		'oauth_consumer_secret' => 'secret'
	);

	public function testDefaultConfig() {
		$oauth = new MockOauth($this->_testConfig);
		$config = $oauth->config();

		$expected = '/oauth/request_token';
		$result = $config['request'];
		$this->assertEqual($expected, $result);
	}

	public function testCustomConfig() {
		$config = $this->_testConfig;
		$config['request'] = 'request_token.php';
		$oauth = new MockOauth($config);
		$config = $oauth->config();

		$expected = 'request_token.php';
		$result = $config['request'];
		$this->assertEqual($expected, $result);
	}

	public function testDecode() {
		$oauth = new MockOauth($this->_testConfig);

		$expected = array('oauth_token' => 12345, 'oauth_secret' => 54321);
		$result = $oauth->decode('oauth_token=12345&oauth_secret=54321');
		$this->assertTrue($result);
	}

	public function testPostRequestToken() {
		$oauth = new MockOauth($this->_testConfig);

		$expected = array(
			'oauth_token' => 'requestkey',
			'oauth_token_secret' => 'requestsecret'
		);
		$result = $oauth->post('request');
		$this->assertEqual($expected, $result);
	}

	public function testPostAcceesToken() {
		$oauth = new MockOauth($this->_testConfig);

		$expected = array(
			'oauth_token' => 'accesskey',
			'oauth_token_secret' => 'accesssecret'
		);
		$result = $oauth->post('access', array('message' => 'hello'), array(
			'params' => array(),
			'token' => array(
				'oauth_token' => 'requestkey',
				'oauth_token_secret' => 'requestsecret'
			)
		));
		$this->assertEqual($expected, $result);
	}

	public function testConfigUrl() {
		$oauth = new MockOauth($this->_testConfig);
		$expected = 'http://localhost:80/';
		$result = $oauth->url();
		$this->assertEqual($expected, $result);

		$expected = 'http://localhost:80/oauth/request_token';
		$result = $oauth->url('request');
		$this->assertEqual($expected, $result);


		$expected = 'http://localhost:80/oauth/access_token';
		$result = $oauth->url('access');
		$this->assertEqual($expected, $result);

	}

	public function testSign() {
		$oauth = new MockOauth($this->_testConfig);
		$params =  array(
			'oauth_signature_method' => 'HMAC-SHA1',
			'params' => array(
				'oauth_consumer_key' => 'key',
				'oauth_nonce' => '4d31073c8ce205ecd3145d6cc0a3a4f6',
				'oauth_timestamp' => '1259606608',
			),
		);
		$params = $oauth->sign($params);

		$expected = 'jpQW675nV7uzAbW2jukVr/kfqDA=';
		$result = $params['oauth_signature'];
		$this->assertEqual($expected, $result);
		
		$params =  array(
			'method' => 'GET',
			'oauth_signature_method' => 'HMAC-SHA1',
			'params' => array(
				'oauth_consumer_key' => 'key',
				'oauth_nonce' => '4d31073c8ce205ecd3145d6cc0a3a4f6',
				'oauth_timestamp' => '1259606608',
			),
		);
		$params = $oauth->sign($params);

		$expected = 'LbeBxtQ9vXOxK6eZgKXBFqIWN7A=';
		$result = $params['oauth_signature'];
		$this->assertEqual($expected, $result);
	}

}
?>