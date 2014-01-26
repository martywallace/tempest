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
		return $this->content;
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
			$find = $token->getMatch();
			$prop = $token->getBody();

			if(property_exists($data, $prop))
			{
				$output->setContent(str_replace($find, $data->$prop, $output->getContent()));
			}
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


	public function getContent(){ return $this->content; }

}