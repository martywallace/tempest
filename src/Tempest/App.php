<?php namespace Tempest;

use Closure;
use Exception;
use Tempest\Enums\Config;
use Throwable;
use Tempest\Events\AppEvent;
use Tempest\Events\ExceptionEvent;
use Tempest\Events\KernelEvent;
use Tempest\Kernel\Kernel;
use Tempest\Kernel\Input;
use Tempest\Kernel\Output;
use Tempest\Services\CacheService;
use Tempest\Services\DatabaseService;
use Tempest\Services\MarkdownService;
use Tempest\Services\TwigService;
use Tempest\Services\SessionService;
use Tempest\Services\LogService;

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
 * @property-read CacheService $cache The inbuilt caching service.
 * @property-read DatabaseService $db The inbuilt database service.
 * @property-read TwigService $twig The inbuilt Twig service, used to render Twig templates.
 * @property-read SessionService $session The inbuilt session handling service.
 * @property-read MarkdownService $markdown The inbuilt service for rendering markdown.
 * @property-read LogService $log The inbuilt service for logging.
 *
 * @author Marty Wallace
 */
abstract class App extends Container {

	/** The framework version. */
	const VERSION = '5.0.0';

	/** Dump data as provided by {@link print_r()}. */
	const DUMP_FORMAT_PRINT_R = 'print_r';

	/** Dump data as provided by {@link var_dump()}. */
	const DUMP_FORMAT_VAR_DUMP = 'var_dump';

	/** Dump data as provided by {@link json_encode()}. */
	const DUMP_FORMAT_JSON = 'json';

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

	/** @var Kernel */
	private $_kernel;

	/** @var Plugin[] */
	private $_plugins = [];

	protected function __construct() {
		$this->addServices([
			'cache' => CacheService::class,
			'db' => DatabaseService::class,
			'twig' => TwigService::class,
			'session' => SessionService::class,
			'markdown' => MarkdownService::class,
			'log' => LogService::class
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

		set_exception_handler(function(Throwable $throwable) {
			$this->terminate($throwable);
		});

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

		date_default_timezone_set($this->config(Config::TIMEZONE, 'UTC'));

		// Setup plugins.
		foreach ($this->plugins() as $plugin) {
			if ($this->hasPlugin($plugin->getHandle())) {
				throw new Exception('Conflicting plugin name "' . $plugin->getHandle() . '".');
			}

			$this->_plugins[$plugin->getHandle()] = $plugin;
		}

		$this->dispatch(AppEvent::SETUP);
		$this->setup();
	}

	public function __get($prop) {
		if ($prop === 'root') return $this->_root;
		if ($prop === 'storage') return $this->config(Config::STORAGE) ? $this->_root . '/' . trim($this->config(Config::STORAGE), '/') : null;
		if ($prop === 'dev') return $this->config(Config::DEV, false);

		if (array_key_exists($prop, $this->_plugins)) {
			return $this->_plugins[$prop];
		}

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
	 * Dump output to the active kernel and terminal the application.
	 *
	 * @see App::DUMP_FORMAT_PRINT_R
	 * @see App::DUMP_FORMAT_VAR_DUMP
	 * @see App::DUMP_FORMAT_JSON
	 *
	 * @param mixed $data The data to debug.
	 * @param string $format The debugging format.
	 *
	 * @throws Exception If there is no active kernel to handle the dump.
	 */
	public function dump($data, $format = self::DUMP_FORMAT_PRINT_R) {
		if (!$this->_kernel) {
			throw new Exception('There is no attached kernel to dump to.');
		}

		$output = $this->_kernel->dump($data, $format);

		if ($output instanceof Output) $output->send();
		else $this->terminate($output);
	}

	/**
	 * Get an attached plugin.
	 *
	 * @param string $name The plugin name.
	 *
	 * @return Plugin
	 *
	 * @throws Exception If the plugin does not exist.
	 */
	public function getPlugin($name) {
		if (!$this->hasPlugin($name)) {
			throw new Exception('Plugin "' . $name . '" does not exist.');
		}

		return $this->_plugins[$name];
	}

	/**
	 * Determine whether this application has the specified plugin attached.
	 *
	 * @param string $name The plugin name as provided when {@link plugins declaring} the attached plugins.
	 *
	 * @return bool
	 */
	public function hasPlugin($name) {
		return array_key_exists($name, $this->_plugins);
	}

	/**
	 * Called after all services are bound to the application.
	 *
	 * @return mixed
	 */
	abstract protected function setup();

	/**
	 * Declares plugins to be bound to the application.
	 *
	 * @return Plugin[]
	 */
	protected function plugins() {
		return [];
	}

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
		$this->_kernel = $this->makeKernel($kernel, $config);

		$this->_kernel->addListener(ExceptionEvent::EXCEPTION, function(ExceptionEvent $event) {
			$this->log->critical($event->getException()->getMessage(), $event->getException()->getTrace());
			$this->dispatch(ExceptionEvent::EXCEPTION, $event);
		});

		$this->dispatch(KernelEvent::BOOTED, new KernelEvent($this->_kernel, $input));

		$output = $this->_kernel->handle($input);
		$this->dispatch(KernelEvent::OUTPUT_READY, new KernelEvent($this->_kernel, $input, $output));

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
	 * Terminate the application and send final output to the outer context.
	 *
	 * @param string $output The output to send.
	 */
	public function terminate($output) {
		$this->dispatch(AppEvent::TERMINATE);

		// No turning back.
		exit($output);
	}

	/**
	 * Get all installed plugins.
	 *
	 * @return Plugin[]
	 */
	public function getPlugins() {
		return $this->_plugins;
	}

}