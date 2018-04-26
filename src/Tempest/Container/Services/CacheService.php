<?php

namespace Tempest\Container\Services;

use Tempest\App;
use Doctrine\Common\Cache\ChainCache;
use Doctrine\Common\Cache\FilesystemCache;

/**
 * Provides application data caching.
 *
 * @author Ascension Web Development
 */
class CacheService extends ChainCache implements Service {

	public function __construct() {
		$fs = new FilesystemCache(App::get()->getStorageRoot() . '/cache');

		parent::__construct([$fs]);
	}

}