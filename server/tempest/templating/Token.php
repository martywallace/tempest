<?php

namespace tempest\templating;


class Token
{

	const PATTERN = '/\{{2}(?!\/)([^\}]+)\}{2}/';

	const TYPE_PROPERTY = 1;
	const TYPE_METHOD = 2;
	const TYPE_RECURSIVE = 3;

	
	private $match;
	private $body;
	private $type;


	public function __construct($matches, $index)
	{
		$this->match = $matches[0][$index];
		$this->body = $matches[1][$index];
		$this->type = $this->getPartType($this->body);

		if($this->type === self::TYPE_RECURSIVE) $this->body = preg_split('/\.+/', $this->body);
		if($this->type === self::TYPE_METHOD) $this->body = trim($this->body, '()');
	}


	public function getPartType($part)
	{
		if(preg_match('/\.+/', $part)) return self::TYPE_RECURSIVE;
		if(preg_match('/\(\)$/', $part)) return self::TYPE_METHOD;

		return self::TYPE_PROPERTY;
	}


	public function getMatch(){ return $this->match; }
	public function getBody(){ return $this->body; }
	public function getType(){ return $this->type; }
	
}