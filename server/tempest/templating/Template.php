<?php

namespace tempest\templating;


class Template
{

	private $content = '';


	public function __construct($file = null)
	{
		//
	}


	public function setContent($content)
	{
		$this->content = $content;
	}


	public function getContent(){ return $this->content; }

}