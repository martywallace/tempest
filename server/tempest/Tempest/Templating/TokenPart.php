<?php

namespace Tempest\Templating;


/** 
 * Represents part of a Token.
 * @author Marty Wallace.
 */
class TokenPart
{

	private $base;
	private $name;


	/** 
	 * Constructor.
	 * @param $base The base part content.
	 */
	public function __construct($base)
	{
		$this->base = $base;
		$this->name = rtrim($base, '()');
	}


	/**
	 * Determines whether this part is intended as a method call.
	 */
	public function isFunction(){ return $this->base !== $this->name; }


	/**
	 * Returns the base token part text.
	 */
	public function getBase(){ return $this->base; }


	/**
	 * Returns the name of this token part, without parenthesis.
	 */
	public function getName(){ return $this->name; }

}