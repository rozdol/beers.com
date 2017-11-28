<?php
namespace App\Test\TestCase\Controller\Api;

use App\Event\Controller\Api\IndexActionListener;
use Cake\Core\Configure;
use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\UnauthorizedException;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Users\Controller\UsersController Test Case
 */
class UsersControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'app.users',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function testToken()
    {
        $table = TableRegistry::get('Users');

        $user = $table->find()->first();

        $data = [
            'username' => 'super-user-1',
            'password' => '12345',
        ];

        $this->post('/api/users/token.json', json_encode($data));
        $this->assertResponseSuccess();
    }

    public function testInitializeForbidden()
    {
        $this->post('/api/users/add.json', json_encode([]));
        $this->assertResponseError();
    }

    public function testTokenInvalid()
    {
        $this->post('/api/users/token.json', json_encode([]));
        $this->assertResponseError();

    }
}
