<?php namespace Tempest\Services;

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
	 * @param string $relative The file or directory path relative to the application root.
	 *
	 * @return bool
	 */
	public function exists($relative) {
		return file_exists($this->absolute($relative));
	}

}