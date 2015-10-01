<?php namespace Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Tempest\Controller;

class GeneralController extends Controller {

	public function index() {
		return app()->twig->render('index.html');
	}

	public function welcome(Request $req, Response $res, Array $args) {
		return 'Welcome ' . $args['name'] . '!';
	}

	public function bindRoutes() {
		return [
			'/' => ['get', 'index'],
			'/welcome/{name}' => ['get', 'welcome']
		];
	}

}