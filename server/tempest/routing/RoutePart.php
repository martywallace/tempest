<?php

namespace tempest\routing;


class RoutePart
{

	const TYPE_FLAT = 1;
	const TYPE_NAMED = 2;


	private $base;
	private $type;


	public function __construct($base)
	{
		$this->base = $base;

		if(preg_match('/^\[[^\]]+\]/', $this->base))
		{
			// Named parameter.
			$this->base = trim($this->base, '[]');
			$this->type = self::TYPE_NAMED;
		}

		else $this->type = self::TYPE_FLAT;
	}


	public function matches(RoutePart $part)
	{
		if($this->getType() === self::TYPE_NAMED || $part->getType() === self::TYPE_NAMED) return true;
		if($this->getBase() === $part->getBase()) return true;

		return false;
	}


	public function getBase(){ return $this->base; }
	public function getType(){ return $this->type; }

}