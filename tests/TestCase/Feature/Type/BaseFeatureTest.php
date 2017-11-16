<?php
namespace App\Test\TestCase\Feature\Type;

use App\Feature\Factory;
use Cake\Controller\Component\AuthComponent;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * App\Feature\Type\BatchFeature Test Case
 */
class BaseFeatureTest extends TestCase
{
    public function testIsActive()
    {
        $auth = $this->createMock(AuthComponent::class);
        $request = $this->createMock(ServerRequest::class);

        Factory::init($auth, $request);

        $feature = Factory::get('Base');

        $this->assertTrue($feature->isActive());
    }
}
