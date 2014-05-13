<?php namespace App\Models;

use Tempest\Database\Model;


class Human extends Model
{
	
	protected $table = 'humans';
	protected $primary = 'id';
	protected $readonly = array('firstName');


	public $id;
	public $firstName;
	public $lastName;

}