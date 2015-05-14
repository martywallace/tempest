<?php

define('ROOT', realpath(__DIR__ . '/../'));

require(ROOT . '/vendor/autoload.php');
require(ROOT . '/app/App.php');


/**
 * Returns the current active application instance.
 *
 * @return App
 */
function app()
{
    return App::instantiate(ROOT, array('app'));
}

app()->start();