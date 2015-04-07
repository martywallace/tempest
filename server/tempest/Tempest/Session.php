<?php namespace Tempest;

/**
 * Assists with handling session data.
 *
 * @author Marty Wallace.
 */
class Session
{

	/**
	 * Gets a session property's value.
	 *
	 * @param string $prop The property to get.
	 * @param mixed $fallback The fallback value to use if the session does not define the property.
	 *
	 * @return mixed
	 */
	public function get($prop = null, $fallback = null)
	{
		if ($prop === null)
		{
			// Returns entire session object.
			return $_SESSION;
		}

		return $this->has($prop) ? $_SESSION[$prop] : $fallback;
	}


	/**
	 * Sets a session property.
	 *
	 * @param string $prop The property to set.
	 * @param mixed $value The value to assign to the property.
	 */
	public function set($prop, $value)
	{
		$_SESSION[$prop] = $value;
	}


	/**
	 * Deletes a property from the session.
	 *
	 * @param string $prop The property to delete.
	 */
	public function del($prop)
	{
		unset($_SESSION[$prop]);
	}


	/**
	 * Determine whether or not the session data contains a given property.
	 *
	 * @param string $prop The property to check for.
	 *
	 * @return bool
	 */
	public function has($prop)
	{
		return isset($_SESSION[$prop]);
	}


	/**
	 * Magic getter - alias for <code>get()</code>.
	 *
	 * @return mixed
	 */
	public function __get($prop) { return $this->get($prop); }


	/**
	 * Magic setter - alias for <code>set()</code>.
	 */
	public function __set($prop, $value) { $this->set($prop, $value); }

}