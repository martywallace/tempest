<?php namespace Tempest\Http;

use Exception;
use Tempest\Tempest;
use Tempest\Utils\JSONUtil;
use Tempest\Models\UploadedFileModel;

/**
 * Middleware used to parse the request body into usable data.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class BodyMiddleware extends Middleware {

	/**
	 * Parse and attach the request body to the request object for usage in future middleware or controller actions.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param callable $next
	 *
	 * @return mixed
	 */
	public function parse(Request $request, Response $response, callable $next) {
		$stack = Tempest::get()->memoization->cache(static::class, 'parseComplex', function() use ($request, $response) {
			if ($request->method === 'GET') {
				return $_GET;
			} else {
				$data = [];

				if (ContentType::matches($request->contentType, ContentType::APPLICATION_X_WWW_FORM_URLENCODED)) {
					parse_str($request->body, $data);
				} else if (ContentType::matches($request->contentType, ContentType::APPLICATION_JSON)) {
					$data = JSONUtil::decode($request->body, true);
				} else {
					if ($request->method === 'POST') return $_POST;
					else throw new Exception('Cannot extract data from a "' . $request->method . '" request with the content-type "' . $request->contentType . '".');
				}

				return $data;
			}
		});

		foreach ($stack as $name => $value) $request->setData($name, $value);

		return $next();
	}

	/**
	 * Convert all uploaded files and convert each into an {@link UploadedFileModel}.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param callable $next
	 *
	 * @return mixed
	 *
	 * @throws Exception If there were errors associated with the files being uploaded.
	 */
	public function parseFiles(Request $request, Response $response, callable $next) {
		foreach ($_FILES as $name => $file) {
			if ($file['error'] === UPLOAD_ERR_OK) {
				$request->setData($name, new UploadedFileModel($file));
			} else if($file['error'] === UPLOAD_ERR_NO_FILE) {
				$request->setData($name, null);
			} else {
				// Throw exceptions for the other stuff.
				switch ($file['error']) {
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						throw new Exception('The filesize of the uploaded file was too large.');
						break;

					case UPLOAD_ERR_PARTIAL:
						throw new Exception('The file was only partially uploaded.');
						break;

					case UPLOAD_ERR_NO_TMP_DIR:
					case UPLOAD_ERR_CANT_WRITE:
						throw new Exception('The file could not be uploaded.');
						break;
				}
			}
		}

		return $next();
	}

}