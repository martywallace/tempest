<?php namespace Tempest\Services;

use Exception;
use Tempest\Models\FileModel;


/**
 * Deals with the filesystem relative to the application path.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class FilesystemService extends Service {

	protected function setup() {
		// ...
	}

	/**
	 * Finds a file relative to the application root and returns a FileModel representing it.
	 *
	 * @param string $path The file path relative to the application root.
	 *
	 * @return FileModel
	 */
	public function find($path) {
		return $this->memoize('_file_' . $path, function() use ($path) {
			return new FileModel($path);
		});
	}

	/**
	 * Creates an absolute filesystem link based on the application root.
	 *
	 * @param string $relative The relative path within the application directory.
	 *
	 * @return string
	 */
	public function absolute($relative) {
		return app()->root . '/' . ltrim($relative, '/');
	}

	/**
	 * Determine whether a file or directory exists.
	 *
	 * @param string $relative The relative path within the application directory.
	 *
	 * @return bool
	 */
	public function exists($relative) {
		return file_exists($this->absolute($relative));
	}

	/**
	 * Alias for require().
	 *
	 * @param string $relative The relative path within the application directory.
	 *
	 * @return mixed
	 *
	 * @throws Exception If the file to import does not exist.
	 */
	public function import($relative) {
		if ($this->exists($relative)) {
			/** @noinspection PhpIncludeInspection */
			return require($this->absolute($relative));
		} else {
			throw new Exception('Could not import "' . $relative . '" - it does not exist.');
		}
	}

}