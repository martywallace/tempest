<?php namespace Tempest\Http;

use Tempest\Utility;

/**
 * A HTTP message.
 *
 * @author Ascension Web Development
 */
abstract class Message {

	/** @var Header[] */
	private $_headers = [];

	/** @var string */
	private $_body = '';

	/**
	 * Get all headers attached to this message.
	 *
	 * @return Header[]
	 */
	public function getHeaders() {
		return $this->_headers;
	}

	/**
	 * Mass assign headers.
	 *
	 * @param array $headers All headers to set.
	 *
	 * @return $this
	 */
	public function setHeaders(array $headers) {
		foreach ($headers as $header => $value) {
			$this->setHeader($header, $value);
		}

		return $this;
	}

	/**
	 * Determine whether this message contains a header.
	 *
	 * @param string $header The header to check for.
	 *
	 * @return bool
	 */
	public function hasHeader($header) {
		return array_key_exists(Utility::kebab($header, true), $this->_headers);
	}

	/**
	 * Get a header.
	 *
	 * @param string $header The header name to get or set.
	 * @param string $fallback A fallback value to provide if the header was not set. If the fallback is non-empty, it
	 * is used as the value of a newly created {@link Header header} instance.
	 *
	 * @return Header
	 */
	public function getHeader($header, $fallback = null) {
		$header = Utility::kebab($header, true);

		return $this->hasHeader($header)
			? $this->_headers[$header]
			: ($fallback === null ? null : new Header($header, $fallback));
	}

	/**
	 * Set a header.
	 *
	 * @param string $header The header to set.
	 * @param string $value The header value.
	 *
	 * @return $this
	 */
	public function setHeader($header, $value) {
		$this->_headers[Utility::kebab($header, true)] = new Header($header, $value);
		return $this;
	}

	/**
	 * Get the current body.
	 *
	 * @return string
	 */
	public function getBody() {
		return $this->_body;
	}

	/**
	 * Set the body.
	 *
	 * @param string $value The value to set the body to.
	 *
	 * @return $this
	 */
	public function setBody($value) {
		$this->_body = $value;
		return $this;
	}

}