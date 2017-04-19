<?php namespace Tempest\Models;

use Tempest\Tempest;


/**
 * A file contained within a request to the application (an uploaded file).
 *
 * @property-read string $name The file name provided by the client.
 * @property-read string $type The file type.
 * @property-read int $size The filesize.
 * @property-read string $temp The temporary path to the file.
 * @property-read string $extension The file extension, based on the file name provided by the client.
 *
 * @package Tempest\Models
 * @author Marty Wallace
 */
class UploadedFileModel extends Model {

	/** @var array */
	private $_detail;

	public function __construct(array $detail) {
		$this->_detail = $detail;
	}

	public function __get($prop) {
		if ($prop === 'name') return $this->_getDetail('name');
		if ($prop === 'type') return $this->_getDetail('type');
		if ($prop === 'size') return $this->_getDetail('size');
		if ($prop === 'temp') return $this->_getDetail('tmp_name');

		if ($prop === 'extension') {
			return Tempest::get()->memoization->cache(static::class, 'extension', strtolower(pathinfo($this->name, PATHINFO_EXTENSION)));
		}

		return null;
	}

	public function jsonSerialize() {
		return [
			'name' => $this->name,
			'extension' => $this->extension,
			'type' => $this->type,
			'size' => $this->size,
			'temp' => $this->temp
		];
	}

	/**
	 * Save the uploaded file into the application directory.
	 *
	 * @param string $path The save destination relative to the application root.
	 *
	 * @return bool Whether or not the save was successful.
	 */
	public function save($path) {
		return move_uploaded_file($this->temp, Tempest::get()->filesystem->absolute($path));
	}

	private function _getDetail($name, $fallback = null) {
		return array_key_exists($name, $this->_detail) ? $this->_detail[$name] : $fallback;
	}

}