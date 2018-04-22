<?php namespace Tempest\Services;

use Doctrine\Common\Cache\ChainCache;
use Doctrine\Common\Cache\FilesystemCache;
use Tempest\App;

/**
 * Provides application data caching.
 *
 * @author Ascension Web Development
 */
class CacheService extends ChainCache implements Service {

	public function __construct() {
		$fs = new FilesystemCache(App::get()->storage . '/cache');

		parent::__construct([$fs]);
	}

}