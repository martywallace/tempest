<?php namespace Tempest\Http;

use Exception;
use Tempest\App;
use Tempest\Utility;

/**
 * A response generated by the HTTP kernel.
 *
 * @author Marty Wallace
 */
class Response {

	/** @var string */
	private $_body = '';

	/** @var int */
	private $_status = Status::OK;

	/** @var array */
	private $_headers = [
		Header::CONTENT_TYPE => ContentType::TEXT_PLAIN
	];

	/**
	 * @return static
	 */
	public static function make() {
		return new static();
	}

	/**
	 * Sets the response body.
	 *
	 * @param string $value The response body.
	 *
	 * @return $this
	 */
	public function body($value) {
		$this->_body = $value;
		return $this;
	}

	/**
	 * Sets the response status.
	 *
	 * @param int $value The response status.
	 *
	 * @return $this
	 */
	public function status($value) {
		$this->_status = $value;
		return $this;
	}

	/**
	 * Create or overwrite a response header.
	 *
	 * @param string $header The header name.
	 * @param string $value THe header value.
	 *
	 * @return $this
	 */
	public function header($header, $value) {
		$this->_headers[Utility::kebab($header, true)] = $value;
		return $this;
	}

	/**
	 * Convenience method to set the Content-Type header.
	 *
	 * @param string $value The content-type to use.
	 *
	 * @return $this
	 */
	public function type($value) {
		return $this->header(Header::CONTENT_TYPE, $value);
	}

	/**
	 * Convenience method to return the result of {@link Twig::render() rendering a Twig template}.
	 *
	 * @param string $template The template to render.
	 * @param array $context Data to provide to the rendered template.
	 *
	 * @return $this
	 */
	public function render($template, array $context = []) {
		return $this->type(ContentType::TEXT_HTML)->body(App::get()->twig->render($template, $context));
	}

	/**
	 * Convenience method to send JSON encoded representation of an array or object.
	 *
	 * @param mixed $data The data to be encoded.
	 *
	 * @return $this
	 */
	public function json($data) {
		return $this->type(ContentType::APPLICATION_JSON)->body(json_encode($data));
	}

	/**
	 * Convenience method to set the Location header and a {@link Status::TEMPORARY_REDIRECT 307 Temporary Redirect} or
	 * {@link Status::PERMANENT_REDIRECT 308 Permanent Redirect} HTTP status.
	 *
	 * @param string $location The redirect location.
	 * @param bool $permanent Whether or not the redirect is permanent.
	 *
	 * @return $this
	 */
	public function redirect($location, $permanent = false) {
		return $this->status($permanent ? Status::PERMANENT_REDIRECT : Status::TEMPORARY_REDIRECT)
			->header(Header::LOCATION, $location);
	}

	/**
	 * Convenience method to set the Refresh header.
	 *
	 * @param string $location The location to redirect to after the provided time.
	 * @param int $seconds The amount of seconds to pass before the redirect.
	 *
	 * @return $this
	 */
	public function flash($location, $seconds = 5) {
		return $this->header(Header::REFRESH, $seconds . '; url=' . $location);
	}

	/**
	 * Convenience method to set the Content-Disposition header for file downloads.
	 *
	 * @param string $filename The download filename.
	 *
	 * @return $this
	 */
	public function download($filename) {
		return $this->header(Header::CONTENT_DISPOSITION, 'attachment; filename="' . $filename . '"');
	}

	/**
	 * Sets the response body to the content of a file, alongside the correct headers for that file type. If the file
	 * does not exist, a 404 response status is sent instead.
	 *
	 * @param string $path The path to the file to send.
	 *
	 * @return $this
	 */
	public function file($path) {
		if (file_exists($path)) {
			return $this->body(file_get_contents($path))->type(mime_content_type($path));
		} else {
			return $this->status(Status::NOT_FOUND);
		}
	}

	/**
	 * Returns the current response body.
	 *
	 * @return string
	 */
	public function getBody() {
		return $this->_body;
	}

	/**
	 * Returns the current response status.
	 *
	 * @return int
	 */
	public function getStatus() {
		return $this->_status;
	}

	/**
	 * Returns the current response headers.
	 *
	 * @return array
	 */
	public function getHeaders() {
		return $this->_headers;
	}

	/**
	 * Send the response and {@link App::terminate() terminate} the application.
	 *
	 * @throws Exception If output has already been sent, voiding the ability to set response headers.
	 */
	public function send() {
		header_remove(Header::X_POWERED_BY);

		http_response_code($this->_status);

		foreach ($this->_headers as $header => $value) {
			header($header . ': ' . $value);
		}

		echo $this->_body;

		App::get()->terminate();
	}

}