<?php namespace Tempest\Tests;

use Tempest\Database\Field;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase {

	public function testIntNull() {
		$field = Field::int();

		$this->assertEquals(false, $field->isNull(1));
		$this->assertEquals(false, $field->isNull(2));
		$this->assertEquals(false, $field->isNull(-1));
		$this->assertEquals(false, $field->isNull(0));
		$this->assertEquals(false, $field->isNull('1'));
		$this->assertEquals(false, $field->isNull('0'));
		$this->assertEquals(false, $field->isNull(0.1));

		$this->assertEquals(true, $field->isNull(false));
		$this->assertEquals(true, $field->isNull(''));
		$this->assertEquals(true, $field->isNull(null));
		$this->assertEquals(true, $field->isNull([]));
	}

}