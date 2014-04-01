<?php namespace Tempest\Database;

class Model
{

	protected $table = null;
	protected $primary = null;


	public static function one($id)
	{
		$def = new static();
	}


	public static function many(Array $ids)
	{
		$def = new static();
	}

}