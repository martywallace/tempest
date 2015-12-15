<?php

return array(
	'/' => array('get', 'Controllers\GeneralController::index'),
	'/welcome/{name}' => array('get', 'Controllers\GeneralController::welcome'),
	'/test' => 'Controllers\Wot'
);