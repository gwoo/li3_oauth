#li3_PECL_oAuth#

##Requirements##
PECL Oauth Extension

##Installation##

* PECL
	* With homebrew, [click here](https://github.com/josegonzalez/homebrew-php) (recommended if you're on osx).
	* With PECL
	```sh
	pecl install oauth
	```

* Library
	* With Git
		*Submodule
		```sh
		cd path/to/your/lithium/libraries; git submodule add https://github.com/JacopKane/li3_pecl_oauth.git; git submodule init li3_pecl_oauth;
		```
		*Clone
		```sh
		cd path/to/your/lithium/libraries; git clone https://github.com/JacopKane/li3_pecl_oauth/zipball/master;
		```
	* Download from here
	```sh
	pecl install oauth
	```

##Configuration##
```php
<?php
//app/config/bootstrap/libraries.php
Libraries::add('li3_pecl_oauth', array(
	'scheme'				=> 'https',
	'host'					=> 'api.twitter.com',
	'oauth_callback'		=> 'http://example.com/app/oauth/client',
	'oauth_consumer_key'	=> 'OAUTH_CONSUMER_KEY',
	'oauth_consumer_secret'	=> 'OAUTH_CONSUMER_SECRET',
	'redirect_success'		=> 'http://example.com/app/twitter_example/',
	'redirect_failed'		=> 'http://example.com/app/twitter_example/failed',
	'namespace'				=> 'twitter' //prefix for sessions, optional.
));

//app/controllers/TwitterExampleController.php
class TwitterExampleController extends \li3_pecl_oauth\controllers\ClientController {
	
	public function _init() {
		return parent::_init();
	}
	
	public function index() {
		$namespace = \lithium\core\Libraries::get('li3_pecl_oauth', 'namespace') ?: 'li3_pecl_oauth';
		$token = \lithium\storage\Session::read("{$namespace}.access");

		if(!$token) {
			return $this->redirect('Client::index');
		}
		
		$result = \li3_pecl_oauth\models\Consumer::get('/account/verify_credentials.json', array(), compact('token'));
		$result = is_string($result) ? json_decode($result) : $result;
		
		return $this->set(compact('result'));
	}
	
	public function failed() {
		exit('ooops.');
	}
}
?>
```

##Roadmap##
* Try to make it more lithium way (as socket and service)
* Create other API libraries compatible works with this
* Add provider support (only consumer right now)
* Add lithium sockets support

Feel free to contribute.

##Me##
[@JacopKane]{https://twitter.com/JacopKane}