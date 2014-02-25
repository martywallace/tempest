<?php

namespace tempest\templating;


class Token
{

	private $base;
	private $name;
	private $params;


	public function __construct($base, $name, $params)
	{
		$this->base = $base;
		$this->name = $name;
		$this->params = $params;
	}


	public function replace($subject, $value)
	{
		return str_replace($this->base, $value, $subject);
	}


	public function isFunction(){ return strlen($this->params) > 0; }


	public function getBase(){ return $this->base; }
	public function getName(){ return $this->name; }
	public function getParams(){ return $this->params; }

}