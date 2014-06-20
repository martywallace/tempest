<?php namespace Tempest\Utils;

use Tempest\Utils\Result;


class JSONResponse extends Result
{

	private $body = [];


	public function add($value)
	{
		$this->body[] = $value;
		return $this;
	}


	public function __toString()
	{
		return $this->getContent();
	}

	
	public function getContent()
	{
		return json_encode([
			"head" => [
				"ok" => $this->isOk(),
				"errors" => $this->getErrors()
			],
			"body" => $this->body
		]);
	}

}