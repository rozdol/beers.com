<?php
namespace App\Test\TestCase\Database\Type;

use Cake\Database\Type;
use Cake\TestSuite\TestCase;
use PDO;

class EncodedFileTypeTest extends TestCase
{
    /**
     * @var \App\Database\Type\EncodedFileType
     */
    public $type;

    /**
     * @var \Cake\Database\Driver
     */
    public $driver;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->type = Type::build('base64');
        $this->driver = $this->getMockBuilder('\Cake\Database\Driver')->getMock();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testToDatabaseExceptionNotArray()
    {
        $result = $this->type->toDatabase('not an array', $this->driver);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testToDatabaseExceptionMissingType()
    {
        $result = $this->type->toDatabase(['foo' => 'bar'], $this->driver);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testToDatabaseExceptionMissingTmpName()
    {
        $result = $this->type->toDatabase(['type' => 'image/png'], $this->driver);
    }

    public function testToDatabase()
    {
        $testFile = WWW_ROOT . DS . 'img' . DS . 'logo.png';
        $testValue = [
            'name' => 'logo.png',
            'type' => 'image/png',
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => filesize($testFile)
        ];
        $expected = 'data:image/png;base64,' . base64_encode(file_get_contents($testFile));
        $result = $this->type->toDatabase($testValue, $this->driver);
        $this->assertEquals($expected, $result, "toDatabase() returned an invalid result");
    }

    public function testToPHP()
    {
        $this->assertEquals('foo', $this->type->toPHP('foo', $this->driver));
        $this->assertEquals('', $this->type->toPHP('', $this->driver));

        $testFile = WWW_ROOT . DS . 'img' . DS . 'logo.png';
        $expected = file_get_contents($testFile);
        $fh = fopen($testFile, 'r');
        $this->assertEquals($expected, $this->type->toPHP($fh, $this->driver));
        fclose($fh);
    }

    public function testToStatement()
    {
        $this->assertEquals(PDO::PARAM_STR, $this->type->toStatement('foo', $this->driver));
    }

    public function testMarshal()
    {
        $this->assertEquals('foo', $this->type->marshal('foo', $this->driver));
    }
}
