<?php

foreach(array('functions', 'autoloader') as $common)
{
	// Include base requirements.
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'server' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . "$common.php";
}


define("ROOT", __DIR__);
define("DIR_SERVER", ROOT . DIRECTORY_SEPARATOR . 'server' . DIRECTORY_SEPARATOR);
define("DIR_BASE", path_normalize(dirname($_SERVER["PHP_SELF"]), '/'));
define("DIR_PUBLIC", DIR_BASE . 'public/');


App\Application::init();