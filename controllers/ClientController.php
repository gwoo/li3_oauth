<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_pecl_oauth\controllers;

use li3_pecl_oauth\models\Consumer;
use lithium\storage\Session;
use lithium\core\Libraries;

class ClientController extends \lithium\action\Controller {
	protected $_config = array();
	protected function _init() {
		parent::_init();
		
		$this->_config = (array) Libraries::get('li3_pecl_oauth');
		$this->_config += array(
			'host'				=> $this->request->env('SERVER_NAME'),
			'oauth_callback'	=> $this->request->env('SERVER_NAME') . '/oauth/client',
			'namespace'			=> 'li3_pecl_oauth'
		);
		
		return Consumer::config($this->_config) ? true : false;
	}

	public function index() {
		$failed = in_array('failed', $this->request->params['args']) ? true : false;

		$access = Session::read("{$this->_config['namespace']}.access");
		$token = Session::read("{$this->_config['namespace']}.request");
		if (!$failed && empty($access) && !empty($this->request->query['oauth_token'])) {
			$token = is_array($token) ? $this->request->query + $token : $token;
			Session::write("{$this->_config['namespace']}.request", $token);
			return $this->_filter(__METHOD__, array('path' => 'Client::access', 'options' => array()), function() {
				extract($path);
				return $this->redirect($path, $options);
			});
		}

		if (!$failed && empty($access)) {
			return $this->_filter(__METHOD__, array('path' => 'Client::access', 'options' => array()), function() {
				extract($path);
				return $this->redirect($path, $options);
			});
			
			return $this->redirect('Client::authorize');
		}
		
		return !$failed;
	}
	
	protected function _failed() {
		Session::delete($this->_config['namespace']);
		return $this->redirect(array('Client::index', 'args' => array('failed' => true)));
	}

	public function authorize() {
		$token = Consumer::token('request');
		if(empty($token)) {
			return $this->_failed();
		}
		Session::write("{$this->_config['namespace']}.request", $token);
		return $this->redirect(Consumer::authorize($token));
	}

	public function access() {
		$token = Session::read("{$this->_config['namespace']}.request");
		if(!empty($token)) {
			$access = Consumer::token('access', compact('token'));
			if(!empty($access)) {
				Session::write("{$this->_config['namespace']}.access", $access);
				return $this->redirect('Client::index');
			}
		}
		return $this->_failed();
	}
	
	public function logout() {
		Session::delete($this->_config['namespace']);
		return $this->redirect('Client::index');
	}

	public function login() {
		$token = Session::read("{$this->_config['namespace']}.request");
		if (empty($token)) {
			return $this->redirect('Client::authorize');
		}
		return $this->redirect(Consumer::authenticate($token));
	}
}

?>