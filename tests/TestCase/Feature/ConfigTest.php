<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Config;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;
use StdClass;

/**
 * App\Feature\Config Test Case
 */
class ConfigTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $data = [
            'name' => 'Foobar',
            'active' => false,
            'options' => [1, 'foo']
        ];
        $this->Config = new Config($data);
    }

    public function tearDown()
    {
        unset($this->Config);

        parent::tearDown();
    }

    public function testGetName()
    {
        $this->assertEquals('Foobar', $this->Config->get('name'));
    }

    public function testGetActive()
    {
        $this->assertFalse($this->Config->get('active'));
    }

    public function testGetAdditionalParameter()
    {
        $this->assertEquals([1, 'foo'], $this->Config->get('options'));
    }

    public function testGetNonExistingParameter()
    {
        $this->assertNull($this->Config->get('Non Existing Parameter'));
    }

    /**
     * @dataProvider RequiredParametersProvider
     */
    public function testMissingRequiredParameter($value)
    {
        $data = ['name' => 'Batch', 'active' => true];
        unset($data[$value]);

        $this->expectException(InvalidArgumentException::class);
        new Config($data);
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testWrongParameterName($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['name' => $value, 'active' => true]);
    }

    /**
     * @dataProvider invalidActiveProvider
     */
    public function testWrongParameterActive($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['active' => $value, 'name' => 'Batch']);
    }

    public function RequiredParametersProvider()
    {
        return [
            ['name'],
            ['active']
        ];
    }

    public function invalidNameProvider()
    {
        return [
            [new StdClass()],
            [['array']],
            [357],
            [true],
            [null]
        ];
    }

    public function invalidActiveProvider()
    {
        return [
            [new StdClass()],
            [['array']],
            ['string'],
            [1],
            [0],
            [null]
        ];
    }
}
