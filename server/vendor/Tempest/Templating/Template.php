<?php namespace Tempest\Templating;

use Tempest\Base\Tempest;
use Tempest\Routing\Output;
use Tempest\Templating\Token;
use Tempest\Templating\TokenPart;


/**
 * Templates manage strings containing tokens which can have values bound to them. Those tokens are
 * then replaced with the bound value for final output.
 * @author Marty Wallace.
 */
class Template extends Output
{

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
	 * @param $base The base content to prepare.
	 */
	public static function prepare($base)
	{
		return str_replace("~/", PUB_ROOT, $base);
	}


	/**
	 * Constructor.
	 * @param $content The starting Template content.
	 */
	public function __construct($content = '')
	{
		$this->setMime('text/html');
		$this->setContent($content);
	}


	/**
	 * Returns the final output data for this Template. Binds application data.
	 * @param $app A reference to the core application instance.
	 */
	public function getFinalOutput(Tempest $app)
	{
		$request = $app->getRouter()->getRequest();
		$reqData = array_merge($request->data(), array("uri" => array("base" => $request->getBase(), "chunks" => $request->getChunks())));

		return $this->bind(array(
			"T_REQUEST_DATA" => base64_encode(json_encode($reqData, JSON_NUMERIC_CHECK)),
			"T_SITE_TITLE" => $app->getConfig()->data("title")

		))->finalize();
	}


	/**
	 * Assigns new content to this Template.
	 * @param $content The new content to allocate to this Template.
	 */
	public function setContent($content = '')
	{
		parent::setContent(self::prepare($content));
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

			$this->setContent($token->replace($this->getContent(), $value));
		}

		return $this;
	}


	/**
	 * Binds multiple Array elements to this Template, repeating the original Template content for
	 * each item in the batch.
	 * @param $batch The Array whose children will be bound to this Template.
	 * @param $empty The content to use if the supplied batch is empty.
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
			if($token->hasPrefix(Token::B_REMOVE_WITHOUT_VALUE))
				$this->setContent($token->replace($this->getContent(), ''));
		}

		return $this;
	}


	/**
	 * Returns an Array of the Tokens found in this Template.
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