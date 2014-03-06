<?php

namespace tempest\routing;

use \tempest\routing\RoutePart;


class Route
{

	private $pattern;
	private $parts = array();
	private $responseClass;
	private $responseMethod;


	public function __construct($pattern, $response = null)
	{
		$this->pattern = \tempest\cleanUri($pattern);

		$response = preg_split('/::/', $response);
		$this->responseClass = $response[0];
		$this->responseMethod = count($response) === 2 ? $response[1] : DEFAULT_RESPONSE_NAME;

		$parts = preg_split(PATTERN_SLASHES, $this->pattern);
		foreach($parts as $part)
		{
			$routePart = new RoutePart($part);
			$this->parts[] = $routePart;
		}
	}


	public function getPart($index)
	{
		return $this->parts[$index];
	}


	public function getPattern(){ return $this->pattern; }
	public function getParts(){ return $this->parts; }
	public function getResponseClass(){ return $this->responseClass; }
	public function getResponseMethod(){ return $this->responseMethod; }
	public function getLastPart(){ return end($this->parts); }

}