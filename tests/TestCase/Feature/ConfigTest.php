<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Config;
use Cake\Controller\Component\AuthComponent;
use Cake\Http\ServerRequest;
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

        $this->auth = $this->createMock(AuthComponent::class);
        $this->request = $this->createMock(ServerRequest::class);

        $data = [
            'name' => 'Foobar',
            'active' => false,
            'auth' => $this->auth,
            'request' => $this->request,
            'options' => [1, 'foo']
        ];
        $this->Config = new Config($data);
    }

    public function tearDown()
    {
        unset($this->auth);
        unset($this->request);
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

    public function testGetAuth()
    {
        $this->assertInstanceOf(AuthComponent::class, $this->Config->get('auth'));
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf(ServerRequest::class, $this->Config->get('request'));
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
        $data = ['name' => 'Batch', 'active' => true, 'auth' => $this->auth, 'request' => $this->request];
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
        new Config(['name' => $value, 'active' => true, 'auth' => $this->auth, 'request' => $this->request]);
    }

    /**
     * @dataProvider invalidActiveProvider
     */
    public function testWrongParameterActive($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['active' => $value, 'name' => 'Batch', 'auth' => $this->auth, 'request' => $this->request]);
    }

    /**
     * @dataProvider invalidAuthProvider
     */
    public function testWrongParameterAuth($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['auth' => $value, 'name' => 'Batch', 'active' => true, 'request' => $this->request]);
    }

    /**
     * @dataProvider invalidRequestProvider
     */
    public function testWrongParameterRequest($value)
    {
        $this->expectException(InvalidArgumentException::class);
        new Config(['request' => $value, 'name' => 'Batch', 'active' => true, 'auth' => $this->auth]);
    }

    public function RequiredParametersProvider()
    {
        return [
            ['name'],
            ['active'],
            ['auth'],
            ['request']
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

    public function invalidAuthProvider()
    {
        return [
            [new StdClass()],
            [['array']],
            ['string'],
            [0],
            [true],
            [null]
        ];
    }

    public function invalidRequestProvider()
    {
        return [
            [new StdClass()],
            [['array']],
            ['string'],
            [0],
            [true],
            [null]
        ];
    }
}
