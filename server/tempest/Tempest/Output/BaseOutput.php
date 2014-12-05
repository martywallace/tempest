<?php namespace Tempest\Output;

use Tempest\Tempest;
use Tempest\HTTP\Request;


/**
 * Represents output sent by the application to the client.
 * @author Marty Wallace.
 */
class BaseOutput
{
	
	private $mime = 'text/plain';
	private $charset = 'utf-8';
	private $content = null;


	/**
	 * Constructor.
	 * @param $mime string The output MIME type.
	 * @param $content string The starting content.
	 */
	public function __construct($mime = 'text/plain', $content = null)
	{
		$this->setMime($mime)->setContent($content);
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
	 * @param $request Request The request to the application.
	 * @return string The final string output.
	 */
	public function getFinalOutput(Tempest $app, Request $request)
	{
		// Defaults to the existing content.
		return $this->getContent();
	}


	/**
	 * Returns the content for this Output.
	 * @return string
	 */
	public function getContent(){ return $this->content; }


	/**
	 * Returns the MIME type associated with this Output.
	 * @return string
	 */
	public function getMime(){ return $this->mime; }


	/**
	 * Returns the charset for this output.
	 * @return string
	 */
	public function getCharset(){ return $this->charset; }


	/**
	 * Assigns new content to this Output.
	 * @param $value string New content.
	 * @return BaseOutput The calling instance, for chaining.
	 */
	public function setContent($value = '')
	{
		$this->content = $value;
		return $this;
	}


	/**
	 * Assigns a new MIME type to this Output.
	 * @param $value string The new MIME type.
	 * @return BaseOutput The calling instance, for chaining.
	 */
	public function setMime($value)
	{
		$this->mime = $value;
		return $this;
	}


	/**
	 * Sets the charset for this output.
	 * @param $value The new charset value.
	 * @return BaseOutput The calling instance, for chaining.
	 */
	public function setCharset($value)
	{
		$this->charset = $value;
		return $this;
	}


	/**
	 * Determine whether this output is of the specified MIME-type.
	 * @param $value string The value to compare.
	 * @return bool
	 */
	public function isMime($value)
	{
		return $this->mime === $value;
	}

}