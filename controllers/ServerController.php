<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_oauth\controllers;

use \li3_oauth\models\Provider;
use \lithium\storage\Session;

class ServerController extends \lithium\action\Controller {

	protected function _init() {
		parent::_init();
		Provider::config(array(
			'host' => 'localhost',
			'request_token' => 'union-of-rad/rad-dev/plugins/oauth/request_token',
			'access_token' => 'union-of-rad/rad-dev/plugins/oauth/access_token',
			'port' => 30500
		));
	}

	public function request_token() {
		if (empty($this->request->data)) {
			return 'Invalid Request';
		}
		$consumer = Provider::fetch($this->request->data['oauth_consumer_key']);
		$request = array(
			'params' => $this->request->data, 'url' => 'request_token',
		) + (array) $consumer;
		if (Provider::verify($request)) {
			$token = Provider::create('token');
			$data = (array) $consumer + (array) $token;
			Provider::store($consumer->oauth_consumer_key, $data);
			Provider::store($token->oauth_token, $data);
			return http_build_query((array) $token);
		}
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