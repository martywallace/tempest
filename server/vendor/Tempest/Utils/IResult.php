<?php namespace Tempest\Utils;

use Tempest\Utils\ResultError;


/**
 * Represents a basic Result; that is, whether something was successful and a list of reasons why it
 * wasn't in the case that it's not.
 * @author Marty Wallace.
 */
interface IResult
{

	/**
	 * Determine whether this Result contains no errors.
	 */
	public function isOk();


	/**
	 * Returns the current list of errors.
	 */
	public function getErrors();


	/**
	 * Registers an error.
	 * @param $error A ResultError instance.
	 */
	public function error(ResultError $error);


	/**
	 * Merge the errors of a target Result with this Result. This Result will contain both sets of errors.
	 * @param $result The target Result.
	 */
	public function merge(IResult $result);

}