<?php namespace Middleware;

use Tempest\Http\Middleware;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Status;


class GeneralMiddleware extends Middleware {

	public function auth(Request $req, Response $res) {
		if ($req->data('auth') !== 'example') {
			$res->status = Status::UNAUTHORIZED;
			$res->body = array('message' => 'No auth.');

			return false;
		}

		return true;
	}

}