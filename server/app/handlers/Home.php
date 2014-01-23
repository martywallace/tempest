<?php

namespace app\handlers;

use \app\handlers\Page;


class Home extends Page
{

	protected function get()
	{
		return 'hello';
	}

}