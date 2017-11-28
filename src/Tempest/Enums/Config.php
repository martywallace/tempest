<?php namespace Tempest\Enums;

/**
 * Inbuilt configuration properties.
 *
 * @author Marty Wallace
 */
class Config extends Enum {

	/** Development mode. */
	const DEV = 'dev';

	/** Database connection and configuration. */
	const DB = 'db';

	/** Default timezone. */
	const TIMEZONE = 'timezone';

	/** Twig template root directory. */
	const TEMPLATES = 'templates';

	/** Application file storage root directory. */
	const STORAGE = 'storage';

}