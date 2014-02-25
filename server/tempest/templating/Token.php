<?php

namespace tempest\templating;


class Token
{

	private $base;
	private $parts;
	private $params;


	public function __construct($base, $prop, $params)
	{
		$this->base = $base;
		$this->parts = preg_split(PATTERN_DOTS, $prop);
		$this->params = $params;
	}


	public function replace($subject, $value)
	{
		return str_replace($this->base, $this->toText($value), $subject);
	}


	private function toText($value)
	{
		if(is_array($value)) return 'array';
		if(is_object($value)) return 'object';

		if($value === true) return 'true';
		if($value === false) return 'false';
		if($value === null) return 'null';

		return $value;
	}


	public function isFunction(){ return strlen($this->params) > 0; }
	public function isMultipart(){ return count($this->parts) > 1; }


	public function getBase(){ return $this->base; }
	public function getParts(){ return $this->parts; }
	public function getFirstPart(){ return $this->parts[0]; }
	public function getTotalParts(){ return count($this->parts); }
	public function getParams(){ return $this->params; }

}