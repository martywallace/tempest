<?php namespace Tempest\Validation;
use Carbon\Carbon;

/**
 * Wraps a specific field within a set of data associated with a validator.
 *
 * @author Marty Wallace
 */
class FieldValidator {

	/** @var Validator */
	private $_validator;

	/** @var string */
	private $_name;

	public function __construct(Validator $validator, $name) {
		$this->_validator = $validator;
		$this->_name = $name;
	}

	/**
	 * Proxies a {@link Validator::rule rule} call to the associated Validator.
	 *
	 * @param string|callable $rule The rule to add.
	 * @param array $params Optional extra params to attach to the validator.
	 *
	 * @return $this
	 */
	public function rule($rule, ...$params) {
		call_user_func_array(
			[$this->_validator, 'rule'],
			array_merge([$rule, $this->_name], $params)
		);

		return $this;
	}

	/**
	 * Mark this field as required.
	 *
	 * @return $this
	 */
	public function required() {
		return $this->rule('required');
	}

	/**
	 * This field must match the value of another field within the source data.
	 *
	 * @param string $field The field to check equality of.
	 *
	 * @return $this
	 */
	public function equals($field) {
		return $this->rule('equals', $field);
	}

	/**
	 * The field must be different to the value of another field within the source data.
	 *
	 * @param string $field The field to check against.
	 *
	 * @return $this
	 */
	public function different($field) {
		return $this->rule('different', $field);
	}

	/**
	 * The field must be an accepted PHP string.
	 *
	 * @return $this
	 */
	public function accepted() {
		return $this->rule('accepted');
	}

	/**
	 * The field must be numeric.
	 *
	 * @return $this
	 */
	public function numeric() {
		return $this->rule('numeric');
	}

	/**
	 * The field must be an integer.
	 *
	 * @return $this
	 */
	public function integer() {
		return $this->rule('integer');
	}

	/**
	 * The field must be a boolean.
	 *
	 * @return $this
	 */
	public function boolean() {
		return $this->rule('boolean');
	}

	/**
	 * The field must be an array.
	 *
	 * @return $this
	 */
	public function array() {
		return $this->rule('array');
	}

	/**
	 * The field must have the specified length.
	 *
	 * @param int $length The accepted length.
	 *
	 * @return $this
	 */
	public function length($length) {
		return $this->rule('length', $length);
	}

	/**
	 * The field must have a length between two values.
	 *
	 * @param int $min The minimum length.
	 * @param int $max The maximum length.
	 *
	 * @return $this
	 */
	public function lengthBetween($min, $max) {
		return $this->rule('lengthBetween', $min, $max);
	}

	/**
	 * The field must have a minimum length.
	 *
	 * @param int $length The minimum accepted length.
	 *
	 * @return $this
	 */
	public function lengthMin($length) {
		return $this->rule('lengthMin', $length);
	}

	/**
	 * The field must have a maximum length.
	 *
	 * @param int $length The maximum accepted length.
	 *
	 * @return $this
	 */
	public function lengthMax($length) {
		return $this->rule('lengthMax', $length);
	}

	/**
	 * The field must have a minimum value.
	 *
	 * @param int $value The minimum value.
	 *
	 * @return $this
	 */
	public function min($value) {
		return $this->rule('min', $value);
	}

	/**
	 * The field must have a maximum value.
	 *
	 * @param int $value The maximum value.
	 *
	 * @return $this
	 */
	public function max($value) {
		return $this->rule('max', $value);
	}

	/**
	 * The field value must appear in the given list.
	 *
	 * @param array $list The list to appear in.
	 *
	 * @return $this
	 */
	public function in(array $list) {
		return $this->rule('in', $list);
	}

	/**
	 * The field value must not appear in the given list.
	 *
	 * @param array $list The list that the value should not appear in.
	 *
	 * @return $this
	 */
	public function notIn(array $list) {
		return $this->rule('notIn', $list);
	}

	/**
	 * The field must be an IP address.
	 *
	 * @return $this
	 */
	public function ip() {
		return $this->rule('ip');
	}

	/**
	 * The field must be a valid email address.
	 *
	 * @return $this
	 */
	public function email() {
		return $this->rule('email');
	}

	/**
	 * The field must be a URL.
	 *
	 * @return $this
	 */
	public function url() {
		return $this->rule('url');
	}

	/**
	 * The field must be an active URL.
	 *
	 * @return $this
	 */
	public function urlActive() {
		return $this->rule('urlActive');
	}

	/**
	 * The field must be alphabetic.
	 *
	 * @return $this
	 */
	public function alpha() {
		return $this->rule('alpha');
	}

	/**
	 * The field must be alphanumeric.
	 *
	 * @return $this
	 */
	public function alphaNum() {
		return $this->rule('alphaNum');
	}

	/**
	 * The field must be in a slugged format.
	 *
	 * @return $this
	 */
	public function slug() {
		return $this->rule('slug');
	}

	/**
	 * The field must match the provided regex.
	 *
	 * @param string $pattern The regex pattern to match against.
	 *
	 * @return $this
	 */
	public function regex($pattern) {
		return $this->rule('regex', $pattern);
	}

	/**
	 * The field must be a valid date format.
	 *
	 * @return $this
	 */
	public function date() {
		return $this->rule('date');
	}

	/**
	 * The field must match the specified date format.
	 *
	 * @param string $format The date format to match.
	 *
	 * @return $this
	 */
	public function dateFormat($format) {
		return $this->rule('dateFormat', $format);
	}

	/**
	 * The field must be a date before the specified time.
	 *
	 * @param Carbon $date The time to be before.
	 *
	 * @return $this
	 */
	public function dateBefore(Carbon $date) {
		return $this->rule('dateBefore', $date);
	}

	/**
	 * The field must be a date after the specified time.
	 *
	 * @param Carbon $date The time to be after.
	 *
	 * @return $this
	 */
	public function dateAfter(Carbon $date) {
		return $this->rule('dateAfter', $date);
	}

	/**
	 * The field must contain the specified value.
	 *
	 * @param string $value The value to check for.
	 *
	 * @return $this
	 */
	public function contains($value) {
		return $this->rule('contains', $value);
	}

	/**
	 * The field must be a valid credit card number format.
	 *
	 * @return $this
	 */
	public function creditCard() {
		return $this->rule('creditCard');
	}

	/**
	 * The field must be an instance of the specified type.
	 *
	 * @param string $type The type.
	 *
	 * @return $this
	 */
	public function instanceOf($type) {
		return $this->rule('instanceOf', $type);
	}

	/**
	 * The field is optional.
	 *
	 * @return $this
	 */
	public function optional() {
		return $this->rule('optional');
	}

}