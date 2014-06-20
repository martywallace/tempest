<?php namespace Tempest\Utils;

class Path
{

	protected $base;
	protected $chunks = [];

	
	public function __construct($base)
	{
		$this->base = strtolower($base);
		$this->base = path_normalize($this->base, '/', true, false);

		if($base === '/') $this->chunks = [];
		else $this->chunks = path_split($base);
	}


	public function chunk($index)
	{
		if($index < 0 || $index > count($this->chunks) - 1) return null;
		return $this->chunks[$index];
	}


	public function __toString()
	{
		return $this->base;
	}


	public function getBase(){ return $this->base; }
	public function getChunks(){ return $this->chunks; }
	public function getLength(){ return count($this->chunks); }

}