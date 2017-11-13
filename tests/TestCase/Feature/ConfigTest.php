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

        $data = ['name' => 'Articles', 'type' => 'Module', 'active' => false];
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

    public function testGetType()
    {
        $this->assertEquals('Module', $this->Config->getType());
    }

    public function testGetName()
    {
        $this->assertEquals('Articles', $this->Config->getName());
    }

    public function testIsActive()
    {
        $this->assertFalse($this->Config->isActive());
    }

    /**
     * @dataProvider invalidTypeProvider
     */
    public function testWrongType($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['type' => $value]);
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testWrongName($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['type' => 'Module', 'name' => $value]);
    }

    /**
     * @dataProvider invalidActiveProvider
     */
    public function testWrongActive($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['type' => 'Module', 'name' => 'Articles', 'active' => $value]);
    }

    public function invalidTypeProvider()
    {
        return [
            [['Module']],
            [true],
            [357],
            [null]
        ];
    }

    public function invalidNameProvider()
    {
        return [
            [['Articles']],
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
