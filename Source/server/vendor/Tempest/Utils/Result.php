<?php namespace Tempest\Utils;


class Result
{
	
	private $errors = array();


	public function error($message)
	{
		$this->errors[] = $message;
	}


	public function merge(Result $result)
	{
		$this->errors = array_merge($this->errors, $result->getErrors());
	}


	public function isOk(){ return count($this->errors) === 0; }
	public function getErrors(){ return $this->errors; }

}