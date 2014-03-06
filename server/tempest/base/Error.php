<?php

namespace tempest\base;


class Error
{

	public $number;
	public $message;
	public $script;
	public $line;


	public function __construct($number, $message, $script, $line)
	{
		$this->number = $number;
		$this->message = $message;
		$this->line = $line;
		$this->script = str_replace(APP_ROOT, '', \tempest\normalizePath($script));
		$this->script = str_replace('.php' . DIRECTORY_SEPARATOR, '', $this->script);
	}

}