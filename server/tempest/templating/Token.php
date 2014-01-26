<?php

namespace tempest\templating;


class Token
{

	const PATTERN = '/\{{2}(?!\/)([^\}]+)\}{2}/';

	
	private $match;
	private $body;


	public function __construct($matches, $index)
	{
		$this->match = $matches[0][$index];
		$this->body = $matches[1][$index];
	}


	public function getMatch(){ return $this->match; }
	public function getBody(){ return $this->body; }
	
}