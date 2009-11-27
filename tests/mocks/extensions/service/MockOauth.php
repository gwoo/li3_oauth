<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\tests\mocks\extensions\service;

class MockOauth extends \li3_oauth\extensions\service\Oauth {

	public function decode($body) {
		return $this->_decode($body);
	}
}

?>