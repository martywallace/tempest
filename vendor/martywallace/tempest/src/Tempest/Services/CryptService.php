<?php namespace Tempest\Services;

use Exception;
use Crypto;


/**
 * Handles encryption and decryption of application data. This library wraps Defuse/PHP-Encryption.
 *
 * @see https://github.com/defuse/php-encryption
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class CryptService extends Service {

	protected function setup() {
		//
	}

	public function __get($prop) {
		if ($prop === 'key') {
			return $this->memoize('key', function() {
				$key = app()->config('key');

				if (!empty($key)) {
					return base64_decode($key);
				} else {
					throw new Exception('Crypt services cannot be used without a key - you can use app()->crypt->generateKey() to get one.');
				}
			});
		}

		return null;
	}

	/**
	 * Encrypt some data with the application key.
	 *
	 * @param string $value The data to encrypt.
	 *
	 * @return string
	 */
	public function encrypt($value) {
		return Crypto::Encrypt($value, $this->key);
	}

	/**
	 * Decrypt some data with the application key.
	 *
	 * @param string $value The encrypted value to decrypt.
	 *
	 * @return string
	 */
	public function decrypt($value) {
		return Crypto::Decrypt($value, $this->key);
	}

	/**
	 * Generate and return a base64 encoded key.
	 *
	 * @return string
	 */
	public function generateKey() {
		return base64_encode(Crypto::CreateNewRandomKey());
	}

}