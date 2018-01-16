<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\SystemInfoHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class SystemInfoHelperTest extends TestCase
{
    public $helper = null;

    public function setUp()
    {
        parent::setUp();
        $View = new View();
        $this->helper = new SystemInfoHelper($View);
    }

    public function testGetProjectVersion()
    {
        $result = $this->helper->getProjectVersion();
        $this->assertTrue(is_string($result), "Returned version is not a string");
        $this->assertFalse(empty($result), "Returned version is an empty string");
    }

    public function testGetProjectVersions()
    {
        $result = $this->helper->getProjectVersions();
        $this->assertTrue(is_array($result), "Returned versions are not an array");
        $this->assertFalse(empty($result), "Returned versions are an empty array");

        $keys = ['current', 'deployed', 'previous'];
        foreach ($keys as $key) {
            $this->assertTrue(array_key_exists($key, $result), "Missing key '$key' in result");
            $this->assertFalse(empty($result[$key]), "Missing value for key '$key'");
        }
    }

    public function testGetProjectUrl()
    {
        $result = $this->helper->getProjectUrl();
        $this->assertTrue(is_string($result), "Returned URL is not a string");
        $this->assertFalse(empty($result), "Returned URL is an empty string");
    }

    public function testGetProjectName()
    {
        $result = $this->helper->getProjectName();
        $this->assertTrue(is_string($result), "Returned name is not a string");
        $this->assertFalse(empty($result), "Returned name is an empty string");
    }

    public function testGetTableStats()
    {
        $result = $this->helper->getTableStats();
        $this->assertTrue(is_array($result), "Returned stats are not an array");
        $this->assertFalse(empty($result), "Returned stats are an empty array");
    }

    public function testAllTables()
    {
        $result = $this->helper->getAllTables();
        $this->assertTrue(is_array($result), "Returned tables are not an array");
        $this->assertFalse(empty($result), "Returned tables are an empty array");
    }

    public function testGetProgressValue()
    {
        $result = $this->helper->getProgressValue(0, 0);
        $this->assertEquals('0%', $result, "Incorrect progress for zero values");

        $result = $this->helper->getProgressValue(20, 100);
        $this->assertEquals('20%', $result, "Incorrect progress for integer values");

        $result = $this->helper->getProgressValue(1.25, 100);
        $this->assertEquals('1%', $result, "Incorrect progress for float values");
    }

    public function testGetLocalModifications()
    {
        $result = $this->helper->getLocalModifications();
        $this->assertTrue(is_array($result), "Returned local modifications are not an array");
    }

    public function testGetLocalModificationsCommand()
    {
        $result = $this->helper->getLocalModificationsCommand();
        $this->assertTrue(is_string($result), "Returned local modifications command is not a string");
        $this->assertEquals('git status --porcelain', $result, "Unknown local modifications command returned");
    }

    public function testServerInfo()
    {
        $result = $this->helper->getServerInfo();
        $this->assertTrue(is_array($result), "Returned server info is not an array");
        $this->assertFalse(empty($result), "Returned server info is an empty array");
    }

    public function testGetNumberOfCpus()
    {
        $result = $this->helper->getNumberOfCpus();
        $this->assertTrue(is_int($result), "Returned number of CPUs is not integer");
        $this->assertGreaterThan(-1, $result, "Returned number of CPUs is not positive integer");
    }

    public function testGetTotalRam()
    {
        $result = $this->helper->getTotalRam();
        $this->assertTrue(is_string($result), "Returned total RAM is not a string");
        $this->assertFalse(empty($result), "Returned total RAM is an empty string");
    }

    public function testGetCakePhpPlugins()
    {
        $result = $this->helper->getCakePhpPlugins();
        $this->assertTrue(is_array($result), "Returned CakePHP plugins is not an array");
        $this->assertFalse(empty($result), "Returned CakePHP plugins is an empty array");
    }

    public function testGetCakePhpVersion()
    {
        $result = $this->helper->getCakePhpVersion();
        $this->assertTrue(is_string($result), "Returned CakePHP version is not a string");
        $this->assertFalse(empty($result), "Returned CakePHP version is an empty string");
    }

    public function testComposerPackages()
    {
        $result = $this->helper->getComposerPackages();
        $this->assertTrue(is_array($result), "Returned composer packages is not an array");
        $this->assertFalse(empty($result), "Returned composer packages is an empty array");
    }

    public function testComposerMatchCounts()
    {
        $packages = $this->helper->getComposerPackages();
        $result = $this->helper->getComposerMatchCounts($packages, ['cake']);
        $this->assertTrue(is_array($result), "Returned composer matches is not an array");
        $this->assertFalse(empty($result), "Returned composer matches is an empty array");
    }

    public function testGetProjectLogo()
    {
        $result = $this->helper->getProjectLogo();
        $this->assertTrue(is_string($result), "Returned project logo is not a string");
        $this->assertFalse(empty($result), "Returned project logo is an empty string");
    }

    public function testGetCopyright()
    {
        $result = $this->helper->getCopyright();
        $this->assertTrue(is_string($result), "Returned project logo is not a string");
        $this->assertFalse(empty($result), "Returned project logo is an empty string");
    }
}
