<?php namespace App;

use Tempest\Base\Tempest;
use Tempest\Database\Repository;
use App\Models\Human;


class Application extends Tempest
{
	
	public function __construct()
	{
		Repository::setup("localhost", "test", "root", "");

		$human = Human::create(array(
			"id" => 4,
			"firstName" => "Marty",
			"lastName" => "Wallace"
		));

		if($human !== null)
		{
			echo $human->id;
		} 

		$h2 = Human::find(10);

		var_dump($h2);
		print_r(Repository::getErrors());

		parent::__construct();
	}

}