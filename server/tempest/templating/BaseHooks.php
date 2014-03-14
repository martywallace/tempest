<?php

namespace tempest\templating;


class BaseHooks
{

	public function ucase($value){ return strtoupper($value); }
	public function lcase($value){ return strtolower($value); }
	public function dmy($value){ return date("d M Y", $value); }
	public function link($value){ return '<a href="' . $value . '">' . $value . '</a>'; }
	public function sha1($value){ return sha1($value); }
	public function trim($value){ return trim($value); }
	public function nl2br($value){ return nl2br($value); }
	public function strong($value){ return "<strong>$value</strong>"; }
	public function slashes($value){ return addslashes($value); }

}