<?php namespace Tempest\Services;

use Exception;
use Tempest\Utility;
use Tempest\Database\Models\User;

/**
 * Manages application session.
 *
 * @author Marty Wallace
 */
class SessionService implements Service {

	const CSRF_TOKEN_NAME = 'CSRFToken';
	const USER_ID_NAME = 'UserID';
	const USER_TOKEN_NAME = 'UserToken';

	/** @var User */
	private $_user;

	/**
	 * Determine whether there is an active session.
	 *
	 * @return bool
	 */
	public function active() {
		return session_status() === PHP_SESSION_ACTIVE;
	}

	/**
	 * Run session garbage collection.
	 *
	 * @return int The total number of sessions that were deleted.
	 */
	public function gc() {
		$total = session_gc();
		return $total === false ? 0 : $total;
	}

	/**
	 * Regenerate the current session ID.
	 *
	 * @param bool $deleteOldSession Whether or not to delete the old session data.
	 *
	 * @return string The new session ID.
	 */
	public function regenerate($deleteOldSession = false) {
		session_regenerate_id($deleteOldSession);
		return session_id();
	}

	/**
	 * Destroy the current session, including all contained data.
	 */
	public function destroy() {
		session_destroy();
	}

	/**
	 * Add or overwrite session data.
	 *
	 * @param string $property The name of the data to add or overwrite.
	 * @param mixed $value The value to add.
	 *
	 * @throws Exception If there is no active session.
	 */
	public function add($property, $value) {
		if (!$this->active()) throw new Exception('There is no active session.');

		$_SESSION[$property] = $value;
	}

	/**
	 * Retrieve {@link Session::add() previously added} session data.
	 *
	 * @param string $property The data to retrieve. If not provided, returns the entire session.
	 * @param mixed $fallback A fallback value to provide if the property did not exist.
	 *
	 * @return mixed
	 *
	 * @throws Exception If there is no active session.
	 */
	public function get($property = null, $fallback = null) {
		if (!$this->active()) throw new Exception('There is no active session.');

		if (empty($property)) return $_SESSION;
		return Utility::evaluate($_SESSION, $property, $fallback);
	}

	/**
	 * Determine whether a property exists within the current session.
	 *
	 * @param string $property The property to check for.
	 *
	 * @return bool
	 *
	 * @throws Exception If there is no active session.
	 */
	public function has($property) {
		if (!$this->active()) throw new Exception('There is no active session.');

		return array_key_exists($property, $_SESSION);
	}

	/**
	 * Remove previously added data from the session.
	 *
	 * @param string $property The property to remove.
	 *
	 * @throws Exception If there is no active session.
	 */
	public function remove($property) {
		if (!$this->active()) throw new Exception('There is no active session.');

		unset($_SESSION[$property]);
	}

	/**
	 * Get the current CSRF token. Creates one if it does not exist.
	 *
	 * @return string
	 */
	public function getCsrfToken() {
		if (!$this->has(self::CSRF_TOKEN_NAME)) {
			$this->regenerateCsrfToken();
		}

		return $this->get(self::CSRF_TOKEN_NAME);
	}

	/**
	 * Generates a new CSRF token and adds it to the session.
	 *
	 * @return string The generated token.
	 */
	public function regenerateCsrfToken() {
		$token = Utility::randomString(64);
		$this->add(self::CSRF_TOKEN_NAME, $token);

		return $token;
	}

	/**
	 * Attempt to associate a {@link User user} with the current session. If there is a user currently associated with
	 * the session, they are first logged out regardless of the validity of the provided credentials.
	 *
	 * @param string $email The user's email address.
	 * @param string $password The user's password.
	 *
	 * @return bool Whether or not the login was successful.
	 */
	public function login($email, $password) {
		$this->logout();

		$user = User::findByCredentials($email, $password);

		if (!empty($user)) {
			$this->add(self::USER_ID_NAME, $user->id);
			$this->add(self::USER_TOKEN_NAME, $user->getToken());

			$this->_user = $user;

			return true;
		}

		return false;
	}

	/**
	 * Remove association with a {@link User user} from the current session.
	 */
	public function logout() {
		$this->_user = null;

		$this->remove(self::USER_ID_NAME);
		$this->remove(self::USER_TOKEN_NAME);
	}

	/**
	 * Get a user associated with the current session.
	 *
	 * @return User
	 */
	public function getUser() {
		if (empty($this->_user)) {
			if ($this->has(self::USER_ID_NAME) && $this->has(self::USER_TOKEN_NAME)) {
				$user = User::find($this->get(self::USER_ID_NAME));

				if (!empty($user) && hash_equals($user->getToken(), $this->get(self::USER_TOKEN_NAME))) {
					$this->_user = $user;
				}
			}
		}

		return $this->_user;
	}

}