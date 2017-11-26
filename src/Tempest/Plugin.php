<?php namespace Tempest;

/**
 * An application plugin, providing an entire suite of additional functionality.
 *
 * @author Marty Wallace
 */
abstract class Plugin extends Container {

	/**
	 * The plugin name.
	 *
	 * @return string
	 */
	abstract public function getName();

	/**
	 * The plugin version.
	 *
	 * @return string
	 */
	abstract public function getVersion();

	/**
	 * Boot the plugin.
	 *
	 * @return mixed
	 */
	abstract public function setup();

	/**
	 * Gets the handle through which the plugin will be referenced, based on its {@link getName() name}.
	 *
	 * @return string
	 */
	public function getHandle() {
		$base = Utility::kebab($this->getName());
		$base = ucwords(str_replace('-', ' ', $base));

		return lcfirst(str_replace(' ', '', $base));
	}

}