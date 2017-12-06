<?php namespace Tempest\Validation;

use Exception;
use Throwable;

/**
 * An exception thrown during {@link Validator::validate() failed validation}.
 */
class ValidationException extends Exception {

	/** @var array */
	private $_errors = [];

	public function __construct(Validator $validator, int $code = 0, Throwable $previous = null) {
		$this->_errors = $validator->errors();

		parent::__construct('There were validation errors.', $code, $previous);
	}

	/**
	 * @return array
	 */
	public function getErrors() {
		return $this->_errors;
	}

}