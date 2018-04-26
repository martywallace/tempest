<?php namespace Tempest;

use Closure;
use Exception;
use Tempest\Enums\Config;
use Throwable;
use Tempest\Container\Container;
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
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * The core application class, from which your own core application class
 * extends. The App class is responsible for bootstrapping your services and
 * configuration.
 *
 * @author Ascension Web Development
 */
abstract class App extends EventDispatcher {

	/** The framework version. */
	const VERSION = '5.0.0';

	/** Dump data as provided by {@link print_r()}. */
	const DUMP_FORMAT_PRINT_R = 'print_r';

	/** Dump data as provided by {@link var_dump()}. */
	const DUMP_FORMAT_VAR_DUMP = 'var_dump';

	/** Dump data as provided by {@link json_encode()}. */
	const DUMP_FORMAT_JSON = 'json';

	/** @var static */
	protected static $instance;

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
		if (!empty(static::$instance)) {
			throw new Exception('The application has already been booted.');
		}

		static::$instance = new static();

		// We use an alternate private method call instead of the constructor so that calls to App::get() don't throw an
		// exception (as static::$_instance would be null until after the constructor resolved).
		static::$instance->_setup($root, $config);

		return static::$instance;
	}

	/**
	 * Statically get the application instance. {@link App::boot()} must be called before this.
	 *
	 * @return static
	 *
	 * @throws Exception If the application was not previously {@link App::boot() booted}.
	 */
	public static function get() {
		if (empty(static::$instance)) {
			throw new Exception('Missing preceding call to App::boot().');
		}

		return static::$instance;
	}

	/** @var string */
	private $root;

	/** @var Environment */
	private $environment;

	/** @var Container */
	private $container;

	/** @var array */
	private $config;

	/** @var Kernel */
	private $kernel;

	protected function __construct() {
		$this->container = new Container();

		$this->container->addMany([
			CacheService::class,
			DatabaseService::class,
			TwigService::class,
			SessionService::class,
			MarkdownService::class,
			LogService::class
		]);
	}

	/**
	 * @param string $root
	 * @param Closure|array|string $config
	 *
	 * @throws Exception
	 */
	private function _setup($root, $config) {
		$this->root = rtrim($root, '/\\');
		$this->environment = new Environment();

		set_exception_handler(function(Throwable $throwable) {
			$this->terminate($throwable);
		});

		if (!empty($config)) {
			if (is_string($config)) {
				$path = $this->root . DIRECTORY_SEPARATOR . $config;

				if (!file_exists($path)) {
					throw new Exception('Configuration file "' . $path . '" does not exist.');
				} else {
					$config = require($path);
				}
			}

			if (is_array($config)) {
				// Raw configuration.
				$this->config = $config;
			} else if (is_callable($config)) {
				$this->config = $config($this->environment);
			} else {
				throw new Exception('Configuration was provided in an unacceptable format.');
			}
		} else {
			$this->config = [];
		}

		array_walk_recursive($this->config, function($value, $key) {
			if (strpos($key, '.') !== false) {
				throw new Exception('Configuration fields cannot contain the "." character, as this is used for nested property querying.');
			}
		});

		date_default_timezone_set($this->config(Config::TIMEZONE, 'UTC'));

		$this->dispatch(AppEvent::SETUP);
		$this->setup();
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
		if ($query === null) return $this->config;
		return Utility::evaluate($this->config, $query, $fallback);
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
		if (!$this->kernel) {
			throw new Exception('There is no attached kernel to dump to.');
		}

		$output = $this->kernel->dump($data, $format);

		if ($output instanceof Output) $output->send();
		else $this->terminate($output);
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
		$this->kernel = $this->makeKernel($kernel, $config);

		$this->kernel->addListener(ExceptionEvent::EXCEPTION, function(ExceptionEvent $event) {
			$this->log->critical($event->getException()->getMessage(), $event->getException()->getTrace());
			$this->dispatch(ExceptionEvent::EXCEPTION, $event);
		});

		$this->dispatch(KernelEvent::BOOTED, new KernelEvent($this->kernel, $input));

		$output = $this->kernel->handle($input);
		$this->dispatch(KernelEvent::OUTPUT_READY, new KernelEvent($this->kernel, $input, $output));

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
	 * The application root directory - the result of moving on directory up
	 * from the value provided to {@link App::boot()}. Always without a trailing
	 * slash.
	 *
	 * @return string
	 */
	public function getRoot(): string {
		return $this->root;
	}

	/**
	 * The application storage directory as defined in the application
	 * configuration. If it is not defined, NULL is returned. Always without a
	 * trailing slash.
	 *
	 * @return string
	 */
	public function getStorageRoot(): string {
		return $this->config(Config::STORAGE) ? $this->root . DIRECTORY_SEPARATOR . trim($this->config(Config::STORAGE), '/') : null;
	}

	/**
	 * Whether or not the application is in development mode as defined by the
	 * application configuration.
	 *
	 * @return bool
	 */
	public function isDevelopmentMode(): bool {
		return $this->config(Config::DEV, false);
	}

	/**
	 * Get the service container.
	 *
	 * @return Container
	 */
	public function getContainer(): Container {
		return $this->container;
	}

}