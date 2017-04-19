<?php namespace Tempest\Services;

use Exception;
use Tempest\Tempest;
use Tempest\Models\FileModel;
use Tempest\Utils\Memoizer;


/**
 * Deals with the filesystem relative to the application path.
 *
 * @see FileModel
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class FilesystemService extends Memoizer implements Service {

	/**
	 * Creates an absolute filesystem link based on the application root.
	 *
	 * @param string $relative The relative path within the application directory.
	 *
	 * @return string
	 */
	public function absolute($relative) {
		return Tempest::get()->root . '/' . ltrim($relative, '/');
	}

	/**
	 * Creates a file relative to the application root and returns a FileModel representing that file.
	 *
	 * @param string $path The file path relative to the application root.
	 * @param string $contents Content to save in the newly created file.
	 *
	 * @return FileModel
	 *
	 * @throws Exception If a file already exists at the target path.
	 */
	public function create($path, $contents = null) {
		if (!$this->exists($path)) {
			$stream = fopen($this->absolute($path), 'w');

			if (!empty($contents)) {
				fwrite($stream, $contents);
			}

			fclose($stream);

			return new FileModel($path);
		} else {
			throw new Exception('A file already exists at "' . $path . '".');
		}
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
	 * Finds an existing file or creates a new one.
	 *
	 * @param string $path The file path relative to the application root.
	 * @return FileModel
	 */
	public function findOrCreate($path) {
		if ($this->exists($path)) return $this->find($path);
		else return $this->create($path);
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
	 * Deletes a file from the application.
	 *
	 * @param string $path The file path relative to the application root.
	 */
	public function delete($path) {
		if ($this->exists($path)) {
			unlink($this->absolute($path));
		}
	}

	/**
	 * Appends some data to an existing file.
	 *
	 * @param string $path The file path relative to the application root.
	 * @param string $contents The data to append.
	 *
	 * @throws Exception If the path does not represent an existing file.
	 */
	public function append($path, $contents) {
		if ($this->exists($path)) {
			$stream = fopen($this->absolute($path), 'a');
			fwrite($stream, $contents);
			fclose($stream);
		} else {
			throw new Exception('Cannot append contents to nonexistent file "' . $path . '".');
		}
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