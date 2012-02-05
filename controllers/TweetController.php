<?php

namespace li3_oauth\controllers;

use li3_oauth\models\Consumer;
use lithium\storage\Session;

class TweetController extends \lithium\action\Controller {

	protected function _init() {
		parent::_init();
		Consumer::config(array(
			'host' => 'twitter.com',
			'oauth_consumer_key' => '',
			'oauth_consumer_secret' => ''
		));
	}

	public function index() {
		$message = null;
		$token = Session::read('oauth.access');

		if (empty($token) && !empty($this->request->query['oauth_token'])) {
			$this->redirect('Tweet::access');
		}
		if (empty($token)) {
			$this->redirect('Tweet::authorize');
		}
		if (!empty($this->request->data)) {
			$result = Consumer::post('/statuses/update.json',
				$this->request->data,
				compact('token')
			);
			$message = json_decode($result);
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
		Session::write('oauth.access', $access);
		$this->redirect('Tweet::index');
	}

	public function login() {
		$token = Session::read('oauth.request');
		if (empty($token)) {
			$this->redirect('Tweet::authorize');
		}
		$this->redirect(Consumer::authenticate($token));
	}
}

?>