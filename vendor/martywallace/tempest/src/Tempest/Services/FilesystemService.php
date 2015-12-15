<?php namespace Tempest\Services;

use Exception;


/**
 * Deals with the filesystem relative to the application path.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class FilesystemService extends Service {

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
	 * Alias for <code>require()</code>.
	 *
	 * @param string $relative The relative path within the application directory.
	 *
	 * @return mixed
	 *
	 * @throws Exception If the file to import does not exist.
	 */
	public function import($relative) {
		if ($this->exists($relative)) {
			return require($this->absolute($relative));
		} else {
			throw new Exception('Could not import "' . $relative . '" - it does not exist.');
		}
	}

}