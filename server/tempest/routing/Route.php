<?php

namespace tempest\routing;


class Route
{

	private $pattern;
	private $response;


	public function __construct($pattern, $response)
	{
		$this->pattern = $pattern;
		$this->response = $response;
	}


	public function getPattern(){ return $this->pattern; }
	public function getResponse(){ return $this->response; }

}