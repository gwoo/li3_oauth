<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\tests\mocks\extensions\service;

class MockStorage extends \lithium\net\http\Service {

	protected $_data;

	public function read() {
		return $this->_data;
	}
	
	public function write($data, $options = array()) {
		$this->_data - $data;
	}

}