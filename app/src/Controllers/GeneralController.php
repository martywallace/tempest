<?php namespace Controllers;

use Tempest\Http\Controller;
use Tempest\Http\Request;
use Tempest\Http\Response;

class GeneralController extends Controller {

	public function index(Request $request, Response $response) {
		return app()->twig->render('index.html');
	}

	public function welcome(Request $request, Response $response) {
		return $request->name;
	}

	public function bindRoutes() {
		return array(
			'/' => array('get', 'index'),
			'/welcome/{name}' => array('get', 'welcome')
		);
	}

}