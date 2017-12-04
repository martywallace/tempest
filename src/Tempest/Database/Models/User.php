<?php namespace Tempest\Database\Models;

use Tempest\Database\Field;
use Tempest\Database\Model;

/**
 * An application user, able to authenticate with the app.
 *
 * @property int $id The user's ID.
 * @property string $email The user's email address.
 * @property string $password The user's hashed password.
 *
 * @author Marty Wallace
 */
class User extends Model {

	/**
	 * Find a user using their email and password.
	 *
	 * @param string $email The user's email address.
	 * @param string $password The user's password.
	 *
	 * @return static
	 */
	public static function findByCredentials($email, $password) {
		$user = static::select()->where('email', strtolower($email))->first();

		if (!empty($user)) {
			if (password_verify($password, $user->password)) {
				return $user;
			}
		}

		return null;
	}

	/**
	 * Find a user using a token derived from their username and password.
	 *
	 * @param string $token The token - the result of merging the user's email and password with a colon, then base64
	 * encoding it.
	 *
	 * @return static
	 */
	public static function findByToken($token) {
		preg_match('/^(?<email>.+?)\:(?<password>.*)$/', base64_decode($token), $credentials);

		if (!empty($credentials)) {
			return static::findByCredentials($credentials['email'], $credentials['password']);
		}

		return null;
	}

	protected static function fields() {
		return [
			'id' => Field::int()->setAutoIncrements(),
			'created' => Field::dateTime()->setDefault('now'),
			'email' => Field::string()->addUniqueKey()->setNotNullable(),
			'password' => Field::string()->setNotNullable()
		];
	}

}