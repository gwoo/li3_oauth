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

		$token = Session::read("{$this->_config['namespace']}");
		if (!$failed && empty($token) && !empty($this->request->query['oauth_token'])) {
			$this->_storeTokens($this->request->query['oauth_token']);
			Session::write("{$this->_config['namespace']}.oauth_verifier", $oauth_verifier);
			
			return $this->redirect('Client::access', array('exit' => true));
		}

		if (!$failed && empty($token)) {
			return $this->redirect('Client::authorize', array('exit' => true));
		}
		
		$token = Session::read("{$this->_config['namespace']}");
		if(!$failed) {
			try {
				$token = Session::read("{$this->_config['namespace']}.request");
				
				$result = Consumer::get('/v2/blog/ambientdata.org/posts/draft',
					array(
						'api_key'		=> $this->_config['oauth_consumer_key'],
						'oauth_token'	=> $token['oauth_token']
					),
					array('host' => 'api.tumblr.com')
				);

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
	
	protected function _storeTokens($oauthToken = false, $oauthTokenSecret = false) {
		if($oauthToken !== false) {
			Session::write("{$this->_config['namespace']}.oauth_token", $oauthToken);
			if($oauthTokenSecret !== false) {
				Session::write("{$this->_config['namespace']}.oauth_token_secret", $oauthTokenSecret);
			}
			return true;
		}
		return false;
	}

	public function authorize() {
		$token = Consumer::token('request');
		if(empty($token)) {
			return $this->_failed();
		}
		$this->_storeTokens($token['oauth_token'], $token['oauth_token_secret']);
		return $this->redirect(Consumer::authorize($token), array('exit' => true));
	}

	public function access() {
		$token = array('oauth_token' => Session::read("{$this->_config['namespace']}.oauth_token"));
		if(!empty($token)) {
			$access = Consumer::token('access', compact('token'));
			if(!empty($access)) {
				$this->_storeTokens($access['oauth_token'], $access['oauth_token_secret']);
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