<?php namespace Tempest\Services;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Tempest\App;
use Tempest\Utility;

/**
 * Manages the connection to the database via Doctrine.
 *
 * @author Marty Wallace
 */
class Database implements Service {

	/** @var EntityManager */
	private $_entityManager;

	public function __construct() {
		$config = Setup::createAnnotationMetadataConfiguration([App::get()->root], App::get()->dev);

		$connection = [
			'dbname' => App::get()->config('db.name'),
			'user' => App::get()->config('db.user'),
			'password' => App::get()->config('db.pass'),
			'host' => App::get()->config('db.host'),
			'driver' => App::get()->config('db.driver'),
		];

		$this->_entityManager = EntityManager::create($connection, $config);
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager() {
		return $this->_entityManager;
	}

}