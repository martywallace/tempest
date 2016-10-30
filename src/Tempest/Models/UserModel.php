<?php namespace Tempest\Models;

/**
 * A user that can authenticate themselves with the application.
 *
 * @package Tempest\Models
 */
class UserModel extends DbModel {

	protected $table = 'users';
	protected $fillable = ['email', 'password'];

	/**
	 * A unique token used to store this User in a session.
	 *
	 * @return string
	 */
	public function getToken() {
		return sha1($this->id . '_' . $this->email . '_' . $this->password);
	}

}