<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\tests\mocks\extensions\service;

class MockService extends \lithium\http\Service {
	
	public function send($method, $path = null, $data = null, $options = array()) {
		if (strpos($path, 'request_token') !== false) {
			return 'oauth_token=requestkey&oauth_token_secret=requestsecret';
		}
		if (strpos($path, 'access_token') !== false) {
			return 'oauth_token=accesskey&oauth_token_secret=accesssecret';
		}
	}

}