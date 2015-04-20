<?php namespace Tempest;


/**
 * Manages application configuration, defined in <code>/config/*.php</code>.
 *
 * @author Marty Wallace.
 */
class Config
{

	private $data = array();


	/**
	 * Constructor.
	 *
	 * @param string $name The name of the config file being managed by this instace.
	 */
	public function __construct($name)
	{
		$data = require_once(APP_ROOT . 'config/' . $name . '.php');

		// Also allow www.{SERVER_NAME}, a common ServerAlias in Apache.
		$compareHost = preg_replace('/^www\./', '', HOST);

		if (array_key_exists('*', $data))
		{
			$this->data = $data['*'];

			if (array_key_exists($compareHost, $data))
			{
				// Consume host-specific configuration and overwrite where necessary.
				$this->data = array_replace_recursive($this->data, $data[$compareHost]);
			}
		}
		else
		{
			// No cascading config - use the entire top level set.
			$this->data = $data;
		}
	}


	/**
	 * Returns configuration data.
	 *
	 * @param $field string The configuration data to get.
	 * @param $default mixed A default value to use, if the data was not found.
	 *
	 * @return mixed The result data, or the default value if it was not found.
	 */
	public function data($field = null, $default = null)
	{
		if ($field === null) return $this->data;

		$path = preg_split('/\.+/', $field);
		if (!array_key_exists($path[0], $this->data)) return $default;

		$target = $this->data;
		foreach ($path as $p)
		{
			if (array_key_exists($p, $target)) $target = $target[$p];
			else return $default;
		}

		return $target;
	}

}