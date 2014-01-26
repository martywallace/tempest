<?php

namespace tempest\templating;

use \tempest\templating\Manager;
use \tempest\templating\Token;


class Template
{

	private $content;
	private $tokens;


	public function __construct($file = null)
	{
		if($file !== null) $this->load($file);
	}


	public function __toString()
	{
		return $this->getContent();
	}


	public function load($file)
	{
		$this->content = Manager::load($file);
		$this->findTokens();
	}


	public function setContent($content)
	{
		$this->content = $content;
		$this->findTokens();
	}


	/**
	 * Updates the Template content by inserting provided data where marked, and returns the resulting Template.
	 * @param $data The data to insert into the template. The data can either be an array or object.
	 * @param $morph Whether this template should be updated as well. Use false if you need to recycle the template for multiple objects e.g. in a loop.
	 */
	public function update($data, $morph = true)
	{
		$output = new Template();
		$output->setContent($this->getContent());

		if(is_array($data))
		{
			// Convert input array to an object.
			$data = json_decode(json_encode($data), false);
		}
		
		// Insert data.
		foreach($this->tokens as $token)
		{
			$body = $token->getBody();
			$value = null;

			if($token->getType() === Token::TYPE_RECURSIVE)
			{
				$base = $data;
				foreach($body as $next)
				{
					if(is_array($base))
					{
						if(array_key_exists($next, $base)) $base = $base[$next];
						else continue 2;
					}

					if(is_object($base))
					{
						$type = $token->getPartType($next);
						if($type === Token::TYPE_PROPERTY)
						{
							if(property_exists($base, $next)) $base = $base->$next;
							else continue 2;
						}

						if($type === Token::TYPE_METHOD)
						{
							$next = trim($next, '()');

							if(method_exists($base, $next)) $base = call_user_method($next, $base);
							else continue 2;
						}
					}

					else
					{
						// Source object cannot contain properties.
						continue 2;
					}
				}

				$value = $base;
			}

			else if($token->getType() === Token::TYPE_PROPERTY && property_exists($data, $body)) $value = $data->$body;
			else if($token->getType() === Token::TYPE_METHOD && method_exists($data, $body)) $value = call_user_method($body, $data);
			else continue;

			if($value === null) $value = 'null';
			if($value === false) $value = 'false';
			if($value === true) $value = 'true';
			if(is_array($value)) $value = 'array';
			if(is_object($value)) $value = 'object';

			$output->setContent(str_replace($token->getMatch(), $value, $output->getContent()));
		}

		if($morph)
		{
			// Update this template if morphing.
			$this->setContent($output->getContent());
		}


		return $output;
	}


	private function findTokens()
	{
		$this->tokens = array();

		preg_match_all(Token::PATTERN, $this->content, $matches);
		for($i = 0; $i < count($matches[0]); $i++)
		{
			$token = new Token($matches, $i);
			$this->tokens[] = $token;
		}
	}


	public function getContent(){ return $this->content === null ? '' : $this->content; }

}