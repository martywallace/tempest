<?php namespace Tempest\Utils;

use Tempest\Utils\Result;


/** 
 * A JSONResponse is typically sent as the output for an API call.
 * @author Marty Wallace.
 */
class JSONResponse extends Result
{

	private $body = [];
	private $padding = null;


	/**
	 * Adds a value to the body of the response.
	 * @param $value The value to add.
	 */
	public function add($value)
	{
		$this->body[] = $value;
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
	 * Provides a string value to represent this JSONResponse.
	 */
	public function __toString()
	{
		return $this->getContent();
	}

	
	/**
	 * Returns the content for this JSONResponse.
	 */
	public function getContent()
	{
		$base = json_encode([
			"head" => [
				"ok" => $this->isOk(),
				"errors" => $this->getErrors()
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

}