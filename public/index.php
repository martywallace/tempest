<?php

error_reporting(-1);

require_once __DIR__ . '/../server/boot.php';

/**
 * Returns the active application instance, or a service that was added to that it.
 *
 * @param string $service The service name.
 *
 * @return App
 */
function tempest()
{
	if(App::getInstance() === null)
	{
		$app = new App();
		$app->start();

		return $app;
	}

	return App::getInstance();
}


// Initialize the application.
tempest();