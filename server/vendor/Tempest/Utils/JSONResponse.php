<?php namespace Tempest\Utils;

use Tempest\Utils\Result;


class JSONResponse extends Result
{

	private $body = [];
	private $padding = null;


	public function add($value)
	{
		$this->body[] = $value;
		return $this;
	}


	public function pad($padding)
	{
		$this->padding = $padding;
		return $this;
	}


	public function __toString()
	{
		return $this->getContent();
	}

	
	public function getContent()
	{
		$base = json_encode([
			"head" => [
				"ok" => $this->isOk(),
				"errors" => $this->getErrors()
			],
			"body" => $this->body
		]);

		if($this->padding !== null && strlen($this->padding) > 0)
		{
			// Enabled JSONP.
			$base = "{$this->padding}({$base})";
		}

		return $base;
	}

}