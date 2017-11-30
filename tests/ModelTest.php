<?php namespace Tempest\Tests;

use PHPUnit\Framework\TestCase;
use Tempest\Tests\Material\ExampleModel;

class ModelTest extends TestCase {

	public function testCreation() {
		$this->assertInstanceOf(ExampleModel::class, ExampleModel::create());
	}

	public function testCreationWithValues() {
		$this->assertEquals('Marty', ExampleModel::create(['first' => 'Marty'])->getRaw('first'));
	}

	public function testDefaultValues() {
		$this->assertEquals(18, ExampleModel::create()->age);
	}

	public function testGetterAccess() {
		$model = ExampleModel::create([
			'first' => 'Marty',
			'last' => 'Wallace'
		]);

		$this->assertEquals('Marty', $model->first);
		$this->assertEquals('Wallace', $model->last);
	}

	public function testSetValues() {
		$model = ExampleModel::create();

		$model->setFieldValue('first', 'Marty');
		$model->last = 'Wallace';

		$this->assertEquals('Marty', $model->first);
		$this->assertEquals('Wallace', $model->last);
	}

	public function testFill() {
		$model = ExampleModel::create()->fill([
			'email' => 'test@test.com',
			'first' => 'Marty'
		]);

		$this->assertEquals('test@test.com', $model->email);
		$this->assertEquals('Marty', $model->first);
		$this->assertEquals(18, $model->age);
		$this->assertNull($model->last);
	}

	public function testGetPrimaryKey() {
		$this->assertEquals('id', ExampleModel::getPrimaryFields()[0]->getName());
	}

	public function testGetUniqueIndexWithMultipleFields() {
		$this->assertCount(2, ExampleModel::getIndexByName('identity')->getFields());
	}

	public function testGetNonIndexedFields() {
		$this->assertCount(3, ExampleModel::getNonIndexedFields());
	}

}