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
			'service' => '\li3_oauth\tests\mocks\extensions\service\MockService',
		),
		'persistent' => false,
		'protocol' => 'http',
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'port' => 80,
		'timeout' => 1
	);

	public function testDefaultConfig() {
		$oauth = new MockOauth($this->_testConfig);
		$config = $oauth->config();

		$expected = 'oauth/request_token';
		$result = $config['request_token'];
		$this->assertEqual($expected, $result);
	}

	public function testCustomConfig() {
		$this->_testConfig['request_token'] = 'request_token.php';
		$oauth = new MockOauth($this->_testConfig);
		$config = $oauth->config();

		$expected = 'request_token.php';
		$result = $config['request_token'];
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
		$result = $oauth->send('request_token', array(
			'hash' => 'HMAC-SHA1', 'method' => 'POST', 'params' => array()
		));
		$this->assertEqual($expected, $result);
	}
	
	public function testPostAcceesToken() {
		$oauth = new MockOauth($this->_testConfig);

		$expected = array(
			'oauth_token' => 'accesskey',
			'oauth_token_secret' => 'accesssecret'
		);
		$result = $oauth->send('access_token', array(
			'hash' => 'HMAC-SHA1', 'method' => 'POST', 'params' => array(),
			'token' => array(
				'oauth_token' => 'requestkey',
				'oauth_token_secret' => 'requestsecret'
			)
		));
		$this->assertEqual($expected, $result);
	}
}
?>