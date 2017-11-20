<?php namespace Tempest\Tests;

use stdClass;
use Tempest\Utility;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase {

	public function testDig() {
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
				]
			]
		];

		$this->assertEquals('e', Utility::evaluate($tree, 'c.d'));
		$this->assertEquals('b', Utility::evaluate($tree, 'a'));
		$this->assertEquals('n', Utility::evaluate($tree, 'c.f.i.l.m'));
		$this->assertEquals(null, Utility::evaluate($tree, 'nonexistentKey'));
		$this->assertEquals('fallback', Utility::evaluate($tree, 'c.f.nonexistentKey', 'fallback'));
		$this->assertEquals('max', Utility::evaluate(['test' => 'max'], 'test'));
	}

	public function testKebab() {
		$this->assertEquals('the-quick-brown-fox', Utility::kebab('The quick brown fox'));
		$this->assertEquals('the-quick-brown-fox', Utility::kebab('the-quick-brown-fox'));
		$this->assertEquals('', Utility::kebab(''));
		$this->assertEquals('', Utility::kebab(' '));
		$this->assertEquals('', Utility::kebab('!@#$%^&*()_+-={}:"<>[];\',.'));
		$this->assertEquals('', Utility::kebab('---'));
		$this->assertEquals('he-said-hello', Utility::kebab('He said "hello"!'));
	}

}