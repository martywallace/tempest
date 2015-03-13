<?php namespace Responses;

use Tempest\HTTP\Responder;
use Tempest\HTTP\Request;


class AdaptivePage extends Responder
{

	protected $name;


	public function setup(Request $r)
	{
		$this->name = $r->last();
		$this->name = $this->name === null ? 'home' : $this->name;
	}


	public function index(Request $request)
	{
		trigger_error('fdsfs');
		trigger_error('fdsfs');
		trigger_error('fdsfs');
		
		$result = tempest()->twig->render($this->name . '.html', array());

		if ($result === null)
		{
			// NULL is provided if the template could not be loaded.
			tempest()->abort(404);
		}

		return $result;
	}

}