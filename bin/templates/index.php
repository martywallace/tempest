<?php

require('../vendor/autoload.php');

/**
 * Alias for {@link App::get()}.
 *
 * @return App
 */
function app() {
	return App::get();
}

App::boot(realpath(__DIR__ . '/../'), 'app/config.php')
	->http(Tempest\Http\Request::capture(), 'app/kernel/http.php')
	->send();