<?php namespace Tempest\Utils;


/**
 * Represents a basic Result; that is, whether something was successful and a list of reasons why it
 * wasn't in the case that it's not.
 * @author Marty Wallace.
 */
class Result
{
	
	private $errors = [];


	/**
	 * Adds some error text to this Result, marking it as unsuccessful.
	 * @param $message The error text.
	 */
	public function error($message)
	{
		$this->errors[] = $message;
		return $this;
	}


	/**
	 * Merges this Result with another and returns a new Result. The merge process will copy errors
	 * existing within both Results and collate them.
	 * @param $result The target Result to merge with.
	 */
	public function merge(Result $result)
	{
		$merged = new Result();
		$errors = array_merge($this->errors, $result->getErrors());

		foreach($errors as $e) $merged->error($e);

		return $this;
	}


	/**
	 * Determines whether this Result was successful (has no errors listed).
	 */
	public function isOk(){ return count($this->errors) === 0; }


	/**
	 * Returns an Array of errors associated with this Result.
	 */
	public function getErrors(){ return $this->errors; }

}