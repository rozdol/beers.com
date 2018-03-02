<?php
namespace App\Test\TestCase\SystemInfo;

use App\SystemInfo\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testGetDriver()
    {
        // With version
        $result = Database::getDriver();
        $this->assertTrue(is_string($result), "getDriver() returned a non-string result");
        $this->assertFalse(empty($result), "getDriver() returned an empty result");

        // Without version
        $result = Database::getDriver();
        $this->assertTrue(is_string($result), "getDriver() returned a non-string result");
        $this->assertFalse(empty($result), "getDriver() returned an empty result");
    }

    public function testGetAllTables()
    {
        $result = Database::getAllTables();
        $this->assertTrue(is_array($result), "getAllTables() returned a non-array result");
        $this->assertFalse(empty($result), "getAllTables() returned an empty result");
    }

    public function testGetTableStats()
    {
        $result = Database::getTableStats();
        $this->assertTrue(is_array($result), "getTableStats() returned a non-array result");
        $this->assertFalse(empty($result), "getTableStats() returned an empty result");
    }
}
