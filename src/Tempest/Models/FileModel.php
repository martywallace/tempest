<?php namespace Tempest\Models;

use Exception;
use Tempest\Tempest;


/**
 * A model representing a file within the application.
 *
 * @property-read string $absolute The absolute path to the file.
 * @property-read string $relative The relative path to the file from the application root.
 *
 * @property-read int $size The filesize, in bytes.
 * @property-read string $extension The file extension.
 * @property-read int $updated Unix timestamp representing last modification time.
 * @property-read string $content The file contents.
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
		$this->_absolute = Tempest::get()->filesystem->absolute($path);
		$this->_relative = $path;

		if (!Tempest::get()->filesystem->exists($path)) {
			throw new Exception('File "' . $this->_absolute . '" does not exist and cannot be represented as a FileModel.');
		}
	}

	public function __get($prop) {
		if ($prop === 'absolute') return $this->_absolute;
		if ($prop === 'relative') return $this->_relative;

		if ($prop === 'extension') return Tempest::get()->memoization->cache(static::class, 'extension', strtolower(pathinfo($this->_absolute, PATHINFO_EXTENSION)));
		if ($prop === 'size') return Tempest::get()->memoization->cache(static::class, 'size', filesize($this->_absolute));
		if ($prop === 'updated') return filemtime($this->_absolute);
		if ($prop === 'content') return Tempest::get()->memoization->cache(static::class, '__content', file_get_contents($this->_absolute));

		return null;
	}

	public function jsonSerialize() {
		return [
			'path' => $this->_relative,
			'extension' => $this->extension,
			'size' => $this->size
		];
	}

}