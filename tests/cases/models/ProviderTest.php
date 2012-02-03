<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\tests\cases\models;

use li3_oauth\models\Provider;

class ProviderTest extends \lithium\test\Unit {

	public function setUp() {
		Provider::config(array(
			'host' => 'localhost',
			'oauth_consumer_key' => 'key',
			'oauth_consumer_secret' => 'secret'
		));
	}

	public function testCreateConsumer() {
		$expected = (object) array(
			'oauth_consumer_key' => 'key', 'oauth_consumer_secret' => 'secret'
		);
		$result = Provider::create('consumer', 'key', 'secret');
		$this->assertEqual($expected, $result);

		$consumer = Provider::create('consumer');

		$expected = '/[a-z0-9]{40}/';
		$result = $consumer->oauth_consumer_key;
		$this->assertPattern($expected, $result);

		$expected = '/[a-z0-9]{40}/';
		$result = $consumer->oauth_consumer_secret;
		$this->assertPattern($expected, $result);
	}

	public function testCreateToken() {
		$expected = (object) array(
			'oauth_token' => 'request_token', 'oauth_token_secret' => 'request_secret'
		);
		$result = Provider::create('token', 'request_token', 'request_secret');
		$this->assertEqual($expected, $result);

		$token = Provider::create('token');

		$expected = '/[a-z0-9]{40}/';
		$result = $token->oauth_token;
		$this->assertPattern($expected, $result);

		$expected = '/[a-z0-9]{40}/';
		$result = $token->oauth_token_secret;
		$this->assertPattern($expected, $result);
	}

	public function testVerify() {
		$request = array(
			'url' => 'request',
			'params' => array(
				'oauth_consumer_key' => 'key',
				'oauth_nonce' => '4d31073c8ce205ecd3145d6cc0a3a4f6',
				'oauth_signature' => 'GfCKugOKkspnq5ihgPR/9xxpf+E=',
				'oauth_signature_method' => 'HMAC-SHA1', 'oauth_timestamp' => '1259606608',
				'oauth_version' => '1.0'
			)
		);
		$result = Provider::verify($request);
		$this->assertTrue($result);
	}

	public function testVerifyWithToken() {
		$request = array(
			'url' => 'request',
			'params' => array(
				'oauth_consumer_key' => 'key',
				'oauth_nonce' => '4d31073c8ce205ecd3145d6cc0a3a4f6',
				'oauth_signature' => '10xRa+G7ql3KjDgZySmn5NqNLqQ=',
				'oauth_signature_method' => 'HMAC-SHA1', 'oauth_timestamp' => '1259606608',
				'oauth_version' => '1.0'
			),
			'token' => array(
				'oauth_token' => 'request_token', 'oauth_token_secret' => 'request_secret'
			)
		);
		$result = Provider::verify($request);
		$this->assertTrue($result);
	}
}

?>