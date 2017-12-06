<?php namespace Tempest\Validation;

use Valitron\Validator as BaseValidator;

/**
 * Thin wrapper around Valitron's validator.
 *
 * @author Marty Wallace
 */
class Validator extends BaseValidator {

	/**
	 * Perform queued validation rules against the provided data. If the validation fails, a ValidationException is
	 * thrown.
	 *
	 * @throws ValidationException
	 */
	public function validate() {
		if (!parent::validate()) {
			throw new ValidationException($this);
		}
	}

	/**
	 * Mark a field as required.
	 *
	 * @param string $field The field to mark required.
	 *
	 * @return $this
	 */
	public function required($field) {
		return $this->rule('required', $field);
	}

}