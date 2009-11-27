<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\extensions\storage;

class File extends \lithium\core\Object {

	public $file = null;

	protected function _init() {
		parent::_init();
		$this->file = LITHIUM_APP_PATH . '/tmp/storage/oauth.ini';
	}

	public function write($key, $value) {
		$value = json_encode($value);
		$data = "{$key}=\"{$value}\"";
		return file_put_contents($this->file, $data, FILE_APPEND);
	}

	public function read($key) {
		$data = parse_ini_file($this->file);
		if (isset($data[$key])) {
			return json_decode($data[$key]);
		}
		return null;
	}

}
?>