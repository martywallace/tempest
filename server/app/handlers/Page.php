<?php

namespace app\handlers;

use \tempest\routing\Handler;


class Page extends Handler
{

	protected function get()
	{
		return 'hello';
	}

}