<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oath\tests\mocks\extensions\service;

class MockSocket extends \lithium\util\Socket {

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

	public function read() {
		return join("\r\n", array(
			'HTTP/1.1 200 OK',
			'Header: Value',
			'Connection: close',
			'Content-Type: text/html;charset=UTF-8',
			'',
			'Test!'
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
	
	public function send($message, $options = array()) {
		$this->write($message);
		return $this->read();
	}
}

?>