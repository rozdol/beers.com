<?php
namespace App\Feature\Type;

use App\Feature\AbstractFeature;

class BaseFeature extends AbstractFeature
{
    /**
     * {@inheritDoc}
     */
    public function isActive()
    {
        return true;
    }
}
