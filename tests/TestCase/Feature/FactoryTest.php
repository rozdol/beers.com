<?php
namespace App\Test\TestCase\Feature;

use App\Feature\Factory;
use App\Feature\FeatureInterface;
use App\Feature\Type\BaseFeature;
use Cake\Controller\Component\AuthComponent;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * App\Feature\Factory Test Case
 */
class FactoryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->auth = $this->createMock(AuthComponent::class);
        $this->request = $this->createMock(ServerRequest::class);
    }

    public function tearDown()
    {
        unset($this->auth);
        unset($this->request);

        parent::tearDown();
    }

    /**
     * For now we just make sure the Factory can be initialized, no assertions are made.
     */
    public function testInit()
    {
        Factory::init($this->auth, $this->request);
    }

    public function testGet()
    {
        $feature = Factory::get('Foobar');
        $this->assertInstanceOf(FeatureInterface::class, $feature);
        $this->assertInstanceOf(BaseFeature::class, $feature);
    }
}
