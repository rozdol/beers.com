<?php
namespace App\Test\TestCase\Controller\Api;

use App\Feature\Factory;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

class ControllerApiTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.CakeDC/Users.users',
        'app.log_audit'
    ];

    public function setUp()
    {
        parent::setUp();

        $token = JWT::encode(
            ['sub' => '00000000-0000-0000-0000-000000000002', 'exp' => time() + 604800],
            Security::salt()
        );

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'authorization' => 'Bearer ' . $token
            ]
        ]);
    }

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

    /**
     * @dataProvider modulesProvider
     */
    public function testIndex($module)
    {
        $this->get('/api/' . Inflector::dasherize($module));

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode($this->_response->body());
        $this->assertTrue($response->success);
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testView($module)
    {
        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->newEntity();
        $table->save($entity);

        $this->get('/api/' . Inflector::dasherize($module) . '/view/' . $entity->get($table->getPrimaryKey()));

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode($this->_response->body());
        $this->assertTrue($response->success);
        $this->assertEquals($entity->get($table->getPrimaryKey()), $response->data->{$table->getPrimaryKey()});
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testAdd($module)
    {
        $table = TableRegistry::getTableLocator()->get($module);

        $this->post('/api/' . Inflector::dasherize($module) . '/add/');

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode($this->_response->body());
        $this->assertTrue($response->success);
        $this->assertEquals(36, strlen($response->data->{$table->getPrimaryKey()}));
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testEdit($module)
    {
        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->newEntity();
        $table->save($entity);

        $this->put('/api/' . Inflector::dasherize($module) . '/edit/' . $entity->get($table->getPrimaryKey()));

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode($this->_response->body());
        $this->assertTrue($response->success);
        $this->assertInternalType('array', $response->data);
        $this->assertEmpty($response->data);
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testDelete($module)
    {
        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->newEntity();
        $table->save($entity);

        $this->delete('/api/' . Inflector::dasherize($module) . '/delete/' . $entity->get($table->getPrimaryKey()));

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode($this->_response->body());
        $this->assertTrue($response->success);
        $this->assertInternalType('array', $response->data);

        $query = $table->find()->where([$table->getPrimaryKey() => $entity->get($table->getPrimaryKey())]);
        $this->assertTrue($query->isEmpty());
    }

    /**
     * Modules provider.
     *
     * @return array
     */
    public function modulesProvider()
    {
        // store default path
        $defaultPath = Configure::read('CsvMigrations.modules.path');

        Configure::write('CsvMigrations.modules.path', CONFIG . 'Modules' . DS);

        $modules = [];
        foreach ((new Folder(App::path('Controller/Api/V1/V0')[0]))->find('^\w+Controller\.php$') as $file) {
            array_push($modules, basename($file, 'Controller.php'));
        }

        $modules = array_filter($modules, [$this, 'isModule']);
        $modules = array_filter($modules, [$this, 'isActive']);

        // restore default path
        Configure::write('CsvMigrations.modules.path', $defaultPath);

        return array_map(function ($module) {
            return [$module];
        }, $modules);
    }

    private function isModule($name)
    {
        $config = (new ModuleConfig(ConfigType::MIGRATION(), $name, null, ['cacheSkip' => true]))->parse();
        $config = json_decode(json_encode($config), true);

        return ! empty($config);
    }

    private function isActive($module)
    {
        $feature = Factory::get('Module' . DS . $module);

        return $feature->isActive();
    }
}
