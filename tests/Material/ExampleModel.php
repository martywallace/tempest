<?php namespace Tempest\Tests\Material;

use Tempest\Database\Field;
use Tempest\Database\Model;

class ExampleModel extends Model {

	protected static function fields() {
		return [
			'id' => Field::int()->setAutoIncrements(),
			'first' => Field::string(),
			'last' => Field::string(),
			'age' => Field::int()->setDefault(18),
			'email' => Field::string()->addUniqueKey('identity'),
			'mobile' => Field::string()->addUniqueKey('identity')
		];
	}

}