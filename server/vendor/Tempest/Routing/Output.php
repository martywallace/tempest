<?php namespace Tempest\Routing;

use Tempest\Base\Tempest;


/**
 * Represents output sent by a Response.
 * @author Marty Wallace.
 */
class Output
{
	
	private $content = '';
	private $mime = 'text/plain';


	/**
	 * Provides a string representation of this Output.
	 */
	public function __toString()
	{
		return $this->getContent();
	}


	/**
	 * Obtains the final output string for this instance. This is triggered by the application core.
	 * @param $app A reference to the core application instance.
	 */
	public function getFinalOutput(Tempest $app)
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
	public function setContent($value = '')
	{
		$this->content = $value;
		return $this;
	}


	/**
	 * Returns the MIME type associated with this Output.
	 */
	public function getMime(){ return $this->mime; }


	/**
	 * Assigns a new MIME type to this Output.
	 */
	public function setMime($value)
	{
		$this->mime = $value;
		return $this;
	}

}