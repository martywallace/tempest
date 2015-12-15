<?php

/**
 * Your application configuration, where API keys, database connection details and environment settings can be defined.
 * Configuration cascades based on the environment your application is running in. The environment is determined by the
 * value provided to $_SERVER['SERVER_NAME'].
 */

return array(
	'*' => array(
		'timezone' => 'Australia/Sydney',
		'routes' => array(
			'/' => array('get', 'Controllers\GeneralController'),
			'/welcome/{name}' => array('get', 'Controllers\GeneralController::welcome')
		)
	),

	'localhost' => array(
		'dev' => true
	),

	'staging.yourwebsite.com' => array(
		'dev' => true,
		'url' => 'http://staging.yourwebsite.com',
		'robots' => 'noindex, nofollow'
	),

	'yourwebsite.com' => array(
		'url' => 'http://yourwebsite.com'
	)
);