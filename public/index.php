<?php

require('../vendor/autoload.php');

/**
 * Returns the current active application instance.
 *
 * @return App
 */
function app() {
    return App::instantiate(realpath(__DIR__ . '/../'), 'app/config');
}

// Instantiate and start the application.
app()->start();