<?php namespace Tempest\HTTP;

use Tempest\Base\Tempest;


/**
 * Represents output sent by a Response.
 * @author Marty Wallace.
 */
class Output
{
	
	private $mime = 'text/plain';
	private $content = '';


	/**
	 * Constructor.
	 * @param $mime string The output MIME type.
	 * @param $content string The starting content.
	 */
	public function __construct($mime = 'text/plain', $content = '')
	{
		$this->mime = $mime;
		$this->content = $content;
	}


	/**
	 * Provides a string representation of this Output.
	 * @return string The output string.
	 */
	public function __toString()
	{
		return $this->getContent();
	}


	/**
	 * Obtains the final output string for this instance. This is triggered by the application core.
	 * @param $app Tempest A reference to the core application instance.
	 * @return string The final string output.
	 */
	public function getFinalOutput(Tempest $app)
	{
		return $this->getContent();
	}


	/**
	 * Returns the content for this Output.
	 * @return string
	 */
	public function getContent(){ return $this->content; }


	/**
	 * Assigns new content to this Output.
	 * @param $value string New content.
	 * @return Output The calling Output, for chaining.
	 */
	public function setContent($value = '')
	{
		$this->content = $value;
		return $this;
	}


	/**
	 * Returns the MIME type associated with this Output.
	 * @return string
	 */
	public function getMime(){ return $this->mime; }


	/**
	 * Assigns a new MIME type to this Output.
	 * @param $value string The new MIME type.
	 * @return Output The calling Output
	 */
	public function setMime($value)
	{
		$this->mime = $value;
		return $this;
	}

}