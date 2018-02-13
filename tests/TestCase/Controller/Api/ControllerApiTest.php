<?php
namespace App\Test\TestCase\Controller\Api;

use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;

class ControllerApiTest extends TestCase
{
    public function testApiFilesPlacedCorrectly()
    {
        $dir = App::path('Controller/Api')[0];
        $dir = new Folder($dir);

        $contents = $dir->read(true, true);
        $found = 0;

        // checking for scanned files
        if (!empty($contents[1])) {
            foreach ($contents[1] as $file) {
                if (preg_match('/^(.*)Controller\.php$/', $file, $matches)) {
                    if (count($matches) > 1) {
                        $found++;
                    }
                }
            }
        }

        $this->assertEquals(0, $found, "Check API directory. Not all controllers were moved to corresponding API subdirs");
    }
}
