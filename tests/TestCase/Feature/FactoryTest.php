<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Factory;
use App\Feature\FeatureInterface;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use ReflectionClass;

/**
 * App\Feature\Factory Test Case
 */
class FactoryTest extends TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(FeatureInterface::class, Factory::create('Batch'));
    }

    public function testCreateNonExisting()
    {
        $this->assertInstanceOf(FeatureInterface::class, Factory::create('NonExistingFeature'));
    }

    public function testExecute()
    {
        Factory::execute('Batch');

        $this->assertTrue(Configure::read('CsvMigrations.batch.active'));
        $this->assertTrue(Configure::read('Search.batch.active'));
    }

    public function testExecuteAll()
    {
        Factory::execute();

        $this->assertTrue(Configure::read('CsvMigrations.batch.active'));
        $this->assertTrue(Configure::read('Search.batch.active'));
    }
}
