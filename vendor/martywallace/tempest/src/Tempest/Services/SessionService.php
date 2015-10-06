<?php namespace Tempest\Services;

/**
 * Manages user sessions in the application.
 *
 * @property-read string $id The current session ID.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class SessionService extends Service {

	public function __construct() {
		// TODO: Investigate correct procedures for clean session setup.
		session_start();
	}

	public function __get($prop) {
		if ($prop === 'id') {
			return session_id();
		}

		return $this->get($prop);
	}

	public function __set($prop, $value) {
		$this->set($prop, $value);
	}

	public function get($prop, $fallback = null) {
		return $this->exists($prop) ? $_SESSION[$prop] : $fallback;
	}

	public function set($prop, $value) {
		$_SESSION[$prop] = $value;
	}

	public function exists($prop) {
		return isset($_SESSION[$prop]);
	}

}