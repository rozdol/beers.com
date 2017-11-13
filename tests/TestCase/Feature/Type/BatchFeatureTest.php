<?php
namespace App\Test\TestCase\Feature\Type;

use App\Feature\Collection;
use App\Feature\Factory;
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

        $data = [
            ['name' => 'Articles', 'type' => 'Module', 'active' => false],
            ['name' => 'Foobar', 'type' => 'Batch', 'active' => true]
        ];
        $this->Collection = new Collection($data);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Collection);

        parent::tearDown();
    }

    public function testEnable()
    {
        $feature = Factory::create($this->Collection->get('Foobar'));

        $feature->enable();

        $this->assertTrue(Configure::read('CsvMigrations.batch.active'));
        $this->assertTrue(Configure::read('Search.batch.active'));
    }

    public function testDisable()
    {
        $feature = Factory::create($this->Collection->get('Foobar'));

        $feature->disable();

        $this->assertFalse(Configure::read('CsvMigrations.batch.active'));
        $this->assertFalse(Configure::read('Search.batch.active'));
    }
}
