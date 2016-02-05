<?php namespace Controllers;

use Tempest\Http\Controller;
use Tempest\Http\Request;
use Tempest\Http\Response;

class GeneralController extends Controller {

	public function index(Request $req, Response $res) {
		// app()->dump(array('wat' => 10), 'var_dump');
		return app()->twig->render('index.html');
	}

	public function welcome(Request $req, Response $res) {
		return 'Welcome, ' . $req->named('name');
	}

}