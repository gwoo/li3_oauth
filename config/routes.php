<?php

use \lithium\http\Router;

Router::connect('/oauth', array(
	'plugin' => 'li3_oauth', 'controller' => 'server', 'action' => 'account'
));
Router::connect('/oauth/client/{:action}/{:args}', array(
	'plugin' => 'li3_oauth', 'controller' => 'client', 'action' => 'index'
));
Router::connect('/oauth/{:action}/{:args}', array(
	'plugin' => 'li3_oauth', 'controller' => 'server', 'action' => 'index'
));

?>