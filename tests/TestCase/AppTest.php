<?php
namespace App\Test\TestCase;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;

class AppTest extends TestCase
{
    /**
     * @dataProvider pluginProvider
     */
    public function testLoadedPlugins($plugin, $config)
    {
        if (empty($config)) {
            $this->assertTrue(Plugin::loaded($plugin), "Plugin $plugin is not loaded");
        } else {
            $enabled = false;
            switch (gettype($config)) {
                case 'string':
                    $enabled = Configure::read($config);
                    break;

                case 'array':
                    foreach ($config as $conf) {
                        if (!Configure::read($conf)) {
                            $enabled = false;
                            break;
                        }

                        $enabled = true;
                    }
                    break;
            }

            $message = "Plugin $plugin is not loaded but [" . implode(' or ', (array)$config) . "] is true";
            $this->assertEquals($enabled, Plugin::loaded($plugin), $message);
        }
    }

    /**
     * Test API files moved to its subdirectories.
     *
     * Making sure that with API versioning being
     * introduced we're not left with not moved
     * controller files
     */
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

    public function pluginProvider()
    {
        return [
            ['ADmad/JwtAuth', 'API.auth'],
            ['AdminLTE', null],
            ['Alt3/Swagger', ['API.auth', 'Swagger.crawl']],
            ['AuditStash', null],
            ['Burzum/FileStorage', null],
            ['CakeDC/Users', null],
            ['Crud', null],
            ['CsvMigrations', null],
            ['DatabaseLog', null],
            ['Groups', null],
            ['Menu', null],
            ['Migrations', null],
            ['Qobo/Utils', null],
            ['RolesCapabilities', null],
            ['Search', null],
            ['Translations', null],
        ];
    }
}
