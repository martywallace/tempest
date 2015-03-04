<?php

/**
 * Your application configuration. The configuration will load '*' and then cascade with any blocks
 * that match the current SERVER_NAME property configured in Apache.
 *
 * Configuration data is used in your application via:
 *
 * <pre>
 *     tempest()->config(property, fallback)
 * </pre>
 *
 * where property is the key used here and fallback is the value to use if no matching key is found.
 */

return array(

	'*' => array(
		'title' => 'New App',
		'timezone' => 'Australia/Sydney',

		'routes' => array(
			'/' => 'AdaptivePage'
		)
	),

	'localhost' => array(
		// Dev mode shows more verbose errors.
		'dev' => true
	)

);