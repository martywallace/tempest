<?php namespace Tempest\Http;

use JsonSerializable;
use Tempest\Utils\JSONUtil;

/**
 * A response sent to the client after a request is made to the application.
 *
 * @property int $status The response status.
 * @property string $contentType The response content type.
 * @property string $body The response body.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
final class Response {

	/** @var int */
	private $_status = Status::OK;

	/** @var string */
	private $_contentType;

	/** @var string */
	private $_body;

	/**
	 * Constructor.
	 *
	 * @param int $status The response status.
	 * @param string $body The response body.
	 */
	public function __construct($status = 200, $body = null) {
		$this->status = $status;
		$this->contentType = array('text/html', 'charset' => 'utf-8');
		$this->body = $body;
	}

	public function __get($prop) {
		if ($prop === 'status') return $this->_status;
		if ($prop === 'contentType') return $this->_contentType;
		if ($prop === 'body') return $this->_body;

		return null;
	}

	public function __set($prop, $value) {
		if ($prop === 'status') {
			if (function_exists('http_response_code')) {
				http_response_code($value);
			} else {
				header('X-PHP-Response-Code: ' . $value, true, $value);
			}

			$this->_status = $value;
		}

		if ($prop === 'contentType') {
			$this->_contentType = $value;
			$this->header('Content-Type', $value);
		}

		if ($prop === 'body') $this->_body = $value;
	}

	public function __isset($prop) {
		return property_exists($this, $prop) ||
			$this->{$prop} !== null;
	}

	/**
	 * Add a response header.
	 *
	 * @param string $name The header name.
	 * @param string|string[] $values The header values.
	 */
	public function header($name, $values) {
		if (!is_array($values)) {
			$values = array($values);
		}

		$value = array();

		foreach ($values as $key => $val) {
			if (is_numeric($key)) $value[] = $val;
			else $value[] = $key . '=' . $val;
		}

		header($name . ': ' . implode('; ', $value));
	}

	/**
	 * Send the response as a file download.
	 *
	 * @param string $filename The filename to use for the downloaded data.
	 */
	public function isDownload($filename) {
		if (!empty($filename)) {
			$this->header('Content-Disposition', array('attachment', 'filename' => $filename));
		}
	}

	/**
	 * Flash the response for a given amount of time before redirecting to a new location.
	 *
	 * @param int $seconds The amount of seconds to wait before redirection.
	 * @param string $location The target location.
	 */
	public function flash($seconds, $location) {
		if (intval($seconds) >= 0 && !empty($location)) {
			$this->header('Refresh', array($seconds, 'url' => $location));
		}
	}

	/**
	 * Redirect the response.
	 *
	 * @param string $location The redirect destination.
	 * @param bool $permanent Whether the redirection should be treated as permanent (adds a 302 status code).
	 */
	public function redirect($location, $permanent = false) {
		$this->status = $permanent ? Status::MOVED_PERMANENTLY : Status::FOUND;
		$this->header('Location', $location);
	}

	/**
	 * Send the response back to the client.
	 */
	public function send() {
		if (is_array($this->_body) || $this->_body instanceof JsonSerializable) {
			// Convert the response to JSON.
			$this->contentType = array('application/json', 'charset' => 'utf-8');
			$this->_body = JSONUtil::encode($this->_body);
		}

		echo $this->_body;

		// Stop doing things once the response was sent.
		exit;
	}

}