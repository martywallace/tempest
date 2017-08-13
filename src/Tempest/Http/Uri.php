<?php namespace Tempest\Http;

/**
 * An object holding a URI.
 *
 * @property-read string $uri The URI this object holds.
 *
 * @author Marty Wallace
 */
class Uri {

	/** @var string */
	private $_uri = '';

	/**
	 * Uri constructor.
	 *
	 * @param string $uri
	 */
	public function __construct($uri) {
		$this->_uri = $uri;
	}

	public function __get($prop) {
		if ($prop === 'uri') return $this->_uri;

		return null;
	}

	/**
	 * Prepend a value to the URI.
	 *
	 * @param string $value THe value to prepend.
	 *
	 * @return $this
	 */
	public function prepend($value) {
		$this->_uri = '/' . trim(trim($value, '/\\') . $this->_uri, '/\\');

		return $this;
	}

}