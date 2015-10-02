<?php namespace Tempest\Http;

use JsonSerializable;

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
class Response {

	/** @var int */
	private $_status = 200;

	/** @var string */
	private $_contentType = ContentType::HTML;

	/** @var string */
	private $_body = null;

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
			header('Content-Type: ' . $value);
		}

		if ($prop === 'body') $this->_body = $value;
	}

	public function send() {
		if (is_array($this->_body) || $this->_body instanceof JsonSerializable) {
			// Convert the response to JSON.
			$this->contentType = ContentType::JSON;
			$this->_body = json_encode($this->_body);
		}

		echo $this->_body;

		// Stop doing things once the response was sent.
		exit;
	}

}