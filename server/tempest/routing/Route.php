<?php

namespace tempest\routing;


class Route
{

	private $pattern;
	private $handler;


	public function __construct($pattern, $handler)
	{
		$this->pattern = $pattern;
		$this->handler = $handler;
	}


	public function getPattern(){ return $this->pattern; }
	public function getHandler(){ return $this->handler; }

}