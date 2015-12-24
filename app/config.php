<?php

/**
 * Your application configuration, where API keys, database connection details and environment settings can be defined.
 * Configuration cascades based on the environment your application is running in. The environment is determined by the
 * value provided to $_SERVER['SERVER_NAME']. Inbuilt configuration options can be reviewed in the README.
 */

return array(
	'*' => array(
		'timezone' => 'Australia/Sydney',
		'routes' => array(
			array('/', 'GeneralController'),
			array('/welcome/{name}', 'GET', 'GeneralMiddleware::auth', 'GeneralController::welcome'),
			array('/welcome/{name}', 'POST', 'GeneralController::welcome')
		)
	),

	'marty.dev' => array(
		'dev' => true
	),

	'staging.yourwebsite.com' => array(
		'dev' => true,
		'url' => 'http://staging.yourwebsite.com'
	),

	'yourwebsite.com' => array(
		'url' => 'http://yourwebsite.com'
	)
);