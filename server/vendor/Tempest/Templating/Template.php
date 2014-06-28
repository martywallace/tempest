<?php namespace Tempest\Templating;

use Tempest\Templating\Token;
use Tempest\Templating\TokenPart;


/**
 * Templates manage strings containing tokens which can have values bound to them. Those tokens are
 * then replaced with the bound value for final output.
 * @author Marty Wallace.
 */
class Template
{

	private $content;


	/**
	 * Loads a new Template whose contents is the value of a specified file.
	 * @param $file The file whose contents will be used for the resulting Template.
	 */
	public static function load($file)
	{
		$path = APP_ROOT . 'static' . SEP . path_normalize($file, SEP, false, false);
		return new static(file_get_contents($path));
	}


	/**
	 * Prepares template content with core replacements.
	 * @param $content The content to prepare.
	 */
	public static function prepare($content)
	{
		return str_replace("~/", PUB_ROOT, $content);
	}


	/**
	 * Binds multiple Array elements to a given Template and returns a new Template whose content is
	 * the value of each resulting Template combined.
	 * @param $template The Template to bind each element to.
	 * @param $batch The Array whose children will be bound to the supplied Template.
	 * @param $empty The Template to return if the supplied batch Array is empty.
	 */
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


	/**
	 * Constructor.
	 * @param $content The starting Template content.
	 */
	public function __construct($content = '')
	{
		$this->update($content);
	}


	/**
	 * Provides a string value to represent this Template.
	 */
	public function __toString()
	{
		return $this->getContent();
	}


	/**
	 * Updates this Template with new content.
	 * @param $content The new content to allocate to this Template.
	 */
	public function update($content = '')
	{
		$this->content = self::prepare($content);
		return $this;
	}


	/**
	 * Binds data to this Template.
	 * @param $data The data to bind. Can be either an Array or class instance (object).
	 * @param $context An optional context. Only tokens with the matching context will be replaced.
	 */
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


	/**
	 * Makes a copy of this Template, by taking the current content and using it as the starting content
	 * for a new Template instance.
	 */
	public function copy()
	{
		return new Template($this->getContent());
	}


	/**
	 * Finalize the Template content. Used automatically if the Template is the output of a Response.
	 */
	public function finalize()
	{
		foreach($this->getTokens() as $token)
		{
			if($token->hasPrefix(Token::B_REMOVE_WITHOUT_VALUE)) $this->update($token->replace($this->content, ''));
		}

		return $this;
	}


	/**
	 * Returns an Array of the Tokens found in this Template.
	 */
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


	/**
	 * Returns the content of this Template.
	 */
	public function getContent(){ return $this->content; }

}