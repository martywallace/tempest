<?php

namespace tempest\routing;

use \tempest\routing\RoutePart;


class Route
{

	private $pattern;
	private $parts = array();
	private $response;


	public function __construct($pattern, $response = null)
	{
		$this->pattern = cleanUri($pattern);
		$this->response = $response;

		$parts = preg_split(PATTERN_SLASHES, $this->pattern);
		foreach($parts as $part)
		{
			$routePart = new RoutePart($part);
			$this->parts[] = $routePart;
		}
	}


	public function getPart($index)
	{
		return ($index >= 0 && $index < count($this->parts)) ? $this->parts[$index] : null;
	}


	public function getPattern(){ return $this->pattern; }
	public function getParts(){ return $this->parts; }
	public function getResponse(){ return $this->response; }
	public function getLastPart(){ return end($this->parts); }
	public function getTotalParts(){ return count($this->parts); }

}