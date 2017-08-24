<?php namespace Tempest\Http;

/**
 * A HTTP message.
 *
 * @author Marty Wallace
 */
interface Message {

	/**
	 * Get the headers attached to this message.
	 *
	 * @return string[]
	 */
	function getHeaders();

	/**
	 * Get the raw body attached to this message.
	 *
	 * @return string
	 */
	function getBody();

}