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

	public function __get($prop) {
		if ($prop === 'user') {
			return $this->memoize('__user', function() {
				$id = Tempest::get()->session->get('__user_id');
				$token = Tempest::get()->session->get('__user_token');

				if (!empty($id) && !empty($token)) {
					/** @var UserModel $user */
					$user = Tempest::get()->db->mapper(UserModel::class)->first(array('id' => $id));

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
	 * @param string $email The user's email address.
	 *
	 * @return UserModel
	 */
	public function find($email) {
		return $this->memoize('__user_' . $email, function() use ($email) {
			return Tempest::get()->db->mapper(UserModel::class)->first(array('email' => $email));
		});
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

		/** @var UserModel $user */
		$user = Tempest::get()->db->mapper(UserModel::class)->create(array(
			'email' => $email,
			'password' => password_hash($password, CRYPT_BLOWFISH),
			'type' => $type
		));

		if (Tempest::get()->db->mapper(UserModel::class)->save($user)) {
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
		return Tempest::get()->db->mapper(UserModel::class)->where(array('email' => $email))->count() > 0;
	}

	/**
	 * Attempt to login to the application. Returns the logged in user if successful, else null.
	 *
	 * @param string $email The user email address.
	 * @param string $password The user password.
	 *
	 * @return UserModel
	 */
	public function login($email, $password) {
		// Force logout before a new attempt.
		$this->logout();

		$user = $this->find($email);

		if (!empty($user)) {
			if (password_verify($password, $user->password)) {
				Tempest::get()->session->set('__user_id', $user->id);
				Tempest::get()->session->set('__user_token', $user->getToken());

				return $user;
			}
		}

		return null;
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