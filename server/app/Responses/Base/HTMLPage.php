<?php namespace Responses\Base;

use Responses\Base\Adapter;
use Tempest\HTTP\Request;
use Tempest\Templating\Template;


class HTMLPage extends Adapter
{

	protected $styles = array();
	protected $scripts = array();


	public function setup(Request $r)
	{
		parent::setup($r);
	}


	public function finalize($output = null)
	{
		if(is_a($output, 'Tempest\Templating\Template'))
		{
			// Bind this page to the output Template.
			// Ensures styles and scripts are attached if required.
			return $output->bind($this, 'page');
		}

		return parent::finalize($output);
	}


	public function getStyles()
	{
		return Template::create('<link rel="stylesheet" href="~/static/{{ value }}" />')->batch($this->styles);
	}


	public function getScripts()
	{
		return Template::create('<script src="~/static/{{ value }}"></script>')->batch($this->scripts);
	}

}