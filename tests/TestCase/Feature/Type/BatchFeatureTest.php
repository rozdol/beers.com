<?php
namespace App\Test\TestCase\Feature\Type;

use App\Feature\Factory;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * App\Feature\Type\BatchFeature Test Case
 */
class BatchFeatureTest extends TestCase
{
    public function testIsActive()
    {
        $feature = Factory::create('Batch');

        $this->assertTrue($feature->isActive());
    }

    public function testEnable()
    {
        $feature = Factory::create('Batch');

        $feature->enable();

        $this->assertTrue(Configure::read('CsvMigrations.batch.active'));
        $this->assertTrue(Configure::read('Search.batch.active'));
    }

    public function testDisable()
    {
        $feature = Factory::create('Batch');

        $feature->disable();

        $this->assertFalse(Configure::read('CsvMigrations.batch.active'));
        $this->assertFalse(Configure::read('Search.batch.active'));
    }
}
