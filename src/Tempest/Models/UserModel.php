<?php namespace Tempest\Models;

/**
 * A user that can authenticate themselves with the application.
 *
 * @property int $id The user ID.
 * @property string $email The user email address.
 * @property string $password The user's hashed password.
 * @property string $type The user type.
 *
 * @package Tempest\Models
 */
class UserModel extends DbModel {

	protected static $table = 'users';

	public static function fields() {
		return array(
			'id' => array('type' => self::FIELD_INT, 'autoincrement' => true, 'primary' => true),
			'email' => array('type' => self::FIELD_STRING, 'unique' => true),
			'password' => array('type' => self::FIELD_STRING),
			'type' => array('type' => self::FIELD_STRING)
		);
	}

	/**
	 * A unique token used to store this User in a session.
	 *
	 * @return string
	 */
	public function getToken() {
		return sha1($this->id . '_' . $this->email . '_' . $this->password);
	}

}