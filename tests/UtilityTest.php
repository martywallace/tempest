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
				],
				'o' => function() {
					return [
						'p' => 'q'
					];
				}
			]
		];
		$this->assertEquals('e', Utility::dig($tree, 'c.d'));
		$this->assertEquals('b', Utility::dig($tree, 'a'));
		$this->assertEquals('n', Utility::dig($tree, 'c.f.i.l.m'));
		$this->assertEquals('q', Utility::dig($tree, 'c.o.p'));
		$this->assertEquals(null, Utility::dig($tree, 'nonexistentKey'));
		$this->assertEquals('fallback', Utility::dig($tree, 'c.f.nonexistentKey', 'fallback'));
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