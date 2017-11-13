<?php
namespace App\Test\TestCase\Feature\Type;

use App\Feature\Collection;
use App\Feature\Factory;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * App\Feature\Type\ModuleFeature Test Case
 */
class ModuleFeatureTest extends TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $data = [
            ['name' => 'Articles', 'type' => 'Module', 'active' => false],
            ['name' => 'Batch', 'type' => 'Batch', 'active' => true]
        ];
        $this->Collection = new Collection($data);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Collection);

        parent::tearDown();
    }

    public function testDisable()
    {
        $feature = Factory::create($this->Collection->get('Articles'));

        $feature->disable();

        $data = Configure::read('RolesCapabilities.accessCheck.skipControllers');

        $this->assertContains('App\\Controller\\ArticlesController', $data);
    }
}
