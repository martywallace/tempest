<?php

namespace tempest\base;

use \tempest\base\Error;
use \tempest\templating\Template;


class ErrorHandler
{

	private $errors = array();


	public function __construct()
	{
		set_error_handler(array($this, 'handle'));
	}


	public function handle($number, $message, $script, $line)
	{
		$this->errors[] = new Error($number, $message, $script, $line);
	}


	public function displayErrors()
	{
		$document = Template::load("tempest/document.html");
		$errorBatchTpl = Template::load("tempest/errors.html");
		$errorTpl = Template::load("tempest/error.html");
		$errorHtml = array();

		foreach($this->errors as $error)
		{
			$errorHtml[] = Template::inject($errorTpl, $error);
		}

		$html = Template::inject($document, array(
			"title" => "Errors",
			"content" => $errorBatchTpl
		));

		die(Template::inject($html, array("errors" => Template::combine($errorHtml))));
	}


	public function hasErrors(){ return count($this->errors) !== 0; }

}