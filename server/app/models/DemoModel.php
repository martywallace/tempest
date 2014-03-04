<?php

namespace app\models;


class DemoModel
{

	public $first = "John";
	public $last = "Doe";
	public $age = 22;
	public $inner;
	public $array = array("test" => 5);
	public $another = "Working";
	public $getFullName = 10;


	public function __construct($first = true)
	{
		if(!$first) return;
		$this->inner = new DemoModel(false);
	}


	public function getFullName()
	{
		return "$this->first $this->last";
	}


	public function getNewModel()
	{
		return new DemoModel();
	}

}