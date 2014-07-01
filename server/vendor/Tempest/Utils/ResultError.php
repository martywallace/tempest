<?php namespace Tempest\Utils;


/**
 * An error that can appear in a Result.
 * @author Marty Wallace.
 */
class ResultError
{

	private $code;
	private $text;

	
	/**
	 * Constructor.
	 * @param $code The error code, for easier identification.
	 * @param $text The error text, for readability.
	 */
	public function __construct($code, $text)
	{
		$this->code = $code;
		$this->text = $text;
	}


	/**
	 * Returns the error code.
	 */
	public function getCode(){ return $this->code; }


	/**
	 * Returns the error text.
	 */
	public function getText(){ return $this->text; }

}