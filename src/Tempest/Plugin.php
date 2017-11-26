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
	abstract static public function getName();

	/**
	 * The plugin version.
	 *
	 * @return string
	 */
	abstract static public function getVersion();

	/**
	 * Gets the handle through which the plugin will be referenced, based on its {@link getName() name}.
	 *
	 * @return string
	 */
	public static function getHandle() {
		$base = Utility::kebab(static::getName());
		$base = ucwords(str_replace('-', ' ', $base));

		return lcfirst(str_replace(' ', '', $base));
	}

	/**
	 * Boot the plugin.
	 *
	 * @return mixed
	 */
	abstract public function setup();

}