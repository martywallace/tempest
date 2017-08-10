<?php namespace Tempest\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Events specific to the core application.
 *
 * @author Marty Wallace
 */
class AppEvent extends Event {

	const SETUP = 'app.setup';
	const TERMINATE = 'app.terminate';

}