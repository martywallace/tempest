<?php namespace Tempest;

use Closure;
use Exception;
use Tempest\Events\AppEvent;
use Tempest\Events\ExceptionEvent;
use Tempest\Events\KernelEvent;
use Tempest\Kernel\Kernel;
use Tempest\Kernel\Input;
use Tempest\Kernel\Output;
use Tempest\Services\Database;
use Tempest\Services\Markdown;
use Tempest\Services\Twig;
use Tempest\Services\Session;

/**
 * The core application class, from which your own core application class extends. The App class is responsible for
 * bootstrapping your services and configuration.
 *
 * @property-read string $root The application root directory - the result of moving on directory up from the value
 * provided to {@link App::boot()}. Always without a trailing slash.
 * @property-read string $storage The application storage directory as defined in the application configuration. If it
 * is not defined, NULL is returned. Always without a trailing slash.
 * @property-read bool $dev Whether or not the application is in development mode.
 *
 * @property-read Database $db The inbuilt database service.
 * @property-read Twig $twig The inbuilt Twig service, used to render Twig templates.
 * @property-read Session $session The inbuilt session handling service.
 * @property-read Markdown $markdown The inbuilt service for rendering markdown.
 *
 * @author Marty Wallace
 */
abstract class App extends Container {

	/** The framework version. */
	const VERSION = '1.0.0';

	/** @var static */
	protected static $_instance;

	/**
	 * Create and boot up an application instance.
	 *
	 * @param string $root The application root directory, usually one level above the webroot (or wherever your
	 * composer.json sits).
	 * @param Closure|array|string $config Application configuration. Can either be provided as a raw configuration
	 * array, a closure providing the configuration array or a path pointing to a file relative to the application root
	 * that provides a configuration array in either of the first two formats. In the case of a closure, it will be
	 * provided with the {@link Environment}.
	 *
	 * @return static
	 *
	 * @throws Exception If the application has already been booted.
	 */
	public static function boot($root, $config = null) {
		if (!empty(static::$_instance)) {
			throw new Exception('The application has already been booted.');
		}

		static::$_instance = new static();

		// We use an alternate private method call instead of the constructor so that calls to App::get() don't throw an
		// exception (as static::$_instance would be null until after the constructor resolved).
		static::$_instance->_setup($root, $config);

		return static::$_instance;
	}

	/**
	 * Statically get the application instance. {@link App::boot()} must be called before this.
	 *
	 * @return static
	 *
	 * @throws Exception If the application was not previously {@link App::boot() booted}.
	 */
	public static function get() {
		if (empty(static::$_instance)) {
			throw new Exception('Missing preceding call to App::boot().');
		}

		return static::$_instance;
	}

	/** @var string */
	private $_root;

	/** @var Environment */
	private $_environment;

	/** @var array */
	private $_config;

	protected function __construct() {
		$this->addServices([
			'db' => Database::class,
			'twig' => Twig::class,
			'session' => Session::class,
			'markdown' => Markdown::class
		]);

		parent::__construct();
	}

	/**
	 * @param string $root
	 * @param Closure|array|string $config
	 *
	 * @throws Exception
	 */
	private function _setup($root, $config) {
		$this->_root = rtrim($root, '/\\');
		$this->_environment = new Environment();

		if (!empty($config)) {
			if (is_string($config)) {
				$path = $this->_root . DIRECTORY_SEPARATOR . $config;

				if (!file_exists($path)) {
					throw new Exception('Configuration file "' . $path . '" does not exist.');
				} else {
					$config = require($path);
				}
			}

			if (is_array($config)) {
				// Raw configuration.
				$this->_config = $config;
			} else if (is_callable($config)) {
				$this->_config = $config($this->_environment);
			} else {
				throw new Exception('Configuration was provided in an unacceptable format.');
			}
		} else {
			$this->_config = [];
		}

		array_walk_recursive($this->_config, function($value, $key) {
			if (strpos($key, '.') !== false) {
				throw new Exception('Configuration fields cannot contain the "." character, as this is used for nested property querying.');
			}
		});

		$this->dispatch(AppEvent::SETUP);
		$this->setup();
	}

	public function __get($prop) {
		if ($prop === 'root') return $this->_root;
		if ($prop === 'storage') return $this->config('storage') ? $this->_root . '/' . trim($this->config('storage'), '/') : null;
		if ($prop === 'dev') return $this->config('dev', false);

		return parent::__get($prop);
	}

	public function __isset($name) {
		return $this->{$name} !== null;
	}

	/**
	 * Gets a value stored in the application configuration.
	 *
	 * @param string $query The name of the property or a dot (.) delimited path to a descendant property to get.
	 * @param mixed $fallback A fallback value to provide if the configuration property was not found.
	 *
	 * @return mixed
	 */
	public function config($query = null, $fallback = null) {
		if ($query === null) return $this->_config;
		return Utility::evaluate($this->_config, $query, $fallback);
	}

	/**
	 * Called after all services are bound to the application.
	 *
	 * @return mixed
	 */
	abstract protected function setup();

	/**
	 * Handle input and generate output through a kernel.
	 *
	 * @param string $kernel The kernel to use.
	 * @param Input $input Input for the kernel.
	 * @param Closure|string $config Configuration to provide the kernel before parsing through the input, provided as
	 * either a Closure accepting the Kernel as its only argument or a string pointing to a file which returns said
	 * Closure relative to the {@link $root application root}.
	 *
	 * @return Output
	 */
	public function handle($kernel, Input $input, $config = null) {
		$kernel = $this->makeKernel($kernel, $config);

		$kernel->addListener(ExceptionEvent::EXCEPTION, function(ExceptionEvent $event) {
			$this->dispatch(ExceptionEvent::EXCEPTION, $event);
		});

		$this->dispatch(KernelEvent::BOOTED, new KernelEvent($kernel, $input));

		$output = $kernel->handle($input);
		$this->dispatch(KernelEvent::OUTPUT_READY, new KernelEvent($kernel, $input, $output));

		return $output;
	}

	/**
	 * Create a new kernel.
	 *
	 * @param string $class The kernel class name.
	 * @param mixed $config Configuration to provide to the kernel.
	 *
	 * @return Kernel
	 *
	 * @throws Exception If the provided class name could not be resolves to an instance of {@link Kernel}.
	 */
	protected function makeKernel($class, $config) {
		if (!class_exists($class)) {
			throw new Exception('Nonexistent class "' . $class . '" cannot be used as a kernel.');
		}

		$kernel = new $class($config);

		if (!($kernel instanceof Kernel)) {
			throw new Exception('Class "' . $class . '" is not a kernel.');
		}

		return $kernel;
	}

	/**
	 * Terminate the application.
	 */
	public function terminate() {
		$this->dispatch(AppEvent::TERMINATE);
		exit;
	}

}