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
     * @dataProvider featuresProvider
     */
    public function testCreate($feature)
    {
        $this->assertInstanceOf(FeatureInterface::class, Factory::create($feature));
    }

    public function testExecute()
    {
        Factory::execute(Feature::BATCH());

        $this->assertTrue(Configure::read('CsvMigrations.batch.active'));
        $this->assertTrue(Configure::read('Search.batch.active'));
    }

    public function featuresProvider()
    {
        return [
            ['Batch'],
            [Feature::BATCH()]
        ];
    }
}
