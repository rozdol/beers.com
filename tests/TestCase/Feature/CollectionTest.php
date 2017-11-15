<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Collection;
use App\Feature\Config;
use Cake\TestSuite\TestCase;

/**
 * App\Feature\Collection Test Case
 */
class CollectionTest extends TestCase
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
            ['name' => 'Batch', 'active' => true]
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

    public function testAll()
    {
        $data = $this->Collection->all();

        $this->assertInternalType('array', $data);

        foreach ($data as $item) {
            $this->assertInstanceOf(Config::class, $item);
        }
    }

    public function testGet()
    {
        $data = $this->Collection->get('Batch');

        $this->assertInstanceOf(Config::class, $data);
    }

    public function testGetNonExisting()
    {
        $data = $this->Collection->get('NonExistingFeature');

        $this->assertNull($data);
    }
}
