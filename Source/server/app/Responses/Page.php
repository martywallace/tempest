<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;


class Page extends Response
{

	public function index(Request $request)
	{
		$this->mime = 'text/html';

		$content = Template::load("/templates/intro.html");
		$content->bind([
			"heading" => "Success!",
			"content" => "Everything appears to be working correctly.<br>Head over to <code>/server/app/Responses/Page.php</code> to modify this response."
		]);

		$template = Template::load("/templates/base.html");
		$template->bind(["content" => $content]);

		return $template;
	}

}