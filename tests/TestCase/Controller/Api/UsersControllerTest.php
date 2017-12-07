<?php
namespace App\Test\TestCase\Controller\Api;

use App\Event\Controller\Api\EditActionListener;
use App\Event\Controller\Api\IndexActionListener;
use App\Event\Controller\Api\ViewActionListener;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\Http\Client;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Users\Controller\UsersController Test Case
 */
class UsersControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.CakeDC/Users.users',
    ];

    /**
     * External API Client object
     *
     * @var \Cake\Http\Client for external api calls.
     */
    protected $apiClient = null;

    public function setUp()
    {
        parent::setUp();

        $this->Users = TableRegistry::get('Users');

        // set headers without auth token by default.
        $this->setHeaders();

        $this->apiClient = new Client([
            'host' => 'localhost:8000',
            'scheme' => 'http',
        ], [
            'type' => 'json',
        ]);

        EventManager::instance()->on(new EditActionListener());
        EventManager::instance()->on(new IndexActionListener());
        EventManager::instance()->on(new ViewActionListener());
    }

    public function tearDown()
    {
        unset($this->token);
        unset($this->Users);

        parent::tearDown();
    }

    private function setHeaders()
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
        ]);
    }

    private function setAuthHeaders($id)
    {
        $token = JWT::encode(
            ['sub' => $id, 'exp' => time() + 604800],
            Security::salt()
        );

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'authorization' => 'Bearer ' . $token
            ],
        ]);
    }

    public function testToken()
    {
        $data = [
            'username' => 'user-6',
            'password' => '12345',
        ];

        $this->post('/api/users/token.json', json_encode($data));

        $this->assertResponseOk();
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');
    }

    public function testTokenWithNonActiveUser()
    {
        $data = [
            'username' => 'user-1',
            'password' => '12345',
        ];

        $this->post('/api/users/token.json', json_encode($data));

        $this->assertResponseError();
        $this->assertResponseCode(401);
        $this->assertContentType('application/json');
    }

    public function testInitializeForbidden()
    {
        // Valid data
        $data = [
            'username' => 'foo',
            'email' => 'foo@company.com',
            'password' => 'bar',
            'active' => true
        ];

        $this->post('/api/users/add.json', json_encode($data));

        $this->assertResponseError();
        $this->assertResponseCode(403);
        $this->assertContentType('application/json');
    }

    public function testTokenInvalid()
    {
        $this->post('/api/users/token.json', json_encode([]));

        $this->assertResponseError();
        $this->assertResponseCode(401);
        $this->assertContentType('application/json');
    }

    public function testViewByLookupField()
    {
        $this->setAuthHeaders('00000000-0000-0000-0000-000000000002');

        $email = 'user-2@test.com';
        $this->get('/api/users/view/' . $email . '.json');

        $this->assertResponseOk();
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');

        $response = json_decode($this->_response->body());

        $this->assertEquals($email, $response->data->email);
    }

    public function testEditByLookupField()
    {
        $this->setAuthHeaders('00000000-0000-0000-0000-000000000002');

        // lookup field
        $username = 'user-1';
        $id = '00000000-0000-0000-0000-000000000001';

        $data = [
            'first_name' => 'Some really random first name'
        ];

        $entity = $this->Users->get($id);

        $this->assertNotEquals($data['first_name'], $entity->get('first_name'));

        $this->put('/api/users/edit/' . $username . '.json', json_encode($data));

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode($this->_response->body());

        $entity = $this->Users->get($id);

        $this->assertEquals($data['first_name'], $entity->get('first_name'));
    }
}
