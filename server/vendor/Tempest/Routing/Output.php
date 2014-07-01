<?php namespace Tempest\Routing;


/**
 * Represents output sent by a Response.
 * @author Marty Wallace.
 */
class Output
{
	
	private $content = '';
	private $mime = MIME_TEXT;


	/**
	 * Provides a string representation of this Output.
	 */
	public function __toString()
	{
		return $this->getContent();
	}


	/**
	 * Returns the content for this Output.
	 */
	public function getContent(){ return $this->content; }


	/**
	 * Assigns new content to this Output.
	 */
	public function setContent($value = ''){ $this->content = $value; }


	/**
	 * Returns the MIME type associated with this Output.
	 */
	public function getMime(){ return $this->mime; }


	/**
	 * Assigns a new MIME type to this Output.
	 */
	public function setMime($value){ $this->mime = $value; }

}