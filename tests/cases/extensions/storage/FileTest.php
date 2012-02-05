<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\tests\cases\extensions\storage;

use li3_oauth\extensions\storage\File;

class FileTest extends \lithium\test\Unit {

	public $path = null;

	public $file = null;

	public function setUp() {
		$this->path = LITHIUM_APP_PATH . '/resources/tmp/tests/test_oauth.json';
		$this->file = new File(array('file' => $this->path));
	}

	public function tearDown() {
		unlink($this->path);
	}

	public function testReadWrite() {
		$expected = '';
		$result = $this->file->read();
		$this->assertEqual($expected, $result);

		$expected = true;
		$result = $this->file->write('some_key', array(
			'oauth_consumer_key' => 'some_key',
			'oauth_consumer_secret' => 'some_secret'
		));
		$this->assertEqual($expected, $result);

		$expected = (object) array('some_key' => (object) array(
			'oauth_consumer_key' => 'some_key','oauth_consumer_secret' => 'some_secret'
		));
		$result = $this->file->read();
		$this->assertEqual($expected, $result);

	}

	public function testWriteReadRemove() {
		$expected = true;
		$result = $this->file->write('some_key', array(
			'oauth_consumer_key' => 'some_key',
			'oauth_consumer_secret' => 'some_secret'
		));
		$this->assertEqual($expected, $result);

		$expected = (object) array('some_key' => (object) array(
			'oauth_consumer_key' => 'some_key','oauth_consumer_secret' => 'some_secret'
		));
		$result = $this->file->read();
		$this->assertEqual($expected, $result);

		$expected = true;
		$result = $this->file->remove('some_key');
		$this->assertEqual($expected, $result);

		$expected = array();
		$result = $this->file->read();
		$this->assertEqual($expected, $result);
	}
}

?>