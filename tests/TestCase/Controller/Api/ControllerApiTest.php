<?php
namespace App\Test\TestCase\Controller\Api;

use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;

class ControllerApiTest extends TestCase
{
    public function testApiFilesPlacedCorrectly()
    {
        $path = App::path('Controller/Api')[0];
        $dir = new Folder($path);
        $found = 0;

        // checking for scanned files
        foreach ($dir->find('^\w+Controller\.php$') as $file) {
            $found++;
        }

        $this->assertEquals(0, $found, "Check API directory. Not all controllers were moved to corresponding API subdirs");
    }
}
