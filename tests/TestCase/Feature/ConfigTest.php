<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Config;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

/**
 * App\Feature\Config Test Case
 */
class ConfigTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $data = ['name' => 'Batch', 'active' => false];
        $this->Config = new Config($data);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Config);

        parent::tearDown();
    }

    public function testGetName()
    {
        $this->assertEquals('Batch', $this->Config->getName());
    }

    public function testIsActive()
    {
        $this->assertFalse($this->Config->isActive());
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testWrongName($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['name' => $value]);
    }

    /**
     * @dataProvider invalidActiveProvider
     */
    public function testWrongActive($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['name' => 'Batch', 'active' => $value]);
    }

    public function invalidNameProvider()
    {
        return [
            ['Articles'],
            [true],
            [357],
            [null]
        ];
    }

    public function invalidActiveProvider()
    {
        return [
            [[true]],
            ['true'],
            [1],
            [0],
            [357],
            [null]
        ];
    }
}
