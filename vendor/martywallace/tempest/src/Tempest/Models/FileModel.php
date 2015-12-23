<?php namespace Tempest\Models;

use Exception;


/**
 * A model representing a file within the application.
 *
 * @property-read string $absolute The absolute path to the file.
 * @property-read string $relative The relative path to the file from the application root.
 *
 * @property-read int $size The filesize, in bytes.
 * @property-read string $extension The file extension.
 *
 * @package Tempest\Models
 * @author Marty Wallace
 */
class FileModel extends Model {

	/** @var string */
	private $_absolute;

	/** @var string */
	private $_relative;

	/**
	 * FileModel constructor.
	 *
	 * @param string $path The file path relative to the application root.
	 *
	 * @throws Exception If the path does not represent an existing file.
	 */
	public function __construct($path) {
		$this->_absolute = app()->filesystem->absolute($path);
		$this->_relative = $path;

		if (!app()->filesystem->exists($path)) {
			throw new Exception('File "' . $this->_absolute . '" does not exist and cannot be represented as a FileModel.');
		}
	}

	public function __get($prop) {
		if ($prop === 'absolute') return $this->_absolute;
		if ($prop === 'relative') return $this->_relative;

		if ($prop === 'extension') {
			return $this->memoize('extension', function() {
				return strtolower(pathinfo($this->_absolute, PATHINFO_EXTENSION));
			});
		}

		if ($prop === 'size') {
			return $this->memoize('size', function() {
				return filesize($this->_absolute);
			});
		}

		return null;
	}

	public function jsonSerialize() {
		return array(
			'path' => $this->_relative,
			'extension' => $this->extension,
			'size' => $this->size
		);
	}

}