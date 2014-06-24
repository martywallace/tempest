<?php namespace Tempest\Base;

class Error
{

	private $number;
	private $string;
	private $file;
	private $line;
	private $context;


	public function __construct($number, $string, $file, $line, $context)
	{
		$this->number = $number;
		$this->string = $string;
		$this->file = path_normalize(str_replace(APP_ROOT, '', $file), '/', true, false);
		$this->line = $line;
		$this->context = $context;
	}


	public function getNumber(){ return $this->number; }
	public function getString(){ return $this->string; }
	public function getFile(){ return $this->file; }
	public function getLine(){ return $this->line; }
	public function getContext(){ return $this->context; }

}