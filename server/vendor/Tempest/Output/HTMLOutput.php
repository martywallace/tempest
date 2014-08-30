<?php namespace Tempest\Output;

use Tempest\Output\BaseOutput;


/**
 * Typical HTML output.
 * @author Marty Wallace.
 */
class HTMLOutput extends BaseOutput
{

	public function __construct($content = null)
	{
		parent::__construct('text/html', $content);
	}

}