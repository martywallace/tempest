<?php namespace Tempest\Services;

use Tempest\Tempest;
use Tempest\Extensions\TwigExtensions;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Extension_Debug;


/**
 * The application Twig service.
 *
 * @property-read Twig_Loader_Filesystem $loader The internal Twig_Loader_Filesystem instance.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class TwigService extends Twig_Environment implements Service {

	const TEMPEST_NAMESPACE = 'tempest';

	/** @var Twig_Loader_Filesystem */
	private $_loader;

	public function __construct() {
		$this->_loader = new Twig_Loader_Filesystem();
		parent::__construct($this->_loader, ['debug' => Tempest::get()->dev]);
	}

	public function setup() {
		$this->_loader->prependPath(realpath(__DIR__ . '/../../../templates/'), self::TEMPEST_NAMESPACE);

		$directories = Tempest::get()->config->get('templates', []);

		if (!is_array($directories)) $directories = [$directories];

		foreach ($directories as $directory) {
			$this->_loader->prependPath(Tempest::get()->filesystem->absolute(trim($directory, '/')));
		}

		if (Tempest::get()->dev) {
			$this->addExtension(new Twig_Extension_Debug());
		}

		$this->addExtension(new TwigExtensions());
	}

	public function __get($prop) {
		if ($prop === 'loader') return $this->_loader;

		return null;
	}

}