<?php
namespace App\Test\TestCase;

use Cake\Core\Configure;
use Cake\Core\Plugin;
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
            $enabled = Configure::read($config);
            if ($enabled) {
                $this->assertTrue(Plugin::loaded($plugin), "Plugin $plugin is not loaded but '$config' is true");
            }
        }
    }

    public function pluginProvider()
    {
        return [
            ['ADmad/JwtAuth', 'API.auth'],
            ['AdminLTE', null],
            ['Alt3/Swagger', null],
            ['AuditStash', null],
            ['BootstrapUI', null],
            ['Burzum/FileStorage', null],
            ['CakeDC/Users', null],
            ['Crud', null],
            ['CsvMigrations', null],
            ['DatabaseLog', null],
            ['DebugKit', 'debug'],
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
