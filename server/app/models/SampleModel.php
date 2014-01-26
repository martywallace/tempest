<?php

namespace app\models;


class SampleModel
{

	public $first = "Marty";
	public $last = "Wallace";


	public function getAge()
	{
		return 22;
	}


	public function getTest()
	{
		$c = new \stdclass();
		$c->thing = 20;
		return $c;
	}

}