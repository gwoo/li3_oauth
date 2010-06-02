<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\tests\cases\models;

use \li3_oauth\models\Consumer;

class ConsumerTest extends \lithium\test\Unit {

	public function setUp() {
		Consumer::config(array(
			'host' => 'localhost',
			'oauth_consumer_key' => 'key',
			'oauth_consumer_secret' => 'secret',
			'request' => 'libraries/oauth_php/example/request_token.php',
			'access' => 'libraries/oauth_php/example/access_token.php',
			'port' => 30500
		));
	}

	public function testAuthorize() {
		$expected = 'http://localhost/oauth/authorize?oauth_token=requestkey';
		$result = Consumer::authorize(array(
			'oauth_token' => 'requestkey',
			'oauth_token_secret' => 'requestsecret'
		));
		$this->assertEqual($expected, $result);
	}

	public function testAuthenticate() {
		$expected = 'http://localhost/oauth/authenticate?oauth_token=requestkey';
		$result = Consumer::authenticate(array(
			'oauth_token' => 'requestkey',
			'oauth_token_secret' => 'requestsecret'
		));
		$this->assertEqual($expected, $result);
	}

	public function testRequestToken() {
		$expected = array(
			'oauth_token' => 'requestkey',
			'oauth_token_secret' => 'requestsecret'
		);
		$result = Consumer::token('request', array(
			'oauth_token' => 'key',
			'oauth_token_secret' => 'secret'
		));
		$this->assertEqual($expected, $result);
	}

	public function testAccessToken() {
		$expected = array(
			'oauth_token' => 'accesskey',
			'oauth_token_secret' => 'accesssecret'
		);
		$token = array(
			'oauth_token' => 'requestkey',
			'oauth_token_secret' => 'requestsecret'
		);
		$result = Consumer::token('access', compact('token'));
		$this->assertEqual($expected, $result);
	}

	public function testPost() {
		$expected = '{"test":"cool"}';
		$token = array(
			'oauth_token' => 'requestkey',
			'oauth_token_secret' => 'requestsecret'
		);
		Consumer::config(array('classes' => array(
			'socket' => '\li3_oauth\tests\mocks\extensions\service\MockSocket',
		)));
		$result = Consumer::post('search', array(), compact('token'));
		$this->assertEqual($expected, $result);
	}
}

?>