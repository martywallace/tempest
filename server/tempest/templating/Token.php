<?php

namespace tempest\templating;


class Token
{

	private $base;
	private $context;
	private $parts;


	public function __construct($base, $context, $prop)
	{
		$this->base = $base;
		$this->context = ltrim($context, '@');
		$parts = preg_split(PATTERN_DOTS, trim($prop, '.'));

		foreach($parts as $p)
		{
			$this->parts[] = new TokenPart($p);
		}
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


	public function isContextual(){ return strlen($this->context) > 0; }
	public function isMultipart(){ return count($this->parts) > 1; }


	public function getBase(){ return $this->base; }
	public function getContext(){ return $this->context; }
	public function getParts(){ return $this->parts; }

}