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
	 * @param $number int The error number.
	 * @param $string string The error text.
	 * @param $file string The file triggering the error.
	 * @param $line int The line number triggering the error.
	 * @param $context Array The error context.
	 */
	public function __construct($number, $string, $file, $line, $context)
	{
		$this->number = $number;
		$this->string = $string;
		$this->file = $file;
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
	 * Returns the name and path of the file triggering this error.
	 */
	public function getFile(){ return $this->file; }


	/**
	 * Return the name of the file triggering this error, without the path.
	 */
	public function getShortFile(){ return basename($this->file); }


	/**
	 * Returns the line number triggering this error.
	 */
	public function getLine(){ return $this->line; }


	/**
	 * Returns the error context.
	 */
	public function getContext(){ return $this->context; }

}