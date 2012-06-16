<?php

use lithium\net\http\Router;

Router::connect('/oauth/client/{:action}/{:args}', array(
	'library' => 'li3_pecl_oauth', 'controller' => 'li3_pecl_oauth.client', 'action' => 'index'
));

?>