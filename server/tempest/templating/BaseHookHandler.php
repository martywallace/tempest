<?php

namespace tempest\templating;


class BaseHookHandler
{

	public function ucase($value)
	{
		return strtoupper($value);
	}


	public function lcase($value)
	{
		return strtolower($value);
	}


	public function dmy($value)
	{
		return date("d M Y", $value);
	}


	public function rev($value)
	{
		return 'rev';
	}

}