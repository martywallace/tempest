<?php namespace Tempest\Base;


/**
 * An error triggered by the application.
 * @author Marty Wallace.
 */
class Error
{

	private $number;
	private $string;
	private $file;
	private $line;
	private $context;


	/**
	 * Constructor.
	 * @param $number The error number.
	 * @param $string The error text.
	 * @param $file The file triggering the error.
	 * @param $line The line number triggering the error.
	 * @param $context The error context.
	 */
	public function __construct($number, $string, $file, $line, $context)
	{
		$this->number = $number;
		$this->string = $string;
		$this->file = path_normalize(str_replace(APP_ROOT, '', $file), '/', true, false);
		$this->line = $line;
		$this->context = $context;
	}


	/**
	 * Returns the error number.
	 */
	public function getNumber(){ return $this->number; }


	/**
	 * Returns the error text.
	 */
	public function getString(){ return $this->string; }


	/**
	 * Returns the file triggering this error.
	 */
	public function getFile(){ return $this->file; }


	/**
	 * Returns the line number triggering this error.
	 */
	public function getLine(){ return $this->line; }


	/**
	 * Returns the error context.
	 */
	public function getContext(){ return $this->context; }

}