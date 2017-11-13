<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Collection;
use App\Feature\Config;
use App\Feature\Factory;
use App\Feature\FeatureInterface;
use Cake\TestSuite\TestCase;

/**
 * App\Feature\Factory Test Case
 */
class FactoryTest extends TestCase
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
            ['name' => 'Batch', 'type' => 'Batch', 'active' => true]
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

    public function testCreate()
    {
        foreach ($this->Collection->all() as $item) {
            $this->assertInstanceOf(FeatureInterface::class, Factory::create($item));
        }
    }
}
