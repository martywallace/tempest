<?php

namespace tempest\templating;


class PartInfo
{

	private $base;
	private $name;


	public function __construct($base)
	{
		$this->base = $base;
		$this->name = rtrim($base, '()');
	}


	public function isFunction(){ return $this->base !== $this->name; }


	public function getBase(){ return $this->base; }
	public function getName(){ return $this->name; }

}