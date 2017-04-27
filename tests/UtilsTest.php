<?php

require('./vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Tempest\Utils\{ArrayUtil, Enum, JSONUtil, ObjectUtil, StringUtil};

class Colors extends Enum {
	const RED = '#F00';
	const BLUE = '#00F';
	const GREEN = '#0F0';
}

class Shapes extends Enum {
	const SQUARE = 'square';
	const RECTANGLE = 'rectangle';
	const TRIANGLE = 'triangle';
}

class UtilsTest extends TestCase {

	public function testSlugify() {
		$simple = 'Where is John?';
		$complex = 'There\'s a load of **** here for $50. Aw35om3!';

		$this->assertEquals('where-is-john', StringUtil::slugify($simple)); // Single pass.
		$this->assertEquals('where-is-john', StringUtil::slugify(StringUtil::slugify($simple))); // Double pass.
		$this->assertEquals('theres-a-load-of-here-for-50-aw35om3', StringUtil::slugify($complex));
	}

	public function testSnakeCaseToCamelCase() {
		$this->assertEquals('thisIsWorkingCorrectly', StringUtil::snakeCaseToCamelCase('this_is_working_correctly'));
		$this->assertEquals('thisIsWorkingCorrectly', StringUtil::snakeCaseToCamelCase('thisIsWorkingCorrectly'));
	}

	public function testCamelCaseTOSnakeCase() {
		$this->assertEquals('this_is_working_correctly', StringUtil::camelCaseToSnakeCase('thisIsWorkingCorrectly'));
		$this->assertEquals('this_is_working_correctly', StringUtil::camelCaseToSnakeCase('this_is_working_correctly'));
	}

	public function testGetDeepValue() {
		$object = new stdClass();
		$object->j = 'k';
		$object->l = [
			'm' => 'n'
		];

		$tree = [
			'a' => 'b',
			'c' => [
				'd' => 'e',
				'f' => [
					'g' => 'h',
					'i' => $object
				],
				'o' => function() {
					return [
						'p' => 'q'
					];
				}
			]
		];

		$this->assertEquals('e', ObjectUtil::getDeepValue($tree, 'c.d'));
		$this->assertEquals('b', ObjectUtil::getDeepValue($tree, 'a'));
		$this->assertEquals('n', ObjectUtil::getDeepValue($tree, 'c.f.i.l.m'));
		$this->assertEquals('q', ObjectUtil::getDeepValue($tree, 'c.o.p'));
		$this->assertEquals(null, ObjectUtil::getDeepValue($tree, 'nonexistentKey'));
		$this->assertEquals('fallback', ObjectUtil::getDeepValue($tree, 'c.f.nonexistentKey', 'fallback'));
	}

	public function testPluck() {
		$values = [
			['name' => 'Marty'],
			['name' => 'Daniel'],
			['name' => 'Carlie']
		];

		$plucked = ObjectUtil::pluck($values, 'name');

		$this->assertEquals(['Marty', 'Daniel', 'Carlie'], $plucked);
	}

	public function testEnum() {
		$this->assertEquals(['RED' => '#F00', 'BLUE' => '#00F', 'GREEN' => '#0F0'], Colors::getAll());
		$this->assertEquals(['SQUARE' => 'square', 'RECTANGLE' => 'rectangle', 'TRIANGLE' => 'triangle'], Shapes::getAll());
		$this->assertEquals('square', Shapes::getValue('SQUARE'));
	}

}