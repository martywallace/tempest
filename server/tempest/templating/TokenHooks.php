<?php

namespace tempest\templating;


class TokenHooks
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

}