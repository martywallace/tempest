<?php

/**
 * Application routes.
 */
return array(

	'*' => array(
		'/sample' => array('controller' => 'SampleController', 'vars' => array('property' => 'value'))
	)

);