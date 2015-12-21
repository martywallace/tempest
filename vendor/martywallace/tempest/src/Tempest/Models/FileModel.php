<?php namespace Tempest\Models;

use Exception;


/**
 * A model representing a file within the application.
 *
 * @property-read string $path The full file path relative to the application root.
 * @property-read int $size The filesize, in bytes.
 * @property-read string $contents The file contents.
 *
 * @package Tempest\Models
 * @author Marty Wallace
 */
class FileModel extends Model {

	/** @var string */
	private $_path;

	public function __construct($path) {
		$this->_path = app()->root . '/' . ltrim($path, '/');

		if (!is_file($this->_path)) {
			throw new Exception('File "' . $this->_path . '" does not exist and cannot be represented as a FileModel.');
		}
	}

	public function __get($prop) {
		if ($prop === 'path') return $this->_path;

		if ($prop === 'size') {
			return $this->memoize('size', function() {
				return filesize($this->_path);
			});
		}

		if ($prop === 'contents') {
			return $this->memoize('contents', function() {
				return file_get_contents($this->_path);
			});
		}

		return null;
	}

	public function jsonSerialize() {
		return array(
			'path' => $this->path,
			'size' => $this->size
		);
	}

}