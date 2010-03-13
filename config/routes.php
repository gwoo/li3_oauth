<?php

use \lithium\net\http\Router;

Router::connect('/oauth', array(
	'library' => 'li3_oauth', 'controller' => 'li3_oauth.server', 'action' => 'account'
));
Router::connect('/oauth/client/{:action}/{:args}', array(
	'library' => 'li3_oauth', 'controller' => 'li3_oauth.client', 'action' => 'index'
));
Router::connect('/oauth/{:action}/{:args}', array(
	'library' => 'li3_oauth', 'controller' => 'li3_oauth.server', 'action' => 'index'
));

?>