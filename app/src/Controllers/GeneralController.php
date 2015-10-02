<?php namespace Controllers;

use Tempest\Http\Controller;
use Tempest\Http\Request;
use Tempest\Http\Response;

class GeneralController extends Controller {

	public function index(Request $request, Response $response) {
		return app()->twig->render('indx.html');
	}

	public function welcome(Request $request, Response $response) {
		return $request->arg('name');
	}

	public function bindRoutes() {
		return [
			'/' => ['get', 'index'],
			'/welcome/{name}' => ['get', 'welcome']
		];
	}

}