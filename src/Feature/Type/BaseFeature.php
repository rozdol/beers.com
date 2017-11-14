<?php
namespace App\Feature\Type;

use App\Feature\FeatureInterface;
use Cake\Core\Configure;

class BaseFeature implements FeatureInterface
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
    }

    /**
     * {@inheritDoc}
     */
    public function isActive()
    {
        return true;
    }
}
