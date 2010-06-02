<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\tests\mocks\extensions\service;

class MockSocket extends \lithium\net\Socket {

	public $data = null;

	public function open() {
		return true;
	}

	public function close() {
		return true;
	}

	public function eof() {
		return true;
	}

	public function read($body = 'Test!') {
		return join("\r\n", array(
			'HTTP/1.1 200 OK',
			'Header: Value',
			'Connection: close',
			'Content-Type: text/html;charset=UTF-8',
			'',
			$body
		));
	}

	public function write($data) {
		return $data;
	}

	public function timeout($time) {
		return true;
	}

	public function encoding($charset) {
		return true;
	}

	public function send($message, array $options = array()) {
		$this->data = $this->write($message);
		if (strpos($message->path, 'request_token') !== false) {
			$body = 'oauth_token=requestkey&oauth_token_secret=requestsecret';
		}
		if (strpos($message->path, 'access_token') !== false) {
			$body = 'oauth_token=accesskey&oauth_token_secret=accesssecret';
		}
		if (strpos($message->path, 'search') !== false) {
			$body = json_encode(array('test' => 'cool'));
		}
		$message = $this->read($body);
		return new $options['classes']['response'](compact('message'));
	}
}

?>