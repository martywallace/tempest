<?php namespace Tempest\Templating;

use Tempest\Utils\FileHelper;


class Template
{

	private $content;

	
	public static function load($file)
	{
		return new Template(FileHelper::getContents(DIR_STATIC . $file));
	}


	public static function prepare($value)
	{
		return str_replace('~/', PUB_ROOT, $value);
	}


	public function __construct($content = '')
	{
		$this->setContent($content);
	}


	public function __toString()
	{
		return $this->content;
	}


	public function setContent($value)
	{
		$this->content = self::prepare($value);
	}


	public function getContent(){ return $this->content; }

}