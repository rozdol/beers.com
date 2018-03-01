<?php
namespace App\Test\TestCase\SystemInfo;

use App\SystemInfo\Server;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    public function testGetInfo()
    {
        $result = Server::getInfo();
        $this->assertTrue(is_array($result), "getInfo() returned a non-array result");
        $this->assertFalse(empty($result), "getInfo() returned an empty result");
    }

    public function testGetOperatingSystem()
    {
        $result = Server::getOperatingSystem();
        $this->assertTrue(is_string($result), "getOperatingSystem() returned a non-string result");
        $this->assertFalse(empty($result), "getOperatingSystem() returned an empty result");
    }

    public function testGetMachineType()
    {
        $result = Server::getMachineType();
        $this->assertTrue(is_string($result), "getMachineType() returned a non-string result");
        $this->assertEquals(php_uname('m'), $result, "getMachineType() returned an incorrect result");
    }

    public function testGetNumberOfCpus()
    {
        $result = Server::getNumberOfCpus();
        $this->assertTrue(is_int($result), "getNumberOfCpus() returned a non-integer result");
        $this->assertGreaterThanOrEqual(0, $result, "getNumberOfCpus() returned an incorrect result");
    }

    public function getTotalRam()
    {
        $result = Server::getTotalRam();
        $this->assertTrue(is_string($result), "getTotalRam() returned a non-string result");
        $this->assertFalse(empty($result), "getTotalRam() returned an empty result");
    }
}
