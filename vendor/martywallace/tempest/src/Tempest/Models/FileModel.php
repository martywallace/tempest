<?php namespace Tempest\Models;

use Exception;


/**
 * A model representing a file within the application.
 *
 * @property-read string $absolute The absolute path to the file.
 * @property-read string $relative The relative path to the file from the application root.
 * @property-read int $size The filesize, in bytes.
 * @property-read string $contents The file contents.
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

		if ($prop === 'size') {
			return $this->memoize('size', function() {
				return filesize($this->_absolute);
			});
		}

		if ($prop === 'contents') {
			return $this->memoize('contents', function() {
				return file_get_contents($this->_absolute);
			});
		}

		return null;
	}

	public function jsonSerialize() {
		return array(
			'path' => $this->_relative,
			'size' => $this->size
		);
	}

	/**
	 * Append data to this file.
	 *
	 * @param string $contents The data to append.
	 */
	public function append($contents) {
		$this->unmemoize('contents');
		app()->filesystem->append($this->_relative, $contents);
	}

	/**
	 * Delete this file from the filesystem.
	 */
	public function delete() {
		app()->filesystem->delete($this->_relative);
	}

}