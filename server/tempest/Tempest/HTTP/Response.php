<?php namespace Tempest\HTTP;

use Tempest\HTTP\Request;


/**
 * Represents a response sent by the application to the client.
 * @author Marty Wallace.
 */
class Response
{
	
	private $mime = 'text/plain';
	private $charset = 'utf-8';
	private $content = null;


	/**
	 * Constructor.
	 *
	 * @param string $mime The output MIME type.
	 * @param string $content The starting content.
	 */
	public function __construct($mime = 'text/plain', $content = null)
	{
		$this->setMime($mime)->setContent($content);
	}


	/**
	 * Provides a string representation of this Response.
	 *
	 * @return string The output string.
	 */
	public function __toString()
	{
		return $this->getContent();
	}


	/**
	 * Obtains the final output string for this instance. This is triggered by the application core.
	 *
	 * @param Request $request The request to the application.
	 *
	 * @return string The final string output.
	 */
	public function getFinalOutput(Request $request)
	{
		// Defaults to the existing content.
		return $this->getContent();
	}


	/**
	 * Returns the content for this Response.
	 *
	 * @return string
	 */
	public function getContent(){ return $this->content; }


	/**
	 * Returns the MIME type associated with this Response.
	 *
	 * @return string
	 */
	public function getMime(){ return $this->mime; }


	/**
	 * Returns the charset for this Response.
	 *
	 * @return string
	 */
	public function getCharset(){ return $this->charset; }


	/**
	 * Assigns new content to this Response.
	 *
	 * @param string $value New content.
	 *
	 * @return Response The calling instance, for chaining.
	 */
	public function setContent($value = '')
	{
		$this->content = $value;
		return $this;
	}


	/**
	 * Assigns a new MIME type to this Response.
	 *
	 * @param string $value The new MIME type.
	 *
	 * @return Response The calling instance, for chaining.
	 */
	public function setMime($value)
	{
		$this->mime = $value;
		return $this;
	}


	/**
	 * Sets the charset for this Response.
	 *
	 * @param string $value The new charset value.
	 *
	 * @return Response The calling instance, for chaining.
	 */
	public function setCharset($value)
	{
		$this->charset = $value;
		return $this;
	}


	/**
	 * Determine whether this Response is of the specified MIME-type.
	 *
	 * @param string $value The value to compare.
	 *
	 * @return bool
	 */
	public function isMime($value)
	{
		return $this->mime === $value;
	}

}