<?php namespace Controllers;

use Tempest\Controller;

class GeneralController extends Controller {

	public function index() {
		return array('ok' => true);
	}

	public function bindRoutes() {
		return array(
			'/' => array('GET', 'index')
		);
	}

}