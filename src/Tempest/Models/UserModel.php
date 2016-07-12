<?php namespace Tempest\Models;
use Tempest\Tempest;

/**
 * A user that can authenticate themselves with the application.
 *
 * @package Tempest\Models
 */
class UserModel extends DbModel {

	/** @var int $id */
	public $id;

	/** @var string $email */
	public $email;

	/** @var string $password */
	public $password;

	/** @var string $type */
	public $type;

	public function save() {
		$query = 'INSERT INTO ' . Tempest::get()->config->get('users.table', 'users') . ' (email, password, type)
			VALUES(:email, :password, :type) ON DUPLICATE KEY UPDATE password = :password, type = :type';

		Tempest::get()->db->query($query, array(
			':email' => $this->email,
			':password' => $this->password,
			':type' => $this->type
		));

		return true;
	}

	/**
	 * A unique token used to store this User in a session.
	 *
	 * @return string
	 */
	public function getToken() {
		return sha1($this->id . '_' . $this->email . '_' . $this->password);
	}

	public function jsonSerialize() {
		return array(
			'id' => intval($this->id),
			'email' => $this->email,
			'type' => $this->type
		);
	}

}