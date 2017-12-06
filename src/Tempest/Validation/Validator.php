<?php namespace Tempest\Validation;

use Closure;
use Valitron\Validator as BaseValidator;

/**
 * Thin wrapper around Valitron's validator.
 *
 * @author Marty Wallace
 */
class Validator extends BaseValidator {

	/**
	 * Creates a new validator.
	 *
	 * @param mixed $data The data to validate.
	 *
	 * @return static
	 */
	public static function create($data) {
		return new static($data);
	}

	/**
	 * Attach multiple rules to this validator using either a callable or an array.
	 *
	 * @param array|Closure $rules Either an array of validation rules, or a closure accepting this validator as its
	 * only argument, through which validation rules can be applied.
	 *
	 * @return $this
	 */
	public function rules($rules) {
		if (is_callable($rules)) $rules($this);
		else parent::rules($rules);

		return $this;
	}

	/**
	 * Perform queued validation rules against the provided data. If the validation fails, a {@link ValidationException}
	 * is thrown. If the exception is thrown within the HTTP kernel, the kernel will terminate with a 400 Bad Request
	 * response containing the validation errors as JSON.
	 *
	 * @return void
	 *
	 * @throws ValidationException
	 */
	public function validate() {
		if (!parent::validate()) {
			throw new ValidationException($this);
		}
	}

	/**
	 * Creates a validator specific to a single field within the data provided to this validator.
	 *
	 * @param string $field The field to validate.
	 *
	 * @return FieldValidator
	 */
	public function field($field) {
		return new FieldValidator($this, $field);
	}

}