<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\extensions\storage;

class File extends \lithium\core\Object {

	protected $_file = null;

	protected $_autoConfig = array('file');

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	protected function _init() {
		parent::_init();
		if (empty($this->_file)) {
			$this->_file = LITHIUM_APP_PATH . '/resources/oauth/storage/oauth.json';
			return;
		}
		if ($this->_file[0] !== '/') {
			$this->_file = LITHIUM_APP_PATH . '/resources/oauth/'. $this->_file;
		}
	}

	/**
	 * undocumented function
	 *
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function write($key, $value) {
		$data = (array) $this->read();
		$data[$key] = $value;
		$data = json_encode($data);
		return file_put_contents($this->_file, $data);
	}

	/**
	 * undocumented function
	 *
	 * @param string $key
	 * @return void
	 */
	public function read($key = null) {
		if (!file_exists($this->_file)) {
			return null;
		}
		$data = json_decode(file_get_contents($this->_file));
		if (isset($data->{$key})) {
			return $data->{$key};
		}
		if ($key) {
			return null;
		}
		return $data;
	}

	/**
	 * undocumented function
	 *
	 * @param string $key
	 * @return void
	 */
	public function remove($key) {
		$data = (array) $this->read();
		if (isset($data[$key])) {
			unset($data[$key]);
		}
		$data = json_encode($data);
		return file_put_contents($this->_file, $data);
	}
}
?>