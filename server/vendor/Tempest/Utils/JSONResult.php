<?php namespace Tempest\Utils;

use Tempest\Routing\Output;
use Tempest\Utils\IResult;
use Tempest\Utils\ResultError;


/** 
 * A JSONResponse is typically sent as the output for an API call.
 * @author Marty Wallace.
 */
class JSONResult extends Output implements IResult
{

	private $body = [];
	private $errors = [];
	private $padding = null;


	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->setMime('application/json');
	}


	/**
	 * Adds a value to the body of the response.
	 * @param $name The name of the value (its key in the resulting array).
	 * @param $value The value to add.
	 */
	public function add($name, $value)
	{
		$this->body[$name] = $value;
		return $this;
	}


	/**
	 * Enabled JSONP-style output of JSON.
	 * @param $padding The name of the padding function wrapping the JSON output.
	 */
	public function pad($padding)
	{
		$this->padding = $padding;
		return $this;
	}

	
	/**
	 * Returns the content for this JSONResponse.
	 */
	public function getContent()
	{
		$errs = [];
		foreach($this->getErrors() as $error)
			$errs[] = ["code" => (string)$error->getCode(), "text" => $error->getText()];


		$base = json_encode([
			"head" => [
				"ok" => $this->isOk(),
				"errors" => $errs
			],
			"body" => $this->body
		]);

		if($this->padding !== null && strlen($this->padding) > 0)
		{
			// Enabled JSONP.
			$base = "{$this->padding}({$base})";
		}

		return $base;
	}


	/**
	 * Registers an error.
	 * @param $error A ResultError instance.
	 */
	public function error(ResultError $error)
	{
		$this->errors[] = $error;
		return $this;
	}


	/**
	 * Merge the errors of a target Result with this Result. This Result will contain both sets of errors.
	 * @param $result The target Result.
	 */
	public function merge(IResult $result)
	{
		$this->errors = array_merge($this->errors, $result->getErrors());
		return $this;
	}


	/**
	 * Determine whether this Result contains no errors.
	 */
	public function isOk(){ return count($this->errors) === 0; }



	/**
	 * Returns the current list of errors.
	 */
	public function getErrors(){ return $this->errors; }

}