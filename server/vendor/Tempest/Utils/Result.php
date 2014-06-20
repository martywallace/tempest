<?php namespace Tempest\Utils;


class Result
{
	
	private $errors = [];


	public function error($message)
	{
		$this->errors[] = $message;
		return $this;
	}


	public function merge(Result $result)
	{
		$this->errors = array_merge($this->errors, $result->getErrors());
		return $this;
	}


	public function isOk(){ return count($this->errors) === 0; }
	public function getErrors(){ return $this->errors; }

}