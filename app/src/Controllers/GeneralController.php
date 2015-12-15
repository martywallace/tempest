<?php namespace Controllers;

use Tempest\Http\Controller;
use Tempest\Http\Request;
use Tempest\Http\Response;

class GeneralController extends Controller {

	public function index(Request $req, Response $res) {
		app()->db->query('SELECT 1');
		return app()->twig->render('index.html');
	}

	public function welcome(Request $req, Response $res) {
		$res->flash(5, '/');

		return 'Welcome, ' . $req->named('name');
	}

}