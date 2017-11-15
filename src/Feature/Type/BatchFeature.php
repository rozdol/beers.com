<?php
namespace App\Feature\Type;

use Cake\Core\Configure;
use RolesCapabilities\Access\AccessFactory;

class BatchFeature extends BaseFeature
{
    /**
     * {@inheritDoc}
     */
    public function isActive()
    {
        $active = parent::isActive();
        if (!$active) {
            return $active;
        }

        $factory = new AccessFactory();

        $request = $this->config->get('request');
        $url = [
            'plugin' => $request->param('plugin'),
            'controller' => $request->param('controller'),
            'action' => 'batch'
        ];

        $auth = $this->config->get('auth');

        return (bool)$factory->hasAccess($url, $auth->user());
    }

    /**
     * {@inheritDoc}
     */
    public function enable()
    {
        Configure::write([
            'CsvMigrations.batch.active' => true,
            'Search.batch.active' => true
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function disable()
    {
        Configure::write([
            'CsvMigrations.batch.active' => false,
            'Search.batch.active' => false
        ]);
    }
}
