<?php namespace Tempest\Utils;


/**
 * Represents a Path.
 * @author Marty Wallace.
 */
class Path
{

	protected $base;
	protected $chunks = array();

	
	/**
	 * Constructor.
	 * @param $base The input path string.
	 */
	public function __construct($base)
	{
		$this->base = strtolower($base);
		$this->base = path_normalize($this->base, '/', true, false);

		if($base === '/') $this->chunks = array();
		else $this->chunks = path_split($base);
	}


	/**
	 * Returns a chunk of this Path at a given index.
	 * @param int $index The index.
	 * @return string The chunk.
	 */
	public function chunk($index)
	{
		if($index < 0 || $index > count($this->chunks) - 1) return null;
		return $this->chunks[$index];
	}


	/**
	 * Provides a string value to represent this Path.
	 */
	public function __toString()
	{
		return $this->base;
	}


	/**
	 * Returns the base string for this Path.
	 */
	public function getBase(){ return $this->base; }


	/**
	 * Returns an Array of chunks that make up this Path.
	 */
	public function getChunks(){ return $this->chunks; }


	/**
	 * Returns the amount of chunks in this Path.
	 */
	public function getLength(){ return count($this->chunks); }

}