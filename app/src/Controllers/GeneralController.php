<?php namespace Controllers;

use Tempest\Http\Controller;
use Tempest\Http\Request;

class GeneralController extends Controller {

	public function index(Request $request) {
		return app()->twig->render('index.html');
	}

	public function welcome(Request $request) {
		return $request->data('hi');
	}

	public function bindRoutes() {
		return [
			'/' => ['get', 'index'],
			'/welcome/{name}' => ['get', 'welcome']
		];
	}

}