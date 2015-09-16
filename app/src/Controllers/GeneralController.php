<?php namespace Controllers;

use Tempest\Routing\Controller;

class GeneralController extends Controller {

	public function index() {
		return app()->twig->render('index.html');
	}

	public function bindRoutes() {
		return array(
			'/' => array('GET', 'index')
		);
	}

}