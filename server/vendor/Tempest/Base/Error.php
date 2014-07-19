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
	private $lines;
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
		$this->file = $file;
		$this->line = $line;
		$this->context = $context;

		$lines = explode("\n", htmlspecialchars(file_get_contents($this->file)));
		$this->lines = array_slice($lines, $this->line - 4, 7);
	}


	/**
	 * Returns a formatted string of the error lines around and including this error.
	 */
	public function writeLines()
	{
		$tabs = null;
		foreach($this->lines as $line)
		{
			if(!preg_match('/^\t/', $line)) continue;

			$t = preg_replace('/^(\t)+/', '', $line);
			$t = strlen($line) - strlen($t);

			if($tabs === null || $tabs > $t) $tabs = $t;
		}

		$output = [];
		foreach($this->lines as $line)
		{
			$item = preg_replace('/^\t{' . $tabs . '}/', '', $line);
			$item = preg_replace('/\t/', '    ', $item);
			$output[] = '<div data-line="' . count($output) . '">' . $item . '</div>';
		}

		return implode($output);
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