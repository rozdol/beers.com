<?php
namespace App\Test\TestCase\Feature\Type;

use App\Feature\Factory;
use App\Feature\Feature;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * App\Feature\Type\BatchFeature Test Case
 */
class BatchFeatureTest extends TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Configure::write('Features', [
            ['name' => Feature::BATCH(), 'active' => false]
        ]);
    }

    public function testEnable()
    {
        $feature = Factory::create(Feature::BATCH());

        $feature->enable();

        $this->assertTrue(Configure::read('CsvMigrations.batch.active'));
        $this->assertTrue(Configure::read('Search.batch.active'));
    }

    public function testDisable()
    {
        $feature = Factory::create(Feature::BATCH());

        $feature->disable();

        $this->assertFalse(Configure::read('CsvMigrations.batch.active'));
        $this->assertFalse(Configure::read('Search.batch.active'));
    }
}
