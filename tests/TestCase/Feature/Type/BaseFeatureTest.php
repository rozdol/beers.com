<?php
namespace App\Test\TestCase\Feature\Type;

use App\Feature\Factory;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * App\Feature\Type\BatchFeature Test Case
 */
class BaseFeatureTest extends TestCase
{
    public function testIsActive()
    {
        $feature = Factory::create('Base');

        $this->assertTrue($feature->isActive());
    }
}
