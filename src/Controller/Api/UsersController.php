<?php
namespace App\Controller\Api;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

class UsersController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        // allow public access to token action
        try {
            parent::initialize();
        } catch (ForbiddenException $e) {
            if ('token' !== $this->request->action) {
                throw new ForbiddenException($e->getMessage());
            }
        }

        if (Configure::read('API.auth')) {
            $this->Auth->allow(['token']);
        }
    }

    /**
     * Method responsible for generating JSON Web Token (WT), after it authenticates the user.
     *
     * @throws \Cake\Network\Exception\UnauthorizedException
     * @link   http://www.bravo-kernel.com/2015/04/how-to-add-jwt-authentication-to-a-cakephp-3-rest-api/
     * @return void
     */
    public function token()
    {
        $user = $this->Auth->identify();
        if (!$user) {
            throw new UnauthorizedException('Invalid username or password');
        }

        $this->set([
            'success' => true,
            'data' => [
                'token' => JWT::encode(
                    [
                        'sub' => $user['id'],
                        'exp' => time() + 604800
                    ],
                    Security::salt()
                )
            ],
            '_serialize' => ['success', 'data']
        ]);
    }
}
