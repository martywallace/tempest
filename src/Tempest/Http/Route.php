<?php namespace Tempest\Http;

use Exception;
use Tempest\Http\Modes\RouteMode;
use Tempest\Http\Modes\ActionMode;
use Tempest\Http\Modes\RedirectMode;
use Tempest\Http\Modes\RenderMode;

/**
 * A route to be handled by the HTTP kernel.
 *
 * @author Ascension Web Development
 */
class Route extends Resource {

	/** @var RouteMode */
	private $mode;

	/** @var string[] */
	private $methods;

	/**
	 * Route constructor.
	 *
	 * @param string[] $methods
	 * @param string $uri
	 */
	public function __construct(array $methods, string $uri) {
		parent::__construct($uri);

		$this->methods = $methods;
	}

	/**
	 * Get the HTTP methods that triggers this route.
	 *
	 * @return string[]
	 */
	public function getMethods(): array {
		return $this->methods;
	}

	/**
	 * Get the route mode (e.g. whether this route renders a template, calls a controller, etc).
	 *
	 * @see Route::MODE_UNDETERMINED
	 * @see Route::MODE_RENDER
	 * @see Route::MODE_CONTROLLER
	 * @see Route::MODE_REDIRECT
	 *
	 * @return RouteMode
	 */
	public function getMode(): RouteMode {
		return $this->mode;
	}

	/**
	 * Attach a template to render when this route is matched.
	 *
	 * @param string $template The name of the template to render.
	 * @param mixed[] $context Context to optionally provide to the template.
	 *
	 * @return self
	 *
	 * @throws Exception If the route already has a behaviour mode set.
	 */
	public function render(string $template, array $context = []): self {
		if ($this->mode) {
			throw new Exception('This route already has a mode set.');
		}

		$this->mode = new RenderMode($template, $context);

		return $this;
	}

	/**
	 * Attach a controller action to call when this route is matched.
	 *
	 * @param string $controller The controller class to instantiate.
	 * @param string $method The method within the controller class to call.
	 * @param array $options User defined data to provide to the controller class.
	 *
	 * @return self
	 *
	 * @throws Exception If the route already has a behaviour mode set.
	 */
	public function action(string $controller, string $method = 'index', array $options = []): self {
		if ($this->mode) {
			throw new Exception('This route already has a mode set.');
		}

		$this->mode = new ActionMode($controller, $method, $options);

		return $this;
	}

	/**
	 * Redirect this route to a new location.
	 *
	 * @param string $location The URL to redirect to.
	 * @param bool $permanent Whether or not the redirect is {@link Status::PERMANENT_REDIRECT permanent} or not.
	 *
	 * @return self
	 *
	 * @throws Exception If the route already has a behaviour mode set.
	 */
	public function redirect(string $location, bool $permanent = false): self {
		if ($this->mode) {
			throw new Exception('This route already has a mode set.');
		}

		$this->mode = new RedirectMode($location, $permanent);

		return $this;
	}

}