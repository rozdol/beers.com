<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Collection;
use App\Feature\Manager;
use Cake\TestSuite\TestCase;

/**
 * App\Feature\Manager Test Case
 */
class ManagerTest extends TestCase
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
        $this->Manager = new Manager(new Collection($data));
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Manager);

        parent::tearDown();
    }

    /**
     * @dataProvider featuresNameProvider
     */
    public function testIsEnabled($name, $active)
    {
        $data = $this->Manager->isEnabled($name);
        $this->assertEquals($active, $data);
        $this->assertInternalType('boolean', $data);
    }

    public function featuresNameProvider()
    {
        return [
            ['Batch', true],
            ['Articles', false],
            ['Authors', true] // returns true for non-defined features
        ];
    }
}
