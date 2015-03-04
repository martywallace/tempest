<?php

require_once __DIR__ . '/../server/boot.php';

/**
 * Returns the active application instance.
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