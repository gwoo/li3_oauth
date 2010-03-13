<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\controllers;

use \li3_oauth\models\Consumer;
use \lithium\storage\Session;

class ClientController extends \lithium\action\Controller {

	protected function _init() {
		parent::_init();
		Consumer::config(array(
			'host' => $this->request->env('SERVER_NAME'),
			'oauth_consumer_key' => '59f87a2f8e430bbad5c84b61ed06304fc9204bcb',
			'oauth_consumer_secret' => '4b498c24588bc56685e68f0d2c52ee6becf96ba3',
			'request_token' => $this->request->env('base') . '/oauth/request_token',
			'access_token' => $this->request->env('base') . '/oauth/request_token',
			'authorize' => $this->request->env('base') . '/oauth/authorize',
			'port' => 30501
		));
	}

	public function index() {
		$message = null;
		$token = Session::read('oauth.access');

		if (empty($token) && !empty($this->request->query['oauth_token'])) {
			$this->redirect(array('controller' => 'client', 'action' => 'access'));
		}

		if (empty($token)) {
			$this->redirect(array('controller' => 'client', 'action' => 'authorize'));
		}
		if (!empty($this->request->data)) {
			$url = '/statuses/update.json';
			$result = Consumer::post($url, $token, $this->request->data);
			$message = json_decode($result);
		}
		return compact('message');
	}

	public function authorize() {
		$token = Consumer::request();
		if (is_array($token) && !empty($token['oauth_token'])) {
			$token += array(
				'oauth_callback_url' => 'http://' .
					$this->request->env('HTTP_HOST') . $this->request->env('base') .
					'/oauth/client/access'
			);
			Session::write('oauth.request', $token);
			$this->redirect(Consumer::authorize($token));
		}

		return (string) $token;
	}

	public function access() {
		$token = Session::read('oauth.request');
		$access = Consumer::access((array) $token);
		Session::write('oauth.access', $access);
		$this->redirect(array('controller' => 'client', 'action' => 'index'));
	}

	public function login() {
		$token = Session::read('oauth.request');
		if (empty($token)) {
			$this->redirect(array('controller' => 'client', 'action' => 'authorize'));
		}
		$this->redirect(Consumer::authenticate($token));
	}

}
?>