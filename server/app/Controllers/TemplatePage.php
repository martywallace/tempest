<?php namespace Controllers;

use Tempest\HTTP\Controller;
use Tempest\HTTP\Request;


class TemplatePage extends Controller
{

	public function setup(Request $request, Array $vars)
	{
		// Custom controller setup.
		// ...
	}


	public function index(Request $request, Array $vars)
	{
		if (array_key_exists('template', $vars))
		{
			$result = tempest()->twig->render($vars['template'] . '.html', array());

			if ($result === null)
			{
				// NULL is provided if the template could not be loaded.
				tempest()->abort(404);
			}

			return $result;
		}
		else
		{
			tempest()->abort(404);
		}
	}

}