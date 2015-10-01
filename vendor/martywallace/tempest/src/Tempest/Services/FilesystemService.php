<?php namespace Tempest\Services;

class FilesystemService extends Service {

	public function absolute($relative) {
		return app()->root . '/' . ltrim($relative);
	}

}