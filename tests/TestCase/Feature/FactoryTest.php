<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Factory;
use App\Feature\Feature;
use App\Feature\FeatureInterface;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use ReflectionClass;

/**
 * App\Feature\Factory Test Case
 */
class FactoryTest extends TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Configure::write('Features', [
            ['name' => Feature::BATCH(), 'active' => false]
        ]);
    }

    /**
     * @dataProvider featuresProvider
     */
    public function testCreate($feature)
    {
        $this->assertInstanceOf(FeatureInterface::class, Factory::create($feature));
    }

    public function testExecute()
    {
        Factory::execute(Feature::BATCH());

        $this->assertFalse(Configure::read('CsvMigrations.batch.active'));
        $this->assertFalse(Configure::read('Search.batch.active'));
    }

    public function featuresProvider()
    {
        return [
            ['Batch'],
            [Feature::BATCH()]
        ];
    }
}
