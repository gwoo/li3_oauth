<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth_pecl\controllers;

use li3_oauth_pecl\models\Consumer;
use lithium\storage\Session;
use lithium\core\Libraries;

class ClientController extends \lithium\action\Controller {
	protected $_config = array();
	protected function _init() {
		parent::_init();
		
		$this->_config = (array) Libraries::get('li3_oauth_pecl');
		$this->_config += array(
			'host'				=> $this->request->env('SERVER_NAME'),
			'oauth_callback'	=> $this->request->env('SERVER_NAME') . '/oauth/client',
			'namespace'			=> 'li3_oauth_pecl'
		);
		
		return Consumer::config($this->_config) ? true : false;
	}

	public function index() {
		$message = 'failed';

		$failed = in_array('failed', $this->request->params['args']) ? true : false;

		$access = Session::read("{$this->_config['namespace']}.access");
		$token = Session::read("{$this->_config['namespace']}.request");
		if (!$failed && empty($access) && !empty($this->request->query['oauth_token'])) {
			$token = is_array($token) ? $this->request->query + $token : $token;
			Session::write("{$this->_config['namespace']}.request", $token);
			return $this->redirect('Client::access', array('exit' => true));
		}

		if (!$failed && empty($access)) {
			return $this->redirect('Client::authorize', array('exit' => true));
		}
		
		if(!$failed) {
			try {
				$result = Consumer::post('/user/info', array(), array(
					'host'	=> 'api.tumblr.com/v2',
					'token'	=> $access
				));
				$message = json_decode($result);
			} catch(\OAuthException $E) {
				$result = array(
					'lastTrace'	=> $E->getTrace()[0],
					'session'	=> $token,
					'response'	=> json_decode($E->lastResponse),
					'message'	=> $E->getMessage()
				);
				exit('<pre>' . print_r($result, true) . '</pre>');
			}
		}
		
		exit(print_r(compact('message'), true));
	}
	
	protected function _failed() {
		Session::delete($this->_config['namespace']);
		return $this->redirect(array('Client::index', 'args' => array('failed' => true)), array('exit' => true));
	}

	public function authorize() {
		$token = Consumer::token('request');
		if(empty($token)) {
			return $this->_failed();
		}
		Session::write("{$this->_config['namespace']}.request", $token);
		return $this->redirect(Consumer::authorize($token), array('exit' => true));
	}

	public function access() {
		$token = Session::read("{$this->_config['namespace']}.request");
		if(!empty($token)) {
			$access = Consumer::token('access', compact('token'));
			if(!empty($access)) {
				Session::write("{$this->_config['namespace']}.access", $access);
				return $this->redirect('Client::index', array('exit' => true));
			}
		}
		return $this->_failed();
	}
	
	public function logout() {
		Session::delete($this->_config['namespace']);
		return $this->redirect('Client::index', array('exit' => true));
	}

	public function login() {
		$token = Session::read("{$this->_config['namespace']}.request");
		if (empty($token)) {
			return $this->redirect('Client::authorize', array('exit' => true));
		}
		return $this->redirect(Consumer::authenticate($token), array('exit' => true));
	}
}

?>