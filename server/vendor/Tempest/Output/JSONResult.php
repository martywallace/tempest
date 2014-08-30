<?php namespace Tempest\Output;

use Tempest\Output\BaseOutput;
use Tempest\Utils\IResult;


/** 
 * A JSONResponse is typically sent as the output for an API call.
 * @author Marty Wallace.
 */
class JSONResult extends BaseOutput implements IResult
{

	private $body = array();
	private $errors = array();
	private $padding = null;


	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct('application/json');
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
		$base = json_encode(array(
			"head" => array(
				"ok" => $this->isOk(),
				"errors" => $this->errors
			),
			"body" => $this->body
		));

		if($this->padding !== null && strlen($this->padding) > 0)
		{
			// Enabled JSONP.
			$base = "{$this->padding}({$base})";
		}

		return $base;
	}


	/**
	 * Registers an error.
	 * @param $error The error text.
	 */
	public function error($error)
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