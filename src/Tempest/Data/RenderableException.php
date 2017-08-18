<?php namespace Tempest\Data;

use Exception;
use ReflectionClass;
use Tempest\App;

/**
 * An exception wrapper that provides cleaner data for rendering.
 *
 * @property-read string $message The exception message.
 * @property-read string $file The file throwing the exception.
 * @property-read int $line The line within the file throwing the exception.
 * @property-read array[] $trace The stack trace for the exception.
 *
 * @author Marty Wallace
 */
class RenderableException {

	/** @var Exception */
	private $_exception;

	public function __construct(Exception $exception) {
		$this->_exception = $exception;
	}

	public function __get($prop) {
		if ($prop === 'message') return $this->_exception->getMessage();
		if ($prop === 'line') return $this->_exception->getLine();
		if ($prop === 'file') return $this->cleanupFilename($this->_exception->getFile());

		if ($prop === 'trace') {
			return array_map(function(array $trace) {
				$reflection = new ReflectionClass($trace['class']);

				return [
					'class' => $trace['class'],
					'short' => $reflection->getShortName(),
					'file' => $this->cleanupFilename($trace['file']),
					'line' => $trace['line'],
					'type' => $trace['type'],
					'function' => $trace['function']
				];
			}, $this->_exception->getTrace());
		}

		return null;
	}

	public function __isset($prop) {
		return $this->{$prop} !== null;
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	protected function cleanupFilename($value) {
		return preg_replace('/\\\+/', '/', trim(str_replace(App::get()->root, '', $value), '/\\'));
	}

}