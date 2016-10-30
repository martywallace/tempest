<?php namespace Tempest\Services;

use Exception;
use Illuminate\Database\Schema\Blueprint;
use Tempest\Tempest;
use Tempest\Models\UserModel;


/**
 * A service to interact with application users.
 *
 * @see UserModel
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
					$user = $this->find($id);

					if (!empty($user)) {
						if (hash_equals($user->getToken(), $token)) {
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
		return Tempest::get()->db->model(UserModel::class)->find($id)->first();
	}

	/**
	 * Finds and returns a user.
	 *
	 * @param string $email The user's email address.
	 *
	 * @return UserModel
	 */
	public function findByEmail($email) {
		return Tempest::get()->db->model(UserModel::class)->where('email', $email)->first();
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
	 *
	 * @return UserModel
	 *
	 * @throws Exception If the email address supplied was not valid.
	 * @throws Exception If there was no value provided as the password.
	 * @throws Exception If a user already exists with the email address provided.
	 */
	public function create($email, $password) {
		$email = strtolower($email);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new Exception('A new user must have a valid email address.');
		} else if ($this->exists($email)) {
			throw new Exception('A user with that email already exists.');
		}

		if (empty($password)) {
			throw new Exception('You must provide a password for the new user.');
		}

		$user = new UserModel([
			'email' => $email,
			'password' => password_hash($password, CRYPT_BLOWFISH)
		]);

		return $user->save() ? $user : null;
	}


	/**
	 * Determine whether a user exists.
	 *
	 * @param string $email The email address to check for.
	 *
	 * @return bool
	 */
	public function exists($email) {
		return Tempest::get()->db->model(UserModel::class)->where('email', $email)->exists();
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

	public function createTable() {
		Tempest::get()->db->schema()->create('users', function(Blueprint $table) {
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password');
		});
	}

}