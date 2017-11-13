<?php
namespace App\Feature\Type;

use App\Feature\AbstractFeature;
use Cake\Core\Configure;

class ModuleFeature extends AbstractFeature
{
    /**
     * {@inheritDoc}
     */
    public function enable()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function disable()
    {
        $accessCheck = Configure::read('RolesCapabilities.accessCheck.skipControllers');
        $accessCheck[] = 'App\\Controller\\' . $this->config->getName() . 'Controller';

        Configure::write('RolesCapabilities.accessCheck.skipControllers', $accessCheck);
    }
}
