<?php namespace Tempest\Services;

use Exception;
use Tempest\App;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Tempest\Data\Connection;
use Tempest\Enums\Config;

/**
 * Manages the connection to the database via Doctrine.
 *
 * @author Marty Wallace
 */
class Database implements Service {

	const DEFAULT_DRIVER = 'pdo_mysql';

	/** @var EntityManager */
	private $_entityManager;

	public function __construct() {
		if (App::get()->config(Config::DB)) {
			$config = Setup::createYAMLMetadataConfiguration([$this->getMetadataConfigurationPath()], App::get()->dev);
			$connection = Connection::fromConnectionString(App::get()->config('db.connection'));

			$doctrine = [
				'dbname' => $connection->getResource(),
				'user' => $connection->getUsername(),
				'password' => $connection->getPassword(),
				'host' => $connection->getHost(),
				'driver' => App::get()->config('db.driver', self::DEFAULT_DRIVER),
			];

			$this->_entityManager = EntityManager::create($doctrine, $config);
		} else {
			throw new Exception('There was no database configuration or connection provided in your application config.');
		}
	}

	/**
	 * Gets the path where ORM configuration is stored.
	 *
	 * @return string
	 */
	public function getMetadataConfigurationPath() {
		return App::get()->root . DIRECTORY_SEPARATOR . trim(App::get()->config('db.config'));
	}

	/**
	 * @return EntityManager
	 */
	public function entities() {
		return $this->_entityManager;
	}

}