<?php namespace Tempest\Templating;


class Hooks
{

	public static function ucase($value){ return strtoupper($value); }
	public static function lcase($value){ return strtolower($value); }
	public static function dmy($value){ return date("d M Y", $value); }
	public static function link($value){ return '<a href="' . $value . '">' . $value . '</a>'; }
	public static function mailto($value){ return '<a href="mailto:' . $value . '">' . $value . '</a>'; }
	public static function sha1($value){ return sha1($value); }
	public static function trim($value){ return trim($value); }
	public static function nl2br($value){ return nl2br($value); }
	public static function strong($value){ return "<strong>$value</strong>"; }
	public static function slashes($value){ return addslashes($value); }

}