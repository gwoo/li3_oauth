<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\controllers;

use li3_oauth\models\Provider;

class ServerController extends \lithium\action\Controller {

	protected function _init() {
		parent::_init();
		Provider::config(array(
			'host' => $this->request->env('SERVER_NAME'),
			'request' => $this->request->env('base') . '/oauth/request_token',
			'access' => $this->request->env('base') . '/oauth/access_token',
			'authorize' => $this->request->env('base') . '/oauth/authorize',
			'port' => 30501
		));
	}

	public function request_token() {
		if (empty($this->request->data)) {
			return $this->render(array('text' => 'Invalid Request', 'status' => 401));
		}

		$consumer = Provider::fetch($this->request->data['oauth_consumer_key']);
		if (!$consumer) {
			return $this->render(array('text' => 'Invalid Consumer Key', 'status' => 401));
		}

		$isValid = Provider::verify(array(
			'params' => $this->request->data, 'url' => 'request_token'
		) + (array) $consumer);

		if ($isValid) {
			$token = Provider::create('token');
			$data = (array) $consumer + (array) $token;
			Provider::store($consumer->oauth_consumer_key, $data);
			Provider::store($token->oauth_token, $data);
			return http_build_query((array) $token);
		}
		$this->render(array('text' => 'Invalid Signature', 'status' => 401));
	}

	public function authorize() {
		if (!empty($this->request->query['oauth_token'])) {
			$token = $this->request->query['oauth_token'];
			$data = Provider::fetch($token);
		}

		if (!empty($this->request->data['allow'])) {

		}
		if (!empty($this->request->data['deny'])) {

		}
		return compact('token');
	}

	public function access_token() {

	}

	public function account() {
		$token = Provider::create('consumer');
		Provider::store($token->oauth_consumer_key, $token);
		return compact('token');
	}
}

?>