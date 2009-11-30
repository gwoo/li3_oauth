<?php

use \lithium\http\Router;


Router::connect('/oauth/{:action}/{:args}', array(
	'plugin' => 'li3_oauth', 'controller' => 'server', 'action' => 'index'
));

?>