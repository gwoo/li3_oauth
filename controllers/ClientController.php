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

class ClientController extends \lithium\action\Controller {

	protected function _init() {
		parent::_init();
		Consumer::config(array(
			'host' => $this->request->env('SERVER_NAME'),
			'oauth_consumer_key' => '59f87a2f8e430bbad5c84b61ed06304fc9204bcb',
			'oauth_consumer_secret' => '4b498c24588bc56685e68f0d2c52ee6becf96ba3',
			'request' => $this->request->env('base') . '/oauth/request_token',
			'access' => $this->request->env('base') . '/oauth/access_token',
			'authorize' => $this->request->env('base') . '/oauth/authorize',
			'port' => 30501
		));
	}

	public function index() {
		$message = null;
		$token = Session::read('oauth.access');
		if (empty($token) && !empty($this->request->query['oauth_token'])) {
			$this->redirect('Client::access');
		}

		if (empty($token)) {
			$this->redirect('Client::authorize');
		}
		return compact('message');
	}

	public function authorize() {
		$token = Consumer::token('request');
		if (is_string($token)) {
			return $token;
		}
		Session::write('oauth.request', $token);
		$this->redirect(Consumer::authorize($token));
	}

	public function access() {
		$token = Session::read('oauth.request');
		$access = Consumer::token('access', compact('token'));
		if (is_string($token)) {
			return $token;
		}
		Session::write('oauth.access', $access);
		$this->redirect('Client::index');
	}

	public function login() {
		$token = Session::read('oauth.request');
		if (empty($token)) {
			$this->redirect('Client::authorize');
		}
		$this->redirect(Consumer::authenticate($token));
	}
}

?>