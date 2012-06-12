<?php

use lithium\net\http\Router;

Router::connect('/oauth/client/{:action}/{:args}', array(
	'library' => 'li3_oauth_pecl', 'controller' => 'li3_oauth_pecl.client', 'action' => 'index'
));

?>