<?php namespace Tempest\Templating;

use Tempest\Utils\FileHelper;
use Tempest\Templating\Token;
use Tempest\Templating\TokenPart;


class Template
{

	private $content;


	public static function load($file)
	{
		$path = APP_ROOT . 'static' . SEP . path_normalize($file, SEP, false, false);
		return new static(FileHelper::getContents($path));
	}


	public static function prepare($content)
	{
		return str_replace("~/", PUB_ROOT, $content);
	}


	public static function batch(Template $template, Array $batch, Template $empty = null)
	{
		if(count($batch) === 0) return $empty === null ? new Template('') : $empty;

		$output = [];
		foreach($batch as $b)
		{
			$output[] = $template->copy()->bind($b);
		}

		return new Template(implode($output));
	}


	public function __construct($content = '')
	{
		$this->update($content);
	}


	public function __toString()
	{
		return $this->content;
	}


	public function update($content = '')
	{
		$this->content = self::prepare($content);
	}


	public function bind($data, $context = null)
	{
		foreach($this->getTokens() as $token)
		{
			$value = $data;

			if($context === null && $token->isContextual()) continue;
			if($context !== null && !$token->isContextual()) continue;
			if($context !== null && $token->getContext() !== $context) continue;

			foreach($token->getParts() as $part)
			{
				$p = $part->getBase();

				if(is_array($value) && array_key_exists($p, $value)) $value = $value[$p];
				else if(is_object($value) && !$part->isFunction() && property_exists($value, $p)) $value = $value->$p;
				else if(is_object($value) && $part->isFunction() && method_exists($value, $part->getName())) $value = $value->{$part->getName()}();

				else continue 2;
			}

			$this->update($token->replace($this->content, $value));
		}

		return $this;
	}


	public function copy()
	{
		return new Template($this->getContent());
	}


	private function getTokens()
	{
		$tokens = [];
		preg_match_all(RGX_TEMPLATE_TOKEN, $this->content, $matches);
		
		for($i = 0; $i < count($matches[0]); $i++)
		{
			$tokens[] = new Token($matches, $i);
		}


		return $tokens;
	}


	public function getContent(){ return $this->content; }

}