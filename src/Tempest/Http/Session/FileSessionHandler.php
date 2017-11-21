<?php namespace Tempest\Http\Session;

use Exception;
use Tempest\App;

/**
 * Manages session data in the filesystem.
 *
 * @author Marty Wallace
 */
class FileSessionHandler extends BaseSessionHandler {

	/** @var string */
	private $_storage;

	/**
	 * FileSessionHandler constructor.
	 *
	 * @param string $storage The storage location for generated sessions. Falls back to the default storage directory
	 * if not provided.
	 */
	public function __construct($storage = null) {
		if (!empty($storage)) $this->_storage = rtrim($storage, '/\\');
		else $this->_storage = App::get()->storage . DIRECTORY_SEPARATOR . 'sessions';
	}

	public function destroy($id) {
		if (file_exists($this->filename($id))) {
			unlink($this->filename($id));
		}

		return true;
	}

	public function gc($lifetime) {
		foreach (scandir($this->_storage) as $file) {
			if (filemtime($file) + $lifetime < time() && file_exists($file)) {
				unlink($file);
			}
		}

		return true;
	}

	public function open($path, $name) {
		if (!is_dir($this->_storage)) {
			throw new Exception('Session storage directory "' . $this->_storage . '" does not exist.');
		}

		if (!is_writable($this->_storage)) {
			throw new Exception('Session storage directory "' . $this->_storage . '" is not writable.');
		}

		return true;
	}

	public function read($id) {
		if (file_exists($this->filename($id))) return file_get_contents($this->filename($id));
		return '';
	}

	public function write($id, $data) {
		return file_put_contents($this->filename($id), $data) === false ? false : true;
	}

	/**
	 * Generate the session filename from the session ID.
	 *
	 * @param string $id The session ID.
	 *
	 * @return string
	 */
	protected function filename($id) {
		return $this->_storage . DIRECTORY_SEPARATOR . $id;
	}

}