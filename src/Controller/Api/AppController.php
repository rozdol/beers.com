<?php
namespace App\Controller\Api;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use CsvMigrations\Controller\Api\AppController as BaseController;
use RolesCapabilities\CapabilityTrait;

class AppController extends BaseController
{
    use CapabilityTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (Configure::read('API.auth')) {
            $this->enableAuthorization();
        }
    }

    /**
     * Enable API authorization checks.
     *
     * @return void
     */
    protected function enableAuthorization()
    {
        $hasAccess = $this->_checkAccess($this->request->params, $this->Auth->user());

        if (!$hasAccess) {
            throw new ForbiddenException();
        }

        $this->loadComponent('RolesCapabilities.Capability', [
            'currentRequest' => $this->request->params
        ]);
    }
}
