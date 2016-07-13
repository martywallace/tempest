<?php namespace Tempest\Services;

use Exception;
use Tempest\Tempest;
use Tempest\Models\UserModel;


/**
 * A service to interact with application users.
 *
 * @see Tempest\Models\UserModel
 *
 * @property-read UserModel $user The current logged in user, if there is one.
 *
 * @package Tempest\Services
 */
class UserService extends Service {

	/**
	 * Get the name of the users table as defined in the configuration, falling back to "users".
	 *
	 * @return string
	 */
	public static function table() {
		return Tempest::get()->config->get('users.table', 'users');
	}

	/**
	 * Gets the available user types as defined in the configuration. All types are converted to lowercase.
	 *
	 * @return string[]
	 */
	public static function types() {
		return array_map(function($type) {
			return strtolower($type);
		}, Tempest::get()->config->get('users.types', array()));
	}

	public function __get($prop) {
		if ($prop === 'user') {
			return $this->memoize('__user', function() {
				$id = Tempest::get()->session->get('__user_id');
				$token = Tempest::get()->session->get('__user_token');

				if (!empty($id) && !empty($token)) {
					/** @var UserModel $user */
					$user = $this->find($id);

					if (!empty($user)) {
						if ($user->getToken() === $token) {
							return $user;
						}
					}
				}

				return null;
			});
		}

		return null;
	}

	/**
	 * Finds and returns a user.
	 *
	 * @param string $id The user's id.
	 *
	 * @return UserModel
	 */
	public function find($id) {
		return $this->memoize('__user_' . $id, function() use ($id) {
			return Tempest::get()->db->one('SELECT * FROM ' . static::table() . ' WHERE id = ?', array($id), UserModel::class);
		});
	}

	/**
	 * Finds and returns a user.
	 *
	 * @param string $email The user's email address.
	 *
	 * @return UserModel
	 */
	public function findByEmail($email) {
		return $this->memoize('__useByEmail_' . $email, function() use ($email) {
			return Tempest::get()->db->one('SELECT * FROM ' . static::table() . ' WHERE email = ?', array($email), UserModel::class);
		});
	}

	/**
	 * Finds and returns a user with matching email and password values. Returns null if the credentials were not valid
	 * for any known users.
	 *
	 * @param string $email The user's email address.
	 * @param string $password The user's password.
	 *
	 * @return UserModel
	 */
	public function findByCredentials($email, $password) {
		$user = $this->findByEmail($email);

		if (!empty($user)) {
			if (password_verify($password, $user->password)) {
				return $user;
			}
		}

		return null;
	}

	/**
	 * Create a new application user.
	 *
	 * @param string $email The user's email address. It must be unique.
	 * @param string $password The user's password. It will be hashed before insertion into the new user record.
	 * @param string $type The user type. Not required but can be used by your application to implement alternate
	 * permission levels, etc.
	 *
	 * @return UserModel
	 *
	 * @throws Exception If the email address supplied was not valid.
	 * @throws Exception If there was no value provided as the password.
	 * @throws Exception If a user already exists with the email address provided.
	 * @throws Exception If there is a type provided and the type does not match a type defined in the configuration.
	 */
	public function create($email, $password, $type = null) {
		$email = strtolower($email);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new Exception('A new user must have a valid email address.');
		} else if ($this->exists($email)) {
			throw new Exception('A user with that email already exists.');
		}

		if (empty($password)) {
			throw new Exception('You must provide a password for the new user.');
		}

		if (!empty($type)) {
			$type = strtolower($type);

			if (!in_array($type, static::types())) {
				throw new Exception('There is no user type named "' . $type . '".');
			}
		}

		$user = UserModel::create(array(
			'email' => $email,
			'password' => password_hash($password, CRYPT_BLOWFISH),
			'type' => $type
		));

		if ($user->save()) {
			return $user;
		}

		return null;
	}


	/**
	 * Determine whether a user exists.
	 *
	 * @param string $email The email address to check for.
	 *
	 * @return bool
	 */
	public function exists($email) {
		return Tempest::get()->db->prop('SELECT COUNT(*) FROM ' . static::table() . ' WHERE email = ?', array($email)) > 0;
	}

	/**
	 * Attempt to login to the application. Returns the logged in user if successful, else null. This method has the
	 * same input and output as {@link findByCredentials()} except it retains the logged in user in the current session,
	 * making them available via {@link $user} thereafter.
	 *
	 * @param string $email The user email address.
	 * @param string $password The user password.
	 *
	 * @return UserModel
	 */
	public function login($email, $password) {
		// Force logout before a new attempt.
		$this->logout();

		$user = $this->findByCredentials($email, $password);

		if (!empty($user)) {
			Tempest::get()->session->set('__user_id', $user->id);
			Tempest::get()->session->set('__user_token', $user->getToken());
		}

		return $user;
	}

	/**
	 * Destroy the current user session.
	 */
	public function logout() {
		$this->unmemoize('__user');

		Tempest::get()->session->del('__user_id');
		Tempest::get()->session->del('__user_token');
	}

}