<?php namespace Tempest\Templating;

use Tempest\Base\Tempest;
use Tempest\Base\Config;
use Tempest\HTTP\Request;
use Tempest\Output\HTMLOutput;


/**
 * Templates manage strings containing tokens which can have values bound to them. Those tokens are
 * then replaced with the bound value for final output.
 * @author Marty Wallace.
 */
class Template extends HTMLOutput
{

	/**
	 * Loads a new Template whose contents is the value of a specified file.
	 * @param $file string The file to load, relative to /client/.
	 * @return Template A new Template.
	 */
	public static function load($file)
	{
		$path = APP_ROOT . 'client' . SEP . path_normalize($file, SEP, false, false);
		return new static(file_get_contents($path));
	}


	/**
	 * An alias of Template::__construct(), for convenience.
	 * @param string $content The starting Template content.
	 * @return Template A new Template.
	 */
	public static function create($content = '')
	{
		return new static($content);
	}


	/**
	 * Prepares template content with core replacements and adjustments.
	 * @param $base string The base content to prepare.
	 * @return string The prepared content.
	 */
	public static function prepare($base)
	{
		return str_replace("~/", PUB_ROOT, $base);
	}


	/**
	 * Returns the final output data for this Template. Binds application data.
	 * @param $app Tempest A reference to the core application instance.
	 * @param $request Request The request to the application.
	 * @return string The final output for this Template.
	 */
	public function getFinalOutput(Tempest $app, Request $request)
	{
		$reqData = array_merge($request->data(), array(
			"base" => PUB_ROOT,
			"uri" => array("base" => $request->getBase(), "chunks" => $request->getChunks())
		));

		return $this->bind(array(
			"T_REQUEST_DATA" => json_encode($reqData, JSON_NUMERIC_CHECK),
			"T_SITE_TITLE" => Config::data("title")

		))->finalize();
	}


	/**
	 * Assigns new content to this Template.
	 * @param $content string The new content to allocate to this Template.
	 * @return Template The calling Template, for chaining.
	 */
	public function setContent($content = '')
	{
		parent::setContent(self::prepare($content));
		return $this;
	}


	/**
	 * Binds data to this Template.
	 * @param $data mixed The data to bind. Can be either an Array or class instance (object).
	 * @param $context string An optional context. Only tokens with the matching context will be replaced.
	 * @return Template The calling Template, for chaining.
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

			$this->setContent($token->replace($this->getContent(), $value));
		}

		return $this;
	}


	/**
	 * Binds multiple Array elements to this Template, repeating the original Template content for
	 * each item in the batch.
	 * @param $batch Array The Array whose children will be bound to this Template.
	 * @param $empty Template The Template to use if the supplied batch is empty.
	 * @return Template The calling Template, for chaining.
	 */
	public function batch(Array $batch, Template $empty = null)
	{
		if(count($batch) === 0)
		{
			$this->setContent($empty === null ? '' : $empty);
			return $this;
		}

		$result = array();
		foreach($batch as $key => $item)
		{
			if(is_array($item) || is_object($item))
				$result[] = $this->copy()->bind($item);
			else
			{
				// Bind $key to {{ key }} and $item to {{ value }}.
				$result[] = $this->copy()->bind(array(
					"key" => $key,
					"value" => $item
				));
			}
		}

		$this->setContent(implode($result));

		return $this;
	}


	/**
	 * Makes a copy of this Template, by taking the current content and using it as the starting content
	 * for a new Template instance.
	 * @return Template A copy of the calling Template.
	 */
	public function copy()
	{
		return new Template($this->getContent());
	}


	/**
	 * Finalize the Template content. Used automatically if the Template is the output of a Response.
	 * @return Template The calling Template, for chaining.
	 */
	public function finalize()
	{
		foreach($this->getTokens() as $token)
		{
			if($token->hasPrefix(Token::B_REMOVE_WITHOUT_VALUE))
				$this->setContent($token->replace($this->getContent(), ''));
		}

		return $this;
	}


	/**
	 * Returns an Array of the Tokens found in this Template.
	 * @return Array The list of matched tokens.
	 */
	private function getTokens()
	{
		$tokens = array();
		preg_match_all(RGX_TEMPLATE_TOKEN, $this->getContent(), $matches);
		
		for($i = 0; $i < count($matches[0]); $i++)
		{
			$tokens[] = new Token($matches, $i);
		}

		return $tokens;
	}

}