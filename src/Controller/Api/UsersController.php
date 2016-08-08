<?php
namespace App\Controller\Api;

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
        parent::initialize();
        $this->Auth->allow(['token']);
    }

    /**
     * Method responsible for generating JSON Web Token (WT), after it authenticates the user.
     *
     * @return array Authentication token
     * @throws \Cake\Network\Exception\UnauthorizedException
     * @link   http://www.bravo-kernel.com/2015/04/how-to-add-jwt-authentication-to-a-cakephp-3-rest-api/
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
