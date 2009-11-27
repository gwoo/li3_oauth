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
			'request_token' => 'libraries/oauth_php/example/request_token.php',
			'access_token' => 'libraries/oauth_php/example/access_token.php',
			'port' => 30500
		));
	}

	public function testAuthorize() {
		$expected = 'http://localhost/oauth/authorize?oauth_token=requestkey&oauth_token_secret=requestsecret';
		$result = Consumer::authorize(array(
			'oauth_token' => 'requestkey',
			'oauth_token_secret' => 'requestsecret'
		));
		$this->assertEqual($expected, $result);
	}
}

?>